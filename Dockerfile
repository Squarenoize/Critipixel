FROM composer:2 AS composer

FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        intl \
        mysqli \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/critipixel.ini

WORKDIR /app

