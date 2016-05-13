FROM php:5.6-apache

COPY composer.json /var/www/

RUN docker-php-ext-install -j$(nproc) pdo_mysql && \
    cd /var/www && \
    apt-get update && \
    apt-get -y install git && \
    curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install && \
    apt-get -y purge git && \
    apt-get -y autoremove && \
    rm composer.phar

COPY src /var/www/html/
COPY lib /var/www/lib/
