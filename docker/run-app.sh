#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-local}

#if [ "$env" != "local" ]; then
    echo "Removing Xdebug..."
    rm -rf /usr/local/etc/php/conf.d/{docker-php-ext-xdebug,xdebug}.ini
#fi

if [ "$env" == "local" ] && [ ! -z "$DEV_UID" ]; then
    echo "Changing www-data UID to $DEV_UID"
    echo "The UID should only be changed in development environments."
    usermod -u $DEV_UID www-data
fi

confd -onetime -backend env

# App OR Testing
if [ "$role" == "app" ]; then
    mkdir -p /etc/supervisor/conf.d/
    ln -sf /etc/supervisor/conf.d-available/app.conf /etc/supervisor/conf.d/app.conf

else
    echo "Could not match the container role \"$role\""
    exit 1
fi

echo "Caching configuration..."
(cd /var/www/html && php artisan config:cache)

exec supervisord -c /etc/supervisor/supervisord.conf
