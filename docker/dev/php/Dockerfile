FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-c"]

RUN apt-get update -y && apt-get install -y \
    curl \
    git \
    gnupg2 \
    inetutils-ping \
    locales \
    netcat \
    openssh-client \
    sudo \
    supervisor \
    tzdata \
    unzip \
    vim \
    zsh

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
    php8.1-dev \
    php8.1-fpm \
    php8.1-intl \
    php8.1-mbstring \
    php8.1-opcache \
    php8.1-pcov \
    php8.1-pgsql \
    php8.1-readline \
    php8.1-xdebug \
    php8.1-xml \
    php8.1-zip

RUN ln -sf /usr/bin/php8.1 /etc/alternatives/php

RUN phpdismod pcov && phpdismod xdebug

RUN mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/probe/php/linux/$(if [ $(uname -m) == "aarch64" ]; then echo 'arm64'; else echo 'amd64'; fi)/$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > /etc/php/8.1/mods-available/blackfire.ini \
    && ln -s /etc/php/8.1/mods-available/blackfire.ini /etc/php/8.1/cli/conf.d/30-blackfire.ini \
    && ln -s /etc/php/8.1/mods-available/blackfire.ini /etc/php/8.1/fpm/conf.d/30-blackfire.ini \
    && rm -rf /tmp/blackfire \
    && mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux/$(if [ $(uname -m) == "aarch64" ]; then echo 'arm64'; else echo 'amd64'; fi) | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -rf /tmp/blackfire

COPY docker/prod/php/files /
COPY docker/dev/php/files /

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ARG USER_ID
ARG GROUP_ID

RUN groupmod -g ${GROUP_ID} -o www-data \
    && usermod -u ${USER_ID} -s /bin/bash -o www-data \
    && echo 'www-data ALL=(ALL) NOPASSWD: ALL' > '/etc/sudoers.d/www-data'

RUN mkdir -p /var/www && chown -R www-data:www-data /var/www

USER www-data

WORKDIR /var/www/html

CMD ["/usr/bin/supervisord"]
