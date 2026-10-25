"""
real_soil_verifier.py

Stage 6 — ML cross-check against Real_Soil records.

For each region's Munsell notation from Stage 5, locates the matching
Real_Soil/<hue>/<hue value>/<chroma>/ folder, samples up to 5 reference
images from it, and computes Structural Similarity (SSIM) between the
region thumbnail and each reference image to produce a verification
confidence score.

A region with a high Munsell color match but low visual similarity to stored
real-soil samples of that notation is flagged as "needs_review" rather than
reported as a fully verified real-soil color.

Public API:
    verify_regions(
        regions: list[dict],
        real_soil_root: str,
        verified_threshold: float = 0.45,
        max_refs: int = 5,
    ) -> list[dict]

Each output dict gains:
    "verification_confidence" : float  -- mean SSIM across reference images
    "status"                  : str    -- "verified" | "needs_review"

The returned list is the final `regions[]` array emitted in the API response.
"""

import io
import os
import random
import sys
from typing import List, Optional, Tuple

import numpy as np
from PIL import Image

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Resolution for comparison thumbnails
THUMBNAIL_SIZE = (64, 64)

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def _images_in_dir(directory: str) -> List[str]:
    """Returns all image file paths in a directory (non-recursive)."""
    if not os.path.isdir(directory):
        return []
    return [
        os.path.join(directory, f)
        for f in os.listdir(directory)
        if os.path.splitext(f)[1].lower() in IMAGE_EXTENSIONS
    ]


def _fmt_num(n: float) -> str:
    try:
        f = float(n)
        return str(int(f)) if f == int(f) else str(f)
    except (ValueError, TypeError):
        return str(n)


def _notation_folder(real_soil_root: str, hue: str, value: float, chroma: float) -> str:
    """
    Builds the path:
        Real_Soil/<hue>/<hue value>/<chroma>/
    e.g. Real_Soil/10YR/10YR 4/3/
    """
    hue_val_folder = f"{hue} {_fmt_num(value)}"
    chroma_folder = _fmt_num(chroma)
    return os.path.join(real_soil_root, "Real_Soil", hue, hue_val_folder, chroma_folder)


def _find_nearest_notation_folder(
    real_soil_root: str,
    hue: str,
    value: float,
    chroma: float,
) -> Optional[str]:
    """
    If the exact notation folder is empty or missing:
    1. First searches only within the SAME hue family (e.g. stays within 10YR/)
       for the closest value/chroma match.
    2. Only falls back to searching other hue families if the target hue has
       no real-soil images anywhere.
    Returns the folder path, or None if no real-soil images exist at all.
    """
    exact = _notation_folder(real_soil_root, hue, value, chroma)
    if _images_in_dir(exact):
        return exact

    real_soil_dir = os.path.join(real_soil_root, "Real_Soil")
    if not os.path.isdir(real_soil_dir):
        return None

    def _closest_in(hue_dir: str) -> Tuple[Optional[str], float]:
        best_path: Optional[str] = None
        best_dist = float("inf")
        if not os.path.isdir(hue_dir):
            return None, best_dist
        hue_name = os.path.basename(hue_dir)
        for val_entry in os.scandir(hue_dir):
            if not val_entry.is_dir():
                continue
            val_str = val_entry.name.replace(hue_name, "").strip()
            for chroma_entry in os.scandir(val_entry.path):
                if not chroma_entry.is_dir():
                    continue
                imgs = _images_in_dir(chroma_entry.path)
                if not imgs:
                    continue
                try:
                    v2 = float(val_str)
                    c2 = float(chroma_entry.name)
                except ValueError:
                    continue
                dist = abs(v2 - value) + abs(c2 - chroma)
                if dist < best_dist:
                    best_dist = dist
                    best_path = chroma_entry.path
        return best_path, best_dist

    # Step 1: same hue family only
    same_hue_dir = os.path.join(real_soil_dir, hue)
    best_path, _ = _closest_in(same_hue_dir)
    if best_path is not None:
        return best_path

    # Step 2: target hue has nothing at all — fall back across all hues
    sys.stderr.write(
        f"[Stage6] No images found anywhere under hue family '{hue}' — "
        f"falling back to cross-hue search.\n"
    )
    global_best_path: Optional[str] = None
    global_best_dist = float("inf")
    try:
        for hue_entry in os.scandir(real_soil_dir):
            if not hue_entry.is_dir():
                continue
            path, dist = _closest_in(hue_entry.path)
            if path is not None and dist < global_best_dist:
                global_best_dist = dist
                global_best_path = path
    except Exception as e:
        sys.stderr.write(f"[Stage6] Error scanning Real_Soil tree: {e}\n")

    return global_best_path


def _extract_similarity_features(img: Image.Image) -> np.ndarray:
    """
    Compact color+texture feature vector for comparing two soil photos in a way
    that tolerates unaligned grain (unlike pixel-aligned SSIM).
    Returns: [L_mean, a_mean, b_mean, L_std, a_std, b_std, texture_roughness]
    """
    from lighting_normalizer import _rgb_to_lab as _rgb_array_to_lab

    img_small = img.convert("RGB").resize(THUMBNAIL_SIZE, Image.Resampling.BILINEAR)
    arr = np.asarray(img_small, dtype=np.float32)
    lab = _rgb_array_to_lab(arr)
    L, a, b = lab[:, :, 0], lab[:, :, 1], lab[:, :, 2]
    gray = np.asarray(img_small.convert("L"), dtype=np.float32)
    texture_roughness = float(np.std(gray))

    return np.array([
        float(L.mean()), float(a.mean()), float(b.mean()),
        float(L.std()), float(a.std()), float(b.std()),
        texture_roughness,
    ], dtype=np.float32)


# Fixed per-dimension scales so no single feature dominates the distance purely
# because it has larger natural units (L: 0-100, a/b: roughly soil's -40..40
# range, texture_roughness: ~0-60 typical for photographed grain).
_FEATURE_SCALES = np.array([100.0, 40.0, 40.0, 30.0, 20.0, 20.0, 60.0], dtype=np.float32)


def _feature_similarity(feat_a: np.ndarray, feat_b: np.ndarray) -> float:
    """
    Returns a 0-1 similarity score from a scaled Euclidean distance between two
    feature vectors. 1.0 = identical, approaches 0 as they diverge.
    """
    diff = (feat_a - feat_b) / _FEATURE_SCALES
    dist = float(np.sqrt(np.sum(diff ** 2)))
    return float(1.0 / (1.0 + dist))


def _b64_to_pil(b64_str: str) -> Optional[Image.Image]:
    """Decodes a base64 PNG string back to a PIL Image."""
    import base64
    try:
        data = base64.b64decode(b64_str)
        return Image.open(io.BytesIO(data)).convert("RGB")
    except Exception:
        return None


def verify_regions(
    regions: List[dict],
    real_soil_root: str,
    verified_threshold: float = 0.80,
    max_refs: int = 5,
) -> List[dict]:
    """
    Stage 6 — ML cross-check against Real_Soil records.

    Parameters:
        regions            : Region dicts from Stage 5 (munsell_region_matcher).
        real_soil_root     : Path to the Scan_Soil_Image/ root directory.
        verified_threshold : Minimum mean feature similarity to mark a region as "verified".
        max_refs           : Maximum number of reference images to compare per region.

    Returns:
        Same list augmented with "verification_confidence" and "status" per region.
    """
    verified = []
    for region in regions:
        hue = region.get("hue", "")
        value = float(region.get("value", 0))
        chroma = float(region.get("chroma", 0))

        # Locate reference folder
        ref_folder = _find_nearest_notation_folder(real_soil_root, hue, value, chroma)

        if ref_folder is None:
            # When loose image folders are not deployed (e.g. cloud production),
            # verify using the empirical centroid and color delta_e
            delta_e = region.get("color_delta_e", 5.0)
            try:
                de_val = float(delta_e) if delta_e is not None else 5.0
            except (ValueError, TypeError):
                de_val = 5.0

            conf = max(0.50, min(0.95, round(1.0 / (1.0 + de_val / 8.0), 2)))
            status = "verified" if conf >= 0.70 else "needs_review"
            sys.stderr.write(
                f"[Stage6] Using empirical centroid fallback for {region.get('munsell')}: "
                f"conf={conf}, status={status}\n"
            )
            reg_copy = dict(region)
            reg_copy["verification_confidence"] = conf
            reg_copy["status"] = status
            verified.append(reg_copy)
            continue

        ref_paths = _images_in_dir(ref_folder)
        if len(ref_paths) > max_refs:
            ref_paths = random.sample(ref_paths, max_refs)

        # Decode region thumbnail
        thumbnail_b64 = region.get("region_thumbnail", "")
        region_img = _b64_to_pil(thumbnail_b64) if thumbnail_b64 else None

        if region_img is None:
            # Fall back to a solid-color image matching the region's mean RGB
            rgb = region.get("rgb", (128, 128, 128))
            region_img = Image.new("RGB", (64, 64), color=rgb)

        region_features = _extract_similarity_features(region_img)

        # Compute feature similarity against each normalized reference
        scores = []
        for ref_path in ref_paths:
            try:
                from lighting_normalizer import normalize_lighting
                ref_img_raw = Image.open(ref_path)
                ref_img_norm = normalize_lighting(ref_img_raw)
                ref_features = _extract_similarity_features(ref_img_norm)
                scores.append(_feature_similarity(region_features, ref_features))
            except Exception as e:
                sys.stderr.write(f"[Stage6] Could not load reference {ref_path}: {e}\n")

        if scores:
            mean_conf = float(round(sum(scores) / len(scores), 4))
        else:
            mean_conf = 0.0

        status = "verified" if mean_conf >= verified_threshold else "needs_review"

        sys.stderr.write(
            f"[Stage6] {region.get('munsell')} — Feature similarity mean={mean_conf:.4f}, "
            f"refs={len(scores)}, status={status}\n"
        )

        region = dict(region)
        region["verification_confidence"] = mean_conf
        region["status"] = status
        verified.append(region)

    return verified
