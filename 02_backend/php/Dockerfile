FROM php:8.2-fpm

# Установка расширений
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY . .

EXPOSE 3000

CMD ["php", "-S", "0.0.0.0:3000"]
