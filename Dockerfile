# ==========================
# Stage 1: Build assets
# ==========================
FROM node:20-alpine AS builder

WORKDIR /var/www/html

COPY package.json package-lock.json* ./
RUN npm install

COPY . .
RUN npm run build


# ==========================
# Stage 2: PHP Laravel
# ==========================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip curl git \
    libzip-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel source
COPY . .

# ✅ Sesuaikan dengan WORKDIR builder
COPY --from=builder /var/www/html/public/build ./public/build

# Install dependencies Laravel
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
 && php artisan config:clear \
 && php artisan cache:clear \
 && php artisan route:clear

EXPOSE 9000
CMD ["php-fpm"]
