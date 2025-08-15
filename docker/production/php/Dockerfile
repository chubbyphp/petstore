FROM rockylinux/rockylinux:10.0

SHELL ["/bin/bash", "-c"]

RUN dnf upgrade -y --refresh

RUN dnf install -y epel-release https://rpms.remirepo.net/enterprise/remi-release-10.rpm

RUN dnf install -y --nobest \
    git \
    glibc-langpack-de \
    langpacks-de \
    nmap-ncat \
    php84-php-ast \
    php84-php-cli \
    php84-php-devel \
    php84-php-fpm \
    php84-php-intl \
    php84-php-mbstring \
    php84-php-opcache \
    php84-php-pecl-apcu \
    php84-php-pecl-pcov \
    php84-php-pecl-xdebug3 \
    php84-php-pecl-zip \
    php84-php-pgsql \
    php84-php-xml \
    procps-ng \
    sudo \
    supervisor \
    unzip \
    vim \
    zsh

RUN ln -sf /usr/bin/php84 /usr/bin/php \
    && echo "extension=pcov.so" > /etc/opt/remi/php84/php.d/40-pcov.ini

ENV TZ=Europe/Zurich

RUN echo "LANG=de_CH.UTF-8" > /etc/locale.conf \
    && cp -f /usr/share/zoneinfo/${TZ} /etc/localtime

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

CMD /opt/remi/php84/root/usr/sbin/php-fpm -c /etc/opt/remi/php84/php-fpm.conf -F
