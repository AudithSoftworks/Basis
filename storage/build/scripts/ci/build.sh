#!/usr/bin/env bash

test -f .env || sed \
    -e "s/DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/g" \
    -e "s/DB_HOST=.*/DB_HOST=${DB_HOST}/g" \
    -e "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/g" \
    -e "s/SAUCE_USERNAME=.*/SAUCE_USERNAME=${SAUCE_USERNAME}/g" \
    -e "s/SAUCE_ACCESS_KEY=.*/SAUCE_ACCESS_KEY=${SAUCE_ACCESS_KEY}/g" .env.example \
    | tee .env > /dev/null 2>&1;

docker-compose exec --privileged dev-env /bin/bash -c "
    export NPM_CONFIG_LOGLEVEL=warn;
    export SAUCE_BUILD=audithsoftworks-basis-travis-job-${TRAVIS_JOB_NUMBER};
    export SAUCE_USERNAME=${SAUCE_USERNAME};
    export SAUCE_ACCESS_KEY=${SAUCE_ACCESS_KEY};

    sudo mkdir -p ~;
    sudo chown -R basis:basis ~;

    daemon -U --respawn -- phantomjs --webdriver=25852 --webdriver-logfile=\$WORKDIR/storage/logs/phantomjs.log --webdriver-loglevel=DEBUG;
    if [[ ${DB_CONNECTION} == 'mysql' ]]; then
        wget -P ./storage/build/tools https://saucelabs.com/downloads/sc-4.4.9-linux.tar.gz;
        tar -C ./storage/build/tools -xzf ./storage/build/tools/sc-4.4.9-linux.tar.gz;
        rm ./storage/build/tools/sc-4.4.9-linux.tar.gz;

        daemon -U --respawn -- /var/www/storage/build/tools/sc-4.4.9-linux/bin/sc --tunnel-domains=basis.audith.org;
    fi;

    crontab -l;
    npm update;

    cd \$WORKDIR;
    if [[ ! -d ./storage/build/tools/woff2 ]]; then
        git clone --depth=1 https://github.com/google/woff2.git ./storage/build/tools/woff2;
        cd /var/www/storage/build/tools/woff2 && git submodule init && git submodule update && make clean all;
    fi;

    cd \$WORKDIR;
    if [[ ! -d ./storage/build/tools/css3_font_converter ]]; then
        git clone --depth=1 https://github.com/zoltan-dulac/css3FontConverter.git ./storage/build/tools/css3_font_converter;
    fi;

    cd \$WORKDIR;
    if [[ -d ./node_modules/.google-fonts ]]; then
        cd ./node_modules/.google-fonts && git pull origin master;
    else
        git clone --depth=1 https://github.com/google/fonts.git ./node_modules/.google-fonts;
        rm -rf ./node_modules/.google-fonts/.git
    fi;

    cd \$WORKDIR;
    if [[ ! -d ./public/fonts/glyphicons ]]; then cp -r ./node_modules/bootstrap-sass/assets/fonts/bootstrap ./public/fonts/glyphicons; fi;
    if [[ ! -d ./public/fonts/font_awesome ]]; then cp -r ./node_modules/font-awesome/fonts ./public/fonts/font_awesome; fi;
    if [[ ! -d ./public/fonts/simple-line-icons ]]; then cp -r ./node_modules/simple-line-icons-webfont/fonts ./public/fonts/simple-line-icons; fi;
    if [[ ! -d ./public/fonts/opensans ]]; then cp -r ./node_modules/.google-fonts/apache/opensans ./public/fonts/opensans; fi;
    if [[ ! -d ./public/fonts/armata ]]; then cp -r ./node_modules/.google-fonts/ofl/armata ./public/fonts/armata; fi;
    if [[ ! -d ./public/fonts/marcellus ]]; then cp -r ./node_modules/.google-fonts/ofl/marcellus ./public/fonts/marcellus; fi;
    if [[ ! -d ./public/fonts/pontano_sans ]]; then cp -r ./node_modules/.google-fonts/ofl/pontanosans ./public/fonts/pontano_sans; fi;
    if [[ ! -d ./public/fonts/montserrat ]]; then cp -r ./node_modules/.google-fonts/ofl/montserrat ./public/fonts/montserrat; fi;

    chmod -R +x /var/www/storage/build/tools;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/simple-line-icons/stylesheet.css public/fonts/simple-line-icons/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/opensans/stylesheet.css public/fonts/opensans/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/montserrat/stylesheet.css public/fonts/montserrat/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/pontano_sans/stylesheet.css public/fonts/pontano_sans/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/armata/stylesheet.css public/fonts/armata/*.ttf;
    ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/marcellus/stylesheet.css public/fonts/marcellus/*.ttf;

    npm run build;
    composer update --prefer-source --no-interaction;

    ./artisan key:generate;
    ./artisan migrate;
    ./artisan passport:install;

    sudo chown -R 1000:1000 ./;
    sudo touch ./storage/logs/laravel.log;
    sudo chown -R www-data:www-data ./storage/framework/views ./storage/logs;
    sudo chmod -R 0777 ./storage/framework/views ./storage/logs;

    ./vendor/bin/phpunit --debug --verbose --testsuite='Unit';

    ls -l ./storage/framework/views/twig ./storage/logs;
    sudo chmod -R 0777 ./storage/framework/views ./storage/logs;

    ./artisan dusk -vvv;

    ls -l ./storage/framework/views/twig ./storage/logs;
    sudo chmod -R 0777 ./storage/framework/views ./storage/logs;

    ./vendor/bin/phpcov merge ./storage/coverage --clover ./storage/coverage/coverage-clover-merged.xml

    ls -l ./storage/framework/views/twig ./storage/logs;
    sudo chmod -R 0777 ./storage/framework/views ./storage/logs;

    ./vendor/bin/phpunit --debug --verbose --no-coverage --testsuite='SauceWebDriver';

    ls -l ./storage/framework/views/twig ./storage/logs;
    sudo chmod -R 0777 ./storage/framework/views ./storage/logs;
";
