FROM composer:lts AS deps

WORKDIR /root

COPY composer.json composer.lock ./
COPY public public
COPY src src

RUN composer install

FROM php:apache

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=deps /root/. /var/www
