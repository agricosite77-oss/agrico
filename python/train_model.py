"""
train_model.py

Baseline classifier training script that reads directly from the SQLite
index built by storage.py / reindex.py -- no need to re-walk the
Scan_Soil_Image/ folder tree.

- Operates as a 2-class classifier (real_soil vs. fake_soil) matching the
  active pipeline, while preserving legacy evaluation for historical color_only
  entries if present in old records.
- Stratifies the train/val/test split by class, and additionally by Munsell hue
  group within real_soil so no single hue dominates the training set.
- Trains a lightweight model (Scikit-Learn HistGradientBoostingClassifier or MobileNetV2 if TensorFlow is installed).
- Prints per-class and per-hue precision/recall and a confusion matrix.
- Flags any Munsell notation folder or sparse classes with too few samples.
"""

import argparse
import importlib.util
import os
import sqlite3
import sys
from collections import Counter

import joblib
import numpy as np
from PIL import Image
from sklearn.ensemble import HistGradientBoostingClassifier
from sklearn.metrics import classification_report, confusion_matrix

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

MIN_SAMPLES_PER_NOTATION = 5  # below this, flag the folder as too sparse


def load_index(db_path: str):
    with sqlite3.connect(db_path) as conn:
        conn.row_factory = sqlite3.Row
        rows = conn.execute("SELECT * FROM soil_scans").fetchall()
    return [dict(r) for r in rows]


def flag_sparse_classes_and_notations(records):
    class_counts = Counter(r["predicted_label"] for r in records)
    print("\n--- Dataset Class Distribution ---")
    for label in sorted(class_counts.keys()):
        c = class_counts[label]
        note = " (legacy class; non-real scans now route to fake_soil)" if label == "color_only" else ""
        print(f"    {label}: {c} sample(s){note}")
    if class_counts.get("color_only", 0) < 5:
        print("    [NOTE] 'color_only' has minimal or zero samples; classifier operates primarily as 2-class (real_soil vs fake_soil).")

    counts = Counter(
        r["munsell_notation"] for r in records if r["predicted_label"] == "real_soil"
    )
    sparse = {notation: c for notation, c in counts.items() if c < MIN_SAMPLES_PER_NOTATION}
    if sparse:
        print("\n[WARNING] Munsell notation folders with too few samples "
              f"(< {MIN_SAMPLES_PER_NOTATION}):")
        for notation, c in sorted(sparse.items()):
            print(f"    {notation}: {c} sample(s)")
    return sparse


def stratified_split(records, val_frac=0.15, test_frac=0.15, seed=42):
    """
    Stratifies by class, and within real_soil, by Munsell hue group, so
    the split doesn't accidentally put all of one hue in train and none
    in test.
    """
    rng = np.random.default_rng(seed)

    def _strata_key(r):
        if r["predicted_label"] == "real_soil":
            return f"real_soil::{r['munsell_hue']}"
        return r["predicted_label"]

    buckets = {}
    for r in records:
        buckets.setdefault(_strata_key(r), []).append(r)

    train, val, test = [], [], []
    for _key, items in buckets.items():
        idx = np.arange(len(items))
        rng.shuffle(idx)
        n = len(items)
        n_val = max(1, int(n * val_frac)) if n >= 3 else 0
        n_test = max(1, int(n * test_frac)) if n >= 3 else 0
        val_idx = set(idx[:n_val])
        test_idx = set(idx[n_val:n_val + n_test])
        for i, item in enumerate(items):
            if i in val_idx:
                val.append(item)
            elif i in test_idx:
                test.append(item)
            else:
                train.append(item)
    return train, val, test


def build_model(num_classes: int, image_size=(224, 224)):
    has_tf = importlib.util.find_spec("tensorflow") is not None
    if has_tf:
        import importlib
        tf = importlib.import_module("tensorflow")
        layers = importlib.import_module("tensorflow.keras.layers")
        models = importlib.import_module("tensorflow.keras.models")

        base = tf.keras.applications.MobileNetV2(
            input_shape=(*image_size, 3), include_top=False, weights="imagenet"
        )
        base.trainable = False

        model = models.Sequential([
            base,
            layers.GlobalAveragePooling2D(),
            layers.Dropout(0.3),
            layers.Dense(128, activation="relu"),
            layers.Dense(num_classes, activation="softmax"),
        ])
        model.compile(optimizer="adam", loss="sparse_categorical_crossentropy", metrics=["accuracy"])
        return model, "tensorflow"

    print("[INFO] TensorFlow not installed. Using Scikit-Learn Gradient Boosting Classifier.")
    model = HistGradientBoostingClassifier(random_state=42, max_iter=100)
    return model, "sklearn"


def extract_features(records):
    """Extracts color, delta_e, and texture features for scikit-learn training."""
    X = []
    for r in records:
        r_val = float(r.get("rgb_r", 0) or 0) / 255.0
        g_val = float(r.get("rgb_g", 0) or 0) / 255.0
        b_val = float(r.get("rgb_b", 0) or 0) / 255.0
        tex = float(r.get("texture_score", 0) or 0)
        de = float(r.get("color_delta_e", 0) or 0)
        X.append([r_val, g_val, b_val, tex, de])
    return np.array(X, dtype="float32")


def load_image_batch(records, label_to_idx, image_size=(224, 224)):
    X, y = [], []
    for r in records:
        try:
            img = Image.open(r["file_path"]).convert("RGB").resize(image_size)
            X.append(np.asarray(img, dtype="float32") / 255.0)
            y.append(label_to_idx[r["predicted_label"]])
        except Exception as exc:  # noqa: BLE001
            print(f"[SKIP] Could not load {r['file_path']}: {exc}", file=sys.stderr)
    return np.array(X), np.array(y)


def evaluate(model, model_type, X_test, y_test, idx_to_label, test_records):
    if model_type == "tensorflow":
        y_pred = np.argmax(model.predict(X_test), axis=1)
    else:
        y_pred = model.predict(X_test)

    labels_order = sorted(idx_to_label.keys())
    target_names = [idx_to_label[i] for i in labels_order]

    print("\n--- Overall classification report (real_soil vs. fake_soil) ---")
    print(classification_report(y_test, y_pred, labels=labels_order, target_names=target_names, zero_division=0))

    print("--- Confusion matrix ---")
    print(confusion_matrix(y_test, y_pred, labels=labels_order))

    # Per-hue breakdown, real_soil only
    print("\n--- Per-hue accuracy (real_soil subset) ---")
    hue_correct, hue_total = Counter(), Counter()
    for i, r in enumerate(test_records):
        if r["predicted_label"] != "real_soil":
            continue
        hue_total[r["munsell_hue"]] += 1
        if idx_to_label[y_pred[i]] == "real_soil":
            hue_correct[r["munsell_hue"]] += 1
    for hue in sorted(hue_total):
        acc = hue_correct[hue] / hue_total[hue]
        print(f"    {hue}: {acc:.2%} ({hue_correct[hue]}/{hue_total[hue]})")


def main() -> int:
    parser = argparse.ArgumentParser(description="Train the soil scan classifier from the SQLite index.")
    parser.add_argument("--root", default=os.environ.get("SCAN_SOIL_ROOT", "./Scan_Soil_Image"))
    parser.add_argument("--epochs", type=int, default=10)
    parser.add_argument("--batch-size", type=int, default=16)
    parser.add_argument("--model-out", default="./soil_scan_model.h5")
    args = parser.parse_args()

    db_path = os.path.join(args.root, "soil_scans_index.sqlite3")
    records = load_index(db_path)
    if not records:
        print(f"No records found in {db_path}. Run reindex.py first.", file=sys.stderr)
        return 1

    flag_sparse_classes_and_notations(records)

    train, val, test = stratified_split(records)
    print(f"\nSplit sizes -> train: {len(train)}  val: {len(val)}  test: {len(test)}")

    labels = sorted({r["predicted_label"] for r in records})
    label_to_idx = {label: i for i, label in enumerate(labels)}
    idx_to_label = {i: label for label, i in label_to_idx.items()}

    model, model_type = build_model(num_classes=len(labels))

    if model_type == "tensorflow":
        X_train, y_train = load_image_batch(train, label_to_idx)
        X_val, y_val = load_image_batch(val, label_to_idx)
        X_test, y_test = load_image_batch(test, label_to_idx)

        model.fit(
            X_train, y_train,
            validation_data=(X_val, y_val),
            epochs=args.epochs,
            batch_size=args.batch_size,
        )
        model.save(args.model_out)
        print(f"\nModel saved to {args.model_out}")
    else:
        X_train = extract_features(train)
        y_train = np.array([label_to_idx[r["predicted_label"]] for r in train])
        X_test = extract_features(test)
        y_test = np.array([label_to_idx[r["predicted_label"]] for r in test])

        model.fit(X_train, y_train)
        out_path = args.model_out.replace(".h5", ".joblib")
        joblib.dump(model, out_path)
        print(f"\nModel saved to {out_path}")

    evaluate(model, model_type, X_test, y_test, idx_to_label, test)
    return 0


if __name__ == "__main__":
    sys.exit(main())
