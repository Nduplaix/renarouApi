FROM php:7.3.12-fpm-alpine3.10

ENV APCU_VERSION 5.1.18
ENV XDEBUG_VERSION 2.9.0
ENV COMPOSER_VERSION 1.9.2

RUN apk add --update --no-cache \
        bash \
        ca-certificates \
        git \
        icu-libs \
        unzip \
        zip \
        libzip-dev && \
    apk add --update --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev && \
    pecl install apcu-${APCU_VERSION} xdebug-${XDEBUG_VERSION} && \
    docker-php-ext-install -j $(nproc) intl zip pdo_mysql && \
    docker-php-ext-enable apcu opcache xdebug && \
    apk del .build-deps && \
    apk add gosu --update --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing/ && \
    addgroup bar && \
    adduser -D -h /home -s /bin/bash -G bar foo

RUN curl -sS https://getcomposer.org/installer \
        | php -- --filename=composer --install-dir=/usr/local/bin --version=${COMPOSER_VERSION}

ADD entrypoint.sh /entrypoint
COPY conf/php.ini /usr/local/etc/php/php.ini
COPY conf/xdebug.ini /usr/local/etc/php/conf.d/debug.ini

WORKDIR /srv
ENTRYPOINT ["/entrypoint"]

