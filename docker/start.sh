#!/bin/bash

if [[ -z "${APP_ENV}" ]]; then

    echo "no APP_ENV var, using defaults"

else

    a2dissite "000-default.conf"
    a2ensite "localhost.conf"

    rm /var/www/html/.env.${APP_ENV}
    cp /var/www/html/.env /var/www/html/.env.${APP_ENV}
    sed -i "s/APP_ENV=.*/APP_ENV=$APP_ENV/" /var/www/html/.env

    sed -i "s/DATABASE_URL=.*/DATABASE_URL=\"mysql:\/\/$MYSQL_USER\:$MYSQL_PASSWORD\@$MYSQL_HOST\:$MYSQL_PORT\/$MYSQL_DATABASE\"/" /var/www/html/.env.$APP_ENV

fi

echo "Starting supervisor with configuration for $APP_ENV"

#run supervisor with subprocesses
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
