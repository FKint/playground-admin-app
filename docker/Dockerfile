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

#############################################
# Composer PHP packages builder for testing #
#############################################
FROM composer as composer-builder-test
WORKDIR /root/app
COPY app /root/app
COPY .laravel.env.dusk.example .env
RUN composer install --optimize-autoloader

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

###################
# PHP image: PROD #
###################
FROM fkint/laravel-docker:latest as app
WORKDIR /var/www
COPY --from=composer-builder /root/app .
COPY --from=npm-builder /root/app/public ./public
# TODO(fkint): find a good way to run "php artisan config:cache" after
# the env file is in the image (cannot do here as the env file is a 
# volume)
RUN php artisan route:cache
VOLUME ["/var/www/storage", "/var/www/.env"]

######################
# PHP image: Testing #
######################
FROM fkint/laravel-docker:latest as app-test
WORKDIR /var/www
COPY --from=composer-builder-test /root/app .
COPY --from=npm-builder /root/app/public ./public
RUN mkdir /var/www/storage/testing && touch /var/www/storage/testing/sqlite
