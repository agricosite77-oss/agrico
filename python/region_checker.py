"""
region_checker.py

Stage 4 — Per-region zoom + real-vs-fake re-check.

For each color region detected in Stage 3, crops the region out of the
normalized image and re-validates it as genuine soil using two independent
checks:

  A. Soil gate re-check: runs predict_is_soil() from soil_gate.py on the
     cropped region sub-image (catches regions that are actually debris,
     rocks, packaging, or non-soil material that slipped through the
     whole-frame gate).

  B. Fake_Soil record similarity: computes mean structural similarity (SSIM)
     between the cropped region and up to 10 stored Fake_Soil images.
     A close match to stored fake-soil records (SSIM >= FAKE_SOIL_SSIM_THRESHOLD)
     is treated as an additional signal to reject the region, catching cases
     that resemble previously confirmed fake soil.

If only one significant color region was found in Stage 3, the whole
normalized image is used directly (no sub-crop needed).

Public API:
    filter_real_regions(
        normalized_image: PIL.Image.Image,
        regions: list[dict],
        fake_soil_dir: str,
    ) -> list[dict]

Each surviving dict gains two new keys:
    "gate_confidence"  : float  -- soil gate confidence for this region
    "is_real_region"   : True   -- (fake regions are simply excluded)
"""

import os
import sys
import random
from typing import List, Tuple

import numpy as np
from PIL import Image

# Threshold above which an SSIM score against a Fake_Soil image causes rejection
FAKE_SOIL_SSIM_THRESHOLD = 0.80

# Max number of Fake_Soil reference images to compare against per region
MAX_FAKE_SOIL_REFS = 10

# Resize resolution for SSIM comparison (keep it small for speed)
SSIM_COMPARE_SIZE = (64, 64)

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def _load_fake_soil_refs(fake_soil_dir: str) -> List[np.ndarray]:
    """
    Loads up to MAX_FAKE_SOIL_REFS grayscale thumbnails from the Fake_Soil directory.
    Returns list of (H, W) float32 arrays normalized to [0, 1].
    """
    if not os.path.isdir(fake_soil_dir):
        return []

    paths = [
        os.path.join(fake_soil_dir, f)
        for f in os.listdir(fake_soil_dir)
        if os.path.splitext(f)[1].lower() in IMAGE_EXTENSIONS
    ]

    if len(paths) > MAX_FAKE_SOIL_REFS:
        paths = random.sample(paths, MAX_FAKE_SOIL_REFS)

    refs = []
    for p in paths:
        try:
            img = Image.open(p).convert("L").resize(SSIM_COMPARE_SIZE, Image.Resampling.BILINEAR)
            refs.append(np.asarray(img, dtype=np.float32) / 255.0)
        except Exception:
            pass
    return refs


def _ssim_score(a: np.ndarray, b: np.ndarray) -> float:
    """
    Computes Structural Similarity Index (SSIM) between two (H, W) float32
    arrays in [0, 1] using skimage if available, else a simple correlation.
    """
    try:
        from skimage.metrics import structural_similarity as ssim  # type: ignore
        score = float(ssim(a, b, data_range=1.0))
        return max(0.0, score)
    except ImportError:
        # Fallback: Pearson correlation as a rough proxy
        a_flat = a.flatten()
        b_flat = b.flatten()
        if a_flat.std() < 1e-6 or b_flat.std() < 1e-6:
            return 0.0
        corr = float(np.corrcoef(a_flat, b_flat)[0, 1])
        return max(0.0, corr)


def _max_ssim_vs_fake(region_thumb: np.ndarray, fake_refs: List[np.ndarray]) -> float:
    """
    Returns the highest SSIM score between `region_thumb` and any fake-soil reference.
    Returns 0.0 if no references are available.
    """
    if not fake_refs:
        return 0.0
    scores = [_ssim_score(region_thumb, ref) for ref in fake_refs]
    return max(scores)


def _region_to_thumbnail(region_image: Image.Image) -> np.ndarray:
    """Converts a region PIL Image to a grayscale (H, W) float32 thumbnail."""
    thumb = region_image.convert("L").resize(SSIM_COMPARE_SIZE, Image.Resampling.BILINEAR)
    return np.asarray(thumb, dtype=np.float32) / 255.0


def filter_real_regions(
    normalized_image: Image.Image,
    regions: List[dict],
    fake_soil_dir: str,
) -> List[dict]:
    """
    Stage 4 — Per-region zoom + real-vs-fake re-check.

    Parameters:
        normalized_image : PIL Image from Stage 2 (lighting-normalized, RGB).
        regions          : List of region dicts from Stage 3 (color_detector).
        fake_soil_dir    : Absolute path to Scan_Soil_Image/Fake_Soil/ directory.

    Returns:
        Subset of `regions` that pass both the soil gate re-check and the
        Fake_Soil similarity check. Each surviving dict is augmented with:
          - "gate_confidence"  : float
          - "region_image"     : PIL.Image.Image  (cropped sub-image, used by Stage 5)
          - "is_real_region"   : True
    """
    # Lazy import to avoid circular dependency
    from soil_gate import predict_is_soil  # noqa: E402
    from color_detector import crop_region  # noqa: E402

    fake_refs = _load_fake_soil_refs(fake_soil_dir)

    real_regions = []

    # If only one region, skip sub-crop and check the whole image
    if len(regions) == 1:
        region = dict(regions[0])
        region["region_image"] = normalized_image.convert("RGB")
        is_soil, gate_conf = predict_is_soil(normalized_image)

        if not is_soil:
            sys.stderr.write(
                f"[Stage4] Single region rejected by soil gate (conf={gate_conf:.3f})\n"
            )
            return []

        region_thumb = _region_to_thumbnail(region["region_image"])
        max_fake_ssim = _max_ssim_vs_fake(region_thumb, fake_refs)

        if max_fake_ssim >= FAKE_SOIL_SSIM_THRESHOLD:
            sys.stderr.write(
                f"[Stage4] Single region rejected: SSIM={max_fake_ssim:.3f} >= "
                f"fake threshold {FAKE_SOIL_SSIM_THRESHOLD}\n"
            )
            return []

        region["gate_confidence"] = round(gate_conf, 3)
        region["is_real_region"] = True
        return [region]

    # Multiple regions: crop and check each independently
    for region in regions:
        region = dict(region)
        pixel_mask = region["pixel_mask"]
        cropped = crop_region(normalized_image, pixel_mask)
        region["region_image"] = cropped

        # A. Soil gate on cropped region
        is_soil, gate_conf = predict_is_soil(cropped)
        if not is_soil:
            sys.stderr.write(
                f"[Stage4] Region {region['color_index']} rejected by soil gate "
                f"(conf={gate_conf:.3f})\n"
            )
            continue

        # B. Fake_Soil similarity check
        region_thumb = _region_to_thumbnail(cropped)
        max_fake_ssim = _max_ssim_vs_fake(region_thumb, fake_refs)

        if max_fake_ssim >= FAKE_SOIL_SSIM_THRESHOLD:
            sys.stderr.write(
                f"[Stage4] Region {region['color_index']} rejected: "
                f"SSIM={max_fake_ssim:.3f} >= fake threshold {FAKE_SOIL_SSIM_THRESHOLD}\n"
            )
            continue

        region["gate_confidence"] = round(gate_conf, 3)
        region["is_real_region"] = True
        real_regions.append(region)

    return real_regions
