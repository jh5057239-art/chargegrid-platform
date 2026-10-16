#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch "${DB_DATABASE:-database/database.sqlite}"
chown -R www-data:www-data storage bootstrap/cache database
chmod -R ug+rwx storage bootstrap/cache database

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not configured. Set it in the hosting environment."
    exit 1
fi

php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground
