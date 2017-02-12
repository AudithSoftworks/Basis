FROM php:5.6
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

WORKDIR /home/basis

ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt-get update -y \
    && apt-get install -y apt-utils \
    && apt-get upgrade -y \
    && apt-get install -y \
        bzip2 \
        cron \
        daemon \
        fontforge \
        git \
        libfreetype6-dev \
        libjpeg-dev \
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
        nano \
        openjdk-7-jre \
        sudo \
        telnet \
        unzip \
        wget
RUN docker-php-ext-install \
        bcmath \
        calendar \
        exif \
        gettext \
        mbstring \
        mcrypt \
        pdo_mysql \
        pdo_pgsql \
        soap \
        sockets \
        xsl \
        zip

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

# Locale
RUN locale-gen en_US.UTF-8 && locale-gen az_AZ.UTF-8 && locale-gen tr_TR.UTF-8 && dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Install php-redis php-mongo php-xdebug
RUN git clone https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo "extension=redis.so" | tee /usr/local/etc/php/conf.d/redis.ini

RUN rm -rf /tmp/pear \
    && echo no | pecl install -o -f mongo \
    && echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

RUN pecl install -of xdebug \
    && rm -rf /tmp/pear \
    && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" | tee /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 1000" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey = basisPhp" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable = 1" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port = 54321" | tee -a /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back = 1" | tee -a /usr/local/etc/php/conf.d/xdebug.ini

# Port for XDebug
EXPOSE 54321

# Timezone
RUN echo "date.timezone = UTC" | tee /usr/local/etc/php/conf.d/date.ini

# Install Node
RUN set -ex \
    && for key in \
        9554F04D7259F04124DE6B476D5A82AC7E37093B \
        94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
        0034A06D9D9B0064CE8ADF6BF1747F4AD2306D93 \
        FD3A5288F042B6850C66B31F09FE44734EB7990E \
        71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
        DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
        B9AE9905FFD7803F25714661B63B535A4C206CA9 \
        C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
    ; do \
        gpg --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys "$key"; \
    done

ENV NPM_CONFIG_LOGLEVEL info
ENV NODE_VERSION 6.9.5

RUN curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz" \
    && curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION-linux-x64.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
    && apt-get install xz-utils \
    && tar -xJf "node-v$NODE_VERSION-linux-x64.tar.xz" -C /usr/local --strip-components=1 \
    && rm "node-v$NODE_VERSION-linux-x64.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
    && ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    && npm install -g npm@latest \
    && npm cache clear \
    && rm -rf /tmp/npm-*

# Install Node global dependencies
RUN npm install -g ttf2eot jshint

# Port for node --inspect
EXPOSE 9229

# Clean-up
RUN apt-get clean \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1000
ARG PGID=1000
RUN groupadd -g $PGID basis && useradd -u $PUID -g basis -M basis
RUN usermod -aG sudo basis
RUN sed -i -e "s/%sudo.*/%sudo ALL=(ALL:ALL) NOPASSWD: ALL/g" /etc/sudoers

# Add Web-font related tools to PATH
USER basis
ENV PATH $PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2
