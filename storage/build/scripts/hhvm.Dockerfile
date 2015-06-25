FROM brunoric/hhvm
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

# Install dependencies
RUN apt-get update -y \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        fontforge \
        ruby \
        ruby-dev \
        git \
        curl \
        make \
        util-linux-locales \
        gcc-4.8-locales \
        ruby-locale

# Locale
RUN locale-gen en_US.UTF-8 && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LC_CTYPE en_US.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | hhvm --php -- --install-dir=/usr/bin --filename=composer

# Install Compass dependencies
RUN gem install compass sass sass-globbing autoprefixer-rails tomdoc fontcustom

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
