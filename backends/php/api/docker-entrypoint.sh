#!/bin/bash
set -e

echo "Starting Symfony application..."

cd /var/www

if [ -f "composer.json" ]; then
    echo "Found composer.json, checking dependencies..."

    if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
        echo "Installing composer dependencies..."
        composer install --optimize-autoloader
    else
        echo "Vendor directory exists, skipping composer install"
        echo "To reinstall dependencies, delete the vendor directory and restart the container"
    fi
else
    echo "Warning: composer.json not found in /var/www"
fi

if [ ! -f "bin/console" ]; then
    echo "ERROR: Symfony console not found at /var/www/bin/console"
    exit 1
fi

echo "Symfony application ready"
echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on 0.0.0.0:8000..."
echo "Document root: /var/www/public"

# Start nginx in foreground
exec nginx -g 'daemon off;'
