#!/bin/sh
set -e

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
