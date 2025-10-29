FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libssl-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
      zip \
      intl \
      bcmath \
      sockets \
      pdo_pgsql \
      pgsql

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /var/www

CMD ["php", "-a"]
