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

# Копируем код API и общие файлы
COPY ../api ./api
COPY ../shared ./shared

WORKDIR /var/www/api

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
