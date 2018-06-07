FROM nginx:stable

ADD app/package-lock.json /opt/app/package-lock.json
ADD app/package.json /opt/app/package.json
ADD app/public /opt/app/public
ADD app/resources/assets /opt/app/resources/assets
ADD app/webpack.mix.js /opt/app/webpack.mix.js
RUN ls /opt/app/public
WORKDIR /opt/app

# Install Node.js
RUN apt-get update && apt-get install -y gcc g++ make curl zip unzip gnupg libpng-dev && curl -sL https://deb.nodesource.com/setup_8.x | bash - && apt-get install -y nodejs && npm install npm@latest -g && npm install && npm run production && mkdir -p /var/www && cp -r /opt/app/public /var/www/ && rm -rf /opt/app && apt-get purge -y gcc g++ make curl zip unzip gnupg libpng-dev nodejs npm && apt-get autoremove -y --purge && apt-get clean

ADD nginx/vhost.conf.production /etc/nginx/conf.d/default.conf
