"""
python/soil_gate.py

Stage 1 Soil-vs-Not-Soil Gate.
Extracts rich spatial texture, gradient isotropy, and chrominance features
to classify an image as genuine soil vs non-soil (faces, hands, skin,
wood, cardboard, paper, fabrics, household objects).
"""

import os
import joblib
import numpy as np
from PIL import Image

GATE_MODEL_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "soil_gate_model.joblib")

_LOADED_MODEL = None


def get_gate_model():
    global _LOADED_MODEL
    if _LOADED_MODEL is None and os.path.exists(GATE_MODEL_PATH):
        try:
            _LOADED_MODEL = joblib.load(GATE_MODEL_PATH)
        except Exception as e:
            print(f"[WARN] Could not load soil gate model from {GATE_MODEL_PATH}: {e}")
            _LOADED_MODEL = None
    return _LOADED_MODEL


def _fft_directionality(gray: np.ndarray) -> float:
    """
    Orientation-invariant directional-texture measure using the angular
    distribution of 2D FFT magnitude energy. Catches grain/streak patterns at
    ANY angle (unlike axis-locked gx/gy variance ratios, which only compare
    0 deg vs 90 deg and miss diagonal grain).

    Returns a directionality index (coefficient of variation across 18 angular
    bins, 0-180 deg). Higher = more anisotropic (wood grain, fabric weave).
    Lower = more isotropic (granular soil texture).
    """
    h, w = gray.shape
    g = gray - gray.mean()

    # Hann window to reduce spectral leakage from image edges
    window = np.outer(np.hanning(h), np.hanning(w))
    g_win = g * window

    F = np.fft.fftshift(np.fft.fft2(g_win))
    mag = np.abs(F)

    cy, cx = h // 2, w // 2
    mag[cy, cx] = 0.0  # zero out DC component

    y_idx, x_idx = np.indices(mag.shape)
    dy = (y_idx - cy).astype(np.float64)
    dx = (x_idx - cx).astype(np.float64)
    radius = np.sqrt(dx ** 2 + dy ** 2)
    # Fold direction to orientation (0-180 deg), since a line has no "sign"
    angle = (np.degrees(np.arctan2(dy, dx)) + 180.0) % 180.0

    r_min, r_max = 4.0, float(min(cy, cx) - 2)
    ring_mask = (radius >= r_min) & (radius <= r_max)
    if not np.any(ring_mask):
        return 0.0

    n_bins = 18
    bin_width = 180.0 / n_bins
    bin_idx = np.clip((angle[ring_mask] // bin_width).astype(np.int32), 0, n_bins - 1)
    weights = mag[ring_mask]

    energies = np.bincount(bin_idx, weights=weights, minlength=n_bins)
    total = energies.sum()
    if total < 1e-6:
        return 0.0

    mean_e = energies.mean()
    std_e = energies.std()
    return float(std_e / (mean_e + 1e-6))


def extract_gate_features(img_path_or_obj, crop_roi: bool = False, roi_fraction: float = 0.60) -> np.ndarray:
    """
    Extracts a 17-dimensional feature vector for soil vs not-soil classification:
    - [0]: Laplacian variance (high-frequency grain)
    - [1]: Laplacian absolute mean
    - [2]: Horizontal gradient variance (gx_var)
    - [3]: Vertical gradient variance (gy_var)
    - [4]: Gradient directional isotropy (min/max gradient variance)
    - [5]: Skin chrominance fraction (pixels in standard digital skin locus)
    - [6-8]: Normalized mean R, G, B
    - [9-11]: Normalized standard deviation R, G, B
    - [12]: R/G ratio
    - [13]: R/B ratio
    - [14]: High-frequency spatial roughness
    - [15]: Overall luminance standard deviation
    - [16]: Angle-invariant directionality (FFT angular energy concentration;
            high = directional grain like wood at any angle, low = isotropic soil)
    """
    if isinstance(img_path_or_obj, str):
        img = Image.open(img_path_or_obj)
    else:
        img = img_path_or_obj

    if crop_roi and 0.0 < roi_fraction < 1.0:
        w, h = img.size
        margin_x = (w * (1.0 - roi_fraction)) / 2.0
        margin_y = (h * (1.0 - roi_fraction)) / 2.0
        left   = int(round(margin_x))
        top    = int(round(margin_y))
        right  = int(round(w - margin_x))
        bottom = int(round(h - margin_y))
        img = img.crop((left, top, right, bottom))

    if img.mode in ("P", "RGBA"):
        img = img.convert("RGBA")
        background = Image.new("RGBA", img.size, (255, 255, 255))
        img = Image.alpha_composite(background, img).convert("RGB")
    else:
        img = img.convert("RGB")

    # Resize to uniform 128x128 for consistent feature scale
    img_small = img.resize((128, 128), Image.Resampling.BILINEAR)
    arr = np.asarray(img_small, dtype="float32")

    R = arr[:, :, 0]
    G = arr[:, :, 1]
    B = arr[:, :, 2]

    # Grayscale luminance
    gray = 0.2989 * R + 0.5870 * G + 0.1140 * B

    # 1. Laplacian kernel convolution [-1 -1 -1; -1 8 -1; -1 -1 -1]
    lap = (
        -4.0 * gray[1:-1, 1:-1]
        + gray[:-2, 1:-1] + gray[2:, 1:-1]
        + gray[1:-1, :-2] + gray[1:-1, 2:]
    )
    lap_var = float(np.var(lap))
    lap_mean = float(np.mean(np.abs(lap)))

    # 2. Directional gradients
    gx = gray[:, 1:] - gray[:, :-1]
    gy = gray[1:, :] - gray[:-1, :]
    gx_var = float(np.var(gx))
    gy_var = float(np.var(gy))
    ratio_xy = float(min(gx_var, gy_var) / (max(gx_var, gy_var) + 1e-5))

    # 3. YCbCr Chrominance & Skin Tone Detection
    Cb = -0.168736 * R - 0.331264 * G + 0.500000 * B + 128.0
    Cr =  0.500000 * R - 0.418688 * G - 0.081312 * B + 128.0
    skin_mask = (Cb >= 77.0) & (Cb <= 127.0) & (Cr >= 133.0) & (Cr <= 173.0)
    skin_fraction = float(np.mean(skin_mask))

    # 4. Color statistics
    r_mean = float(np.mean(R))
    g_mean = float(np.mean(G))
    b_mean = float(np.mean(B))
    r_std = float(np.std(R))
    g_std = float(np.std(G))
    b_std = float(np.std(B))
    rg_ratio = float(r_mean / (g_mean + 1e-5))
    rb_ratio = float(r_mean / (b_mean + 1e-5))

    # 5. High-frequency spatial roughness
    hf_energy = float(np.mean(np.abs(gray - np.mean(gray))))
    gray_std = float(np.std(gray))

    # 6. Angle-invariant directionality (catches diagonal/any-angle grain,
    #    unlike ratio_xy which only compares 0 deg vs 90 deg)
    directionality = _fft_directionality(gray)

    return np.array([
        lap_var, lap_mean,
        gx_var, gy_var, ratio_xy,
        skin_fraction,
        r_mean / 255.0, g_mean / 255.0, b_mean / 255.0,
        r_std / 255.0, g_std / 255.0, b_std / 255.0,
        rg_ratio, rb_ratio,
        hf_energy,
        gray_std,
        directionality,          # <-- NEW: index [16]
    ], dtype="float32")


def predict_is_soil(image_path_or_obj, crop_roi: bool = False, roi_fraction: float = 0.60) -> tuple[bool, float]:
    """
    Evaluates whether an image is genuine soil (True) or non-soil/fake (False).
    Returns (is_soil: bool, confidence: float).
    """
    model = get_gate_model()
    features = extract_gate_features(image_path_or_obj, crop_roi=crop_roi, roi_fraction=roi_fraction)

    if model is not None:
        X = features.reshape(1, -1)
        prob = model.predict_proba(X)[0]
        # Class 1 is real_soil, Class 0 is not_soil
        soil_prob = float(prob[1]) if len(prob) > 1 else float(prob[0])
        is_soil = bool(soil_prob >= 0.50)
        conf = soil_prob if is_soil else (1.0 - soil_prob)
        return is_soil, round(conf, 3)

    # Heuristic fallback if model file has not yet been generated
    lap_var = features[0]
    ratio_xy = features[4]
    gray_std = features[15]
    directionality = features[16]
    # NOTE: 1.8 is an untuned starting threshold — verify/adjust against your
    # own wood vs. soil samples once real data is available; this fallback path
    # is only used before soil_gate_model.joblib exists.
    is_soil = (
        (lap_var > 35.0) and (ratio_xy > 0.40) and (gray_std > 8.0)
        and (directionality < 1.8)
    )
    conf = 0.75 if is_soil else 0.80
    return bool(is_soil), conf
