#!/bin/bash
set -e

echo "Starting Symfony application..."

cd /var/www

# Function to wait for database
wait_for_db() {
    echo "Waiting for PostgreSQL to be ready..."

    # Extract database connection details from DATABASE_URL
    DB_HOST=${DB_HOST:-database}
    DB_PORT=${DB_PORT:-5432}
    DB_USER=${DB_USER:-appuser}
    DB_NAME=${DB_NAME:-appdb}

    MAX_RETRIES=30
    RETRY_COUNT=0

    until PGPASSWORD=${DB_PASSWORD:-apppass} psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c '\q' 2>/dev/null; do
        RETRY_COUNT=$((RETRY_COUNT + 1))
        if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
            echo "ERROR: PostgreSQL is not available after $MAX_RETRIES attempts"
            exit 1
        fi
        echo "PostgreSQL is unavailable - attempt $RETRY_COUNT/$MAX_RETRIES - sleeping"
        sleep 2
    done

    echo "PostgreSQL is ready!"
}

if [ -f "composer.json" ]; then
    echo "Found composer.json, install dependencies..."
    composer install --optimize-autoloader
else
    echo "Warning: composer.json not found in /var/www"
fi

if [ ! -f "bin/console" ]; then
    echo "ERROR: Symfony console not found at /var/www/bin/console"
    exit 1
fi

echo "Symfony application ready"

# Wait for database before any DB operations
wait_for_db
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
