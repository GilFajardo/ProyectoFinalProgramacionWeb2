#!/bin/sh
# Se ejecuta en cada arranque del contenedor, después de las automatizaciones
# de Laravel de la imagen base (storage:link, migrate --force y optimize).
set -e

cd /var/www/html

# El volumen persistente de storage/app puede llegar vacío: recrea la estructura.
mkdir -p storage/app/public storage/app/private storage/fonts

# Catálogo obligatorio de categorías (el seeder es idempotente).
if [ "${SEED_CATEGORIAS:-true}" = "true" ]; then
    php artisan db:seed --class=CategoriaSeeder --force --no-interaction
fi

php artisan filament:optimize --no-interaction
