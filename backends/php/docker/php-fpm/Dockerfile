FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    zip \
    libssl-dev \
    libicu-dev \
    libpq-dev \
    postgresql-client \
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

# Copy nginx config
COPY docker/php-fpm/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default && \
    rm -f /etc/nginx/sites-enabled/default.conf

# Copy PHP-FPM config
COPY docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.d/zzz-custom.conf

# Copy entrypoint script
COPY docker/php-fpm/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
