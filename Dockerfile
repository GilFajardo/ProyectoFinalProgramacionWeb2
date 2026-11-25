# syntax=docker/dockerfile:1

############################################
# Base: PHP 8.4 + Nginx + PHP-FPM (serversideup)
############################################
FROM serversideup/php:8.4-fpm-nginx AS base

USER root
# Extensiones extra que pide el proyecto (Filament: intl; dompdf: gd).
RUN install-php-extensions intl gd bcmath exif
USER www-data

############################################
# Dependencias de PHP (sin paquetes de desarrollo)
############################################
FROM base AS vendor

WORKDIR /var/www/html

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY --chown=www-data:www-data . .

# Genera el autoload optimizado y ejecuta package:discover + filament:upgrade
# (publica los assets de Filament en public/css, public/js y public/fonts).
RUN mkdir -p storage/app/public storage/app/private storage/fonts storage/logs \
        storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-interaction

############################################
# Assets de frontend (Vite + Tailwind)
############################################
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Tailwind escanea vistas de vendor (paginación), por eso se copia el proyecto con vendor.
COPY --from=vendor /var/www/html ./
RUN npm run build

############################################
# Imagen final
############################################
FROM base

ENV PHP_OPCACHE_ENABLE=1 \
    PHP_MEMORY_LIMIT=256M \
    PHP_UPLOAD_MAX_FILE_SIZE=10M \
    PHP_POST_MAX_SIZE=12M \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true \
    SSL_MODE=off

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build
COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/

EXPOSE 8080
