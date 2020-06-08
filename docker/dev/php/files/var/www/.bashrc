alias php-fpm-restart='/usr/bin/supervisorctl -c /etc/supervisor/supervisord.conf restart php-fpm'

alias xdebug-on='sudo bash -c "echo \"zend_extension=xdebug.so\" > /etc/php/7.4/fpm/conf.d/20-xdebug.ini" && php-fpm-restart'
alias xdebug-off='sudo rm -f /etc/php/7.4/fpm/conf.d/20-xdebug.ini && sudo rm -f /etc/php/7.4/fpm/conf.d/20-xdebug.ini && php-fpm-restart'

if [ -f ~/.bash_aliases ]; then
    . ~/.bash_aliases
fi
