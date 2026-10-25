#!/usr/bin/env python3
"""
prepare_kaggle_dataset.py - Packages real and fake soil images into a clean zip
ready for direct drag-and-drop upload to Kaggle Datasets.
"""

import argparse
import os
import random
import shutil
import zipfile

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".webp"}


def find_images(folder):
    paths = []
    if not os.path.exists(folder):
        return paths
    for root, _, files in os.walk(folder):
        for f in files:
            if os.path.splitext(f)[1].lower() in IMAGE_EXTENSIONS:
                paths.append(os.path.join(root, f))
    return paths


def main():
    parser = argparse.ArgumentParser(description="Package dataset for Kaggle upload")
    parser.add_argument("--fake-sample", type=int, default=3000,
                        help="Number of non-soil/face images to sample (default: 3000)")
    parser.add_argument("--out-zip", default="kaggle_dataset.zip",
                        help="Output zip file path (default: kaggle_dataset.zip)")
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))
    repo_root = os.path.dirname(script_dir)

    # 1. Collect Real Soil Images
    real_paths = []
    real_dirs = [
        os.path.join(script_dir, "Scan_Soil_Image", "Real_Soil"),
        os.path.join(script_dir, "external_datasets", "Soil-Dataset"),
    ]
    for d in real_dirs:
        real_paths.extend(find_images(d))
    # Deduplicate by filename/basename
    seen_real = set()
    unique_real = []
    for p in real_paths:
        base = os.path.basename(p)
        if base not in seen_real and not base.endswith("_stage2_comparison.jpg"):
            seen_real.add(base)
            unique_real.append(p)

    print(f"[1/3] Found {len(unique_real)} unique real soil images.")

    # 2. Collect Fake Soil / Face Images
    fake_dirs = [
        os.path.join(script_dir, "Scan_Soil_Image", "Fake_Soil"),
        os.path.join(script_dir, "external_datasets", "faces_raw"),
    ]
    fake_paths = []
    for d in fake_dirs:
        fake_paths.extend(find_images(d))

    seen_fake = set()
    unique_fake = []
    for p in fake_paths:
        base = os.path.basename(p)
        if base not in seen_fake:
            seen_fake.add(base)
            unique_fake.append(p)

    print(f"[2/3] Found {len(unique_fake)} unique non-soil/face images.")
    random.seed(42)
    sample_size = min(args.fake_sample, len(unique_fake))
    sampled_fake = random.sample(unique_fake, sample_size)
    print(f"      Sampled {sample_size} diverse fake soil images.")

    # 3. Create Zip file
    out_zip = os.path.join(repo_root, args.out_zip)
    print(f"[3/3] Creating zip file at '{out_zip}'...")
    with zipfile.ZipFile(out_zip, "w", zipfile.ZIP_DEFLATED) as zf:
        for i, p in enumerate(unique_real):
            arcname = f"dataset/real_soil/real_{i:05d}{os.path.splitext(p)[1]}"
            zf.write(p, arcname)

        for i, p in enumerate(sampled_fake):
            arcname = f"dataset/fake_soil/fake_{i:05d}{os.path.splitext(p)[1]}"
            zf.write(p, arcname)

    size_mb = os.path.getsize(out_zip) / (1024 * 1024)
    print(f"\n[SUCCESS] '{out_zip}' created successfully ({size_mb:.2f} MB).")
    print(f"Total: {len(unique_real)} real soil + {len(sampled_fake)} fake soil images.")
    print("Ready to upload directly to Kaggle Datasets!")
    return 0


if __name__ == "__main__":
    main()
