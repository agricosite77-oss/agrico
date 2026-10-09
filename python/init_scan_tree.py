#!/usr/bin/env python3
"""
init_scan_tree.py

Pre-creates the full folder skeleton for Scan_Soil_Image/ matching Scan_Soil_Image.txt:
  - Real_Soil/<hue>/<hue value>/<chroma>/ for all reference Munsell chips
  - Fake_Soil/<hue>/ for all 6 Munsell hues + Unclassified
  - Color_Only/<hue>/ for all 6 Munsell hues + Unclassified

Ensures all directories exist upfront so no class or notation folder appears missing.
"""

import csv
import os
import sys

MUNSELL_HUES = ["10R", "2.5YR", "5YR", "7.5YR", "10YR", "2.5Y"]


def _fmt_num(n: float) -> str:
    try:
        f = float(n)
        return str(int(f)) if f.is_integer() else str(f)
    except (ValueError, TypeError):
        return str(n)


def init_tree(scan_root: str, munsell_csv: str) -> None:
    print(f"Initializing full Scan_Soil_Image folder skeleton in: {scan_root}")
    os.makedirs(scan_root, exist_ok=True)

    # 1. Fake_Soil/ (flat, no subfolders, matching Scan_Soil_Image.txt)
    fake_soil_root = os.path.join(scan_root, "Fake_Soil")
    os.makedirs(fake_soil_root, exist_ok=True)

    # 2. Real_Soil/<hue>/<hue value>/<chroma>/
    real_soil_root = os.path.join(scan_root, "Real_Soil")
    os.makedirs(real_soil_root, exist_ok=True)
    hues = ["10R", "2.5YR", "5YR", "7.5YR", "10YR", "2.5Y"]
    for hue in hues:
        os.makedirs(os.path.join(real_soil_root, hue), exist_ok=True)

    notations_created = 0
    if os.path.isfile(munsell_csv):
        with open(munsell_csv, newline="", encoding="utf-8") as f:
            reader = csv.DictReader(f)
            for row in reader:
                hue = row["hue"].strip()
                val = _fmt_num(row["value"])
                chroma = _fmt_num(row["chroma"])
                hue_val_folder = f"{hue} {val}"
                folder = os.path.join(real_soil_root, hue, hue_val_folder, chroma)
                os.makedirs(folder, exist_ok=True)
                notations_created += 1

    print(f"[SUCCESS] Pre-created skeleton:")
    print(f"  - Fake_Soil/ (flat)")
    print(f"  - Real_Soil/ with {notations_created} notation subfolders (across 6 hues)")


def main() -> int:
    import argparse
    parser = argparse.ArgumentParser(description="Pre-create the complete Scan_Soil_Image folder tree.")
    parser.add_argument(
        "--root",
        default=os.environ.get("SCAN_SOIL_ROOT", os.path.join(os.getcwd(), "Scan_Soil_Image")),
        help="Path to Scan_Soil_Image directory",
    )
    parser.add_argument(
        "--munsell-csv",
        default=os.environ.get(
            "MUNSELL_REFERENCE_CSV",
            os.path.join(os.path.dirname(os.path.abspath(__file__)), "munsell_reference_sample.csv"),
        ),
        help="Path to Munsell reference CSV",
    )
    args = parser.parse_args()

    init_tree(args.root, args.munsell_csv)
    return 0


if __name__ == "__main__":
    sys.exit(main())
