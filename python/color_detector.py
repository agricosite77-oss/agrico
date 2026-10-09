"""
color_detector.py

Stage 3 — Multi-color detection.

Takes a lighting-normalized PIL Image and extracts the significant distinct
color regions present in it, along with per-region pixel masks and proportions.

Implements exactly what the ColorThief README describes
(https://github.com/lokesh/color-thief):

  1. getPalette() → extract N candidate palette swatches from the image.
  2. region option  → sample only a sub-rectangle of the image (the scan
     reticle / center-crop region) so background pixels outside the scan
     frame don't skew the palette.
  3. Pixel-counting pass → assign every pixel to its nearest swatch by
     squared Euclidean distance in RGB space, then count pixels per swatch.
  4. proportion  → fraction of total ROI pixels owned by each swatch
     (equivalent to the .proportion field on a Color object in the JS v3 API).
  5. Minimum proportion threshold → discard swatches below ~10% (shadow /
     glare / edge noise rather than a real distinct soil color).
  6. Mean RGB re-computation → use the mean of the actual pixels assigned to
     each swatch (more accurate than the raw palette swatch RGB).

The Python `colorthief` package does not expose `.proportion` natively
(unlike the JS v3), so we compute it ourselves in the pixel-counting pass —
the result is identical.

Public API:
    detect_color_regions(
        image         : PIL.Image.Image,
        max_colors    : int   = 6,
        min_proportion: float = 0.10,
        roi_fraction  : float = 0.74,   # center-crop fraction (matches JS region default)
    ) -> list[dict]

Each dict in the returned list:
    {
        "rgb":          (R, G, B) tuple   -- mean RGB of this region (proportion-weighted)
        "proportion":   float             -- fraction of ROI pixels (0..1)  ← .proportion
        "pixel_mask":   np.ndarray bool   -- (H, W) True where this region's pixels are
        "color_index":  int               -- index in the palette (0-based)
    }
"""

import io
from typing import List, Optional, Tuple

import numpy as np
from PIL import Image


# ── ColorThief region option ───────────────────────────────────────────────────
# Mirrors the JS API: region: { x, y, width, height } as fractions (0-1).
# Default: center crop of 60% of the image on each axis (tightened from 74%
# to prevent bowl, tray, or background edge contamination from leaking into color regions).
# Can be overridden via SCAN_ROI_FRACTION environment variable.
import os
DEFAULT_ROI_FRACTION = float(os.environ.get("SCAN_ROI_FRACTION", "1.0"))


def _apply_region_crop(image: Image.Image, roi_fraction: float) -> Image.Image:
    """
    Crops the image to a centered sub-rectangle with side length
    `roi_fraction` × original size — equivalent to ColorThief JS's
    `region: { x: margin, y: margin, width: roi_fraction, height: roi_fraction }`.

    Parameters:
        image        : PIL Image (RGB).
        roi_fraction : Fraction of each dimension to keep (0 < roi_fraction <= 1.0).

    Returns:
        Cropped PIL Image (RGB).
    """
    if roi_fraction >= 1.0:
        return image

    w, h = image.size
    margin_x = (w * (1.0 - roi_fraction)) / 2.0
    margin_y = (h * (1.0 - roi_fraction)) / 2.0

    left   = int(round(margin_x))
    top    = int(round(margin_y))
    right  = int(round(w - margin_x))
    bottom = int(round(h - margin_y))

    return image.crop((left, top, right, bottom))


def _nearest_color_index(pixel_arr: np.ndarray, palette: np.ndarray) -> np.ndarray:
    """
    Assigns each pixel to the index of its nearest palette entry using
    squared Euclidean distance in RGB space.

    Implements the pixel-counting pass described in the ColorThief README:
    "assign each pixel to its closest palette color, count, divide by total
    ROI pixels to get proportions per color."

    pixel_arr : (H, W, 3) float32  — ROI pixels
    palette   : (K, 3)    float32  — palette swatches from ColorThief

    Returns (H, W) int32 index array.
    """
    H, W, _ = pixel_arr.shape
    flat = pixel_arr.reshape(-1, 3)                              # (H*W, 3)
    diffs = flat[:, np.newaxis, :] - palette[np.newaxis, :, :]  # (H*W, K, 3)
    dists = np.sum(diffs ** 2, axis=-1)                          # (H*W, K)
    idx = np.argmin(dists, axis=-1)                              # (H*W,)
    return idx.reshape(H, W).astype(np.int32)


def _get_palette_colorthief(
    image: Image.Image,
    max_colors: int,
    quality: int = 5,
) -> List[Tuple[int, int, int]]:
    """
    Calls ColorThief `get_palette(color_count, quality)` to extract palette
    swatches — equivalent to the JS `getPalette(source, { colorCount, quality })`.

    quality=5 means sample every 5th pixel (JS default is 10; 5 gives better
    accuracy for small soil sample images without being too slow).

    Falls back to the image mean color if `colorthief` is not installed.
    """
    try:
        from colorthief import ColorThief  # type: ignore
        buf = io.BytesIO()
        image.save(buf, format="PNG")
        buf.seek(0)
        ct = ColorThief(buf)
        if max_colors == 1:
            return [ct.get_color(quality=quality)]
        palette = ct.get_palette(color_count=max_colors, quality=quality)
        return palette
    except Exception:
        # Graceful fallback: mean color as a single swatch
        arr = np.asarray(image, dtype=np.float32)
        mean_rgb = tuple(int(round(v)) for v in arr.mean(axis=(0, 1))[:3])
        return [mean_rgb]  # type: ignore[return-value]


def locate_color_box(
    img_rgb: np.ndarray,
    color_rgb: tuple,
    max_de: float = 12.0,
    pixel_mask: Optional[np.ndarray] = None,
) -> dict:
    """
    Locates the primary continuous blob of `color_rgb` in `img_rgb`.
    If `pixel_mask` (H×W boolean array from actual pixel assignments) is provided,
    it identifies the most representative contiguous cluster of this color on the soil surface,
    avoiding border-edge artifacts and crack-erasing morphological filters.
    Returns both absolute pixel coordinates and normalized (0-1) coordinates.
    """
    H, W = img_rgb.shape[:2]
    try:
        import cv2

        if pixel_mask is not None and np.any(pixel_mask):
            mask_u8 = pixel_mask.astype(np.uint8) * 255
            kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3))
            closed = cv2.morphologyEx(mask_u8, cv2.MORPH_CLOSE, kernel)
            n, labels, stats, centroids = cv2.connectedComponentsWithStats(closed)
        else:
            from skimage.color import rgb2lab, deltaE_ciede2000
            lab = rgb2lab(img_rgb.astype(np.float64) / 255.0)
            ref = rgb2lab(np.array([[color_rgb]], dtype=np.float64) / 255.0)[0, 0]
            de = deltaE_ciede2000(lab, ref[None, None, :])
            mask_u8 = (de < max_de).astype(np.uint8) * 255
            kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3))
            closed = cv2.morphologyEx(mask_u8, cv2.MORPH_CLOSE, kernel)
            n, labels, stats, centroids = cv2.connectedComponentsWithStats(closed)

        target_cx = W / 2.0
        target_cy = H / 2.0

        best_score = -1.0
        best_box = None

        if n > 1:
            for i in range(1, n):
                x, y, w, h, area = stats[i]
                cx, cy = centroids[i]

                if area < 16:
                    continue

                # Penalize components clinging strictly to the outer boundary edges
                is_edge = (x <= 3 or (x + w) >= W - 3 or y <= 3 or (y + h) >= H - 3)
                edge_mult = 0.25 if is_edge else 1.0

                dist_norm = np.hypot((cx - target_cx) / target_cx, (cy - target_cy) / target_cy)
                score = (area ** 0.6) * (1.0 / (1.0 + 0.6 * dist_norm)) * edge_mult

                if score > best_score:
                    best_score = score
                    # Bound to a clean representative inspection window (10% to 26% of image dimension)
                    target_w = max(int(W * 0.10), min(w, int(W * 0.26)))
                    target_h = max(int(H * 0.08), min(h, int(H * 0.20)))

                    bx = max(int(W * 0.02), min(int(W * 0.98) - target_w, int(x + (w - target_w) / 2)))
                    by = max(int(H * 0.02), min(int(H * 0.98) - target_h, int(y + (h - target_h) / 2)))

                    best_box = {
                        "x": int(bx),
                        "y": int(by),
                        "width": int(target_w),
                        "height": int(target_h),
                        "norm_x": round(float(bx / W), 4),
                        "norm_y": round(float(by / H), 4),
                        "norm_w": round(float(target_w / W), 4),
                        "norm_h": round(float(target_h / H), 4),
                    }

        if best_box:
            return best_box
    except Exception:
        pass

    return {
        "x": 0, "y": 0, "width": W, "height": H,
        "norm_x": 0.0, "norm_y": 0.0, "norm_w": 1.0, "norm_h": 1.0,
    }



def detect_color_regions(
    image: Image.Image,
    max_colors: int = 6,
    min_proportion: float = 0.10,
    roi_fraction: float = DEFAULT_ROI_FRACTION,
) -> List[dict]:
    """
    Stage 3 — Multi-color detection.

    Follows the ColorThief README pipeline exactly:
      1. (region crop) Center-crop to `roi_fraction` of image dimensions so
         background outside the scan reticle is excluded — mirrors the JS
         `region: { x, y, width, height }` option.
      2. (getPalette) Extract up to `max_colors` palette swatches via ColorThief.
      3. (pixel-counting pass) Assign every pixel to nearest swatch; count and
         divide by total to produce `.proportion` for each swatch.
      4. (threshold) Discard swatches below `min_proportion` (noise/edge).
      5. (mean re-computation) Replace raw swatch RGB with the mean of actual
         pixels assigned to it (more accurate for irregular soil textures).
      6. (sort) Return regions sorted by proportion descending — dominant first.

    Parameters:
        image          : PIL Image (any mode; converted to RGB internally).
        max_colors     : Max palette swatches (default 6, same as JS colorCount).
        min_proportion : Min pixel fraction to keep a region (default 10%).
        roi_fraction   : Fraction of image to use as the center-crop ROI
                         (default 0.74, matching the reticle window in
                         ScanSoilScreen.js: RETICLE_SIZE = SCREEN_WIDTH * 0.74).

    Returns:
        List of region dicts sorted by proportion descending. Each dict:
          - rgb          : (R, G, B) mean color of the region
          - proportion   : float — .proportion equivalent (share of ROI pixels)
          - pixel_mask   : (H, W) bool ndarray over the FULL image dimensions
          - color_index  : int palette index
    """
    img_rgb = image.convert("RGB")

    # ── Step 1: Apply region crop (ColorThief JS `region` option) ─────────────
    roi_img = _apply_region_crop(img_rgb, roi_fraction)

    # ── Step 2: Extract palette swatches from the ROI ─────────────────────────
    palette_tuples = _get_palette_colorthief(roi_img, max_colors)
    palette = np.array(palette_tuples, dtype=np.float32)  # (K, 3)

    # ── Step 3: Pixel-counting pass on the ROI ────────────────────────────────
    roi_arr = np.asarray(roi_img, dtype=np.float32)       # (Hc, Wc, 3)
    Hc, Wc = roi_arr.shape[:2]
    total_roi_pixels = Hc * Wc

    roi_assignment = _nearest_color_index(roi_arr, palette)  # (Hc, Wc) int32

    # Build pixel_mask over the FULL image dimensions so Stage 4 (region_checker)
    # can crop out each region from the full normalized image correctly.
    full_arr = np.asarray(img_rgb, dtype=np.float32)      # (H, W, 3)
    H, W = full_arr.shape[:2]

    # Compute ROI offset to map crop coordinates back to full-image coordinates
    roi_x0 = int(round(W * (1.0 - roi_fraction) / 2.0))
    roi_y0 = int(round(H * (1.0 - roi_fraction) / 2.0))

    regions = []
    for k, swatch_rgb in enumerate(palette_tuples):
        # Count pixels in the ROI that belong to this swatch
        roi_mask = (roi_assignment == k)  # (Hc, Wc) bool
        count = int(roi_mask.sum())
        proportion = count / total_roi_pixels  # .proportion

        if proportion < min_proportion:
            continue  # discard noise (< 10% of ROI)

        # Mean RGB from actual assigned pixels (more accurate than swatch)
        region_pixels = roi_arr[roi_mask]
        if len(region_pixels) == 0:
            continue
        mean_rgb = tuple(int(round(v)) for v in region_pixels.mean(axis=0)[:3])

        # Build full-image boolean mask by embedding the ROI mask back into
        # a full H×W array at the correct offset.
        full_mask = np.zeros((H, W), dtype=bool)
        full_mask[
            roi_y0: roi_y0 + Hc,
            roi_x0: roi_x0 + Wc,
        ] = roi_mask

        # Locate the continuous blob where this color was actually detected
        box_data = locate_color_box(full_arr.astype(np.uint8), mean_rgb, pixel_mask=full_mask)

        regions.append({
            "rgb": mean_rgb,
            "proportion": round(proportion, 4),
            "pixel_mask": full_mask,
            "color_index": k,
            "bounding_box": box_data,
            "box": {
                "x": box_data["norm_x"],
                "y": box_data["norm_y"],
                "w": box_data["norm_w"],
                "h": box_data["norm_h"],
            },
        })

    # Sort by proportion descending (dominant color first)
    regions.sort(key=lambda r: r["proportion"], reverse=True)
    return regions


def crop_region(image: Image.Image, pixel_mask: np.ndarray) -> Image.Image:
    """
    Returns a PIL Image containing only the pixels from `pixel_mask`,
    cropped to the bounding box of that mask. Background pixels are filled white.

    Used by Stage 4 to isolate individual color regions for re-checking.

    Parameters:
        image      : Original PIL Image (RGB).
        pixel_mask : (H, W) bool ndarray matching image dimensions.

    Returns:
        Cropped PIL Image (RGB) of the region's bounding box.
    """
    arr = np.asarray(image.convert("RGB"), dtype=np.uint8).copy()

    # Crop to bounding box of mask (preserve natural image pixels without whiting out)

    # Crop to bounding box of mask
    rows = np.any(pixel_mask, axis=1)
    cols = np.any(pixel_mask, axis=0)
    if not rows.any() or not cols.any():
        return image.convert("RGB")

    r_min = int(np.argmax(rows))
    r_max = int(len(rows) - 1 - np.argmax(rows[::-1]))
    c_min = int(np.argmax(cols))
    c_max = int(len(cols) - 1 - np.argmax(cols[::-1]))

    cropped = arr[r_min:r_max + 1, c_min:c_max + 1]
    return Image.fromarray(cropped, mode="RGB")
