#!/usr/bin/env bash

# docker build -f storage/build/scripts/nginx/Dockerfile -t audithsoftworks/basis:nginx .
# docker build -f storage/build/scripts/php_5.6-fpm/Dockerfile -t audithsoftworks/basis:php_5.6-fpm .;

# docker build -f storage/build/scripts/php_5.6/Dockerfile -t audithsoftworks/basis:php_5.6 .;
# docker build -f storage/build/scripts/hhvm/Dockerfile -t audithsoftworks/basis:hhvm .;


docker-compose -f docker-compose-php56.yml pull;
docker-compose -f docker-compose-php56.yml up -d;
docker-compose -f docker-compose-php56.yml ps;
docker exec basis_php56_1 /bin/bash -c "echo $(docker inspect -f '{{ .NetworkSettings.IPAddress }}' basis_nginxForPhpFpm56_1) basis.audith.org | tee -a /etc/hosts";

sleep 5;
mysql -h $(docker inspect -f '{{ .NetworkSettings.IPAddress }}' basis_mysql56_1) -u root -e "CREATE DATABASE IF NOT EXISTS basis;";
# psql -h $(docker inspect -f '{{ .NetworkSettings.IPAddress }}' basis_postgres94_1) -U postgres -c "CREATE DATABASE basis;";

cat .env.example | sed s/DB_HOST=.*/DB_HOST=mysql56/g | sed s/DB_USERNAME=.*/DB=mysql/g | sed s/DB_PASSWORD=.*//g | tee .env;
docker exec basis_php56_1 /bin/bash -c "
    cd /home/basis && npm update && bower --config.interactive=false --allow-root update;

    cd /home/basis/public/bower_components/fine-uploader && npm install && grunt package;

    cd /home/basis && git clone --depth=1 --branch=1.14.0 https://github.com/jzaefferer/jquery-validation.git /home/basis/public/bower_components/jquery.validation;
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
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/simple-line-icons/stylesheet.css public/fonts/simple-line-icons/*.ttf;
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/opensans/stylesheet.css public/fonts/opensans/*.ttf;
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/montserrat/stylesheet.css public/fonts/montserrat/*.ttf;
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/pontano_sans/stylesheet.css public/fonts/pontano_sans/*.ttf;
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/armata/stylesheet.css public/fonts/armata/*.ttf;
    PATH=$PATH:/home/basis/storage/build/tools/sfnt2woff:/home/basis/storage/build/tools/woff2 ./storage/build/tools/css3_font_converter/convertFonts.sh --use-font-weight --output=public/fonts/marcellus/stylesheet.css public/fonts/marcellus/*.ttf;

    compass compile;
    gulp;
    composer selfupdate && composer update --prefer-source --no-interaction;
";
