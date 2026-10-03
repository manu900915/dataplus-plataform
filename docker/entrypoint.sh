#!/bin/sh
set -e

# Crear directorios necesarios y ajustar permisos
mkdir -p /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Si se pasa un comando (ej: php artisan migrate), ejecutarlo; si no, iniciar php-fpm
if [ $# -gt 0 ]; then
    exec "$@"
else
    exec php-fpm
fi
