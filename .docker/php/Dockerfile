ARG MODE="prod"
FROM php:7.4-fpm AS prod

RUN apt update \
    && apt install \
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
        libzip-dev \
        unzip \
        zip \
        zlib1g-dev \
    && docker-php-ext-configure \
        gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-configure \
        intl \
    && docker-php-ext-configure \
        pcntl \
        --enable-pcntl \
    && docker-php-ext-install \
        gd \
        intl \
        mysqli \
        opcache \
        pdo \
        pdo_mysql \
        pcntl \
        posix \
        soap \
        xmlrpc \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

#FROM composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/

WORKDIR "/var/www/html/"

FROM prod AS dev

RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

FROM ${MODE} AS final
