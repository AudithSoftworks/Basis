## Audith Basis on Laravel 5.2

[![Build Status](https://circleci.com/gh/AudithSoftworks/Basis.png?circle-token=a8a3c6fee8d17e9a55b9589a53f8cb27b0c180d3)](https://circleci.com/gh/AudithSoftworks/Basis)

[![Circle CI](https://circleci.com/gh/AudithSoftworks/Basis.svg?style=svg&circle-token=a8a3c6fee8d17e9a55b9589a53f8cb27b0c180d3)](https://circleci.com/gh/AudithSoftworks/Basis)
[![Latest Stable Version](https://poser.pugx.org/audithsoftworks/basis/v/stable.svg)](https://packagist.org/packages/audithsoftworks/basis)
[![License](https://poser.pugx.org/audithsoftworks/basis/license.svg)](https://packagist.org/packages/audithsoftworks/basis)

[![](https://images.microbadger.com/badges/version/audithsoftworks/basis.svg)](https://microbadger.com/images/audithsoftworks/basis "Docker Hub public images")
[![](https://images.microbadger.com/badges/image/audithsoftworks/basis.svg)](https://microbadger.com/images/audithsoftworks/basis "Docker Hub public images layers")

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/22803477-ebe7-4906-a57c-f53bfae62ba3/mini.png)](https://insight.sensiolabs.com/projects/22803477-ebe7-4906-a57c-f53bfae62ba3)
[![Code Quality](https://scrutinizer-ci.com/g/AudithSoftworks/Basis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AudithSoftworks/Basis)
[![Coverage](https://scrutinizer-ci.com/g/AudithSoftworks/Basis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AudithSoftworks/Basis)

[![Dependency Status](https://www.versioneye.com/user/projects/559127ee396561002000000a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/559127ee396561002000000a)
[![Dependency Status](https://www.versioneye.com/user/projects/559127ef3965610029000177/badge.svg?style=flat)](https://www.versioneye.com/user/projects/559127ef3965610029000177)
[![Dependency Status](https://www.versioneye.com/user/projects/559128153965610020000024/badge.svg?style=flat)](https://www.versioneye.com/user/projects/559128153965610020000024)

Audith Basis is an enhanced version of Laravel framework, a feature list for which is provided below.

### Features

* [Back-end] _Completely localized routes_ - access your endpoints and URLs in your own language, with Unicode support.
* [Front-end/UI] _Back-end control panel_ - comes with built-in control panel, featuring sample pages for your to expand upon.
* [Front-end/UI] _Custom web-fonts, web typography support_ - build and use your own web-fonts with included toolset, from any TTF/OTF fontsets.
* [DevOps/CI/CD] _Docker support_ - fully Dockerized package with pre-built PHP 5.7, 7.0, HHVM containers.

### Installation

Coming soon!

### Setting up your Developer Environment

I have included a build script in ```./storage/scripts/dev-env/build.sh``` inside of which you can see steps necessary to spin up desired Docker configuration and prepare your development environment. Steps involved are:

1. Build or pull necessary Docker containers.
2. Start your desired Docker-Compose configuration (any of: PHP 5.6, 7.0 or HHVM).
3. Update your ```/etc/hosts``` file to point to the primary container in your Docker configuration - generally ```php_XXX``` is the primary container, which is linked to ```php_XXX-fpm``` and other machines.
4. Create ```.env``` file, containing your environmental variables.
5. Switch into the primary container environment, to start building your environment:
    1. Install NPM and Bower dependencies.
    2. Install ```fine-uploader``` package and build it (used in UI, for file uploads).
    3. Install ```jquery-validation``` package and build it (used in UI, for form validation).
    4. Install ```woff-2``` and it's submodules; and build them (used to build custom web-fonts).
    5. Install ```css3-font-converter``` package and build it (used to build custom web-fonts).
    6. Copy required font files from ```google-fonts``` local Bower location and build your web-fonts.
    7. Compile SASS files using pre-installed Compass.
    8. Run Gulp to build static web assets.
    9. Install Composer dependencies.
    10. Using Laravel Artisan, generate an encryption key and run migrations.
    11. Since Docker runs with root privileges, ```chown``` all newly created files to your host machine UUID:GUID (assuming it is 1000:1000, modify if necessary).
    
Additionally, I've included few commands to shut down Docker-Compose configuration and cleanup your host machine from unnecessary Docker assets.
