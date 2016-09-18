#!/usr/bin/env bash

test -f .env || sed \
    -e "s/DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/g" \
    -e "s/DB_HOST=.*/DB_HOST=${DB_HOST}/g" \
    -e "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/g" \
    -e "s/SAUCE_USERNAME=.*/SAUCE_USERNAME=${SAUCE_USERNAME}/g" \
    -e "s/SAUCE_ACCESS_KEY=.*/SAUCE_ACCESS_KEY=${SAUCE_ACCESS_KEY}/g" .env.example \
    | tee .env > /dev/null 2>&1;

docker exec basis_php${PHP_VERSION}-cli_1 /bin/bash -c "
    export NPM_CONFIG_LOGLEVEL=warn;
    export SAUCE_BUILD=travis-job-${TRAVIS_JOB_NUMBER};
    export SAUCE_USERNAME=${SAUCE_USERNAME};
    export SAUCE_ACCESS_KEY=${SAUCE_ACCESS_KEY};

    if [[ ${PHP_VERSION} == 7 && ${DB_CONNECTION} == 'mysql' ]]; then
        wget -P ./storage/build/tools https://saucelabs.com/downloads/sc-4.4.0-linux.tar.gz;
        tar -C ./storage/build/tools -xzf ./storage/build/tools/sc-4.4.0-linux.tar.gz;
        rm ./storage/build/tools/sc-4.4.0-linux.tar.gz;

        daemon -U -- /home/basis/storage/build/tools/sc-4.4.0-linux/bin/sc --tunnel-domains=basis.audith.org;
    fi;

    cd /home/basis && npm update && bower --config.interactive=false --allow-root --loglevel=warn update;

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

    ./vendor/bin/phpunit --debug --verbose --testsuite='Illuminate TestCases';
    if [[ ${PHP_VERSION} == 7 && ${DB_CONNECTION} == 'mysql' ]]; then ./vendor/bin/phpunit --debug --verbose --no-coverage --testsuite='SauceWebDriver TestCases'; fi;
";
