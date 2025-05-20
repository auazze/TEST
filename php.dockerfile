FROM php:8.3-fpm-alpine

RUN apk add --no-cache zip git

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction

COPY . .
