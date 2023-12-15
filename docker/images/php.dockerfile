FROM php:8.0-fpm-alpine

RUN deluser www-data &&\
    adduser -DH -h /home/www-data -s /sbin/nologin -u 1000 www-data

## Libraries
RUN set -eux &&\
    echo -e "\e[32mInstalling lib dependencies \e[39m" &&\
    apk add --no-cache libzip libzip-dev libpng libpng-dev libjpeg-turbo libjpeg-turbo-dev libwebp libwebp-dev freetype freetype-dev icu icu-dev postgresql postgresql-dev libxslt libxslt-dev zeromq zeromq-dev git autoconf g++ gcc make &&\
    echo -e "\e[32mLibs dependicies instaled \e[39m" \
    echo -e "\e[32mConfiguring PHP extentions \e[39m" &&\
    docker-php-ext-configure gd --enable-gd --with-freetype --with-webp --with-jpeg &&\
    cd /usr/src/ &&\
    git clone https://github.com/zeromq/php-zmq.git &&\
    cd php-zmq &&\
    phpize &&\
    ./configure &&\
    make &&\
    make install &&\
    echo -e "\e[32mInstalling PHP Extentions \e[39m" &&\
    docker-php-ext-install -j$(nproc) bcmath exif gd intl mysqli opcache pcntl pdo_mysql pdo_pgsql soap sockets xsl zip &&\
    docker-php-ext-enable zmq &&\
    echo -e "\e[32mPHP extentions instaled\e[39m" &&\
    php -m &&\
    echo -e "\e[32mPurging libs \e[39m" &&\
    apk del libzip-dev libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev icu-dev postgresql-dev libxslt-dev zeromq-dev git autoconf g++ gcc make &&\
    rm -fR /usr/src/php-zmq &&\
    echo -e "\e[32mInstalling Composer\e[39m" &&\
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer &&\
    echo -e "\e[32mInstalling Chromiun Headless\e[39m" &&\
    apk add --no-cache nmap &&\
    echo @edge http://nl.alpinelinux.org/alpine/edge/community >> /etc/apk/repositories &&\
    echo @edge http://nl.alpinelinux.org/alpine/edge/main >> /etc/apk/repositories &&\
    apk add --no-cache chromium harfbuzz "freetype>2.8" ttf-freefont nss &&\
    chromium-browser --version
