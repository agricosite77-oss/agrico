@echo off
title AgriCo PromptNorm Neural Sidecar (Port 8500)
echo ========================================================
echo   Starting AgriCo PromptNorm Neural Sidecar (Port 8500)
echo ========================================================
cd /d "%~dp0python"
python -m uvicorn promptnorm_service:app --host 127.0.0.1 --port 8500 --workers 1
pause
