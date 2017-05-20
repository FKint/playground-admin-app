FROM php:7.0-apache

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite.load

RUN curl -sL https://deb.nodesource.com/setup_7.x > node_setup.sh && chmod +x node_setup.sh && ./node_setup.sh && rm node_setup.sh
RUN apt-get update && apt-get install -y git zip nodejs

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');"

COPY . /var/www/html

RUN php composer.phar install
RUN php artisan key:generate
RUN npm install
RUN npm run dev

RUN service apache2 restart
