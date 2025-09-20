# ==========================
# Stage 1: Build assets
# ==========================
FROM node:20-alpine AS builder

WORKDIR /app

# Copy package.json dulu supaya npm cache efisien
COPY package*.json ./
RUN npm install

# Copy semua source (kecuali yg di .dockerignore)
COPY . .

RUN npm run build


# ==========================
# Stage 2: PHP Laravel
# ==========================
FROM php:8.3-fpm

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip curl git \
    libzip-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel source (kecuali node_modules, vendor, dll)
COPY . .

# Copy hasil build frontend
COPY --from=builder /app/public/build ./public/build

# Install dependencies Laravel
RUN composer install

# Set permissions (supaya storage & cache bisa ditulis)
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
