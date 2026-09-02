FROM rockylinux/rockylinux:10.0

SHELL ["/bin/bash", "-c"]

RUN dnf upgrade -y --refresh

RUN dnf install -y epel-release https://rpms.remirepo.net/enterprise/remi-release-10.rpm

RUN dnf install -y --nobest \
    git \
    glibc-langpack-de \
    langpacks-de \
    libatomic \
    nmap-ncat \
    php85-php-ast \
    php85-php-bcmath \
    php85-php-cli \
    php85-php-devel \
    php85-php-fpm \
    php85-php-intl \
    php85-php-mbstring \
    php85-php-opcache \
    php85-php-pecl-apcu \
    php85-php-pecl-mongodb \
    php85-php-pecl-pcov \
    php85-php-pecl-xdebug3 \
    php85-php-pecl-zip \
    php85-php-sodium \
    php85-php-xml \
    procps-ng \
    sudo \
    supervisor \
    unzip \
    vim \
    zsh

RUN ln -sf /usr/bin/php85 /usr/bin/php \
    && rm /etc/opt/remi/php85/php.d/15-xdebug.ini \
    && rm /etc/opt/remi/php85/php.d/40-pcov.ini

ENV TZ=Europe/Zurich

RUN echo "LANG=de_CH.UTF-8" > /etc/locale.conf \
    && cp -f /usr/share/zoneinfo/${TZ} /etc/localtime

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY docker/production/php/files /
COPY docker/development/php/files /

ARG USER_ID
ARG GROUP_ID

RUN getent group ${GROUP_ID} >/dev/null || groupadd -g ${GROUP_ID} php \
    && useradd -u ${USER_ID} -g ${GROUP_ID} -s /bin/bash -M php \
    && chown -Rf php: /home/php \
    && echo 'php ALL=(ALL) NOPASSWD: ALL' > '/etc/sudoers.d/php'

USER php

WORKDIR /home/php

RUN curl -fsSL https://fnm.vercel.app/install | bash -s -- --install-dir "$HOME/.fnm" --skip-shell \
    && source ~/.bashrc \
    && fnm install 24 \
    && fnm default 24 \
    && fnm use 24 \
    && npm install -g corepack@latest

RUN source ~/.bashrc \
    && npm install -g @anthropic-ai/claude-code --allow-scripts=@anthropic-ai/claude-code \
    && mkdir -p "$HOME/.claude"

RUN source ~/.bashrc \
    && npm install -g @openai/codex \
    && mkdir -p "$HOME/.codex"

RUN source ~/.bashrc \
    && npm install -g opencode-ai@latest --allow-scripts=opencode-ai \
    && mkdir -p "$HOME/.config/opencode" \
    && mkdir -p "$HOME/.local/share/opencode"

RUN source ~/.bashrc \
    && npm install -g @earendil-works/pi-coding-agent --allow-scripts=@google/genai,protobufjs \
    && mkdir -p "$HOME/.pi/agent" \
    && pi install git:github.com/huggingface/pi-llama

WORKDIR /app

CMD /usr/bin/supervisord
