#!/usr/bin/env bash

#docker build -f storage/build/scripts/nginx/Dockerfile -t audithsoftworks/basis:nginx .
#docker build -f storage/build/scripts/php_5.6/Dockerfile -t audithsoftworks/basis:php_5.6 .;
#docker build -f storage/build/scripts/php_5.6-fpm/Dockerfile -t audithsoftworks/basis:php_5.6-fpm .;
#docker build -f storage/build/scripts/php_7/Dockerfile -t audithsoftworks/basis:php_7 .;
#docker build -f storage/build/scripts/php_7-fpm/Dockerfile -t audithsoftworks/basis:php_7-fpm .;

#docker-compose build

if [ -z ${PHP_VERSION+x} ]; then export PHP_VERSION='5'; fi; # 5|7

#docker-compose pull;

docker-compose up -d php${PHP_VERSION}-cli;
docker-compose ps;
docker exec basis_php${PHP_VERSION}-cli_1 \
    /bin/bash -c "echo $(docker inspect -f '{{ .NetworkSettings.Networks.basis_default.IPAddress }}' basis_nginxForPhp${PHP_VERSION}_1) basis.audith.org | tee -a /etc/hosts";

test -f .env || cat .env.example | tee .env > /dev/null 2>&1;

###############################################################################################################
# Before running the next command, make sure you have also exported SAUCE_USERNAME and SAUCE_ACCESS_KEY
# env variables to the environment for which the next 'docker exec' is being run.
###############################################################################################################

docker exec basis_php${PHP_VERSION}-cli_1 /bin/bash -c "
    wget -P ./storage/build/tools https://saucelabs.com/downloads/sc-4.4.0-linux.tar.gz;
    tar -C ./storage/build/tools -xzf ./storage/build/tools/sc-4.4.0-linux.tar.gz;
    rm ./storage/build/tools/sc-4.4.0-linux.tar.gz;

    daemon -U -- /home/basis/storage/build/tools/sc-4.4.0-linux/bin/sc --tunnel-domains=basis.audith.org;

    cd /home/basis && npm update && bower --config.interactive=false --allow-root update;

    cd /home/basis/public/bower_components/fine-uploader && npm install && make build;

    cd /home/basis && git clone --depth=1 --branch=1.15.0 https://github.com/jzaefferer/jquery-validation.git /home/basis/public/bower_components/jquery.validation;
    cd /home/basis/public/bower_components/jquery.validation && rm -rf .git && npm install && grunt;

    cd /home/basis && git clone --depth=1 https://github.com/google/woff2.git /home/basis/storage/build/tools/woff2;
    cd /home/basis/storage/build/tools/woff2 && git submodule init && git submodule update && make clean all;

    cd /home/basis && git clone --depth=1 https://github.com/zoltan-dulac/css3FontConverter.git /home/basis/storage/build/tools/css3_font_converter;

    cp -r ./public/bower_components/bootstrap/fonts ./public/fonts/glyphicons;
    cp -r ./public/bower_components/fontawesome/fonts ./public/fonts/font_awesome;
    cp -r ./public/bower_components/simple-line-icons-webfont/fonts ./public/fonts/simple-line-icons;
    cp -r ./public/bower_components/google-fonts/apache/opensans ./public/fonts/opensans;
    cp -r ./public/bower_components/google-fonts/ofl/armata ./public/fonts/armata;
    cp -r ./public/bower_components/google-fonts/ofl/marcellus ./public/fonts/marcellus;
    cp -r ./public/bower_components/google-fonts/ofl/pontanosans ./public/fonts/pontano_sans;
    cp -r ./public/bower_components/google-fonts/ofl/montserrat ./public/fonts/montserrat;

    chmod -R +x /home/basis/storage/build/tools;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/simple-line-icons/stylesheet.css public/fonts/simple-line-icons/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/opensans/stylesheet.css public/fonts/opensans/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/montserrat/stylesheet.css public/fonts/montserrat/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/pontano_sans/stylesheet.css public/fonts/pontano_sans/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/armata/stylesheet.css public/fonts/armata/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/marcellus/stylesheet.css public/fonts/marcellus/*.ttf;

    compass compile;
    gulp;
    composer selfupdate && composer update --prefer-source --no-interaction;

    ./artisan key:generate;
    ./artisan migrate;
    ./artisan passport:install;

    chown -R 1000:1000 ./;

    ./vendor/bin/phpunit --debug --verbose;
";

#docker-compose down;
#docker rm $(docker ps -a | grep "Exited" | awk "{print \$1}");
#docker rmi $(docker images | grep "<none>" | awk "{print \$3}");
