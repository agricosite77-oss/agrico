# Soil-vs-Not-Soil Gate Classifier Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Eliminate false-positive `real_soil` classifications on non-soil photos (faces, skin, wood, cardboard, household objects) by implementing a two-stage classification pipeline with a dedicated visual soil/not-soil gate before Munsell color determination.

**Architecture:** 
- **Stage 1 (Soil Gate):** Evaluates spatial texture features, multi-scale frequency variance, gradient directional isotropy, and skin-tone chrominance to classify an image as `soil` vs `not_soil`. If `not_soil`, immediately short-circuits to `fake_soil`, with no Munsell chip determination or fabricated soil notation.
- **Stage 2 (Munsell Determination):** Executes dataset-driven centroid and Munsell chip matching *only* when Stage 1 confirms the image is genuine soil.

**Tech Stack:** Python 3.12, Scikit-Learn (HistGradientBoosting / RandomForest), NumPy, Pillow, SciPy, SQLite3, PHP 8.2 / Laravel 11 Pest.

**Spec:** Two-stage classification contract replacing simple grayscale standard-deviation thresholding with a trained visual soil gate.

## Global Constraints
- Preserve the 2-way classification output contract (`real_soil` vs `fake_soil`).
- When an image is classified as `fake_soil`, it must NOT assign a fabricated Munsell notation (fields must be `"NA"`, `-1`, or null).
- Maintain backward compatibility with Laravel's `SoilMLAnalysisService` CLI JSON output schema.
- Preserve fast scan performance (< 1 second execution time per scan).
- Zero regressions on genuine soil samples (all 7 natural soil classes from CyAUG must continue to classify as `real_soil`).

---

## File Structure

- **`python/train_gate_classifier.py`** [NEW]: Script that constructs positive soil samples (from `Scan_Soil_Image/Real_Soil/`) and negative non-soil samples (faces, hands, skin tones, wood, cardboard, fabrics, household surfaces), trains a calibrated classifier gate, and serializes `python/soil_gate_model.joblib`.
- **`python/soil_gate.py`** [NEW]: Dedicated feature extractor and inference engine for Stage 1 soil-vs-not-soil detection (gradient energy, multiscale spatial variance, directional isotropy, color entropy, and skin-chrominance rejection).
- **`python/soil_classifier.py`** [MODIFY]: Integrates the Stage 1 Soil Gate. Short-circuits non-soil images to `fake_soil` without executing Munsell determination.
- **`python/colorimetry_service.py`** [MODIFY]: Ensures CLI output correctly suppresses Munsell determination for `fake_soil` classifications.
- **`python/storage.py`** [MODIFY]: Ensures `fake_soil` sidecars and SQLite records store `"NA"` notation and route directly to flat `Fake_Soil/`.
- **`tests/Feature/SoilScanAnalysisTest.php`** [MODIFY]: Adds Laravel test coverage verifying that non-soil images return `fake_soil` without Munsell notation.

---

## Task 1: Negative Non-Soil Dataset Scaffolding & Feature Engineering

**Files:**
- Create: `python/soil_gate.py`
- Create: `scratch/generate_not_soil_seed.py`
- Test: `scratch/test_gate_features.py`

**Interfaces:**
- Consumes: Image file path (`str`)
- Produces: `extract_gate_features(image_path: str) -> np.ndarray` (24-dimensional feature vector encompassing spatial frequency, gradient isotropy, color covariance, and skin-tone chrominance).

- [ ] **Step 1: Write the feature extraction module in `python/soil_gate.py`**

```python
"""
python/soil_gate.py

Feature extractor and inference gate for Stage 1 soil-vs-not-soil classification.
Extracts:
1. Multiscale spatial Laplacian variance (fine, medium, coarse grain)
2. Directional gradient isotropy (distinguishes isotropic granular soil from directional wood grain/fabrics)
3. Skin chrominance distance (detects human skin tones in YCbCr space)
4. Local contrast entropy and saturation distribution
"""

import os
import joblib
import numpy as np
from PIL import Image

GATE_MODEL_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "soil_gate_model.joblib")

def extract_gate_features(img_path_or_obj) -> np.ndarray:
    if isinstance(img_path_or_obj, str):
        img = Image.open(img_path_or_obj).convert("RGB")
    else:
        img = img_path_or_obj.convert("RGB")

    img_small = img.resize((128, 128))
    arr = np.asarray(img_small, dtype="float32")

    # 1. Grayscale & Multiscale Laplacian Energy
    gray = 0.2989 * arr[:, :, 0] + 0.5870 * arr[:, :, 1] + 0.1140 * arr[:, :, 2]
    
    # Laplacian kernel convolution
    lap = (
        -4 * gray[1:-1, 1:-1]
        + gray[:-2, 1:-1] + gray[2:, 1:-1]
        + gray[1:-1, :-2] + gray[1:-1, 2:]
    )
    lap_var = float(np.var(lap))
    lap_mean = float(np.mean(np.abs(lap)))

    # 2. Directional Gradients (Sobel-like) to detect anisotropic grain (wood, fabric)
    gx = gray[:, 1:] - gray[:, :-1]
    gy = gray[1:, :] - gray[:-1, :]
    gx_var = float(np.var(gx))
    gy_var = float(np.var(gy))
    ratio_xy = min(gx_var, gy_var) / (max(gx_var, gy_var) + 1e-5) # isotropic soil ~ 0.8-1.0; wood ~ 0.1-0.4

    # 3. YCbCr Chrominance & Skin Tone Detection
    # Standard digital skin locus: Cb in [77, 127], Cr in [133, 173]
    R, G, B = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]
    Y  =  0.299 * R + 0.587 * G + 0.114 * B
    Cb = -0.168736 * R - 0.331264 * G + 0.5 * B + 128
    Cr =  0.5 * R - 0.418688 * G - 0.081312 * B + 128
    
    skin_mask = (Cb >= 77) & (Cb <= 127) & (Cr >= 133) & (Cr <= 173)
    skin_fraction = float(np.mean(skin_mask))

    # 4. Color statistics
    r_mean, g_mean, b_mean = float(np.mean(R)), float(np.mean(G)), float(np.mean(B))
    r_std, g_std, b_std = float(np.std(R)), float(np.std(G)), float(np.std(B))
    rg_ratio = r_mean / (g_mean + 1e-5)
    rb_ratio = r_mean / (b_mean + 1e-5)

    # 5. High-frequency spatial roughness (granular vs smooth)
    hf_energy = float(np.mean(np.abs(gray - np.mean(gray))))

    return np.array([
        lap_var, lap_mean,
        gx_var, gy_var, ratio_xy,
        skin_fraction,
        r_mean / 255.0, g_mean / 255.0, b_mean / 255.0,
        r_std / 255.0, g_std / 255.0, b_std / 255.0,
        rg_ratio, rb_ratio,
        hf_energy,
        float(np.std(gray))
    ], dtype="float32")
```

- [ ] **Step 2: Create a seed generator for negative non-soil images in `scratch/generate_not_soil_seed.py`**
Generate / assemble diverse non-soil examples (faces, hands, skin tones, wood grains, cardboard, paper, solid swatches, household plastics).

- [ ] **Step 3: Verify feature extraction runs without errors**
Run: `python -c "import sys; sys.path.insert(0, 'python'); from soil_gate import extract_gate_features; print(extract_gate_features('public/images/hero-bg.jpg'))"`
Expected: 16-element float array output.

---

## Task 2: Train Stage 1 Soil Gate Model

**Files:**
- Create: `python/train_gate_classifier.py`
- Output: `python/soil_gate_model.joblib`
- Test: `scratch/test_gate_predictions.py`

**Interfaces:**
- Consumes: Soil samples from `Scan_Soil_Image/Real_Soil/` and non-soil samples from `python/external_datasets/not_soil/`
- Produces: `python/soil_gate_model.joblib` (Calibrated classifier returning `is_soil: bool, confidence: float`).

- [ ] **Step 1: Write `python/train_gate_classifier.py`**
Trains an ensemble classifier (`HistGradientBoostingClassifier(max_iter=150, random_state=42)`) on:
- Class 1 (`soil`): 2,000 real soil samples sampled across all 6 Munsell hues from `Scan_Soil_Image/Real_Soil/`.
- Class 0 (`not_soil`): Negative dataset encompassing skin tones, faces, hands, wood, cardboard, paper, fabric, and plain colors.

- [ ] **Step 2: Execute training script**
Run: `python python/train_gate_classifier.py`
Expected: Training completes with > 99.5% accuracy on held-out validation set, saving `python/soil_gate_model.joblib`.

- [ ] **Step 3: Verify model prediction on test images**
Run: `python scratch/test_gate_predictions.py`
Expected:
- Real soil images -> `is_soil = True` (confidence > 0.90)
- Face, skin, wood, cardboard -> `is_soil = False` (confidence > 0.90)

---

## Task 3: Integrate Soil Gate into Classification Pipeline

**Files:**
- Modify: `python/soil_classifier.py`
- Modify: `python/colorimetry_service.py`
- Modify: `python/storage.py`

**Interfaces:**
- Consumes: Image path (`str`), `MunsellTable`
- Produces: `ClassificationResult` with short-circuit to `fake_soil` on gate rejection.

- [ ] **Step 1: Update `python/soil_classifier.py`**
In `classify_soil(image_path, munsell_table)`:
1. Run Stage 1 Soil Gate:
   ```python
   is_soil, gate_conf = predict_is_soil(image_path)
   if not is_soil:
       return ClassificationResult(
           label="fake_soil",
           confidence=round(gate_conf, 3),
           munsell_hue="NA",
           munsell_value=-1,
           munsell_chroma=-1,
           munsell_notation="NA",
           color_delta_e=-1.0,
           texture_score=round(_texture_score(image_path), 3),
           rgb=dominant_rgb(image_path),
       )
   ```
2. If Stage 1 passes (`is_soil == True`), proceed to Stage 2 Munsell chip determination and dataset centroid matching.

- [ ] **Step 2: Update `python/colorimetry_service.py`**
When `label == "fake_soil"`, output `munsell: null` (or `notation: "NA"`) so no fabricated soil color code is displayed or persisted.

- [ ] **Step 3: Update `python/storage.py`**
Ensure `_destination_dir()` routes all non-soil scans directly into flat `Scan_Soil_Image/Fake_Soil/`.

---

## Task 4: Regression Testing & Code Review

**Files:**
- Modify: `tests/Feature/SoilScanAnalysisTest.php`
- Run: Code Reviewer Subagent via `code-reviewer.md` template

**Interfaces:**
- Consumes: `git rev-parse HEAD~N`, `git rev-parse HEAD`
- Produces: Dispatched Code Review report with approval.

- [ ] **Step 1: Add automated non-soil regression test in Pest**
Verify `SoilScanAnalysisTest.php` tests a non-soil image (skin tone / face) and asserts `label === 'fake_soil'` and `munsell_notation === 'NA'` or null.

- [ ] **Step 2: Run test suite**
Run: `php artisan test --filter=SoilScanAnalysisTest`
Expected: PASS.

- [ ] **Step 3: Dispatch code review subagent**
Follow `requesting-code-review/SKILL.md` using `code-reviewer.md` template. Verify all critical and important items are addressed.

---

## Verification Plan

### Automated Tests
1. `python python/colorimetry_service.py scratch/test_non_soil/skin_tone.jpg` -> `label: fake_soil`, `munsell: NA`
2. `python python/colorimetry_service.py scratch/test_non_soil/wood_texture.jpg` -> `label: fake_soil`, `munsell: NA`
3. `python python/colorimetry_service.py scratch/test_non_soil/cardboard.jpg` -> `label: fake_soil`, `munsell: NA`
4. `python python/colorimetry_service.py python/external_datasets/CyAUG-Dataset/Alluvial_Soil/1.jpg` -> `label: real_soil`, `10YR 4/3`
5. `php artisan test --filter=SoilScanAnalysisTest` -> 100% pass.
