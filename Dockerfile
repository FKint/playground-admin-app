FROM php:7.0-apache

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite.load

RUN curl -sL https://deb.nodesource.com/setup_7.x > node_setup.sh && chmod +x node_setup.sh && ./node_setup.sh && rm node_setup.sh
RUN apt-get update && apt-get install -y git zip nodejs wget
RUN npm install -g cross-env cross-spawn is-windows

WORKDIR /opt

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/32c2c34883cf31c57e4729d1afaf09facad7615b/web/installer -O - -q | php -- --quiet

RUN service apache2 restart
