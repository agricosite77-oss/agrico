#!/usr/bin/env python3
"""
reindex.py

Walks an existing Scan_Soil_Image/ folder tree and (re)builds the SQLite
index by re-running classification + Munsell matching on files that
aren't indexed yet. Use this to bootstrap the index from images you
already placed on disk (e.g. a seed dataset), or to catch anything the
API-driven save_scan() path missed.

Usage:
    python reindex.py --mode full
    python reindex.py --mode sampled --n 10
    python reindex.py --mode full --root /custom/path/Scan_Soil_Image \
                       --munsell-csv /custom/munsell_reference.csv \
                       --source seed_dataset

Modes:
    full     - process every image file found anywhere under the tree
    sampled  - process at most N images per leaf subfolder (default 10):
               10 per Munsell notation folder under Real_Soil/, 10 from
               Fake_Soil/, 10 from Color_Only/. Useful for quick spot
               checks / fast re-training runs without walking everything.

Already-indexed files (matched by content hash) are skipped automatically
so re-running this script never creates duplicate entries.
"""

import argparse
import os
import sys
from collections import defaultdict

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from munsell_lookup import MunsellTable          # noqa: E402
from soil_classifier import classify_soil        # noqa: E402
from storage import SoilScanStore, sha256_of_file  # noqa: E402

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def _is_image(filename: str) -> bool:
    return os.path.splitext(filename)[1].lower() in IMAGE_EXTENSIONS


def _collect_by_leaf_folder(root: str) -> dict:
    """Groups image file paths by their immediate parent folder."""
    grouped = defaultdict(list)
    for dirpath, _dirnames, filenames in os.walk(root):
        for fname in filenames:
            if _is_image(fname):
                grouped[dirpath].append(os.path.join(dirpath, fname))
    return grouped


def run(root: str, munsell_csv: str, mode: str, n: int, source: str) -> None:
    munsell_table = MunsellTable(munsell_csv, scan_root=root)
    store = SoilScanStore(root_dir=root)

    grouped = _collect_by_leaf_folder(root)

    processed = 0
    skipped_already_indexed = 0
    skipped_by_sample_cap = 0
    failed = 0

    for folder, files in grouped.items():
        files_to_process = files if mode == "full" else files[:n]
        if mode == "sampled":
            skipped_by_sample_cap += max(0, len(files) - n)

        for path in files_to_process:
            try:
                file_hash = sha256_of_file(path)
                if store.already_indexed(file_hash):
                    skipped_already_indexed += 1
                    continue

                result = classify_soil(path, munsell_table)
                store.save_scan(path, result, source=source)
                processed += 1
                print(
                    f"[OK]      {os.path.basename(path)}  ->  {result.label} "
                    f"(conf={result.confidence}, munsell={result.munsell_notation}, "
                    f"texture={result.texture_score}, delta_e={result.color_delta_e})"
                )

            except Exception as exc:  # noqa: BLE001
                failed += 1
                print(f"[FAILED]  {path}  ->  {exc}", file=sys.stderr)

    print("\n--- Reindex summary ---")
    print(f"Mode:                    {mode}" + (f" (n={n})" if mode == "sampled" else ""))
    print(f"Processed (new):         {processed}")
    print(f"Skipped (already indexed): {skipped_already_indexed}")
    if mode == "sampled":
        print(f"Skipped (over sample cap): {skipped_by_sample_cap}")
    print(f"Failed:                  {failed}")


def main() -> int:
    parser = argparse.ArgumentParser(description="Reindex Scan_Soil_Image/ into the SQLite index.")
    parser.add_argument(
        "--init", action="store_true",
        help="Pre-create the full Scan_Soil_Image folder skeleton (Real_Soil, Fake_Soil)",
    )
    parser.add_argument(
        "--mode", choices=["full", "sampled"], default="full",
        help="full = process every image; sampled = up to N per subfolder",
    )
    parser.add_argument(
        "--n", type=int, default=10,
        help="Images per subfolder in sampled mode (default: 10)",
    )
    default_root = os.environ.get(
        "SCAN_SOIL_ROOT",
        os.path.join(os.path.dirname(os.path.abspath(__file__)), "Scan_Soil_Image")
    )
    parser.add_argument(
        "--root", default=default_root,
        help="Path to Scan_Soil_Image/ (default: $SCAN_SOIL_ROOT or python/Scan_Soil_Image)",
    )
    default_csv = os.path.join(os.path.dirname(os.path.abspath(__file__)), "munsell_reference_full.csv")
    if not os.path.exists(default_csv):
        default_csv = os.path.join(os.path.dirname(os.path.abspath(__file__)), "munsell_reference_sample.csv")
    parser.add_argument(
        "--munsell-csv",
        default=os.environ.get("MUNSELL_REFERENCE_CSV", default_csv),
        help="Path to the Munsell reference CSV",
    )
    parser.add_argument(
        "--source", default="seed_dataset",
        help="Value stored in the 'source' field for these scans (default: seed_dataset)",
    )
    args = parser.parse_args()

    if args.init:
        from init_scan_tree import init_tree
        init_tree(args.root, args.munsell_csv)

    run(root=args.root, munsell_csv=args.munsell_csv, mode=args.mode, n=args.n, source=args.source)
    return 0


if __name__ == "__main__":
    sys.exit(main())
