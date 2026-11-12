#!/usr/bin/env bash
set -e

mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database 2>/dev/null || true

echo "==> Running migrations & seeders..."
php artisan migrate --force --seed || true

echo "==> Caching application configurations..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting Reliable Electrics CRM server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
