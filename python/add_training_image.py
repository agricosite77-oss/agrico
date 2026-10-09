#!/usr/bin/env python3
"""
add_training_image.py — Manual training-data labeling tool.

Files a photo directly into Scan_Soil_Image/Real_Soil/<hue>/<value>/<chroma>/ or
Scan_Soil_Image/Fake_Soil/ based on a human-provided label. No ML classification
runs here — this is for building ground-truth training data, not testing the model.

CONTRACT:
    argv[1] = image_path
    argv[2] = label ("real_soil" | "fake_soil")
    argv[3..5] = hue, value, chroma (REQUIRED only when label == "real_soil")
    stdout: {"success": true, "stored_path": "...", "label": "..."} or {"success": false, "error": "..."}
"""

import json
import os
import shutil
import sqlite3
import sys
import uuid
from datetime import datetime, timezone

import hashlib

def sha256_of_file(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()


def _fmt_num(n) -> str:
    try:
        f = float(n)
        return str(int(f)) if f.is_integer() else str(f)
    except Exception:
        return str(n)


def main() -> int:
    if len(sys.argv) < 3:
        print(json.dumps({
            "success": False,
            "error": "Usage: add_training_image.py <image_path> <real_soil|fake_soil> [hue value chroma]",
        }))
        return 1

    image_path, label = sys.argv[1], sys.argv[2]
    if not os.path.isfile(image_path):
        print(json.dumps({"success": False, "error": f"Image not found: {image_path}"}))
        return 1

    scan_root = os.environ.get("SCAN_SOIL_ROOT", os.path.join(os.getcwd(), "Scan_Soil_Image"))
    db_path = os.path.join(scan_root, "soil_scans_index.sqlite3")

    munsell_hue = "NA"
    munsell_val = -1.0
    munsell_chr = -1.0
    munsell_not = "NA"

    if label == "real_soil":
        # Munsell parameters (hue, value, chroma) are optional
        if len(sys.argv) >= 6 and sys.argv[3] and sys.argv[4] and sys.argv[5]:
            hue, value, chroma = sys.argv[3], sys.argv[4], sys.argv[5]
            hue_val_folder = f"{hue} {_fmt_num(value)}"
            chroma_folder = _fmt_num(chroma)
            dest_dir = os.path.join(scan_root, "Real_Soil", hue, hue_val_folder, chroma_folder)
            notation_part = f"{hue}{_fmt_num(value)}-{_fmt_num(chroma)}"

            munsell_hue = hue
            try:
                munsell_val = float(value)
                munsell_chr = float(chroma)
            except Exception:
                pass
            munsell_not = f"{hue} {_fmt_num(value)}/{_fmt_num(chroma)}"
        else:
            dest_dir = os.path.join(scan_root, "Real_Soil")
            notation_part = "NA"
            munsell_hue = "NA"
            munsell_val = -1.0
            munsell_chr = -1.0
            munsell_not = "NA"
    elif label == "fake_soil":
        dest_dir = os.path.join(scan_root, "Fake_Soil")
        notation_part = "NA"
    else:
        print(json.dumps({"success": False, "error": f"Unknown label: {label}"}))
        return 1

    try:
        os.makedirs(dest_dir, exist_ok=True)
        ext = os.path.splitext(image_path)[1].lower() or ".jpg"
        timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
        image_id = uuid.uuid4().hex
        uid8 = image_id[:8]
        filename = f"{label}_{notation_part}_{timestamp}_{uid8}{ext}"
        dest_path = os.path.join(dest_dir, filename)
        shutil.copy2(image_path, dest_path)

        # Create companion .json sidecar
        sidecar_path = os.path.splitext(dest_path)[0] + ".json"
        metadata = {
            "image_id": image_id,
            "file_path": os.path.abspath(dest_path),
            "predicted_label": label,
            "confidence": 1.0,
            "munsell_hue": munsell_hue,
            "munsell_value": munsell_val,
            "munsell_chroma": munsell_chr,
            "munsell_notation": munsell_not,
            "color_delta_e": 0.0 if label == "real_soil" else -1.0,
            "texture_score": 1.0,
            "source": "manual_training",
            "timestamp": timestamp,
        }
        with open(sidecar_path, "w", encoding="utf-8") as f:
            json.dump(metadata, f, indent=2)

        # Index in SQLite if db exists
        if os.path.exists(db_path):
            try:
                file_hash = sha256_of_file(dest_path)
                with sqlite3.connect(db_path) as conn:
                    conn.execute(
                        """
                        INSERT OR REPLACE INTO soil_scans (
                            image_id, file_path, predicted_label, confidence,
                            munsell_hue, munsell_value, munsell_chroma,
                            munsell_notation, color_delta_e, texture_score,
                            file_sha256, source, timestamp
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        """,
                        (
                            image_id,
                            os.path.abspath(dest_path),
                            label,
                            1.0,
                            munsell_hue,
                            munsell_val,
                            munsell_chr,
                            munsell_not,
                            0.0 if label == "real_soil" else -1.0,
                            1.0,
                            file_hash,
                            "manual_training",
                            timestamp,
                        ),
                    )
                    conn.commit()
            except Exception:
                pass  # Non-fatal if sqlite index insertion encounters table lock

    except Exception as exc:
        print(json.dumps({"success": False, "error": str(exc)}))
        return 1

    print(json.dumps({
        "success": True,
        "stored_path": dest_path,
        "label": label,
        "notation": munsell_not if label == "real_soil" else "NA",
    }))
    return 0


if __name__ == "__main__":
    sys.exit(main())
