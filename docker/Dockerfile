##################
# Base PHP image #
##################
# Based on: https://github.com/laradock/php-fpm/blob/master/Dockerfile-74
# and https://github.com/laradock/laradock/blob/master/php-fpm/Dockerfile
FROM php:8.0.14-fpm as php-fpm
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
        --with-jpeg \
        --with-freetype && \
    docker-php-ext-install gd && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip

#################################
# Composer PHP packages builder #
#################################
FROM composer:2.0 as composer-builder
WORKDIR /root/app 
COPY app/composer.json /root/app/composer.json
COPY app/composer.lock /root/app/composer.lock
COPY app/database /root/app/database
RUN composer install --no-interaction --no-progress --no-dev --optimize-autoloader --no-scripts

#############################################
# Composer PHP packages builder for testing #
#############################################
FROM composer-builder as composer-builder-test
WORKDIR /root/app
COPY .laravel.env.dusk.example .env
RUN mkdir -p ./storage/testing && touch ./storage/testing/testing.sqlite
RUN composer install --no-interaction --no-progress --optimize-autoloader --no-scripts

##############################
# NPM package Assets builder #
##############################
FROM node:12-alpine as npm-builder
RUN apk add yarn
WORKDIR /root/app
COPY app/yarn.lock /root/app/yarn.lock
COPY app/package.json /root/app/package.json
RUN yarn
COPY app/resources/assets /root/app/resources/assets
COPY app/webpack.mix.js /root/app/webpack.mix.js
RUN yarn run production

##########################################################################
# Run install scripts using cached vendor folder and current source code #
##########################################################################
FROM composer-builder-test as composer-installer-test
WORKDIR /root/app
COPY app .
RUN composer install --no-interaction --no-progress --optimize-autoloader

######################
# PHP image: Testing #
######################
FROM php-fpm as app-test
WORKDIR /var/www
COPY --chown=www-data:www-data --from=composer-installer-test /root/app .
COPY --chown=www-data:www-data --from=npm-builder /root/app/public ./public

##############
# Nginx PROD #
##############
FROM nginx:1.17.10-alpine as nginx-prod
ADD nginx/vhost.conf.production /etc/nginx/conf.d/default.conf
WORKDIR /var/www
COPY --chown=nginx:nginx app/public ./public
COPY --chown=nginx:nginx --from=npm-builder /root/app/public ./public

#############
# Nginx DEV #
#############
FROM nginx:1.17.10-alpine as nginx-dev
ADD nginx/vhost.conf.development /etc/nginx/conf.d/default.conf

##########################################################################
# Run install scripts using cached vendor folder and current source code #
##########################################################################
FROM composer-builder as composer-installer
WORKDIR /root/app
COPY app .
RUN composer install --no-interaction --no-progress --no-dev --optimize-autoloader

###################
# PHP image: PROD #
###################
FROM php-fpm as app-prod
WORKDIR /var/www
RUN mkdir /var/www/.config && chown www-data:www-data /var/www/.config
USER www-data
COPY --chown=www-data:www-data --from=composer-installer /root/app .
COPY --chown=www-data:www-data --from=npm-builder /root/app/public ./public
