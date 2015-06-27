FROM php:5.5
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

WORKDIR /home/basis

# Install dependencies
RUN apt-get update -y \
    && apt-get install -y apt-utils \
    && apt-get install -y \
        git \
        fontforge \
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
        openjdk-7-jre \
        ruby \
        ruby-dev \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        gettext \
        mbstring \
        mcrypt \
        mysql \
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
    && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20121212/xdebug.so" | tee /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 1000" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo no | pecl install -o -f mongo \
    && echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

# Locale
RUN locale-gen en_US.UTF-8 && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Install Compass dependencies
RUN gem install compass sass sass-globbing autoprefixer-rails fontcustom

# Install Node
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 7937DFD2AB06298B2293C3187D33FF9D0246406D 114F43EE0176B71C7BC219DD50A3051F888C628D
ENV NODE_VERSION 0.12.4
ENV NPM_VERSION 2.11.1
RUN curl -SLO "http://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.gz" \
    && curl -SLO "http://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --verify SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION-linux-x64.tar.gz\$" SHASUMS256.txt.asc | sha256sum -c - \
    && tar -xzf "node-v$NODE_VERSION-linux-x64.tar.gz" -C /usr/local --strip-components=1 \
    && rm "node-v$NODE_VERSION-linux-x64.tar.gz" SHASUMS256.txt.asc \
    && npm install -g npm@"$NPM_VERSION" \
    && npm cache clear

# Install Node dependencies
RUN npm install -g bower gulp ttf2eot

ENV PATH $PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2
