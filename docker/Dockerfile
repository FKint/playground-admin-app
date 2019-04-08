##############################
# NPM package Assets builder #
##############################
FROM node:8 as npm-builder
RUN npm install npm@latest -g 
COPY app /root/app
WORKDIR /root/app
RUN npm install
RUN npm run production

#################################
# Composer PHP packages builder #
#################################
FROM composer as composer-builder
WORKDIR /root/app 
COPY app /root/app
RUN composer install --no-dev --optimize-autoloader

##############
# Nginx PROD #
##############
FROM nginx:stable as nginx-prod
ADD nginx/vhost.conf.production /etc/nginx/conf.d/default.conf
WORKDIR /var/www
COPY --from=npm-builder /root/app/public .

#############
# Nginx DEV #
#############
FROM nginx:stable as nginx-dev
ADD nginx/vhost.conf.development /etc/nginx/conf.d/default.conf

#############
# PHP image #
#############
# Based on: https://github.com/laradock/php-fpm/blob/master/Dockerfile-73
# and https://github.com/laradock/laradock/blob/master/php-fpm/Dockerfile
FROM php:7.3-fpm as php-fpm
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
    pecl channel-update pecl.php.net && \
    apt-get install -y --no-install-recommends \
        apt-utils \
        curl \
        libmemcached-dev \
        libz-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        libmcrypt-dev \
        libzip-dev zip unzip && \
    rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-configure gd \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip

###################
# PHP image: PROD #
###################
FROM php-fpm as app
WORKDIR /var/www
COPY --from=composer-builder /root/app .
COPY --from=npm-builder /root/app/public ./public
# TODO(fkint): find a good way to run "php artisan config:cache" after
# the env file is in the image (cannot do here as the env file is a 
# volume)
RUN php artisan route:cache
VOLUME ["/var/www/storage", "/var/www/.env"]