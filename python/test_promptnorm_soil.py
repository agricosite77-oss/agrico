"""
Standalone PromptNorm quality-validation script for soil photos.

Usage
-----
    python test_promptnorm_soil.py --images path/to/soil/photos/

Saves side-by-side before/after images to ./test_output/.
Run this BEFORE wiring PromptNorm into the live pipeline.

Requirements (must be installed first):
    pip install torch torchvision --index-url https://download.pytorch.org/whl/cu121
    pip install lightning einops lpips torchmetrics pillow numpy

Repos that must be cloned alongside python/:
    python/promptnorm_repo/        (davidserra9/promptnorm)
    python/depth_anything_v2_repo/ (DepthAnything/Depth-Anything-V2)

Checkpoint:
    python/pretrained_ckpt/promptnorm.ckpt
    python/pretrained_ckpt/depth_anything_v2_vitb.pth
"""

import argparse
import os
import sys
import time
from pathlib import Path

import numpy as np
from PIL import Image

# ---------------------------------------------------------------------------
# Path setup
# ---------------------------------------------------------------------------
SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR / "promptnorm_repo"))
sys.path.insert(0, str(SCRIPT_DIR / "depth_anything_v2_repo"))

try:
    import torch
    import torch.nn as nn
    import lightning.pytorch as pl
except ImportError as e:
    print(
        f"\n[ERROR] Missing dependency: {e}\n"
        "Run: pip install torch torchvision --index-url https://download.pytorch.org/whl/cu121\n"
        "     pip install lightning einops lpips torchmetrics\n"
    )
    sys.exit(1)

try:
    from einops import rearrange  # noqa
    from lpips import LPIPS
    from torchmetrics.image import StructuralSimilarityIndexMeasure  # noqa
except ImportError as e:
    print(f"[ERROR] Missing dependency: {e}\n     pip install lightning einops lpips torchmetrics")
    sys.exit(1)

try:
    from model import PromptNorm
except ImportError:
    print("[ERROR] Cannot import PromptNorm. Is python/promptnorm_repo/ cloned?")
    sys.exit(1)


class PromptNormModel(pl.LightningModule):
    def __init__(self):
        super().__init__()
        self.net = PromptNorm(decoder=True)
        self.lpips_loss = LPIPS(net="vgg").requires_grad_(False)
        self.loss_fn = nn.L1Loss()

    def forward(self, x, depth):
        return self.net(x, depth)


def load_promptnorm(ckpt_path, device):
    model = PromptNormModel()
    state = torch.load(ckpt_path, map_location=device)
    sd = state.get("state_dict", state)
    model.load_state_dict(sd, strict=True)
    model.to(device)
    model.eval()
    return model


def load_depth_model(ckpt_path, device):
    try:
        from depth_anything_v2.dpt import DepthAnythingV2
    except ImportError:
        print("[ERROR] Cannot import DepthAnythingV2. Is python/depth_anything_v2_repo/ cloned?")
        sys.exit(1)
    model_cfg = {"encoder": "vitb", "features": 128, "out_channels": [96, 192, 384, 768]}
    depth_model = DepthAnythingV2(**model_cfg)
    depth_model.load_state_dict(torch.load(ckpt_path, map_location=device), strict=True)
    depth_model.to(device)
    depth_model.eval()
    return depth_model


def depth_to_normals(depth_np):
    dz_dx = np.gradient(depth_np, axis=1)
    dz_dy = np.gradient(depth_np, axis=0)
    nx, ny, nz = -dz_dx, -dz_dy, np.ones_like(depth_np)
    norm = np.sqrt(nx**2 + ny**2 + nz**2) + 1e-8
    nx, ny, nz = nx/norm, ny/norm, nz/norm
    normals = np.stack([nx, ny, nz], axis=-1)
    return ((normals + 1.0) / 2.0).astype(np.float32)


def get_surface_normals(pil_img, depth_model, device):
    import torchvision.transforms as T
    transform = T.Compose([
        T.Resize((518, 518)),
        T.ToTensor(),
        T.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    orig_w, orig_h = pil_img.size
    img_t = transform(pil_img).unsqueeze(0).to(device)
    with torch.no_grad():
        depth = depth_model(img_t)
    if depth.ndim == 4:
        depth = depth.squeeze(1)
    depth_np = depth.squeeze(0).cpu().numpy()
    depth_pil = Image.fromarray(depth_np).resize((orig_w, orig_h), Image.BILINEAR)
    depth_np = np.array(depth_pil, dtype=np.float32)
    d_min, d_max = depth_np.min(), depth_np.max()
    if d_max - d_min > 1e-6:
        depth_np = (depth_np - d_min) / (d_max - d_min)
    return depth_to_normals(depth_np)


TARGET_SIZE = 512


def preprocess(pil_img):
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


def normals_to_tensor(normals_np, padded_size):
    target_w, target_h = padded_size
    normals_pil = Image.fromarray((normals_np * 255).astype(np.uint8))
    normals_pil = normals_pil.resize((target_w, target_h), Image.BILINEAR)
    n = torch.from_numpy(np.array(normals_pil, dtype=np.float32) / 255.0)
    return n.permute(2, 0, 1).unsqueeze(0)


def postprocess(tensor, orig_size, scaled_size):
    out = tensor.squeeze(0).permute(1, 2, 0).cpu().numpy()
    out = np.clip(out, 0.0, 1.0)
    out_pil = Image.fromarray((out * 255).astype(np.uint8))
    out_pil = out_pil.crop((0, 0, scaled_size[0], scaled_size[1]))
    out_pil = out_pil.resize(orig_size, Image.LANCZOS)
    return out_pil


def make_comparison(before, after, label):
    from PIL import ImageDraw, ImageFont
    w, h = before.size
    after_r = after.resize((w, h), Image.LANCZOS)
    bar_h = 30
    canvas = Image.new("RGB", (w * 2, h + bar_h), (30, 30, 30))
    canvas.paste(before, (0, bar_h))
    canvas.paste(after_r, (w, bar_h))
    draw = ImageDraw.Draw(canvas)
    try:
        font = ImageFont.truetype("arial.ttf", 18)
    except Exception:
        font = ImageFont.load_default()
    draw.text((10, 5), "BEFORE", fill=(255, 200, 100), font=font)
    draw.text((w + 10, 5), f"AFTER (PromptNorm) -- {label}", fill=(100, 255, 150), font=font)
    return canvas


def main():
    parser = argparse.ArgumentParser(description="PromptNorm soil photo validation")
    parser.add_argument("--images", required=True, help="Directory of soil photos")
    parser.add_argument("--promptnorm-ckpt",
                        default=str(SCRIPT_DIR / "pretrained_ckpt" / "promptnorm.ckpt"))
    parser.add_argument("--depth-ckpt",
                        default=str(SCRIPT_DIR / "pretrained_ckpt" / "depth_anything_v2_vitb.pth"))
    parser.add_argument("--output", default=str(SCRIPT_DIR / "test_output"))
    parser.add_argument("--device", default="auto")
    args = parser.parse_args()

    if args.device == "auto":
        device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    else:
        device = torch.device(args.device)
    print(f"[INFO] Using device: {device}")
    if device.type == "cuda":
        print(f"[INFO] GPU: {torch.cuda.get_device_name(0)}")

    for ckpt, name in [(args.promptnorm_ckpt, "PromptNorm"), (args.depth_ckpt, "Depth Anything V2")]:
        if not os.path.exists(ckpt):
            print(f"[ERROR] {name} checkpoint not found: {ckpt}")
            print("  PromptNorm:   https://github.com/davidserra9/promptnorm/releases/download/v1.0/promptnorm.ckpt")
            print("  DepthAny V2:  https://huggingface.co/depth-anything/Depth-Anything-V2-Base/resolve/main/depth_anything_v2_vitb.pth")
            sys.exit(1)

    images_dir = Path(args.images)
    image_files = sorted(
        list(images_dir.glob("*.jpg")) +
        list(images_dir.glob("*.jpeg")) +
        list(images_dir.glob("*.png"))
    )
    if not image_files:
        print(f"[ERROR] No images found in {images_dir}")
        sys.exit(1)
    print(f"[INFO] Found {len(image_files)} image(s)")

    out_dir = Path(args.output)
    out_dir.mkdir(parents=True, exist_ok=True)

    print("[INFO] Loading PromptNorm...")
    promptnorm = load_promptnorm(args.promptnorm_ckpt, device)
    print("[INFO] Loading Depth Anything V2...")
    depth_model = load_depth_model(args.depth_ckpt, device)

    times = []
    for img_path in image_files:
        print(f"\n[INFO] Processing: {img_path.name}")
        try:
            pil_img = Image.open(img_path).convert("RGB")
            t0 = time.time()

            normals_np = get_surface_normals(pil_img, depth_model, device)
            img_t, orig_size, scaled_size, padded_size = preprocess(pil_img)
            img_t = img_t.to(device)
            normals_t = normals_to_tensor(normals_np, padded_size).to(device)

            with torch.no_grad():
                output_t = promptnorm(img_t, normals_t)

            result_img = postprocess(output_t, orig_size, scaled_size)
            elapsed = time.time() - t0
            times.append(elapsed)
            print(f"[INFO]   Done in {elapsed:.2f}s")

            stem = img_path.stem
            comparison = make_comparison(pil_img, result_img, img_path.name)
            comparison.save(str(out_dir / f"{stem}_before_after.jpg"), "JPEG", quality=92)
            result_img.save(str(out_dir / f"{stem}_normalized.jpg"), "JPEG", quality=92)
            print(f"[INFO]   Saved: {out_dir / stem}_before_after.jpg")

        except torch.cuda.OutOfMemoryError:
            print(f"[WARNING] CUDA OOM on {img_path.name} -- skipping")
            torch.cuda.empty_cache()
        except Exception as e:
            print(f"[ERROR]   {img_path.name}: {e}")
            import traceback; traceback.print_exc()

    print(f"\n[DONE] {len(times)}/{len(image_files)} images processed")
    if times:
        print(f"[DONE] Avg inference: {np.mean(times):.2f}s")
    print(f"[DONE] Output: {out_dir}/")


if __name__ == "__main__":
    main()
