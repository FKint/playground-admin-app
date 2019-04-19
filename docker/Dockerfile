#################################
# Composer PHP packages builder #
#################################
FROM composer as composer-builder
WORKDIR /root/app 
COPY app/composer.json /root/app/composer.json
COPY app/composer.lock /root/app/composer.lock
COPY app/database /root/app/database
RUN composer install --no-dev --optimize-autoloader --no-scripts

#############################################
# Composer PHP packages builder for testing #
#############################################
FROM composer-builder as composer-builder-test
WORKDIR /root/app
COPY .laravel.env.dusk.example .env
RUN composer install --optimize-autoloader --no-scripts

##############################
# NPM package Assets builder #
##############################
FROM node:8 as npm-builder
RUN npm install npm@latest -g 
COPY app/webpack.mix.js /root/app/webpack.mix.js
COPY app/package-lock.json /root/app/package-lock.json
COPY app/package.json /root/app/package.json
COPY app/resources/assets /root/app/resources/assets
WORKDIR /root/app
RUN npm install
RUN npm run production

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

##########################################################################
# Run install scripts using cached vendor folder and current source code #
##########################################################################
FROM composer-builder as composer-installer
WORKDIR /root/app
COPY app .
RUN composer install --no-dev --optimize-autoloader

###################
# PHP image: PROD #
###################
FROM fkint/laravel-docker:latest as app
WORKDIR /var/www
COPY --from=composer-installer /root/app .
COPY --from=npm-builder /root/app/public ./public
# TODO(fkint): find a good way to run "php artisan config:cache" after
# the env file is in the image (cannot do here as the env file is a 
# volume)
RUN php artisan route:cache
VOLUME ["/var/www/storage", "/var/www/.env"]

##########################################################################
# Run install scripts using cached vendor folder and current source code #
##########################################################################
FROM composer-builder-test as composer-installer-test
WORKDIR /root/app
COPY app .
RUN composer install --optimize-autoloader

######################
# PHP image: Testing #
######################
FROM fkint/laravel-docker:latest as app-test
WORKDIR /var/www
COPY app .
COPY --from=composer-installer-test /root/app .
COPY --from=npm-builder /root/app/public ./public
RUN mkdir /var/www/storage/testing && touch /var/www/storage/testing/sqlite
