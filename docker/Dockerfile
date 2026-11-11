##################
# Base PHP image #
##################
# Based on: https://github.com/laradock/php-fpm/blob/master/Dockerfile-74
# and https://github.com/laradock/laradock/blob/master/php-fpm/Dockerfile
FROM php:8.5.12-fpm AS php-fpm
ENV DEBIAN_FRONTEND=noninteractive
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
FROM composer:2.10.3 AS composer-builder
WORKDIR /root/app 
COPY app/composer.json /root/app/composer.json
COPY app/composer.lock /root/app/composer.lock
COPY app/database /root/app/database
RUN composer install --no-interaction --no-progress --no-dev --optimize-autoloader --no-scripts

#############################################
# Composer PHP packages builder for testing #
#############################################
FROM composer:2.10.3 AS composer-builder-test
WORKDIR /root/app
COPY app/composer.json /root/app/composer.json
COPY app/composer.lock /root/app/composer.lock
COPY app/database /root/app/database
COPY .laravel.env.dusk.example .env
RUN mkdir -p ./storage/testing && touch ./storage/testing/testing.sqlite
RUN composer install --no-interaction --no-progress --optimize-autoloader --no-scripts

##############################
# NPM package Assets builder #
##############################
FROM node:26.13.0-alpine3.24 AS npm-builder
RUN apk add yarn
WORKDIR /root/app
COPY app/yarn.lock /root/app/yarn.lock
COPY app/package.json /root/app/package.json
RUN yarn install --frozen-lockfile
COPY app/resources/assets /root/app/resources/assets
COPY app/vite.config.js /root/app/vite.config.js
RUN yarn build

##########################################################################
# Add current source code to the cached vendor folder and dump autoloader #
##########################################################################
FROM composer-builder-test AS composer-installer-test
WORKDIR /root/app
COPY app .
RUN composer dump-autoload --optimize

####################################################################
# Version files: built last so a new commit only invalidates this  #
# tiny layer. Empty build args (local builds) produce no files.    #
####################################################################
FROM alpine:3.22 AS version
ARG VERSION_SHA1=
ARG VERSION_GITHUB_LINK=
ARG VERSION_TIMESTAMP=
RUN mkdir /version && \
    if [ -n "$VERSION_SHA1" ]; then \
        echo "$VERSION_SHA1" > /version/version_sha1 && \
        echo "$VERSION_GITHUB_LINK" > /version/version_github_link && \
        echo "$VERSION_TIMESTAMP" > /version/version_timestamp; \
    fi

######################
# PHP image: Testing #
######################
FROM php-fpm AS app-test
WORKDIR /var/www
COPY --chown=www-data:www-data --from=composer-installer-test /root/app .
COPY --chown=www-data:www-data --from=npm-builder /root/app/public ./public
COPY --chown=www-data:www-data --from=version /version/ ./

##############
# Nginx PROD #
##############
FROM nginx:1.30.6-alpine AS nginx-prod
WORKDIR /var/www
COPY --chown=nginx:nginx app/public ./public
COPY --chown=nginx:nginx --from=npm-builder /root/app/public ./public

#############
# Nginx DEV #
#############
FROM nginx:1.30.6-alpine AS nginx-dev
WORKDIR /var/www
COPY --chown=nginx:nginx app/public ./public
COPY --chown=nginx:nginx --from=npm-builder /root/app/public ./public

##########################################################################
# Add current source code to the cached vendor folder and dump autoloader #
##########################################################################
FROM composer-builder AS composer-installer
WORKDIR /root/app
COPY app .
RUN composer dump-autoload --no-dev --optimize

###################
# PHP image: PROD #
###################
FROM php-fpm AS app-prod
WORKDIR /var/www
RUN mkdir /var/www/.config && chown www-data:www-data /var/www/.config
USER www-data
COPY --chown=www-data:www-data --from=composer-installer /root/app .
COPY --chown=www-data:www-data --from=npm-builder /root/app/public ./public
COPY --chown=www-data:www-data --from=version /version/ ./
