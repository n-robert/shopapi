FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y wget git unzip libpq-dev libicu-dev libpng-dev libzip-dev libjpeg-dev libfreetype6-dev\
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install pgsql \
    && docker-php-ext-enable pgsql

ADD ./php.ini /usr/local/etc/php/php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /var/www
