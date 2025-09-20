# ==========================
# Stage 1: Build assets
# ==========================
FROM node:20-alpine AS builder

WORKDIR /app

COPY package*.json ./
RUN npm install

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

# Set Composer safe mode
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Copy composer files dulu
COPY composer.json composer.lock ./

# Install dependencies Laravel (tanpa vendor)
RUN composer install --no-dev

# Copy seluruh source code
COPY . .

# Copy hasil build frontend
COPY --from=builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
