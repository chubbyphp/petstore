alias php-fpm-restart='/usr/bin/supervisorctl -c /etc/supervisor/supervisord.conf restart php-fpm'

alias xdebug-on='sudo phpenmod xdebug && php-fpm-restart'
alias xdebug-off='sudo phpdismod xdebug && php-fpm-restart'
alias pcov-on='sudo phpenmod pcov && php-fpm-restart'
alias pcov-off='sudo phpdismod pcov && php-fpm-restart'

if [ -f ~/.bash_aliases ]; then
    . ~/.bash_aliases
fi
