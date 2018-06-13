#!/bin/bash

echo "START chromedriver!"
./vendor/laravel/dusk/bin/chromedriver-linux &
echo "SERVE artisan!"
php artisan serve --env=.env &
echo "START tests"
php artisan dusk
echo "DONE"