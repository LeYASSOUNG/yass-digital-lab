# ================================================================
# Dockerfile — Backend Laravel 12 (Multi-Stage Build)
# ================================================================
# Stage 1 : deps     → Installe les dépendances Composer
# Stage 2 : production → Image finale PHP-FPM + Nginx légère
# ================================================================

# --- Stage 1 : Composer Dependencies ---
FROM composer:2.8 AS deps

WORKDIR /app

# Copier les fichiers Composer en premier (meilleure utilisation du cache)
COPY composer.json composer.lock ./

# Installer les dépendances de production uniquement (sans --dev)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copier le reste du code source
COPY . .

# Exécuter les scripts post-install manuellement
RUN composer dump-autoload --optimize --no-dev

# ================================================================
# --- Stage 2 : Production Image ---
# ================================================================
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="Yass Digital Lab <diarrass1507@gmail.com>"
LABEL version="2.0.0"
LABEL description="Laravel 12 API Backend — Yass Digital Lab"

# --- Extensions PHP requises ---
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        nginx \
        supervisor \
        postgresql-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        libxml2-dev \
        curl \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        gd \
        zip \
        exif \
        bcmath \
        opcache \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear \
    && rm -rf /var/cache/apk/*

# --- Configuration PHP Optimisée pour Production ---
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/php-production.ini $PHP_INI_DIR/conf.d/99-yass.ini
COPY docker/php/opcache.ini $PHP_INI_DIR/conf.d/98-opcache.ini

# --- Configuration Nginx ---
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# --- Configuration Supervisor (Gestion PHP-FPM + Nginx) ---
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# --- Code source applicatif ---
WORKDIR /var/www/html

COPY --from=deps /app .

# --- Permissions Storage & Cache ---
RUN mkdir -p storage/logs \
             storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# --- Script d'entrée (migrations, config:cache, etc.) ---
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
