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
    && rm /etc/opt/remi/php82/php.d/15-xdebug.ini \
    && rm /etc/opt/remi/php82/php.d/40-pcov.ini

RUN echo "LANG=de_CH.UTF-8" > /etc/locale.conf \
    && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime

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
