#!/bin/bash
set +e
# Works with both Compose v2 ("docker compose") and v1 ("docker-compose").
if docker compose version >/dev/null 2>&1; then
  COMPOSE=(docker compose)
else
  COMPOSE=(docker-compose)
fi
COMPOSE+=(-f docker/docker-compose.yml.production --project-name playgroundadminapp --project-directory .)

"${COMPOSE[@]}" pull
"${COMPOSE[@]}" up -d
# -T: no TTY needed; "exec" resolves the container by service name (container names differ between v1 and v2).
"${COMPOSE[@]}" exec -T app php artisan migrate
"${COMPOSE[@]}" exec -T app php artisan cache:clear
"${COMPOSE[@]}" exec -T app php artisan config:cache
"${COMPOSE[@]}" exec -T app php artisan route:cache
set -e
