FROM fluent/fluentd:v1.19-2

USER root

RUN apt-get update \
    && apt-get install -y build-essential ruby-dev \
    && gem install fluent-plugin-opensearch \
    && gem sources --clear-all \
    && apt-get purge -y --auto-remove build-essential ruby-dev \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* /var/tmp/* /usr/lib/ruby/gems/*/cache/*.gem

COPY docker/production/php-fluentd/files /

USER fluent
