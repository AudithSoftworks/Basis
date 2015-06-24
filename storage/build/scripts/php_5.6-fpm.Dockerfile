FROM php:5.5-fpm
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

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

# Locale
RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8
