FROM php:8.2-fpm-alpine AS base

# System dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    mysql-client

# PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# OPcache (production optimization)
RUN docker-php-ext-install opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first (layer cache friendly)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application
COPY . .

# Finalize autoloader
RUN composer dump-autoload --optimize --no-dev

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# PHP config
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

EXPOSE 9000

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php-fpm"]
