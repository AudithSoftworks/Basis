FROM ubuntu:wily
MAINTAINER Shahriyar Imanov <shehi@imanov.me>

WORKDIR /home/basis

ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt-get update -y \
    && apt-get install -y \
        apt-utils \
        wget
RUN wget -O - http://dl.hhvm.com/conf/hhvm.gpg.key | apt-key add -
RUN echo deb http://dl.hhvm.com/ubuntu wily main | tee /etc/apt/sources.list.d/hhvm.list
RUN apt-get update -y \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        autoconf \
        automake \
        build-essential \
        bison \
        curl \
        fontforge \
        gcc-4.8-locales \
        git \
        hhvm \
        libc6-dev \
        libgmp-dev \
        libmemcached-dev \
        libreadline6 \
        libreadline6-dev \
        libssl-dev \
        libtool \
        libxml2-dev \
        libyaml-dev \
        make \
        ncurses-dev \
        openjdk-7-jre \
        openssl \
        ruby \
        ruby-dev \
        ruby-locale \
        supervisor \
        util-linux-locales \
        zlib1g \
        zlib1g-dev \
    && apt-get clean \
    && apt-get autoremove -y

# Scripts
ADD storage/build/configs/supervisor-config/ /etc/supervisor/conf.d/
ADD storage/build/scripts/hhvm/ /scripts/
RUN chmod 755 /scripts/*.sh

# Exposing HHVM-FastCGI port
EXPOSE 9000

# Locale
RUN locale-gen en_US.UTF-8 && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales
ENV LANG en_US.UTF-8
ENV LC_CTYPE en_US.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | hhvm --php -- --install-dir=/usr/bin --filename=composer

# Install Compass dependencies
RUN gem install compass sass sass-globbing autoprefixer-rails tomdoc fontcustom

# Install Node
RUN set -ex \
  && for key in \
    9554F04D7259F04124DE6B476D5A82AC7E37093B \
    94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
    0034A06D9D9B0064CE8ADF6BF1747F4AD2306D93 \
    FD3A5288F042B6850C66B31F09FE44734EB7990E \
    71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
    DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
  ; do \
    gpg --keyserver ha.pool.sks-keyservers.net --recv-keys "$key"; \
  done
ENV NPM_CONFIG_LOGLEVEL info
ENV NODE_VERSION 5.2.0
ENV NPM_VERSION 3.5.1
RUN curl -SLO "http://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.gz" \
    && curl -SLO "http://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --verify SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION-linux-x64.tar.gz\$" SHASUMS256.txt.asc | sha256sum -c - \
    && tar -xzf "node-v$NODE_VERSION-linux-x64.tar.gz" -C /usr/local --strip-components=1 \
    && rm "node-v$NODE_VERSION-linux-x64.tar.gz" SHASUMS256.txt.asc \
    && npm install -g npm@"$NPM_VERSION" \
    && npm cache clear

# Install Node dependencies
RUN npm install -g bower gulp grunt-cli ttf2eot

ENV PATH $PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2

# Default command
CMD /scripts/start.sh
