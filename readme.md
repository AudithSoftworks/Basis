## Audith Basis on Laravel 5.3

[![Build Status](https://travis-ci.org/AudithSoftworks/Basis.svg?branch=master)](https://travis-ci.org/AudithSoftworks/Basis)
[![Latest Stable Version](https://poser.pugx.org/audithsoftworks/basis/v/stable.svg)](https://packagist.org/packages/audithsoftworks/basis)
[![License](https://poser.pugx.org/audithsoftworks/basis/license.svg)](https://packagist.org/packages/audithsoftworks/basis)
[![Dependency Status](https://www.versioneye.com/user/projects/57c57d33939fc600471d4c2b/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57c57d33939fc600471d4c2b)

[![](https://images.microbadger.com/badges/version/audithsoftworks/basis.svg)](https://microbadger.com/images/audithsoftworks/basis "Docker Hub public images")
[![](https://images.microbadger.com/badges/image/audithsoftworks/basis.svg)](https://microbadger.com/images/audithsoftworks/basis "Docker Hub public images layers")

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/22803477-ebe7-4906-a57c-f53bfae62ba3/mini.png)](https://insight.sensiolabs.com/projects/22803477-ebe7-4906-a57c-f53bfae62ba3)
[![Code Quality](https://scrutinizer-ci.com/g/AudithSoftworks/Basis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AudithSoftworks/Basis)
[![Coverage](https://scrutinizer-ci.com/g/AudithSoftworks/Basis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AudithSoftworks/Basis)

Audith Basis is an enhanced version of Laravel framework, a feature list for which is provided below.

### Features

* [Back-end] _Completely localized routes_ - access your endpoints and URLs in your own language, with Unicode support.
* [Front-end/UI] _Back-end control panel_ - comes with built-in control panel, featuring sample pages for you to expand upon.
* [Front-end/UI] _Custom web-fonts, web typography support_ - build and use your own web-fonts with included toolset, from any TTF/OTF fontsets.
* [Front-end/UI] _Webpack support_ - build all web assets with Webpack.
* [DevOps/CI/CD] _Docker support_ - fully Dockerized package with pre-built PHP 5.6 and 7.0 containers.

### Installation

#### Setting up your Developer Environment

I have included a build script in ```./storage/scripts/dev-env/build.sh``` inside of which you can see steps necessary to spin up desired Docker configuration and prepare your development environment. Steps involved are:

1. Build or pull necessary Docker containers.
2. Start your desired Docker-Compose configuration (any of: PHP 5.6 or 7.0).
3. Update your ```/etc/hosts``` file to point to the primary container in your Docker configuration - generally ```php_XXX``` is the primary container, which is linked to ```php_XXX-fpm``` and other machines.
4. Create ```.env``` file, containing your environmental variables.
5. Switch into the primary container environment, to start building your environment (Note: before doing so, please read the important note in ```build.sh``` file!):
    1. Install Sauce Connect and start it as a daemon.
    2. Install NPM dependencies.
    3. Install ```woff-2``` and it's submodules; and build them (used to build custom web-fonts).
    4. Install ```css3-font-converter``` package and build it (used to build custom web-fonts).
    5. Clone/update ```google-fonts``` to local storage, copy required font files and build your web-fonts.
    6. Run Webpack to build web assets.
    7. Install Composer dependencies.
    8. Using Laravel Artisan, generate an encryption key and run migrations, install Laravel Passport keys.
    9. Since Docker runs with root privileges, ```chown``` all newly created files to your host machine UUID:GUID (assuming it is 1000:1000, modify if necessary).
    10. And finally, run all the tests.
    
Additionally, I've included few commands to shut down Docker-Compose configuration and cleanup your host machine from unnecessary Docker assets.
