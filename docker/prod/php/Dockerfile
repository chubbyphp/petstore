FROM ubuntu:20.04

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

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu focal main" > /etc/apt/sources.list.d/ondrej-ubuntu-php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
    # keys.andreas-puls.de

RUN apt-get update -y && apt-get install -y \
    php8.0-apcu \
    php8.0-ast \
    php8.0-cli \
    php8.0-curl \
    php8.0-fpm \
    php8.0-mbstring \
    php8.0-mongodb \
    php8.0-opcache \
    php8.0-readline \
    php8.0-xml \
    php8.0-zip

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

CMD ["/usr/sbin/php-fpm8.0", "-c", "/etc/php/8.0/fpm/php-fpm.conf", "-F"]
