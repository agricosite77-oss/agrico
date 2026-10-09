"""
munsell_lookup.py

Dataset-driven Munsell matcher with calibrated reference CSV fallback.
Computes empirical Lab color centroids from images stored in
Scan_Soil_Image/Real_Soil/<hue>/<hue value>/<chroma>/ and matches
incoming scans to the nearest notation centroid.
"""

import csv
import json
import math
import os
import sys
from dataclasses import dataclass
from typing import Dict, List, Optional, Tuple

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from PIL import Image

try:
    from lighting_normalizer import normalize_lighting
except ImportError:
    try:
        from python.lighting_normalizer import normalize_lighting
    except ImportError:
        normalize_lighting = None

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def _fmt_num(n: float) -> str:
    try:
        f = float(n)
        return str(int(f)) if f.is_integer() else str(f)
    except (ValueError, TypeError):
        return str(n)


@dataclass
class MunsellChip:
    hue: str            # e.g. "10R"
    value: float        # e.g. 4 or 2.5
    chroma: float       # e.g. 3
    rgb: Tuple[int, int, int]
    lab: Optional[Tuple[float, float, float]] = None
    sample_count: int = 0
    source: str = "reference_csv"  # "reference_csv" or "dataset_centroid"
    avg_texture: float = 0.20

    def __post_init__(self):
        if self.lab is None:
            self.lab = _rgb_to_lab(self.rgb)

    @property
    def notation(self) -> str:
        return f"{self.hue} {_fmt_num(self.value)}/{_fmt_num(self.chroma)}"

    @property
    def folder_notation(self) -> str:
        return self.notation


def _rgb_to_lab(rgb: Tuple[int, int, int]) -> Tuple[float, float, float]:
    """Simple sRGB -> CIE Lab conversion (D65)."""
    def _srgb_to_linear(c):
        c = c / 255.0
        return c / 12.92 if c <= 0.04045 else ((c + 0.055) / 1.055) ** 2.4

    r, g, b = (_srgb_to_linear(c) for c in rgb)

    # sRGB -> XYZ (D65)
    x = r * 0.4124 + g * 0.3576 + b * 0.1805
    y = r * 0.2126 + g * 0.7152 + b * 0.0722
    z = r * 0.0193 + g * 0.1192 + b * 0.9505

    # Normalize by D65 white point
    xn, yn, zn = 0.95047, 1.00000, 1.08883
    x, y, z = x / xn, y / yn, z / zn

    def f(t):
        return t ** (1 / 3) if t > 0.008856 else (7.787 * t) + (16 / 116)

    fx, fy, fz = f(x), f(y), f(z)
    L = (116 * fy) - 16
    a = 500 * (fx - fy)
    b_ = 200 * (fy - fz)
    return (L, a, b_)


def _lab_distance(lab1: Tuple[float, float, float], lab2: Tuple[float, float, float]) -> float:
    return math.sqrt(sum((a - b) ** 2 for a, b in zip(lab1, lab2)))


import numpy as np


def dominant_rgb_from_image(img) -> Tuple[int, int, int]:
    """Same center-crop average as dominant_rgb(), using numpy for high performance."""
    img = img.convert("RGB")
    w, h = img.size
    left, top = w // 4, h // 4
    right, bottom = w - left, h - top
    center = img.crop((left, top, right, bottom))
    arr = np.asarray(center, dtype=np.float32)
    mean_rgb = arr.mean(axis=(0, 1))
    return (int(round(mean_rgb[0])), int(round(mean_rgb[1])), int(round(mean_rgb[2])))


def dominant_rgb(image_path: str) -> Tuple[int, int, int]:
    """
    Returns the dominant RGB color of an image using a simple average of
    the center crop (avoids background/edges skewing the estimate).
    """
    from PIL import Image
    return dominant_rgb_from_image(Image.open(image_path))


class MunsellTable:
    """
    Dataset-Driven Munsell Matcher.
    Combines calibrated reference CSV baseline with empirical centroids computed
    from images stored in Scan_Soil_Image/Real_Soil/.
    """
    def __init__(
        self,
        csv_path: str,
        scan_root: Optional[str] = None,
        min_samples_for_centroid: int = 1,
    ):
        self.csv_path = csv_path
        if not scan_root:
            scan_root = os.environ.get("SCAN_SOIL_ROOT")
        if not scan_root:
            candidate_storage = os.path.join(os.getcwd(), "storage", "app", "Scan_Soil_Image")
            candidate_root = os.path.join(os.getcwd(), "Scan_Soil_Image")
            if os.path.isdir(candidate_storage):
                scan_root = candidate_storage
            elif os.path.isdir(candidate_root):
                scan_root = candidate_root
        self.scan_root = scan_root
        self.min_samples = min_samples_for_centroid
        self.reference_chips: Dict[str, MunsellChip] = {}
        self.centroids: Dict[str, MunsellChip] = {}
        self._load_reference_csv(csv_path)
        self.refresh_centroids()

    def _load_reference_csv(self, csv_path: str) -> None:
        if not os.path.exists(csv_path):
            raise FileNotFoundError(
                f"Munsell reference CSV not found: {csv_path}. Provide hue,value,chroma,r,g,b rows."
            )
        with open(csv_path, newline="", encoding="utf-8") as f:
            reader = csv.DictReader(f)
            for row in reader:
                chip = MunsellChip(
                    hue=row["hue"].strip(),
                    value=float(row["value"]),
                    chroma=float(row["chroma"]),
                    rgb=(int(row["r"]), int(row["g"]), int(row["b"])),
                    source="reference_csv",
                    sample_count=0,
                )
                self.reference_chips[chip.notation] = chip
        if not self.reference_chips:
            raise ValueError(f"No Munsell chips loaded from {csv_path}")

    def refresh_centroids(self, force: bool = False) -> None:
        """
        Recomputes or loads cached empirical notation centroids.
        """
        # Seed centroids dictionary with reference baseline chips
        self.centroids = {
            k: MunsellChip(
                hue=v.hue,
                value=v.value,
                chroma=v.chroma,
                rgb=v.rgb,
                lab=v.lab,
                sample_count=0,
                source="reference_csv",
            )
            for k, v in self.reference_chips.items()
        }

        if not self.scan_root:
            return

        cache_path = os.path.join(self.scan_root, "empirical_centroids.json")
        if not force and os.path.isfile(cache_path):
            try:
                import json
                with open(cache_path, "r", encoding="utf-8") as f:
                    cached = json.load(f)
                for notation, c in cached.items():
                    self.centroids[notation] = MunsellChip(
                        hue=c["hue"],
                        value=float(c["value"]),
                        chroma=float(c["chroma"]),
                        rgb=tuple(c["rgb"]),
                        lab=tuple(c["lab"]),
                        sample_count=int(c["sample_count"]),
                        source=c.get("source", "dataset_centroid"),
                    )
                return
            except Exception:
                pass

        real_soil_dir = os.path.join(self.scan_root, "Real_Soil")
        if not os.path.isdir(real_soil_dir):
            return

        # Walk Real_Soil folder tree: Real_Soil/<hue>/<hue value>/<chroma>/
        empirical_data = {}
        try:
            for hue_entry in os.scandir(real_soil_dir):
                if not hue_entry.is_dir():
                    continue
                hue = hue_entry.name
                for val_entry in os.scandir(hue_entry.path):
                    if not val_entry.is_dir():
                        continue
                    for chroma_entry in os.scandir(val_entry.path):
                        if not chroma_entry.is_dir():
                            continue
                        chroma_str = chroma_entry.name
                        val_str = val_entry.name.replace(hue, "").strip()
                        try:
                            val_num = float(val_str)
                            chroma_num = float(chroma_str)
                        except ValueError:
                            continue

                        notation = f"{hue} {_fmt_num(val_num)}/{_fmt_num(chroma_num)}"

                        img_paths = [
                            e.path
                            for e in os.scandir(chroma_entry.path)
                            if e.is_file() and os.path.splitext(e.name)[1].lower() in IMAGE_EXTENSIONS
                        ]

                        if img_paths:
                            empirical_data[notation] = {
                                "hue": hue,
                                "value": val_num,
                                "chroma": chroma_num,
                                "paths": img_paths,
                            }
        except Exception as e:
            sys.stderr.write(f"[WARN] Error scanning Real_Soil directory: {e}\n")
            return

        for notation, data in empirical_data.items():
            img_paths = data["paths"]
            count = len(img_paths)
            if count >= self.min_samples:
                labs = []
                rgbs = []
                # Sample up to 30 representative images for fast, statistically stable centroid calculation
                sampled_paths = img_paths[:30] if len(img_paths) > 30 else img_paths
                for p in sampled_paths:
                    try:
                        raw_img = Image.open(p)
                        normalized_img = normalize_lighting(raw_img) if normalize_lighting is not None else raw_img
                        rgb = dominant_rgb_from_image(normalized_img)
                        lab = _rgb_to_lab(rgb)
                        labs.append(lab)
                        rgbs.append(rgb)
                    except Exception as e:
                        sys.stderr.write(f"[WARN] Failed to process reference image {p}: {e}\n")

                if labs:
                    avg_l = sum(l[0] for l in labs) / len(labs)
                    avg_a = sum(l[1] for l in labs) / len(labs)
                    avg_b = sum(l[2] for l in labs) / len(labs)
                    avg_r = int(round(sum(r[0] for r in rgbs) / len(rgbs)))
                    avg_g = int(round(sum(r[1] for r in rgbs) / len(rgbs)))
                    avg_b_val = int(round(sum(r[2] for r in rgbs) / len(rgbs)))

                    self.centroids[notation] = MunsellChip(
                        hue=data["hue"],
                        value=data["value"],
                        chroma=data["chroma"],
                        rgb=(avg_r, avg_g, avg_b_val),
                        lab=(avg_l, avg_a, avg_b),
                        sample_count=len(labs),
                        source="dataset_centroid",
                    )
            else:
                sys.stderr.write(
                    f"[INFO] Notation {notation} has {count} sample(s) (< min {self.min_samples}); using reference CSV baseline.\n"
                )

        try:
            import json
            cache_data = {
                k: {
                    "hue": v.hue,
                    "value": v.value,
                    "chroma": v.chroma,
                    "rgb": list(v.rgb),
                    "lab": list(v.lab),
                    "sample_count": v.sample_count,
                    "source": v.source,
                }
                for k, v in self.centroids.items()
                if v.source == "dataset_centroid"
            }
            with open(cache_path, "w", encoding="utf-8") as f:
                json.dump(cache_data, f, indent=2)
        except Exception:
            pass

    @property
    def chips(self) -> List[MunsellChip]:
        return list(self.centroids.values())

    def nearest(self, rgb: Tuple[int, int, int]) -> Tuple[Optional[MunsellChip], float]:
        """
        Returns (closest MunsellChip centroid, delta_e_distance).
        delta_e_distance is Euclidean distance in Lab space.
        """
        target_lab = _rgb_to_lab(rgb)
        best_chip = None
        best_dist = float("inf")
        for chip in self.centroids.values():
            chip_lab = chip.lab if chip.lab is not None else _rgb_to_lab(chip.rgb)
            dist = _lab_distance(target_lab, chip_lab)
            if dist < best_dist:
                best_dist = dist
                best_chip = chip
        return best_chip, best_dist

    def is_within_soil_gamut(self, rgb: Tuple[int, int, int], max_delta_e: float = 14.0) -> bool:
        _, dist = self.nearest(rgb)
        return dist <= max_delta_e
