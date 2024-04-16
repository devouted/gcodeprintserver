FROM php:8.2-apache

ARG USERID=1000
ARG GROUPID=1000

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    net-tools \
    libpq-dev \
    libgmp-dev \
    libxml2-dev \
    libxslt-dev \
    libcurl4-gnutls-dev \
    libgnutls28-dev \
    zlib1g-dev \
    mariadb-client \
    mc htop \
    cron \
    supervisor \
    libldap2-dev 

RUN docker-php-ext-install zip \
    && docker-php-ext-install gmp \
    && docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bcmath

# Composer
COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
  composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
  composer clear-cache

# configure apache
RUN a2enmod access_compat alias auth_basic auth_digest authn_core authn_file authz_core authz_groupfile authz_host authz_user \
    autoindex cache cache_disk deflate dir env expires ext_filter filter headers ldap mime mpm_prefork negotiation php proxy proxy_ajp \
    proxy_connect proxy_http reqtimeout rewrite setenvif socache_shmcb ssl status substitute vhost_alias

#xdebug
RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug
#remove xdebug from container - should be available only by configuration
RUN rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./docker/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY ./docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

COPY ./docker/bootstrap.sh /usr/local/bin/bootstrap.sh
RUN chmod +x /usr/local/bin/bootstrap.sh

COPY ./docker/php.ini /usr/local/etc/php/php.ini

#add user apache for internal container
RUN groupadd --gid $GROUPID apache
RUN useradd -l --system --shell /bin/bash --uid $USERID --gid $GROUPID apache
RUN usermod -a -G dialout apache

RUN echo "su apache -c \"/var/www/html/bin/console \$@\"" > /usr/local/bin/run
RUN chmod +x /usr/local/bin/run

RUN echo "su apache -c \"/var/www/html/bin/phpunit \$@\"" > /usr/local/bin/phpunit
RUN chmod +x /usr/local/bin/phpunit

RUN echo "su apache -c \"/var/www/html/vendor/bin/rector \$@\"" > /usr/local/bin/rector
RUN chmod +x /usr/local/bin/rector

#crontab
COPY ./docker/crontab /etc/cron.d/crontab
RUN chmod -R 644 /etc/cron.d/crontab

#run apache as user apache
ENV APACHE_RUN_USER=apache
ENV APACHE_RUN_GROUP=apache

CMD ["start.sh"]
