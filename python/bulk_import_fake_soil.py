#!/usr/bin/env python3
"""
bulk_import_fake_soil.py — Recursively find all images in a source folder (e.g. a
downloaded Kaggle face dataset), take a RANDOM sample of them, and import that sample
into Scan_Soil_Image/Fake_Soil/. Random sampling matters here specifically because
CASIA-WebFace has one folder per identity — sampling randomly across the whole file
list (not just the first N found) ensures diversity across many different people
rather than over-representing whichever folders happen to sort first.

Usage:
    python bulk_import_fake_soil.py <source_folder> --sample N [--seed 42]

--sample N   Number of images to randomly select and import (e.g. 1000).
--seed N     Random seed for reproducibility (default 42).
"""

import argparse
import os
import random
import shutil
import uuid
from datetime import datetime, timezone

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def find_all_images(source_folder):
    paths = []
    for root, _, files in os.walk(source_folder):
        for fname in files:
            if os.path.splitext(fname)[1].lower() in IMAGE_EXTENSIONS:
                paths.append(os.path.join(root, fname))
    return paths


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("source_folder", help="Folder containing the downloaded face images")
    parser.add_argument("--sample", type=int, required=True, help="Number of images to randomly sample and import")
    parser.add_argument("--seed", type=int, default=42, help="Random seed for reproducible sampling")
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))
    default_scan_root = os.path.join(script_dir, "Scan_Soil_Image")
    scan_root = os.environ.get("SCAN_SOIL_ROOT", default_scan_root)
    dest_dir = os.path.join(scan_root, "Fake_Soil")
    os.makedirs(dest_dir, exist_ok=True)

    print(f"Scanning '{args.source_folder}' for all images...")
    all_paths = find_all_images(args.source_folder)
    print(f"Found {len(all_paths)} total images across all identity folders.")

    if not all_paths:
        print("No images found in the source folder!")
        return 1

    random.seed(args.seed)
    sample_size = min(args.sample, len(all_paths))
    sampled_paths = random.sample(all_paths, sample_size)
    print(f"Randomly sampled {sample_size} images across diverse identities.")

    count = 0
    for src_path in sampled_paths:
        ext = os.path.splitext(src_path)[1].lower()
        timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
        uid8 = uuid.uuid4().hex[:8]
        filename = f"fake_soil_NA_{timestamp}_{uid8}{ext}"
        dest_path = os.path.join(dest_dir, filename)

        try:
            shutil.copy2(src_path, dest_path)
            count += 1
            if count % 100 == 0:
                print(f"Imported {count}/{sample_size} images so far...")
        except Exception as e:
            print(f"Skipped {src_path}: {e}")

    print(f"\nDone. Successfully imported {count} randomly-sampled images into {dest_dir}")
    return 0


if __name__ == "__main__":
    main()
