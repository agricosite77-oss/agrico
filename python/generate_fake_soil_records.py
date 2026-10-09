"""
generate_fake_soil_records.py

Synchronizes any images from .agents/skills/Fake_Soil into Scan_Soil_Image/Fake_Soil,
cleans obsolete entries in Scan_Soil_Image/soil_scans_index.sqlite3, removes the
misplaced .agents/skills/Fake_Soil folder, and generates companion .json sidecar
metadata files and SQLite index entries for every image in Scan_Soil_Image/Fake_Soil.
"""

import glob
import hashlib
import json
import os
import shutil
import sqlite3
import sys
import uuid
from datetime import datetime, timezone
from PIL import Image

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from soil_classifier import _texture_score
from munsell_lookup import dominant_rgb

IMAGE_EXTS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def sha256_of_file(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()


def sync_images(src_dir: str, dst_dir: str):
    os.makedirs(dst_dir, exist_ok=True)
    if not os.path.exists(src_dir):
        print(f"[INFO] Source directory {src_dir} does not exist. Skipping sync.")
        return

    copied = 0
    for fname in os.listdir(src_dir):
        ext = os.path.splitext(fname)[1].lower()
        if ext in IMAGE_EXTS:
            src_path = os.path.join(src_dir, fname)
            dst_path = os.path.join(dst_dir, fname)
            if not os.path.exists(dst_path) or os.path.getsize(src_path) != os.path.getsize(dst_path):
                shutil.copy2(src_path, dst_path)
                copied += 1
    print(f"[SYNC] Copied {copied} new/updated images from {src_dir} to {dst_dir}.")


def cleanup_source(src_dir: str):
    if os.path.exists(src_dir):
        shutil.rmtree(src_dir, ignore_errors=True)
        print(f"[CLEANUP] Removed temporary folder {src_dir}.")


def clean_stale_sqlite_index(db_path: str):
    if not os.path.exists(db_path):
        print(f"[DB] Index database not found at {db_path}.")
        return

    with sqlite3.connect(db_path) as conn:
        cursor = conn.cursor()
        # Find all rows whose file_path does not exist on disk
        rows = cursor.execute("SELECT image_id, file_path FROM soil_scans").fetchall()
        stale_ids = [r[0] for r in rows if not os.path.exists(r[1])]
        if stale_ids:
            cursor.executemany("DELETE FROM soil_scans WHERE image_id = ?", [(i,) for i in stale_ids])
            conn.commit()
            print(f"[DB] Purged {len(stale_ids)} stale rows pointing to deleted files.")
        else:
            print("[DB] No stale rows found.")


def generate_records_for_fake_soil(dst_dir: str, db_path: str):
    os.makedirs(dst_dir, exist_ok=True)
    images = [
        os.path.join(dst_dir, f)
        for f in os.listdir(dst_dir)
        if os.path.splitext(f)[1].lower() in IMAGE_EXTS
    ]

    print(f"[RECORDS] Processing {len(images)} fake soil images in {dst_dir}...")

    with sqlite3.connect(db_path) as conn:
        cursor = conn.cursor()
        indexed_count = 0
        json_created = 0

        for path in images:
            file_hash = sha256_of_file(path)
            sidecar_path = os.path.splitext(path)[0] + ".json"

            # Compute dominant rgb and texture
            try:
                rgb = dominant_rgb(path)
            except Exception:
                rgb = (0, 0, 0)

            try:
                tex = _texture_score(path)
            except Exception:
                tex = 0.0

            timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
            image_id = uuid.uuid4().hex

            # Check if sidecar exists
            if os.path.exists(sidecar_path):
                try:
                    with open(sidecar_path, "r", encoding="utf-8") as f:
                        meta = json.load(f)
                    image_id = meta.get("image_id", image_id)
                except Exception:
                    pass

            metadata = {
                "image_id": image_id,
                "file_path": os.path.abspath(path),
                "predicted_label": "fake_soil",
                "confidence": 0.99,
                "munsell_hue": "NA",
                "munsell_value": -1,
                "munsell_chroma": -1,
                "munsell_notation": "NA",
                "color_delta_e": -1.0,
                "texture_score": round(tex, 3),
                "rgb": rgb,
                "source": "seed_fake_soil",
                "timestamp": timestamp,
            }

            with open(sidecar_path, "w", encoding="utf-8") as f:
                json.dump(metadata, f, indent=2)
            json_created += 1

            # Check if in sqlite
            row = cursor.execute("SELECT image_id FROM soil_scans WHERE file_sha256 = ?", (file_hash,)).fetchone()
            if not row:
                cursor.execute(
                    """
                    INSERT INTO soil_scans (
                        image_id, file_path, predicted_label, confidence,
                        munsell_hue, munsell_value, munsell_chroma,
                        munsell_notation, color_delta_e, texture_score, file_sha256,
                        source, timestamp
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    """,
                    (
                        image_id,
                        os.path.abspath(path),
                        "fake_soil",
                        0.99,
                        "NA",
                        -1,
                        -1,
                        "NA",
                        -1.0,
                        round(tex, 3),
                        file_hash,
                        "seed_fake_soil",
                        timestamp,
                    ),
                )
                indexed_count += 1

        conn.commit()
        print(f"[RECORDS] Created/updated {json_created} JSON sidecars.")
        print(f"[DB] Inserted {indexed_count} new entries into {db_path}.")


def main():
    root = os.path.abspath("Scan_Soil_Image")
    fake_soil_dir = os.path.join(root, "Fake_Soil")
    agents_fake_soil = os.path.abspath(os.path.join(".agents", "skills", "Fake_Soil"))
    db_path = os.path.join(root, "soil_scans_index.sqlite3")

    print("=== Step 1: Syncing images from .agents/skills/Fake_Soil ===")
    sync_images(agents_fake_soil, fake_soil_dir)

    print("\n=== Step 2: Cleaning up source folder .agents/skills/Fake_Soil ===")
    cleanup_source(agents_fake_soil)

    print("\n=== Step 3: Purging stale entries from SQLite index ===")
    clean_stale_sqlite_index(db_path)

    print("\n=== Step 4: Generating JSON sidecars and SQLite records for Fake_Soil ===")
    generate_records_for_fake_soil(fake_soil_dir, db_path)

    print("\n=== Finished successfully ===")


if __name__ == "__main__":
    main()
