FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-c"]

RUN apt-get update -y && apt-get install -y \
    curl \
    git \
    gnupg2 \
    locales \
    netcat \
    openssh-client \
    tzdata \
    unzip \
    vim

RUN locale-gen de_CH.UTF-8 && update-locale LANG=de_CH.UTF-8 LC_ALL=de_CH.UTF-8 \
    && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime && dpkg-reconfigure --frontend noninteractive tzdata

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ondrej-ubuntu-php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
    # keys.andreas-puls.de

RUN apt-get update -y && apt-get install -y \
    php8.1-apcu \
    php8.1-ast \
    php8.1-cli \
    php8.1-curl \
    php8.1-fpm \
    php8.1-intl \
    php8.1-mbstring \
    php8.1-opcache \
    php8.1-pgsql \
    php8.1-readline \
    php8.1-xml \
    php8.1-zip

RUN ln -sf /usr/bin/php8.1 /etc/alternatives/php

COPY docker/prod/php/files /

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY bin /var/www/html/bin
COPY config /var/www/html/config
COPY public /var/www/html/public
COPY src /var/www/html/src
COPY swagger /var/www/html/swagger
COPY tests /var/www/html/tests
COPY composer.json phpunit.xml /var/www/html/

RUN chown -R www-data:www-data /var/www

USER www-data

RUN cd /var/www/html && \
    composer install

WORKDIR /var/www/html

CMD ["/usr/sbin/php-fpm8.1", "-c", "/etc/php/8.1/fpm/php-fpm.conf", "-F"]
