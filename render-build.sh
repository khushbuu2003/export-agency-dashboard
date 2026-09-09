#!/usr/bin/env bash
# exit on error
set -o errexit

echo "--- Installing Composer Dependencies ---"
composer install --no-dev --optimize-autoloader

echo "--- Preparing SQLite Database ---"
mkdir -p database
touch database/database.sqlite

echo "--- Running Database Migrations & Seeders ---"
php artisan migrate --force
php artisan db:seed --force

echo "--- Caching Configuration & Routes ---"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "--- Render Build Completed Successfully ---"
