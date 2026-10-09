"""
python/train_gate_classifier.py

Trains a calibrated binary classifier (soil vs. not_soil) using:
- Positive class: Real soil images from CyAUG-Dataset and Scan_Soil_Image/Real_Soil/
- Negative class: Synthetic and real non-soil images (skin tones, faces, hands,
  wood planks, cardboard, paper, fabrics, household surfaces, screenshots).

Saves the trained model to python/soil_gate_model.joblib.
"""

import glob
import os
import sys
import numpy as np
from PIL import Image
from sklearn.ensemble import HistGradientBoostingClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, confusion_matrix
import joblib

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from soil_gate import extract_gate_features, GATE_MODEL_PATH


def generate_synthetic_non_soil_samples(n=1000):
    """
    Generates realistic non-soil synthetic images covering:
    - Human skin tones (smooth gradients, facial lighting, pores)
    - Wood planks (strong directional streaks)
    - Cardboard and paper (flat matte, low high-frequency energy)
    - Fabric & clothing (cross-hatch grid patterns)
    - Artificial solid colors and swatches
    """
    samples = []
    rng = np.random.default_rng(42)

    for i in range(n):
        mode = i % 5
        h, w = 128, 128

        if mode == 0:
            # Human skin tone: Smooth gradients + soft noise
            # R in [150, 240], G in [100, 190], B in [70, 160] with R > G > B
            base_r = rng.integers(150, 240)
            base_g = int(base_r * rng.uniform(0.65, 0.82))
            base_b = int(base_g * rng.uniform(0.65, 0.85))
            
            # Add smooth lighting gradient (typical of portrait / face lighting)
            gx, gy = np.meshgrid(np.linspace(-1, 1, w), np.linspace(-1, 1, h))
            grad = (gx * rng.uniform(-20, 20) + gy * rng.uniform(-20, 20)).astype(np.float32)
            noise = rng.normal(0, rng.uniform(1.5, 4.0), (h, w, 3)) # smooth skin
            
            arr = np.zeros((h, w, 3), dtype=np.float32)
            arr[:, :, 0] = np.clip(base_r + grad + noise[:, :, 0], 0, 255)
            arr[:, :, 1] = np.clip(base_g + grad + noise[:, :, 1], 0, 255)
            arr[:, :, 2] = np.clip(base_b + grad + noise[:, :, 2], 0, 255)
            samples.append(Image.fromarray(arr.astype(np.uint8)))

        elif mode == 1:
            # Wood grain: Strong directional streaks (anisotropic)
            base_val = rng.integers(80, 180)
            wood = np.zeros((h, w, 3), dtype=np.float32)
            freq = rng.uniform(2, 10)
            for y in range(h):
                line = base_val + 35.0 * np.sin(y / freq) + rng.normal(0, 3)
                wood[y, :, 0] = np.clip(line, 0, 255)
                wood[y, :, 1] = np.clip(line * rng.uniform(0.6, 0.75), 0, 255)
                wood[y, :, 2] = np.clip(line * rng.uniform(0.35, 0.5), 0, 255)
            samples.append(Image.fromarray(wood.astype(np.uint8)))

        elif mode == 2:
            # Cardboard / paper packaging: Very low high-frequency variance
            base_r = rng.integers(140, 210)
            base_g = int(base_r * rng.uniform(0.75, 0.88))
            base_b = int(base_g * rng.uniform(0.60, 0.78))
            noise = rng.normal(0, rng.uniform(0.8, 2.5), (h, w, 3))
            arr = np.clip(np.array([base_r, base_g, base_b]) + noise, 0, 255)
            samples.append(Image.fromarray(arr.astype(np.uint8)))

        elif mode == 3:
            # Fabric / woven grid / plaid: Regular periodic grid
            arr = np.zeros((h, w, 3), dtype=np.float32)
            base_c = rng.integers(50, 200, size=3)
            stripe = (np.arange(h)[:, None] % 8 < 4) ^ (np.arange(w)[None, :] % 8 < 4)
            arr = base_c + stripe[:, :, None] * rng.integers(20, 50)
            samples.append(Image.fromarray(np.clip(arr, 0, 255).astype(np.uint8)))

        else:
            # Flat solid colors / plastic / walls
            base_c = rng.integers(20, 240, size=3)
            arr = np.full((h, w, 3), base_c, dtype=np.uint8)
            samples.append(Image.fromarray(arr))

    return samples


def main():
    print("[1/4] Gathering dataset samples...")
    script_dir = os.path.dirname(os.path.abspath(__file__))
    project_root = os.path.abspath(os.path.join(script_dir, ".."))

    # 1. Real soil images from external datasets (CyAUG-Dataset, Soil-Dataset) and Scan_Soil_Image/Real_Soil
    external_soil_paths = (
        glob.glob(os.path.join(script_dir, "external_datasets", "CyAUG-Dataset", "**", "*.*"), recursive=True) +
        glob.glob(os.path.join(script_dir, "external_datasets", "Soil-Dataset", "**", "*.*"), recursive=True)
    )
    external_soil_paths = [
        p for p in external_soil_paths
        if os.path.splitext(p)[1].lower() in {".jpg", ".jpeg", ".png", ".bmp", ".webp"}
    ]
    custom_real_paths = (
        glob.glob(os.path.join(script_dir, "Scan_Soil_Image", "Real_Soil", "**", "*.jpg"), recursive=True) +
        glob.glob(os.path.join(script_dir, "Scan_Soil_Image", "Real_Soil", "**", "*.jpeg"), recursive=True) +
        glob.glob(os.path.join(script_dir, "Scan_Soil_Image", "Real_Soil", "**", "*.png"), recursive=True)
    )
    soil_paths = list(dict.fromkeys(external_soil_paths + custom_real_paths))

    print(f"Found {len(soil_paths)} real soil images ({len(external_soil_paths)} from external datasets, {len(custom_real_paths)} from Scan_Soil_Image/Real_Soil).")
    # Subsample 1,500 soil images for balanced training
    rng = np.random.default_rng(42)
    if len(soil_paths) > 1500:
        soil_sample_paths = rng.choice(soil_paths, size=1500, replace=False)
    else:
        soil_sample_paths = soil_paths

    X = []
    y = []

    print("[2/4] Extracting features for real soil (Class 1)...")
    for p in soil_sample_paths:
        try:
            feats = extract_gate_features(p)
            X.append(feats)
            y.append(1)
        except Exception:
            continue

    print(f"Loaded {len(X)} real soil samples.")

    # 2. Real non-soil images: Fake_Soil dataset + UI/screenshot images + synthetic set
    print("[3/4] Extracting features for non-soil (Class 0)...")
    fake_soil_paths = []
    fake_soil_dir = os.path.join(script_dir, "Scan_Soil_Image", "Fake_Soil")
    if os.path.exists(fake_soil_dir):
        for fname in os.listdir(fake_soil_dir):
            ext = os.path.splitext(fname)[1].lower()
            if ext in {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}:
                fake_soil_paths.append(os.path.join(fake_soil_dir, fname))

    print(f"Loaded {len(fake_soil_paths)} genuine non-soil/fake-soil images from {fake_soil_dir}.")
    fake_soil_extracted = 0
    for p in fake_soil_paths:
        try:
            img = Image.open(p)
            # Original
            X.append(extract_gate_features(img))
            y.append(0)
            fake_soil_extracted += 1

            # Horizontal flip augmentation
            img_flip = img.transpose(Image.Transpose.FLIP_LEFT_RIGHT)
            X.append(extract_gate_features(img_flip))
            y.append(0)

            # Center crop augmentation (85%)
            w, h = img.size
            if w > 20 and h > 20:
                crop_box = (int(w * 0.075), int(h * 0.075), int(w * 0.925), int(h * 0.925))
                img_crop = img.crop(crop_box)
                X.append(extract_gate_features(img_crop))
                y.append(0)

            # 90-degree rotation augmentation
            img_rot = img.transpose(Image.Transpose.ROTATE_90)
            X.append(extract_gate_features(img_rot))
            y.append(0)
        except Exception:
            continue

    print(f"Extracted {fake_soil_extracted} base fake soil images ({fake_soil_extracted * 4} augmented features).")

    # General UI / Screenshot non-soil
    real_non_soil = (
        glob.glob(os.path.join(project_root, "public", "images", "*.jpg")) +
        glob.glob(os.path.join(project_root, "public", "images", "*.png"))
    )
    for p in real_non_soil:
        try:
            feats = extract_gate_features(p)
            X.append(feats)
            y.append(0)
        except Exception:
            continue

    synth_non_soil = generate_synthetic_non_soil_samples(n=1200)
    for img in synth_non_soil:
        try:
            feats = extract_gate_features(img)
            X.append(feats)
            y.append(0)
        except Exception:
            continue

    X = np.array(X, dtype="float32")
    y = np.array(y, dtype="int32")
    print(f"Total dataset size: {len(X)} (Real Soil: {sum(y == 1)}, Non-Soil / Fake: {sum(y == 0)})")

    print("[4/4] Training HistGradientBoostingClassifier Soil Gate...")
    # --- Train / test split ---
    # Use stratify only when each class has at least 2 samples (required by sklearn)
    unique_classes, class_counts = np.unique(y, return_counts=True)
    can_stratify = all(c >= 2 for c in class_counts)
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.20, random_state=42, stratify=(y if can_stratify else None)
    )

    model = HistGradientBoostingClassifier(
        max_iter=150,
        learning_rate=0.08,
        max_leaf_nodes=31,
        class_weight="balanced",
        random_state=42,
    )
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    print("\n--- Gate Classifier Evaluation Report ---")

    present_labels = sorted(np.unique(np.concatenate([y_test, y_pred])))
    all_label_names = ["not_soil (fake)", "real_soil"]
    used_target_names = [all_label_names[i] for i in present_labels]

    if len(present_labels) < 2:
        print(f"WARNING: Test set only contains class(es): {[all_label_names[i] for i in present_labels]}")
        print(f"  Real_Soil count in dataset: {sum(np.array(y) == 1)} — add more Real_Soil images for a valid split.")

    print(classification_report(
        y_test, y_pred,
        labels=present_labels,
        target_names=used_target_names,
        digits=4,
        zero_division=0,
    ))
    print("Confusion Matrix (rows=actual, cols=predicted):")
    print(confusion_matrix(y_test, y_pred, labels=present_labels))
    if len(present_labels) == 2:
        print(f"  Labels: {used_target_names}")

    joblib.dump(model, GATE_MODEL_PATH)
    print(f"\nSuccessfully trained and saved Soil Gate model to {GATE_MODEL_PATH}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
