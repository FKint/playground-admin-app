# Playground admin web app using Laravel

## Setup (local)

* Run `docker-compose -f docker-compose.yml.development up`
* Copy `.env.example` to `.env` and `.laravel.env.development.example` to `.laravel.env`.
* Open a shell in the container (e.g. `docker exec -it playgroundadminapp_app_1 /bin/bash`)
* Run `./install-packages.sh` to install and pack Composer and NPM packages.
* Run `php artisan key:generate` to generate a key for the application.
* Run `php artisan migrate:refresh --seed` to build the database tables and fill it with initial data.
* The web application runs on port 21000 (`http://localhost:21000`). PHPMyAdmin runs on port 21001 (`http://localhost:21001`).

## Updates (local)

* Make sure the containers are running (`docker-compose up`).
* Pull changes from git (`git pull`)
* Open a shell in the container (e.g. `docker exec -it playgroundadminapp_app_1 /bin/bash`)
* Run `./install-packages.sh` to update composer packages and node modules and to pack the assets for the application.
* Run `php artisan migrate` to apply database changes.

## Tests (local)
* Open a shell in the container (e.g. `docker exec -it playgroundadminapp_app_1 /bin/bash`)
* Run `./vendor/phpunit/phpunit/phpunit`

## Setup (docker-compose production)
* `ufw allow 80` and `ufw allow 443`.
* `ufw allow from 127.0.0.1 to 127.0.0.1 port 21306 proto tcp`
* `ufw allow from 127.0.0.1 to 127.0.0.1 port 21001 proto tcp`
* Install `certbot`:
    * `add-apt-repository ppa:certbot/certbot && apt-get update && apt-get install -y certbot && apt-get clean`
    * `certbot certonly --authenticator standalone  -d admin.jokkebrok.be --pre-hook "docker-compose -f /root/playground-admin-app/docker-compose.yml.production stop" --post-hook "docker-compose -f /root/playground-admin-app/docker-compose.yml.production start"`
    * Add `certbot renew` to cron.
* Configure a `.env` file.
* Configure a `.laravel.production.env` file.
* In the container: run `php artisan key:generate`.
* In the container: run `php artisan migrate`.
* In the container: run `php artisan config:cache`.
* (Not supported yet) In the container: run `php artisan route:cache`.

## Update (docker-compose production) with longer downtime
* `git pull`
* `docker-compose -f docker-compose.yml.production pull`
* `docker-compose -f docker-compose.yml.production up --build`
* In the container: run `php artisan migrate`
* In the container: run `php artisan config:cache`.
* In the container: run `php artisan route:cache`.

## Access PHPMyAdmin 
* `ssh root@admin.jokkebrok.be -L 22001:localhost:21001 -N`
* Visit `http://localhost:22001`

## Access MySQL
* `ssh root@admin.jokkebrok.be -L 22306:localhost:21306 -N`
* Connect to MySQL at `localhost:22306`

## License
This project (and the Laravel framework too) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
