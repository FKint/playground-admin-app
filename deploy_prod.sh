#!/bin/bash
DOCKER_COMPOSE_COMMAND=docker-compose -f docker/docker-compose.yml.production --project-name playgroundadminapp --project-dir .
$DOCKER_COMPOSE_COMMAND pull
$DOCKER_COMPOSE_COMMAND up -d
docker run -it playgroundadminapp_app_1 php artisan migrate
docker run -it playgroundadminapp_app_1 php artisan cache:clear
docker run -it playgroundadminapp_app_1 php artisan config:cache
docker run -it playgroundadminapp_app_1 php artisan route:cache