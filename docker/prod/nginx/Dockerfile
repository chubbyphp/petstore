FROM ubuntu:19.10

ENV DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-c"]

RUN apt-get update -y && apt-get install -y \
    gnupg2 \
    locales \
    tzdata

RUN locale-gen de_CH.UTF-8 && update-locale LANG=de_CH.UTF-8 LC_ALL=de_CH.UTF-8 \
    && cp -f /usr/share/zoneinfo/Europe/Zurich /etc/localtime && dpkg-reconfigure --frontend noninteractive tzdata

RUN echo "deb http://ppa.launchpad.net/nginx/stable/ubuntu eoan main" > /etc/apt/sources.list.d/nginx-stable.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 00A6F0A3C300EE8C

RUN apt-get update -y && apt-get install -y nginx-light

COPY prod/nginx/files /

CMD ["nginx", "-g", "daemon off;"]
