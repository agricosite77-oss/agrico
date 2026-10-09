"""
storage.py

Handles saving each scanned image into the Scan_Soil_Image/ folder tree,
writing a companion .json metadata file, and maintaining a SQLite index
for fast dataset lookups (used by both re-training and the reindex CLI).

Folder layout:
    Scan_Soil_Image/
        Real_Soil/<hue>/<hue value/chroma>/<filename>.jpg
        Fake_Soil/<filename>.jpg
        Color_Only/<filename>.jpg

Filename convention:
    <label>_<munsell_notation_or_NA>_<timestamp>_<uid8>.<ext>
    e.g. real_soil_10R4-3_20260901T053000Z_9f3a1c02.jpg
"""

import hashlib
import json
import os
import shutil
import sqlite3
import uuid
from dataclasses import asdict
from datetime import datetime, timezone
from typing import Optional, TYPE_CHECKING

if TYPE_CHECKING:
    from soil_classifier import ClassificationResult

DB_SCHEMA = """
CREATE TABLE IF NOT EXISTS soil_scans (
    image_id        TEXT PRIMARY KEY,
    file_path       TEXT NOT NULL,
    predicted_label TEXT NOT NULL, -- 'real_soil' | 'fake_soil' (historical: 'color_only')
    confidence      REAL NOT NULL,
    munsell_hue     TEXT,
    munsell_value   REAL,
    munsell_chroma  REAL,
    munsell_notation TEXT,
    color_delta_e   REAL,
    texture_score   REAL,
    regions         TEXT,          -- JSON array of per-region results (Stages 3-6)
    status          TEXT,          -- 'verified' | 'needs_review' (Stage 6 dominant-region status)
    stage2_normalized_path TEXT,   -- path to Stage 2 lighting-normalized image
    file_sha256     TEXT NOT NULL UNIQUE,
    source          TEXT NOT NULL,
    timestamp       TEXT NOT NULL
);
"""


def sha256_of_file(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()


def _fmt_num(n: float) -> str:
    try:
        f = float(n)
        return str(int(f)) if f.is_integer() else str(f)
    except (ValueError, TypeError):
        return str(n)


def _safe_notation_for_filename(notation: str) -> str:
    # "10R 4/3" -> "10R4-3" (filesystem/URL safe)
    return notation.replace(" ", "").replace("/", "-")


class SoilScanStore:
    def __init__(self, root_dir: str, db_path: Optional[str] = None):
        self.root_dir = root_dir
        self.db_path = db_path or os.path.join(root_dir, "soil_scans_index.sqlite3")
        os.makedirs(self.root_dir, exist_ok=True)
        self._ensure_root_skeleton()
        self._init_db()

    def _ensure_root_skeleton(self) -> None:
        """Pre-creates the class and hue directories so none appear missing."""
        os.makedirs(os.path.join(self.root_dir, "Fake_Soil"), exist_ok=True)
        hues = ["10R", "2.5YR", "5YR", "7.5YR", "10YR", "2.5Y"]
        for hue in hues:
            os.makedirs(os.path.join(self.root_dir, "Real_Soil", hue), exist_ok=True)

    def _init_db(self) -> None:
        with sqlite3.connect(self.db_path) as conn:
            conn.execute(DB_SCHEMA)
            # Migration guards for columns added after initial schema
            for alter_sql in [
                "ALTER TABLE soil_scans ADD COLUMN texture_score REAL",
                "ALTER TABLE soil_scans ADD COLUMN regions TEXT",
                "ALTER TABLE soil_scans ADD COLUMN status TEXT",
                "ALTER TABLE soil_scans ADD COLUMN stage2_normalized_path TEXT",
            ]:
                try:
                    conn.execute(alter_sql)
                except sqlite3.OperationalError:
                    pass  # column already exists
            conn.commit()

    def _destination_dir(self, result: ClassificationResult) -> str:
        """
        Builds the destination directory matching Scan_Soil_Image.txt:
        Real_Soil/<hue>/<hue value>/<chroma>/ (e.g. Real_Soil/10R/10R 4/3/)
        Fake_Soil/ (flat, no subfolders)
        """
        if result.label == "real_soil":
            hue_folder = result.munsell_hue if result.munsell_hue and result.munsell_hue != "NA" else "Unclassified"
            hue_val_folder = f"{result.munsell_hue} {_fmt_num(result.munsell_value)}"
            chroma_folder = _fmt_num(result.munsell_chroma)
            return os.path.join(
                self.root_dir, "Real_Soil", hue_folder, hue_val_folder, chroma_folder
            )
        else:  # fake_soil (and any other non-real-soil scans) -> flat Fake_Soil/
            return os.path.join(self.root_dir, "Fake_Soil")

    def already_indexed(self, file_sha256: str) -> bool:
        with sqlite3.connect(self.db_path) as conn:
            row = conn.execute(
                "SELECT image_id FROM soil_scans WHERE file_sha256 = ?", (file_sha256,)
            ).fetchone()
            return row is not None

    def save_scan(
        self,
        source_image_path: str,
        result: ClassificationResult,
        source: str = "user_scan",
    ) -> dict:
        """
        Copies the image into the correct class/Munsell folder, writes a
        .json sidecar, and inserts a row into the SQLite index.
        Skips (no-op, returns existing record) if this exact file was
        already indexed (dedupe by content hash).

        Returns a dict matching the JSON schema returned to PHP/React
        Native by colorimetry_service.py.
        """
        file_hash = sha256_of_file(source_image_path)

        if self.already_indexed(file_hash):
            with sqlite3.connect(self.db_path) as conn:
                conn.row_factory = sqlite3.Row
                row = conn.execute(
                    "SELECT * FROM soil_scans WHERE file_sha256 = ?", (file_hash,)
                ).fetchone()
                rec = dict(row)
                if not rec.get("stage2_normalized_path"):
                    file_p = rec.get("file_path", "")
                    if file_p:
                        sidecar_p = os.path.splitext(file_p)[0] + ".json"
                        if os.path.exists(sidecar_p):
                            try:
                                with open(sidecar_p, "r", encoding="utf-8") as sf:
                                    sc_meta = json.load(sf)
                                    if sc_meta.get("stage2_normalized_path"):
                                        rec["stage2_normalized_path"] = sc_meta["stage2_normalized_path"]
                                    if sc_meta.get("stage2_normalized_color"):
                                        rec["stage2_normalized_color"] = sc_meta["stage2_normalized_color"]
                            except Exception:
                                pass
                        if not rec.get("stage2_normalized_path"):
                            base_no_ext, ext = os.path.splitext(file_p)
                            candidate_norm = f"{base_no_ext}_stage2_normalized{ext}"
                            if os.path.exists(candidate_norm):
                                rec["stage2_normalized_path"] = candidate_norm
                return rec

        dest_dir = self._destination_dir(result)
        os.makedirs(dest_dir, exist_ok=True)

        ext = os.path.splitext(source_image_path)[1].lower() or ".jpg"
        timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
        uid8 = uuid.uuid4().hex[:8]
        notation_part = _safe_notation_for_filename(result.munsell_notation)
        filename = f"{result.label}_{notation_part}_{timestamp}_{uid8}{ext}"

        dest_path = os.path.join(dest_dir, filename)
        shutil.copy2(source_image_path, dest_path)

        # Save Stage 2 lighting-normalized image in the same folder
        norm_image = getattr(result, "normalized_image", None)
        norm_dest_path = None
        if norm_image is not None:
            norm_filename = f"{result.label}_{notation_part}_{timestamp}_{uid8}_stage2_normalized{ext}"
            norm_dest_path = os.path.join(dest_dir, norm_filename)
            try:
                norm_image.save(norm_dest_path, quality=95)
            except Exception:
                norm_dest_path = None

        # Generate and save Stage 2 4-image comparison grid in the same folder
        comp_dest_path = None
        comp_explanation = None
        if norm_image is not None:
            try:
                from lighting_normalizer import generate_stage2_comparison
                comp_grid, comp_explanation = generate_stage2_comparison(norm_image)
                comp_filename = f"{result.label}_{notation_part}_{timestamp}_{uid8}_stage2_comparison{ext}"
                comp_dest_path = os.path.join(dest_dir, comp_filename)
                comp_grid.save(comp_dest_path, quality=95)
            except Exception as exc:
                import sys
                sys.stderr.write(f"[Storage] Failed to generate stage 2 comparison: {exc}\n")
                comp_dest_path = None
                comp_explanation = None

        image_id = uuid.uuid4().hex

        # Strip non-serialisable fields (pixel_mask, region_image) before storing
        serialisable_regions = []
        for r in (result.regions or []):
            serialisable_regions.append({
                k: v for k, v in r.items()
                if k not in ("pixel_mask", "region_image")
            })
        regions_json = json.dumps(serialisable_regions)

        metadata = {
            "image_id": image_id,
            "file_path": dest_path,
            "predicted_label": result.label,
            "confidence": result.confidence,
            "munsell_hue": result.munsell_hue,
            "munsell_value": result.munsell_value,
            "munsell_chroma": result.munsell_chroma,
            "munsell_notation": result.munsell_notation,
            "color_delta_e": result.color_delta_e,
            "texture_score": result.texture_score,
            "rgb": list(result.rgb),
            "regions": serialisable_regions,
            "status": getattr(result, "status", "needs_review"),
        }
        if norm_dest_path and os.path.exists(norm_dest_path):
            metadata["stage2_normalized_path"] = norm_dest_path
            try:
                from munsell_lookup import dominant_rgb_from_image
                norm_rgb = dominant_rgb_from_image(norm_image)
                metadata["stage2_normalized_color"] = {
                    "rgb": list(norm_rgb),
                    "hex": f"#{norm_rgb[0]:02X}{norm_rgb[1]:02X}{norm_rgb[2]:02X}",
                }
            except Exception:
                pass

        if comp_dest_path and os.path.exists(comp_dest_path) and comp_explanation:
            metadata["stage2_comparison"] = {
                "image_path": comp_dest_path,
                "explanation": comp_explanation,
            }

        metadata["source"] = source
        metadata["timestamp"] = timestamp

        sidecar_path = os.path.splitext(dest_path)[0] + ".json"
        with open(sidecar_path, "w", encoding="utf-8") as f:
            json.dump(metadata, f, indent=2)

        with sqlite3.connect(self.db_path) as conn:
            conn.execute(
                """
                INSERT INTO soil_scans (
                    image_id, file_path, predicted_label, confidence,
                    munsell_hue, munsell_value, munsell_chroma,
                    munsell_notation, color_delta_e, texture_score, regions, status,
                    file_sha256, source, timestamp, stage2_normalized_path
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                """,
                (
                    image_id, dest_path, result.label, result.confidence,
                    result.munsell_hue, result.munsell_value, result.munsell_chroma,
                    result.munsell_notation, result.color_delta_e, result.texture_score,
                    regions_json, getattr(result, "status", "needs_review"),
                    file_hash, source, timestamp, norm_dest_path,
                ),
            )
            conn.commit()

        return metadata
