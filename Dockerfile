FROM php:7.4-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/html
