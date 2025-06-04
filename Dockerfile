# ===== APP STAGE =====
FROM php:8.2-fpm-alpine

# Install ekstensi PHP tambahan
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql intl gd zip sockets

# Install Composer di App Stage
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Copy source code langsung
COPY . .

# Install dependency Laravel, RabbitMQ, Twilio, dsb.
RUN composer require vladimir-yuldashev/laravel-queue-rabbitmq:"^14.2" twilio/sdk \
    && composer install --ignore-platform-req=ext-sockets --prefer-dist --no-dev --optimize-autoloader

# Set permission
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
