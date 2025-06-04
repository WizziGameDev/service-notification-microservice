# ===== BUILD STAGE =====
FROM composer:2.7 AS build

WORKDIR /app

COPY . .

# Install dependencies (include RabbitMQ + Twilio)
RUN composer require vladimir-yuldashev/laravel-queue-rabbitmq twilio/sdk --no-interaction --ignore-platform-req=ext-sockets \
    && composer install --ignore-platform-req=ext-sockets --prefer-dist --no-dev --optimize-autoloader

# ===== APP STAGE =====
FROM php:8.2-fpm-alpine

# Install PHP extensions & system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    nodejs \
    npm \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql intl gd zip sockets

# Force PHP-FPM to listen on all interfaces (avoid 127.0.0.1 issue)
RUN echo "listen = 0.0.0.0:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www

# Copy code & vendor from build stage
COPY --from=build /app /var/www

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
