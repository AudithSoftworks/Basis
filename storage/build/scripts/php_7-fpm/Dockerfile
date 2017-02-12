FROM php:7-fpm
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -y && apt-get install -y apt-utils && apt-get upgrade -y
RUN apt-get install -y \
        git \
        libcurl4-gnutls-dev \
        libfreetype6-dev \
        libjpeg-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libpq-dev \
        libsqlite3-dev \
        libxml2-dev \
        libxslt1-dev \
        locales \
        locales-all \
        telnet \
        wget
RUN docker-php-ext-install \
        bcmath \
        calendar \
        exif \
        gettext \
        mbstring \
        mcrypt \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        soap \
        sockets \
        xsl \
        zip

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

# Install PECL php-xdebug and php-mongodb extensions
RUN pecl install -of xdebug mongodb \
    && rm -rf /tmp/pear \
    && echo "zend_extension=xdebug.so" | tee /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 1000" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey = basisPhpFpm" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable = 1" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port = 54321" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back = 1" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongo.ini

# Install php-redis extension through Git
RUN git clone https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && git checkout php7 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && rm -rf /tmp/phpredis \
    && echo "extension=redis.so" | tee /usr/local/etc/php/conf.d/redis.ini

# Timezone
RUN echo "date.timezone = UTC" | tee /usr/local/etc/php/conf.d/date.ini

# Locale
RUN locale-gen en_US.UTF-8 && locale-gen az_AZ.UTF-8 && locale-gen tr_TR.UTF-8 && dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# Clean-up
RUN apt-get clean \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 54321
