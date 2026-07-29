#!/usr/bin/env bash

set -e

echo "🚀 Starting Automated Production Deployment for KanbanFlow..."

# 1. Pull Latest Code
git pull origin main

# 2. Install Production Composer Dependencies
composer install --no-dev --optimize-autoloader

# 3. Install NPM Dependencies & Build Vite Assets
npm ci
npm run build

# 4. Run Database Migrations
php artisan migrate --force

# 5. Clear & Cache Routes, Views, Configs
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 6. Restart Queue & Reload PHP-FPM
php artisan queue:restart

echo "✅ Production Deployment Completed Successfully!"
