FROM php:5.6-fpm
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -y \
    && apt-get install -y apt-utils \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libxml2-dev \
        libcurl4-gnutls-dev \
        libpq-dev \
        libsqlite3-dev \
        libxslt1-dev \
        locales \
        locales-all \
        wget \
        telnet \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        gettext \
        mbstring \
        mcrypt \
        mysql \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        soap \
        sockets \
        xsl \
        zip

RUN pecl install -o -f redis xdebug \
    && rm -rf /tmp/pear \
    && echo "extension=redis.so" | tee /usr/local/etc/php/conf.d/redis.ini \
    && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" | tee /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 1000" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo no | pecl install -o -f mongo \
    && echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

# Locale
RUN locale-gen en_US.UTF-8 && dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8
