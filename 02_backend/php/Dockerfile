FROM php:8.2-fpm

# Установка расширений и утилит
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Установка зависимостей, если есть composer.json
RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader; fi

EXPOSE 3000

# Для development - встроенный сервер
CMD ["php", "-S", "0.0.0.0:3000", "-t", "public"]
