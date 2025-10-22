#!/bin/bash

echo "Building Vue assets for Newsfeed..."

# Stop any running npm watch process
echo "Stopping any running npm watch process..."
pkill -f "npm run watch" || true

# Clear the view cache
echo "Clearing Laravel view cache..."
php artisan view:clear

# Clear the compiled assets
echo "Clearing compiled assets..."
php artisan config:clear
php artisan route:clear

# Remove the old compiled JS
echo "Removing old compiled JS..."
rm -rf public/js/app*.js
rm -rf public/js/manifest*.json

# Build the new assets
echo "Building new assets..."
npm run build

# If build fails, try dev
if [ $? -ne 0 ]; then
    echo "Build failed, trying dev mode..."
    npm run dev
fi

echo "Build complete!"
echo "Please restart your Laravel server to see the changes."