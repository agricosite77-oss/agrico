#!/bin/sh
set -e

# Persistent dataset lives on the Railway Volume (e.g. SCAN_SOIL_ROOT=/data/Scan_Soil_Image)
if [ -n "$SCAN_SOIL_ROOT" ]; then
  mkdir -p "$SCAN_SOIL_ROOT/Real_Soil" "$SCAN_SOIL_ROOT/Fake_Soil" 2>/dev/null || true
  chown -R www-data:www-data "$SCAN_SOIL_ROOT" 2>/dev/null || true
  # Build the Stage 1 cache in the background if it is missing (never blocks boot)
  if [ ! -f "$SCAN_SOIL_ROOT/stage1_reference_cache.npz" ]; then
    ( /opt/venv/bin/python /app/python/build_stage1_reference_cache.py >> /tmp/stage1_cache_boot.log 2>&1 & ) || true
  fi
fi

# Ensure public storage is linked
php artisan storage:link --force 2>/dev/null || true

# Run pending database migrations
php artisan migrate --force 2>/dev/null || true

# Optimize framework caches for production
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# FrankenPHP binds to the port provided by Railway ($PORT)
export SERVER_NAME=":${PORT:-8080}"

exec frankenphp run --config /etc/caddy/Caddyfile
