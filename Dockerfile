FROM php:7.0-apache
COPY . /var/www/html

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite.load

RUN service apache2 restart
