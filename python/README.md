# Soil Scan module

Extends the existing `colorimetry_service.py` CLI contract (image path in
via `sys.argv`, JSON out via `stdout`) with real_soil / fake_soil /
color_only classification, Munsell matching, file storage, and a SQLite
index — without changing how PHP invokes it.

## Files

| File | Purpose |
|---|---|
| `colorimetry_service.py` | **Drop-in replacement for your existing script.** Same CLI contract. |
| `munsell_lookup.py` | Munsell CSV loader + nearest-chip color matching (RGB→Lab distance). |
| `munsell_reference_sample.csv` | Sample data for the `10R` hue only — **replace with your full official Munsell table.** |
| `soil_classifier.py` | Texture + color-gamut heuristic classifier. Swap in a trained CNN later without touching the rest of the pipeline. |
| `storage.py` | Saves each scan into `Scan_Soil_Image/<class>/[hue/notation]/`, writes a `.json` sidecar, indexes into SQLite, dedupes by file hash. |
| `reindex.py` | Batch CLI: `--mode full` or `--mode sampled --n 10`. Bootstraps the index from images already on disk. |
| `train_model.py` | Baseline MobileNetV2 training script, reads from the SQLite index, stratified by class + hue, flags sparse notation folders. |
| `ScanSoilScreen.js` | Example React Native screen: capture/pick → upload → show result. |

## Setup

```bash
pip install -r requirements.txt --break-system-packages
```

Set environment variables (or edit the defaults in `colorimetry_service.py`):

```bash
export SCAN_SOIL_ROOT=/path/to/Scan_Soil_Image
export MUNSELL_REFERENCE_CSV=/path/to/munsell_reference_full.csv
```

## How PHP calls it (unchanged)

Your existing `SoilMLAnalysisService::callPythonMicroservice()` already
does `shell_exec("python3 colorimetry_service.py " . escapeshellarg($imagePath))`
(or similar) and parses the JSON from stdout — **no change needed there.**
Just replace the old `colorimetry_service.py` with this one, and make sure
`SCAN_SOIL_ROOT` / `MUNSELL_REFERENCE_CSV` are set in the environment the
PHP process runs in (e.g. via `.env` + `putenv()`/`Process` env array, or
hardcode paths in the script if your deployment doesn't pass env vars
through `shell_exec`).

## Bootstrapping the index from existing images

If you already have images sitting in `Scan_Soil_Image/` (e.g. seed data),
run:

```bash
python reindex.py --mode full --source seed_dataset
```

For a quick spot-check / fast retrain without processing everything:

```bash
python reindex.py --mode sampled --n 10
```

## Training

```bash
python train_model.py --root ./Scan_Soil_Image --epochs 10
```

## Notes / things to decide

- `munsell_reference_sample.csv` only has approximate values for `10R` to
  match your existing folder — it is **not** the official Munsell
  renotation table. Replace it with the real dataset before relying on
  classification accuracy.
- `soil_classifier.py` ships with a texture-variance + color-distance
  heuristic (zero extra model files needed to run). Replace
  `_texture_score()` with a real trained model's output when ready.
- Decide: should new `Real_Soil/<hue>/<notation>/` folders be created
  automatically for notations not yet in the tree (current behavior), or
  should unmatched scans go to a `Real_Soil/Unclassified/` folder for
  manual review instead? Current code auto-creates folders — change
  `storage.py::_destination_dir()` if you want the review-queue behavior.
