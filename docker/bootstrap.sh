#!/bin/bash

if [[ -z "${APP_ENV}" ]]; then

  echo "no APP_ENV var, using defaults"

else

    cp /var/www/html/.env /var/www/html/.env.${APP_ENV}
    sed -i "s/APP_ENV=[a-z]*/APP_ENV=$APP_ENV/" /var/www/html/.env

    sed -i "s/DATABASE_URL=.*/DATABASE_URL=\"mysql:\/\/$MYSQL_USER\:$MYSQL_PASSWORD\@$MYSQL_HOST\:$MYSQL_PORT\/$MYSQL_DATABASE\"/" /var/www/html/.env.$APP_ENV

fi

cd /var/www/html/
rm -R vendor/*
su apache -c "composer install"

