FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    librabbitmq-dev \
    libssl-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
      zip \
      intl \
      bcmath \
      sockets \
      pdo_pgsql \
      pgsql \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /var/www

# Копируем composer.json из корня PHP бэкенда
COPY ../composer.json ../composer.lock* ./

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Копируем код Worker и общие файлы
COPY ../worker ./worker
COPY ../shared ./shared

WORKDIR /var/www/worker

CMD ["php", "worker.php"]
