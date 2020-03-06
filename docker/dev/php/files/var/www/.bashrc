alias php-fpm-restart='/usr/bin/supervisorctl -c /etc/supervisor/supervisord.conf restart php-fpm'

if [ -f ~/.bash_aliases ]; then
    . ~/.bash_aliases
fi
