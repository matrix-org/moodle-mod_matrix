FROM php:7.2.33-fpm

RUN apt-get update \
    && apt-get install \
        --allow-downgrades \
        --allow-change-held-packages \
        --allow-remove-essential \
        --no-install-recommends \
        --yes \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libpng-dev \
        libxml2-dev \
        unzip \
        zip \
        zlib1g-dev \
    && docker-php-ext-configure \
        intl \
    && docker-php-ext-configure \
        gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
        gd \
        intl \
        mysqli \
        opcache \
        pdo \
        pdo_mysql \
        soap \
        xmlrpc \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/

WORKDIR "/var/www/html/"
