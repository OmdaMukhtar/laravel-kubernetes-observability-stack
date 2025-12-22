# Stage 0: Frontend build
FROM node:20 AS frontend
WORKDIR /app
COPY src/package*.json src/vite.config.js ./
RUN npm install
COPY src/ .
RUN npm run build

# Stage 1: Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY src/ /app
RUN composer install --no-dev --optimize-autoloader

# Stage 2: PHP-FPM + Nginx
FROM php:8.2-fpm
# Install PHP extensions
RUN apt-get update && apt-get install -y \
  nginx \
  git \
  unzip \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libzip-dev \
  zip \
  curl \
  vim \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copy Laravel app
COPY src/ /var/www

# Copy vendor from stage
COPY --from=vendor /src/vendor /var/www/vendor

# Copy compiled frontend assets
COPY --from=frontend /src/public/build /var/www/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www \
  && chmod -R 755 /var/www/storage

EXPOSE 80

# Start PHP-FPM + Nginx
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
