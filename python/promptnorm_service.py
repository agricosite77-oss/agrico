"""
PromptNorm FastAPI sidecar service.

Start with:
    uvicorn promptnorm_service:app --host 127.0.0.1 --port 8500 --workers 1

Or in dev (auto-reload):
    uvicorn promptnorm_service:app --host 127.0.0.1 --port 8500 --reload

Endpoints
---------
GET  /health          -- Returns {"status": "ok", "device": "cuda|cpu"}
POST /normalize       -- Accepts multipart image upload, returns JPEG bytes
"""

import io
import os
import sys
import time
import logging
from pathlib import Path

import numpy as np
from PIL import Image

# ---------------------------------------------------------------------------
# Path setup -- allow importing from cloned repos
# ---------------------------------------------------------------------------
SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR / "promptnorm_repo"))
sys.path.insert(0, str(SCRIPT_DIR / "depth_anything_v2_repo"))

# Checkpoint paths (override via env vars)
PROMPTNORM_CKPT = os.environ.get(
    "PROMPTNORM_CKPT",
    str(SCRIPT_DIR / "pretrained_ckpt" / "promptnorm.ckpt")
)
DEPTH_CKPT = os.environ.get(
    "DEPTH_CKPT",
    str(SCRIPT_DIR / "pretrained_ckpt" / "depth_anything_v2_vitb.pth")
)
DEVICE_PREF = os.environ.get("PROMPTNORM_DEVICE", "auto")

logging.basicConfig(level=logging.INFO, format="[%(levelname)s] %(message)s")
log = logging.getLogger("promptnorm_service")

# ---------------------------------------------------------------------------
# Lazy imports so the module loads even if FastAPI is not yet installed
# ---------------------------------------------------------------------------
try:
    from fastapi import FastAPI, File, UploadFile, HTTPException
    from fastapi.responses import Response
except ImportError:
    raise ImportError("FastAPI not installed. Run: pip install fastapi uvicorn")

try:
    import torch
    import torch.nn as nn
    from einops import rearrange  # noqa -- needed by model.py
except ImportError as e:
    raise ImportError(
        f"Missing torch dependency: {e}."
    )

try:
    from model import PromptNorm
except ImportError:
    raise ImportError("Cannot import PromptNorm. Is python/promptnorm_repo/ cloned?")

# ---------------------------------------------------------------------------
# Global model state (loaded once at startup)
# ---------------------------------------------------------------------------
_device: torch.device = None
_promptnorm = None
_depth_model = None


def _select_device() -> torch.device:
    if DEVICE_PREF == "cpu":
        return torch.device("cpu")
    elif DEVICE_PREF == "cuda":
        return torch.device("cuda")
    else:  # auto
        return torch.device("cuda" if torch.cuda.is_available() else "cpu")


def _load_promptnorm(ckpt_path: str, device: torch.device):
    net = PromptNorm(decoder=True)
    state = torch.load(ckpt_path, map_location=device, weights_only=False)
    sd = state.get("state_dict", state)
    # Strip the "net." prefix that Lightning's wrapper adds to checkpoint keys
    sd = {k.replace("net.", "", 1): v for k, v in sd.items() if k.startswith("net.")}
    net.load_state_dict(sd, strict=True)
    net.to(device)
    net.eval()
    return net


def _load_depth_model(ckpt_path: str, device: torch.device):
    try:
        from depth_anything_v2.dpt import DepthAnythingV2
    except ImportError:
        raise ImportError("Cannot import DepthAnythingV2. Is python/depth_anything_v2_repo/ cloned?")
    model_cfg = {"encoder": "vitb", "features": 128, "out_channels": [96, 192, 384, 768]}
    model = DepthAnythingV2(**model_cfg)
    model.load_state_dict(torch.load(ckpt_path, map_location=device, weights_only=True), strict=True)
    model.to(device)
    model.eval()
    return model


# ---------------------------------------------------------------------------
# Image processing helpers
# ---------------------------------------------------------------------------
TARGET_SIZE = 512


def _depth_to_normals(depth_np: np.ndarray) -> np.ndarray:
    """Convert H×W depth map to H×W×3 surface normals in [0,1]."""
    dz_dx = np.gradient(depth_np, axis=1)
    dz_dy = np.gradient(depth_np, axis=0)
    nx, ny, nz = -dz_dx, -dz_dy, np.ones_like(depth_np)
    norm = np.sqrt(nx**2 + ny**2 + nz**2) + 1e-8
    nx, ny, nz = nx / norm, ny / norm, nz / norm
    normals = np.stack([nx, ny, nz], axis=-1)
    return ((normals + 1.0) / 2.0).astype(np.float32)


def _get_surface_normals(pil_img: Image.Image, device: torch.device) -> np.ndarray:
    import torchvision.transforms as T
    transform = T.Compose([
        T.Resize((518, 518)),
        T.ToTensor(),
        T.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    orig_w, orig_h = pil_img.size
    img_t = transform(pil_img).unsqueeze(0).to(device)
    with torch.no_grad():
        depth = _depth_model(img_t)
    if depth.ndim == 4:
        depth = depth.squeeze(1)
    depth_np = depth.squeeze(0).cpu().numpy()

    # Resize depth to original resolution
    depth_pil = Image.fromarray(depth_np).resize((orig_w, orig_h), Image.BILINEAR)
    depth_np = np.array(depth_pil, dtype=np.float32)

    # Normalize depth to [0,1]
    d_min, d_max = depth_np.min(), depth_np.max()
    if d_max - d_min > 1e-6:
        depth_np = (depth_np - d_min) / (d_max - d_min)

    return _depth_to_normals(depth_np)


def _preprocess(pil_img: Image.Image):
    """Returns (tensor [1,3,H,W], orig_size, scaled_size, padded_size)."""
    img = pil_img.convert("RGB")
    orig_size = img.size
    w, h = img.size
    scale = TARGET_SIZE / min(w, h)
    new_w, new_h = int(round(w * scale)), int(round(h * scale))
    img = img.resize((new_w, new_h), Image.LANCZOS)
    pad_w = (8 - new_w % 8) % 8
    pad_h = (8 - new_h % 8) % 8
    padded = Image.new("RGB", (new_w + pad_w, new_h + pad_h), 0)
    padded.paste(img, (0, 0))
    padded_size = padded.size
    t = torch.from_numpy(np.array(padded, dtype=np.float32) / 255.0)
    t = t.permute(2, 0, 1).unsqueeze(0)
    return t, orig_size, (new_w, new_h), padded_size


def _normals_to_tensor(normals_np: np.ndarray, padded_size) -> torch.Tensor:
    target_w, target_h = padded_size
    normals_pil = Image.fromarray((normals_np * 255).astype(np.uint8))
    normals_pil = normals_pil.resize((target_w, target_h), Image.BILINEAR)
    n = torch.from_numpy(np.array(normals_pil, dtype=np.float32) / 255.0)
    return n.permute(2, 0, 1).unsqueeze(0)


def _postprocess(tensor: torch.Tensor, orig_size, scaled_size) -> Image.Image:
    out = tensor.squeeze(0).permute(1, 2, 0).cpu().numpy()
    out = np.clip(out, 0.0, 1.0)
    out_pil = Image.fromarray((out * 255).astype(np.uint8))

    # 1. Drop the black padding — crop to the size we actually resized to pre-pad
    out_pil = out_pil.crop((0, 0, scaled_size[0], scaled_size[1]))

    # 2. THEN upscale back to the original resolution
    out_pil = out_pil.resize(orig_size, Image.LANCZOS)

    return out_pil


def _run_inference(pil_img: Image.Image, device: torch.device) -> Image.Image:
    """Core inference: normals -> PromptNorm -> normalized image."""
    normals_np = _get_surface_normals(pil_img, device)
    img_t, orig_size, scaled_size, padded_size = _preprocess(pil_img)
    img_t = img_t.to(device)
    normals_t = _normals_to_tensor(normals_np, padded_size).to(device)
    with torch.no_grad():
        output_t = _promptnorm(img_t, normals_t)
    return _postprocess(output_t, orig_size, scaled_size)


# ---------------------------------------------------------------------------
# FastAPI application
# ---------------------------------------------------------------------------
app = FastAPI(title="PromptNorm Sidecar", version="1.0")


@app.on_event("startup")
async def startup_event():
    global _device, _promptnorm, _depth_model

    if not os.path.exists(PROMPTNORM_CKPT):
        log.error(f"PromptNorm checkpoint not found: {PROMPTNORM_CKPT}")
        raise RuntimeError(f"Missing checkpoint: {PROMPTNORM_CKPT}")
    if not os.path.exists(DEPTH_CKPT):
        log.error(f"Depth Anything V2 checkpoint not found: {DEPTH_CKPT}")
        raise RuntimeError(f"Missing checkpoint: {DEPTH_CKPT}")

    _device = _select_device()
    log.info(f"Loading models on device: {_device}")
    if _device.type == "cuda":
        log.info(f"GPU: {torch.cuda.get_device_name(0)}")

    t0 = time.time()
    _promptnorm = _load_promptnorm(PROMPTNORM_CKPT, _device)
    log.info(f"PromptNorm loaded in {time.time()-t0:.1f}s")

    t0 = time.time()
    _depth_model = _load_depth_model(DEPTH_CKPT, _device)
    log.info(f"Depth Anything V2 loaded in {time.time()-t0:.1f}s")

    log.info("Sidecar ready.")


@app.get("/health")
async def health():
    return {
        "status": "ok",
        "device": str(_device) if _device else "not_loaded",
        "models_loaded": _promptnorm is not None and _depth_model is not None,
    }


@app.post("/normalize")
async def normalize(file: UploadFile = File(...)):
    """
    Accept a JPEG/PNG upload. Return a JPEG-encoded normalized image.
    Falls back gracefully on CUDA OOM.
    """
    if _promptnorm is None or _depth_model is None:
        raise HTTPException(status_code=503, detail="Models not loaded")

    try:
        data = await file.read()
        pil_img = Image.open(io.BytesIO(data)).convert("RGB")
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Invalid image: {e}")

    try:
        t0 = time.time()
        result = _run_inference(pil_img, _device)
        elapsed = time.time() - t0
        log.info(f"Normalized {file.filename or 'image'} in {elapsed:.2f}s")
    except torch.cuda.OutOfMemoryError:
        log.warning("CUDA OOM -- retrying on CPU")
        torch.cuda.empty_cache()
        try:
            # Move models to CPU temporarily
            _promptnorm.cpu()
            _depth_model.cpu()
            result = _run_inference(pil_img, torch.device("cpu"))
            # Move back to GPU
            _promptnorm.to(_device)
            _depth_model.to(_device)
        except Exception as e2:
            raise HTTPException(status_code=500, detail=f"Inference failed (CPU fallback): {e2}")
    except Exception as e:
        log.error(f"Inference error: {e}")
        raise HTTPException(status_code=500, detail=f"Inference failed: {e}")

    # Encode result as JPEG
    buf = io.BytesIO()
    result.save(buf, format="JPEG", quality=92)
    buf.seek(0)
    return Response(content=buf.getvalue(), media_type="image/jpeg")
