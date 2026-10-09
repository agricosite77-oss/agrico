#!/usr/bin/env python3
"""
diagnose_gate_decision.py — Prints the exact gate features and decision for a
specific image, to diagnose why something was accepted or rejected.

Usage:
    python diagnose_gate_decision.py <image_path>
"""

import sys
import os

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from soil_gate import extract_gate_features, predict_is_soil, get_gate_model

FEATURE_NAMES = [
    "lap_var (grain/texture variance)",
    "lap_mean (texture abs mean)",
    "gx_var (horizontal gradient variance)",
    "gy_var (vertical gradient variance)",
    "ratio_xy (0deg/90deg isotropy)",
    "skin_fraction (YCbCr skin chrominance ratio)",
    "r_mean (normalized)",
    "g_mean (normalized)",
    "b_mean (normalized)",
    "r_std (normalized)",
    "g_std (normalized)",
    "b_std (normalized)",
    "rg_ratio",
    "rb_ratio",
    "hf_energy (high-frequency roughness)",
    "gray_std (luminance std dev)",
    "directionality (FFT angular energy concentration)",
]

def main():
    if len(sys.argv) < 2:
        print("Usage: python diagnose_gate_decision.py <image_path>")
        return 1

    image_path = sys.argv[1]
    if not os.path.exists(image_path):
        print(f"Error: Image not found at {image_path}")
        return 1

    features = extract_gate_features(image_path)
    is_soil, confidence = predict_is_soil(image_path)

    model = get_gate_model()
    raw_soil_prob = None
    if model is not None:
        X = features.reshape(1, -1)
        prob = model.predict_proba(X)[0]
        raw_soil_prob = float(prob[1]) if len(prob) > 1 else float(prob[0])

    print(f"\n=== Gate Decision for: {image_path} ===")
    print(f"Result: {'REAL_SOIL' if is_soil else 'NOT_SOIL (rejected)'}")
    print(f"Confidence: {confidence:.4f}")
    if raw_soil_prob is not None:
        print(f"Raw Soil Probability (P(soil)): {raw_soil_prob:.4f} (Threshold: 0.5000)")
    print("\n--- Feature values ---")
    for name, val in zip(FEATURE_NAMES, features):
        print(f"  {name}: {val:.4f}")

    return 0

if __name__ == "__main__":
    sys.exit(main())
