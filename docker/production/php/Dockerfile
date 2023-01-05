FROM rockylinux:9.1

SHELL ["/bin/bash", "-c"]

RUN dnf upgrade -y --refresh

RUN dnf install -y epel-release https://rpms.remirepo.net/enterprise/remi-release-9.1.rpm

RUN dnf install -y --nobest \
    compat-openssl11 \
    git \
    glibc-langpack-de \
    langpacks-de \
    nmap-ncat \
    php82-php-ast \
    php82-php-cli \
    php82-php-devel \
    php82-php-fpm \
    php82-php-intl \
    php82-php-mbstring \
    php82-php-opcache \
    php82-php-pecl-apcu \
    php82-php-pecl-pcov \
    php82-php-pecl-xdebug3 \
    php82-php-pecl-zip \
    php82-php-pgsql \
    php82-php-xml \
    procps-ng \
    sudo \
    supervisor \
    unzip \
    vim \
    zsh

RUN ln -sf /usr/bin/php82 /usr/bin/php \
    && echo "extension=pcov.so" > /etc/opt/remi/php82/php.d/40-pcov.ini

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

CMD /opt/remi/php82/root/usr/sbin/php-fpm -c /etc/opt/remi/php82/php-fpm.conf -F
