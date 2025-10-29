#!/bin/bash
set -e

echo "Starting Symfony application..."

cd /var/www

cat > /var/www/.env << EOF
DEFAULT_URI=
DATABASE_URL=postgresql://${DB_USER:-appuser}:${DB_PASSWORD:-apppass}@database:5432/${DB_NAME:-appdb}?serverVersion=17&charset=utf8
BITRIX24_PHP_SDK_APPLICATION_CLIENT_ID=${CLIENT_ID:-bitrix_application_client_id}
BITRIX24_PHP_SDK_APPLICATION_CLIENT_SECRET=${CLIENT_SECRET:-bitrix_application_client_secret}
BITRIX24_PHP_SDK_APPLICATION_SCOPE=${SCOPE:-bitrix_application_scope}
EOF

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

# Create log directories
mkdir -p /var/log/php/nginx
mkdir -p /var/log/php/phpfpm
mkdir -p /var/log/php/symfony

mkdir -p /var/www/var
chmod -R 755 /var/www
chmod -R 777 /var/www/var

chmod -R 777 /var/log/php/nginx
chmod -R 777 /var/log/php/phpfpm
chmod -R 777 /var/log/php/symfony


echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on 0.0.0.0:8000..."
echo "Document root: /var/www/public"

# Start nginx in foreground
exec nginx -g 'daemon off;'
