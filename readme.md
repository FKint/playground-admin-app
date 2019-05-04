[![CircleCI](https://circleci.com/gh/FKint/playground-admin-app/tree/master.svg?style=svg)](https://circleci.com/gh/FKint/playground-admin-app/tree/master)

# Warning: this information is outdated

# Playground admin web app using Laravel

## Development

### Local development instance

* Copy `.env.example` to `.env` and `.laravel.env.development.example` to `.laravel.env.development`.
* Run `docker-compose -f docker/docker-compose.yml.development --project-name development --project-dir . up --build`
* Open a shell in the container (e.g. `docker exec -it development_app_1 /bin/bash`)
* Run `php artisan migrate:refresh --seed` to build the database tables and fill it with initial data. Run `php artisan migrate` if the database already exists and you only want to update the schema.
* The web application runs on port 21000 (`http://localhost:21000`) of the host OS. PHPMyAdmin runs on port 21001 (`http://localhost:21001`).
* The `vendor` and `node_modules` folders are not synchronized to the host file system. The best way to update these is to rebuild the containers after updating the `composer.json`, `composer.lock`, `package.json` or `yarn.lock` files. 
* Updating the PHP packages (`composer.json` and `composer.lock`) is best done using a composer container: ```docker run -it -v "`pwd`/app:/app" composer /bin/sh```. This will also update the `vendor` folder on the host file system. Mount the individual files to speed up the process.
* Updating the NPM packages (`package.json` or `yarn.lock`) is best done using a `node:8-alpine` container: ```docker run -it -v "`pwd`/app:/opt/app" node:8-alpine /bin/sh```. This will also update the `node_modules` folder on the host file system. Mount the individual files to speed up the process.


### Run tests (local)
* Run `docker-compose -f docker/docker-compose.yml.dusk-testing --project-name dusk-tests --project-dir . up --build` to set up the selenium container and the Laravel server.
* Dusk tests: in a separate terminal, run `docker exec -it dusk-tests_app-container_1 php artisan dusk`
* Unit tests: in a separate terminal, run `docker exec -it dusk-tests_app-container_1 ./vendor/phpunit/phpunit/phpunit`

## Production

### System setup

#### Firewall
* `ufw allow 80` and `ufw allow 443`.
* `ufw allow from 127.0.0.1 to 127.0.0.1 port 21306 proto tcp`
* `ufw allow from 127.0.0.1 to 127.0.0.1 port 21001 proto tcp`

#### Certbot
* **TODO: update the docker-compose script**
* Install `certbot`:
    * `add-apt-repository ppa:certbot/certbot && apt-get update && apt-get install -y certbot && apt-get clean`
    * `certbot certonly --authenticator standalone  -d admin.jokkebrok.be --pre-hook "docker-compose -f /root/playground-admin-app/docker/docker-compose.yml.production --project-dir /root/playground-admin-app stop" --post-hook "docker-compose -f /root/playground-admin-app/docker-compose.yml.production  --project-dir /root/playground-admin-app start"`
    * Add `certbot renew` to cron.

#### Configuration
* Configure a `.env` file.
* Configure a `.laravel.env.production` file.
* In the container: run `php artisan key:generate`.
* In the container: run `php artisan migrate`.
* In the container: run `php artisan config:cache`.
* (Not supported yet) In the container: run `php artisan route:cache`.

## Update (docker-compose production) with longer downtime
* `git pull`: This only affects the docker-compose.yml.production file. The images are pulled from Docker Hub.
* `docker-compose -f docker/docker-compose.yml.production --project-dir . pull`
* `docker-compose -f docker/docker-compose.yml.production --project-dir . up --build`
* In the container: run `php artisan migrate`
* In the container: run `php artisan config:cache`.
* In the container: run `php artisan route:cache`.

## Access PHPMyAdmin 
* `ssh root@admin.jokkebrok.be -L 22001:localhost:21001 -N`
* Visit `http://localhost:22001`

## Access MySQL
* `ssh root@admin.jokkebrok.be -L 22306:localhost:21306 -N`
* Connect to MySQL at `localhost:22306`

## Dev recommendations
* Suggested pre-commit hook: 

```bash
#!/bin/bash
cd app
git stash save --keep-index
php-cs-fixer fix --dry-run --diff --diff-format udiff
RESULT=$?
git stash apply -q
cd ..
if [ "$RESULT" -eq "0" ]; then
  exit 0;
else
  echo "PHP CS Fixer found errors which prevented this commit from succeeding.";
  exit $RESULT;
fi;
```

## License
This project (and the Laravel framework too) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
