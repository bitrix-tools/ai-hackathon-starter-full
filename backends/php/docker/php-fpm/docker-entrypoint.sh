#!/bin/bash
set -e

echo "Starting Symfony application..."

cd /var/www

if [ ! -f /var/www/.env ] then
    cat >> /var/www/.env << EOF
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
###< symfony/framework-bundle ###

###> symfony/routing ###
# Configure how to generate URLs in non-HTTP contexts, such as CLI commands.
# See https://symfony.com/doc/current/routing.html#generating-urls-in-commands
DEFAULT_URI=
###< symfony/routing ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1|.*\.cloudpub\.ru)(:[0-9]+)?$'
###< nelmio/cors-bundle ###

DATABASE_URL=postgresql://${DB_USER:-appuser}:${DB_PASSWORD:-apppass}@database:5432/${DB_NAME:-appdb}?serverVersion=17&charset=utf8
BITRIX24_PHP_SDK_APPLICATION_CLIENT_ID=${CLIENT_ID:-bitrix_application_client_id}
BITRIX24_PHP_SDK_APPLICATION_CLIENT_SECRET=${CLIENT_SECRET:-bitrix_application_client_secret}
BITRIX24_PHP_SDK_APPLICATION_SCOPE=${SCOPE:-bitrix_application_scope}
EOF
fi

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
