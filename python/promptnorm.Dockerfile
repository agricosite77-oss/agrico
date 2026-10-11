FROM python:3.11-slim

WORKDIR /app

RUN apt-get update && apt-get install -y git curl && rm -rf /var/lib/apt/lists/*

COPY python/promptnorm_repo/ ./promptnorm_repo/
COPY python/depth_anything_v2_repo/ ./depth_anything_v2_repo/
COPY python/promptnorm_service.py .

RUN mkdir -p pretrained_ckpt && \
    curl -L https://github.com/davidserra9/promptnorm/releases/download/v1.0/promptnorm.ckpt \
         -o pretrained_ckpt/promptnorm.ckpt && \
    curl -L https://huggingface.co/depth-anything/Depth-Anything-V2-Base/resolve/main/depth_anything_v2_vitb.pth \
         -o pretrained_ckpt/depth_anything_v2_vitb.pth

RUN pip install --no-cache-dir \
    torch torchvision --index-url https://download.pytorch.org/whl/cpu \
    lightning einops lpips torchmetrics fastapi uvicorn pillow numpy

EXPOSE 8500
CMD ["uvicorn", "promptnorm_service:app", "--host", "0.0.0.0", "--port", "8500"]
