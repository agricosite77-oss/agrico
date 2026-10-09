#!/usr/bin/env python3
"""
bootstrap_real_soil_from_dataset.py — Auto-labels an unlabeled soil image dataset
by matching each image's dominant color against the Munsell reference CSV chart,
then files it into the corresponding Real_Soil/<hue>/<value>/<chroma>/ folder.

This is a BOOTSTRAP, not a final answer — matches are based on the static reference
CSV (not yet empirical data), so accuracy improves automatically over time as
munsell_lookup.py's refresh_centroids() switches to real dataset centroids once
enough images accumulate per notation.

Usage:
    python bootstrap_real_soil_from_dataset.py <source_folder> [--sample N] [--seed 42] [--refresh-centroids]

--sample N            Only process a random sample of N images instead of the whole dataset
                      (useful for testing before committing to the full run).
--seed 42             Random seed for deterministic sampling.
--refresh-centroids   Automatically recomputes empirical centroids after filing.
"""

import argparse
import os
import random
import shutil
import sys
import uuid
from datetime import datetime, timezone

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from munsell_lookup import MunsellTable, dominant_rgb

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def find_all_images(source_folder):
    paths = []
    for root, _, files in os.walk(source_folder):
        for fname in files:
            if os.path.splitext(fname)[1].lower() in IMAGE_EXTENSIONS:
                paths.append(os.path.join(root, fname))
    return paths


def fmt_num(n):
    f = float(n)
    return str(int(f)) if f.is_integer() else str(f)


def main():
    parser = argparse.ArgumentParser(
        description="Auto-labels an unlabeled soil dataset into the Real_Soil folder tree."
    )
    parser.add_argument("source_folder", help="Folder containing the unlabeled soil dataset")
    parser.add_argument("--sample", type=int, default=None, help="Randomly sample N images instead of processing all")
    parser.add_argument("--seed", type=int, default=42, help="Random seed for sampling reproducibility")
    parser.add_argument(
        "--refresh-centroids",
        action="store_true",
        help="Recompute and refresh empirical centroids after finishing the filing process"
    )
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))
    scan_root = os.environ.get("SCAN_SOIL_ROOT")
    if not scan_root:
        candidate_python = os.path.join(script_dir, "Scan_Soil_Image")
        candidate_cwd = os.path.join(os.getcwd(), "Scan_Soil_Image")
        if os.path.isdir(candidate_python):
            scan_root = candidate_python
        elif os.path.isdir(candidate_cwd):
            scan_root = candidate_cwd
        else:
            scan_root = candidate_python

    default_csv = os.path.join(script_dir, "munsell_reference_full.csv")
    if not os.path.isfile(default_csv):
        default_csv = os.path.join(script_dir, "munsell_reference_sample.csv")

    print(f"Using Scan Root: {scan_root}")
    print(f"Loading Munsell reference table from: {default_csv} ...")
    munsell_table = MunsellTable(default_csv, scan_root=scan_root)

    print(f"Scanning '{args.source_folder}' for images...")
    all_paths = find_all_images(args.source_folder)
    print(f"Found {len(all_paths)} total images.")

    if not all_paths:
        print("No images found to process.")
        return

    if args.sample:
        random.seed(args.seed)
        all_paths = random.sample(all_paths, min(args.sample, len(all_paths)))
        print(f"Processing a random sample of {len(all_paths)} images.")

    notation_counts = {}
    count = 0
    skipped = 0

    for src_path in all_paths:
        try:
            rgb = dominant_rgb(src_path)
            chip, delta_e = munsell_table.nearest(rgb)
            if chip is None:
                skipped += 1
                continue

            hue_val_folder = f"{chip.hue} {fmt_num(chip.value)}"
            chroma_folder = fmt_num(chip.chroma)
            dest_dir = os.path.join(scan_root, "Real_Soil", chip.hue, hue_val_folder, chroma_folder)
            os.makedirs(dest_dir, exist_ok=True)

            ext = os.path.splitext(src_path)[1].lower()
            timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
            uid8 = uuid.uuid4().hex[:8]
            notation_part = f"{chip.hue}{fmt_num(chip.value)}-{fmt_num(chip.chroma)}"
            filename = f"real_soil_{notation_part}_{timestamp}_{uid8}{ext}"
            dest_path = os.path.join(dest_dir, filename)

            shutil.copy2(src_path, dest_path)
            count += 1

            notation_key = chip.notation
            notation_counts[notation_key] = notation_counts.get(notation_key, 0) + 1

            if count % 100 == 0:
                print(f"Processed {count} images so far...")
        except Exception as e:
            print(f"Skipped {src_path}: {e}")
            skipped += 1

    print(f"\nDone. Filed {count} images across {len(notation_counts)} notations. Skipped {skipped}.")
    print("\nTop notations by count:")
    for notation, n in sorted(notation_counts.items(), key=lambda x: -x[1])[:15]:
        print(f"  {notation}: {n}")

    if args.refresh_centroids:
        print("\nRefreshing empirical centroids...")
        munsell_table.refresh_centroids(force=True)
        print("Empirical centroids refreshed successfully.")


if __name__ == "__main__":
    main()
