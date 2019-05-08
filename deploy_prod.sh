#!/bin/bash
docker-compose -f docker/docker-compose.yml.production --project-name playgroundadminapp --project-dir . pull
docker-compose -f docker/docker-compose.yml.production --project-name playgroundadminapp --project-dir . up -d
docker exec -it playgroundadminapp_app_1 php artisan migrate
docker exec -it playgroundadminapp_app_1 php artisan cache:clear
docker exec -it playgroundadminapp_app_1 php artisan config:cache
docker exec -it playgroundadminapp_app_1 php artisan route:cache