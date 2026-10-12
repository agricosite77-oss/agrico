#!/usr/bin/env python3
"""Benchmarks Stage 1 before/after the live dataset comparison, plus leave-one-out accuracy.

Variants (each measured per image):
  A  gate only (current Stage 1: predict_is_soil on full + ROI)
  B  gate + compare_to_reference (cache-backed)           [warm, in-process]
  B* B as a fresh subprocess incl. interpreter+import+cache load (real scan = new process)
  C  gate + naive raw-image comparison (reads refs every scan) - shows why the cache matters
  S  scaling: compare_to_reference with the cache tiled to 10k / 50k rows

Usage: benchmark_stage1.py [--root DIR] [--n 100] [--fresh 10]
"""
import argparse
import os
import statistics
import subprocess
import sys
import time

import numpy as np
from PIL import Image

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, HERE)
EXT = {".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".webp"}


def stats(ms):
    ms = sorted(ms)
    return f"mean={statistics.mean(ms):8.2f}  p50={ms[len(ms)//2]:8.2f}  p95={ms[int(len(ms)*0.95)-1]:8.2f} ms"


def timed(fn, items):
    out = []
    for it in items:
        t = time.perf_counter()
        fn(it)
        out.append((time.perf_counter() - t) * 1000)
    return out


def sample_images(root, n):
    import random
    random.seed(7)
    pools = {"Real_Soil": [], "Fake_Soil": []}
    for sub in pools:
        for dp, _, fs in os.walk(os.path.join(root, sub)):
            pools[sub] += [os.path.join(dp, f) for f in fs if os.path.splitext(f)[1].lower() in EXT]
    half = n // 2
    return (random.sample(pools["Real_Soil"], min(half, len(pools["Real_Soil"])))
            + random.sample(pools["Fake_Soil"], min(n - half, len(pools["Fake_Soil"]))))


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--root", default=os.environ.get("SCAN_SOIL_ROOT"))
    ap.add_argument("--n", type=int, default=100)
    ap.add_argument("--fresh", type=int, default=10)
    a = ap.parse_args()
    os.environ["SCAN_SOIL_ROOT"] = a.root

    import soil_gate
    from soil_gate import predict_is_soil, compare_to_reference

    imgs = sample_images(a.root, a.n)
    pil = [Image.open(p).convert("RGB") for p in imgs]
    predict_is_soil(pil[0]); compare_to_reference(pil[0])  # warm model + cache

    cache = soil_gate._load_reference_cache()
    print(f"images={len(pil)}  cache real={len(cache[0])} fake={len(cache[1])}\n")

    def stage1_a(im):
        predict_is_soil(im); predict_is_soil(im)  # full + ROI as in soil_classifier

    def stage1_b(im):
        stage1_a(im); compare_to_reference(im)

    ra = timed(stage1_a, pil)
    rb = timed(stage1_b, pil)
    print("A  Stage 1 before (gate only)      ", stats(ra))
    print("B  Stage 1 after  (gate + cache)   ", stats(rb))
    cmp_only = timed(compare_to_reference, pil)
    print("   live-compare alone              ", stats(cmp_only))
    print(f"   overhead mean: +{statistics.mean(rb) - statistics.mean(ra):.2f} ms "
          f"({(statistics.mean(rb)/statistics.mean(ra) - 1)*100:.1f}%)\n")

    # C: naive - reads raw refs each scan (capped to 200 refs so the benchmark finishes)
    root = a.root
    ref_paths = []
    for sub in ("Real_Soil", "Fake_Soil"):
        for dp, _, fs in os.walk(os.path.join(root, sub)):
            ref_paths += [os.path.join(dp, f) for f in fs if os.path.splitext(f)[1].lower() in EXT]
    ref_paths = ref_paths[:200]

    def naive(im):
        q = soil_gate.extract_combined_features(im)
        for p in ref_paths:
            with Image.open(p) as r:
                soil_gate.extract_combined_features(r.convert("RGB"))
    rc = timed(naive, pil[:3])
    print(f"C  naive raw-read of {len(ref_paths)} refs/scan  ", stats(rc),
          f"  (x{len(ref_paths)/ (len(cache[0])+len(cache[1])):.2f} of full dataset)\n")

    # S: scaling
    q = pil[0]
    for rows in (len(cache[0]) + len(cache[1]), 10_000, 50_000):
        reps_r = max(1, rows // len(cache[0]) // 2); reps_f = max(1, rows // len(cache[1]) // 2)
        old = soil_gate._REF_CACHE
        soil_gate._REF_CACHE = (np.tile(cache[0], (reps_r, 1)), np.tile(cache[1], (reps_f, 1)), cache[2], cache[3])
        ms = timed(lambda im: compare_to_reference(im), pil[:30])
        soil_gate._REF_CACHE = old
        print(f"S  compare @ ~{rows:>6} rows           ", stats(ms))

    # B*: fresh-process cost (what a real scan pays)
    code = ("import sys,os,time;t=time.perf_counter();sys.path.insert(0,%r);"
            "from PIL import Image;import soil_gate as g;im=Image.open(sys.argv[1]).convert('RGB');"
            "t1=time.perf_counter();g.predict_is_soil(im);t2=time.perf_counter();g.compare_to_reference(im);t3=time.perf_counter();"
            "print((t2-t1)*1000,(t3-t2)*1000)" % HERE)
    gates, cmps = [], []
    for p in imgs[: a.fresh]:
        out = subprocess.run([sys.executable, "-c", code, p], capture_output=True, text=True,
                             env=os.environ.copy()).stdout.split()
        if len(out) == 2:
            gates.append(float(out[0])); cmps.append(float(out[1]))
    if cmps:
        print(f"\nB* fresh process (cold): first gate call mean={statistics.mean(gates):.1f} ms, "
              f"cold compare (np.load + features) mean={statistics.mean(cmps):.2f} ms")

    # Leave-one-out accuracy of live comparison (per class)
    real, fake = cache[0], cache[1]
    def loo(own, other, sign):
        """margin = real_sim - fake_sim (sign=+1 for real queries, -1 for fake queries)."""
        margins = []
        for i in range(len(own)):
            d_own = np.sqrt(((np.delete(own, i, axis=0) - own[i]) ** 2).sum(1))
            d_oth = np.sqrt(((other - own[i]) ** 2).sum(1))
            s_own = (1/(1+np.partition(d_own, 4)[:5])).mean()
            s_oth = (1/(1+np.partition(d_oth, 4)[:5])).mean()
            margins.append(sign * (s_own - s_oth))
        return np.array(margins)
    mr = loo(real, fake, +1)
    mf = loo(fake, real, -1)
    print(f"\nLeave-one-out: real recall={(mr > 0).mean():.3f}  fake recall={(mf < 0).mean():.3f}")
    pr = np.percentile(mr, [1, 5, 10, 50])
    print("margin (real queries)  p1/p5/p10/p50:", np.round(pr, 3))
    print("margin (fake queries)  p50/p90/p95/p99:", np.round(np.percentile(mf, [50, 90, 95, 99]), 3))
    for thr in (0.02, 0.05, 0.10):
        print(f"veto @ margin<=-{thr:.2f}: would reject {(mr <= -thr).mean()*100:.1f}% of real, "
              f"{(mf <= -thr).mean()*100:.1f}% of fake")
    return 0


if __name__ == "__main__":
    sys.exit(main())
