FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-c"]

RUN apt-get update -y && apt-get install -y \
    curl \
    git \
    gnupg2 \
    locales \
    openssh-client \
    sudo \
    tzdata \
    unzip \
    vim

RUN locale-gen de_CH.UTF-8 && update-locale LANG=de_CH.UTF-8 LC_ALL=de_CH.UTF-8 \
    && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime && dpkg-reconfigure --frontend noninteractive tzdata

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu focal main" > /etc/apt/sources.list.d/ondrej-ubuntu-php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
    # keys.andreas-puls.de

RUN apt-get update -y && apt-get install -y \
    php7.4-cli \
    php7.4-curl \
    php7.4-fpm \
    php7.4-json \
    php7.4-mbstring \
    php7.4-opcache \
    php7.4-pgsql \
    php7.4-phpdbg \
    php7.4-readline \
    php7.4-xml \
    php7.4-zip \
    php-apcu \
    php-ast

COPY prod/nginx/files /

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN mkdir -p /var/www && chown -R www-data:www-data /var/www

USER www-data

RUN composer global require hirak/prestissimo

WORKDIR /var/www/html

CMD ["/usr/sbin/php-fpm7.4", "-c", "/etc/php/7.4/fpm/php-fpm.conf", "-F"]
