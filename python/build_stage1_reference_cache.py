#!/usr/bin/env python3
"""Builds/refreshes $SCAN_SOIL_ROOT/stage1_reference_cache.npz.

Incremental: re-reads only new/changed files (keyed by relpath+size+mtime).
Atomic write + lock file so a scan never reads a half-written cache.
Prints a JSON summary on stdout.

Usage: build_stage1_reference_cache.py [--root DIR] [--full]
"""
import argparse
import json
import os
import sys
import time

import numpy as np
from PIL import Image

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from soil_gate import extract_combined_features, REF_CACHE_NAME, REF_FEATURE_VERSION, REF_DIMS  # noqa: E402

EXT = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def scan(root):
    out = []  # (label, relpath, size, mtime_ns)
    for label, sub in ((1, "Real_Soil"), (0, "Fake_Soil")):
        base = os.path.join(root, sub)
        for dp, _, files in os.walk(base):
            for f in files:
                if os.path.splitext(f)[1].lower() in EXT:
                    p = os.path.join(dp, f)
                    st = os.stat(p)
                    out.append((label, os.path.relpath(p, root).replace("\\", "/"),
                                st.st_size, st.st_mtime_ns))
    return out


def load_old(path):
    try:
        with np.load(path, allow_pickle=False) as z:
            if int(z["version"]) != REF_FEATURE_VERSION:
                return {}
            return {(str(k), int(s), int(m)): (int(l), f) for k, s, m, l, f in
                    zip(z["keys"], z["sizes"], z["mtimes"], z["labels"], z["feats"])}
    except Exception:
        return {}


def acquire_lock(lock):
    for _ in range(120):  # wait up to ~60 s for another builder
        try:
            return os.open(lock, os.O_CREAT | os.O_EXCL | os.O_WRONLY)
        except FileExistsError:
            try:
                if time.time() - os.path.getmtime(lock) > 600:  # stale lock
                    os.remove(lock)
            except OSError:
                pass
            time.sleep(0.5)
    raise RuntimeError("another builder holds the lock")


def main():
    ap = argparse.ArgumentParser()
    default_root = os.environ.get(
        "SCAN_SOIL_ROOT",
        os.path.join(os.path.dirname(os.path.abspath(__file__)), "Scan_Soil_Image")
    )
    ap.add_argument("--root", default=default_root)
    ap.add_argument("--full", action="store_true", help="ignore old cache, re-read everything")
    a = ap.parse_args()
    if not a.root or not os.path.isdir(a.root):
        print(json.dumps({"success": False, "error": f"bad root: {a.root}"}))
        return 1

    path = os.path.join(a.root, REF_CACHE_NAME)
    lock = path + ".lock"
    try:
        fd = acquire_lock(lock)
    except RuntimeError as e:
        print(json.dumps({"success": False, "error": str(e)}))
        return 1
    t0 = time.perf_counter()
    try:
        old = {} if a.full else load_old(path)
        rows, reused, computed, failed = [], 0, 0, 0
        for label, rel, size, mt in scan(a.root):
            hit = old.get((rel, size, mt))
            if hit:
                feat, reused = hit[1], reused + 1
            else:
                try:
                    with Image.open(os.path.join(a.root, rel)) as im:
                        feat = extract_combined_features(im.convert("RGB"))
                    computed += 1
                except Exception as e:
                    sys.stderr.write(f"[cache] skip {rel}: {e}\n")
                    failed += 1
                    continue
            rows.append((label, rel, size, mt, feat))

        labels = np.array([r[0] for r in rows], dtype=np.int8)
        feats = (np.stack([r[4] for r in rows]).astype(np.float32)
                 if rows else np.zeros((0, REF_DIMS), np.float32))
        mu = feats.mean(axis=0) if len(feats) else np.zeros(REF_DIMS, np.float32)
        sd = (feats.std(axis=0) + 1e-6) if len(feats) else np.ones(REF_DIMS, np.float32)
        tmp = path + ".tmp.npz"
        np.savez_compressed(
            tmp, version=np.int32(REF_FEATURE_VERSION), feats=feats, labels=labels,
            mu=mu.astype(np.float32), sd=sd.astype(np.float32),
            keys=np.array([r[1] for r in rows]),
            sizes=np.array([r[2] for r in rows], np.int64),
            mtimes=np.array([r[3] for r in rows], np.int64),
            real=feats[labels == 1], fake=feats[labels == 0])
        os.replace(tmp, path)  # atomic: scans never see a partial file

        # Stage 5 centroid cache is never invalidated on its own -> drop it when data changed
        if computed or len(rows) != len(old):
            c = os.path.join(a.root, "empirical_centroids.json")
            if os.path.isfile(c):
                os.remove(c)

        print(json.dumps({"success": True, "real": int((labels == 1).sum()),
                          "fake": int((labels == 0).sum()), "reused": reused,
                          "computed": computed, "failed": failed,
                          "seconds": round(time.perf_counter() - t0, 2)}))
        return 0
    finally:
        os.close(fd)
        try:
            os.remove(lock)
        except OSError:
            pass


if __name__ == "__main__":
    sys.exit(main())
