FROM rockylinux:9.0

SHELL ["/bin/bash", "-c"]

RUN dnf upgrade -y --refresh

RUN dnf install -y epel-release https://rpms.remirepo.net/enterprise/remi-release-9.rpm

RUN dnf install -y --nobest \
    compat-openssl11 \
    git \
    glibc-langpack-de \
    langpacks-de \
    nmap-ncat \
    php81-php-ast \
    php81-php-cli \
    php81-php-devel \
    php81-php-fpm \
    php81-php-intl \
    php81-php-mbstring \
    php81-php-opcache \
    php81-php-pecl-apcu \
    php81-php-pecl-mongodb \
    php81-php-pecl-pcov \
    php81-php-pecl-xdebug3 \
    php81-php-pecl-zip \
    php81-php-xml \
    procps-ng \
    sudo \
    supervisor \
    unzip \
    vim \
    zsh

RUN ln -sf /usr/bin/php81 /usr/bin/php \
    && echo "extension=pcov.so" > /etc/opt/remi/php81/php.d/40-pcov.ini

RUN echo "LANG=de_CH.UTF-8" > /etc/locale.conf \
    && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY docker/production/php/files /

RUN mkdir /home/php \
    && groupadd -g 1000 php \
    && useradd -u 1000 -g 1000 -s /bin/bash -M php \
    && chown -Rf php: /home/php

COPY bin /app/bin
COPY config /app/config
COPY public /app/public
COPY src /app/src
COPY composer.json openapi.yml /app/

RUN chown -Rf php: /app

USER php

WORKDIR /app

RUN composer install --no-dev --optimize-autoloader

CMD /opt/remi/php81/root/usr/sbin/php-fpm -c /etc/opt/remi/php81/php-fpm.conf -F
