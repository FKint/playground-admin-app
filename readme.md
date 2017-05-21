#Playground admin web app using Laravel

##Setup (local)

* Run `docker-compose up`
* Copy `.env.example` to `.env`
* Open a shell in the container (e.g. `docker exec -it playgroundadminapp_web_1 /bin/bash`)
* Run `./install.sh` to install composer packages and node modules and to pack the assets for the application.
* Run `php artisan key:generate` to generate a key for the application.
* Run `php artisan migrate:refresh --seed` to build the database tables and fill it with initial data.
* The web application runs on port 21000 (`http://localhost:21000`). PHPMyAdmin runs on port 21001 (`http://localhost:21001`).
## Updates (local)

* Pull changes from git (`git pull`)
* Open a shell in the container (e.g. `docker exec -it playgroundadminapp_web_1 /bin/bash`)
* Run `./install.sh` to update composer packages and node modules and to pack the assets for the application.
* Run `php artisan migrate` to apply database changes.

## License
This project (and the Laravel framework too) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
