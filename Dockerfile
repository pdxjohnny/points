FROM php:5.6-apache

RUN docker-php-ext-install -j$(nproc) pdo_mysql

COPY src/ /var/www/html/
