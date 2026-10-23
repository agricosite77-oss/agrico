# ----------------------------------------------------
# Stage 1: Build Frontend Assets with Node
# ----------------------------------------------------
FROM public.ecr.aws/docker/library/node:20-bookworm-slim AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ----------------------------------------------------
# Stage 2: Production FrankenPHP / PHP 8.4 Application
# ----------------------------------------------------
FROM dunglas/frankenphp:1-php8.4-bookworm

# Install system dependencies, Python 3, and scientific image libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libgl1 \
    libglib2.0-0 \
    python3 \
    python3-pip \
    python3-venv \
    python-is-python3 \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions (pdo_pgsql, gd, intl, mbstring, zip, opcache, bcmath, pcntl)
RUN install-php-extensions \
    pdo_pgsql \
    gd \
    intl \
    mbstring \
    zip \
    opcache \
    bcmath \
    pcntl

# Install Composer from official image
COPY --from=public.ecr.aws/docker/library/composer:2 /usr/bin/composer /usr/bin/composer

# Create virtual environment for Python dependencies to prevent PEP 668 errors
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
ENV PYTHON_BINARY="/opt/venv/bin/python"

WORKDIR /app

# Install Python ML and colorimetry requirements in venv
COPY python/requirements.txt /app/python/requirements.txt
RUN /opt/venv/bin/pip install --no-cache-dir -r /app/python/requirements.txt

# Copy application source code
COPY . /app

# Copy built frontend assets from node_builder
COPY --from=node_builder /app/public/build /app/public/build

# Install PHP production dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=php+

# Copy Caddyfile and ensure entrypoint is executable with Linux LF line endings
COPY Caddyfile /etc/caddy/Caddyfile
RUN sed -i 's/\r$//' /app/docker-entrypoint.sh && chmod +x /app/docker-entrypoint.sh

# Configure file permissions for storage and cache
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public/scans \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Environment defaults
ENV PORT=8080
ENV SERVER_NAME=":${PORT}"

EXPOSE ${PORT}

ENTRYPOINT ["/bin/sh", "/app/docker-entrypoint.sh"]
