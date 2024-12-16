FROM rockylinux:9.3

SHELL ["/bin/bash", "-c"]

RUN dnf upgrade -y --refresh

RUN dnf install -y epel-release https://rpms.remirepo.net/enterprise/remi-release-9.rpm

RUN dnf install -y --nobest \
    compat-openssl11 \
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
    && rm /etc/opt/remi/php84/php.d/15-xdebug.ini \
    && rm /etc/opt/remi/php84/php.d/40-pcov.ini

ENV TZ=Europe/Zurich

RUN echo "LANG=de_CH.UTF-8" > /etc/locale.conf \
    && cp -f /usr/share/zoneinfo/${TZ} /etc/localtime

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY docker/production/php/files /
COPY docker/development/php/files /

ARG USER_ID
ARG GROUP_ID

RUN groupadd -g ${GROUP_ID} php \
    && useradd -u ${USER_ID} -g ${GROUP_ID} -s /bin/bash -M php \
    && chown -Rf php: /home/php \
    && echo 'php ALL=(ALL) NOPASSWD: ALL' > '/etc/sudoers.d/php'

USER php

WORKDIR /app

CMD /usr/bin/supervisord
