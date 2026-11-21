#!/bin/bash
# Usage: ./deploy_prod.sh [update|verify]
#   update (default): pull images, recreate changed containers, run migrations/caches, then verify.
#   verify:           read-only; list and check that the running containers use the latest images.
#                     Both modes also print the versions of the software running inside the containers.
# Exits non-zero when verification finds a service that is not running the latest image.

# Works with both Compose v2 ("docker compose") and v1 ("docker-compose").
if docker compose version >/dev/null 2>&1; then
  COMPOSE=(docker compose)
else
  COMPOSE=(docker-compose)
fi
COMPOSE+=(-f docker/docker-compose.yml.production --project-name playgroundadminapp --project-directory .)

update() {
  "${COMPOSE[@]}" pull
  "${COMPOSE[@]}" up -d
  # -T: no TTY needed; "exec" resolves the container by service name (container names differ between v1 and v2).
  "${COMPOSE[@]}" exec -T app php artisan migrate
  "${COMPOSE[@]}" exec -T app php artisan cache:clear
  "${COMPOSE[@]}" exec -T app php artisan config:cache
  "${COMPOSE[@]}" exec -T app php artisan route:cache
}

# Prints the first line a command prints inside a service's container, or "unknown" (never fails the script).
# Querying the running software, not the image tag, shows what is really serving requests.
report() {
  local label=$1 service=$2 out
  shift 2
  out=$("${COMPOSE[@]}" exec -T "$service" "$@" 2>&1) || out=unknown
  out=${out%%$'\n'*}
  printf '%-12s %s\n' "$label" "${out:-unknown}" | cut -c1-140
}

# phpMyAdmin has no CLI version command: read the constant from its sources (path differs between 5.x and 6.x).
PMA_SCRIPT='v=$(grep -hoE "VERSION = .[0-9.]+" /var/www/html/libraries/classes/Version.php /var/www/html/src/Version.php 2>/dev/null | head -n1 | tr -dc "0-9."); echo "phpMyAdmin ${v:-unknown}, $(php -v | head -n1 | cut -d" " -f1-2)"'

# Versions of the software running inside the containers (informational; does not affect the exit code).
versions() {
  echo "Software inside the running containers:"
  report php-fpm app php-fpm -v
  report laravel app php artisan --version
  report nginx nginx nginx -v
  report mysql database mysqld --version
  report redis cache redis-server --version
  report phpmyadmin phpmyadmin sh -c "$PMA_SCRIPT"
}

# Prints one line per service and returns 1 if any service is not on the latest image.
verify() {
  local failed=0 service container image running localid remote status
  printf '%-12s %-45s %-16s %s\n' SERVICE IMAGE STATUS "RUNNING IMAGE ID"
  for service in $("${COMPOSE[@]}" config --services); do
    container=$("${COMPOSE[@]}" ps -q "$service")
    if [ -z "$container" ]; then
      printf '%-12s %-45s %-16s\n' "$service" - NOT-RUNNING
      failed=1
      continue
    fi
    image=$(docker inspect --format '{{.Config.Image}}' "$container")
    running=$(docker inspect --format '{{.Image}}' "$container")
    localid=$(docker image inspect --format '{{.Id}}' "$image" 2>/dev/null)
    # Digest the registry currently serves for this tag.
    remote=$(docker buildx imagetools inspect "$image" --format '{{.Manifest.Digest}}' 2>/dev/null)
    if [ -z "$remote" ]; then
      status=REGISTRY-ERROR
    elif [ "$running" != "$localid" ]; then
      status=STALE-CONTAINER   # newer image pulled, container not recreated
    elif ! docker image inspect --format '{{join .RepoDigests " "}}' "$image" | grep -q "$remote"; then
      status=OUTDATED          # registry has a newer image than the local one
    else
      status=UP-TO-DATE
    fi
    [ "$status" = UP-TO-DATE ] || failed=1
    printf '%-12s %-45s %-16s %s\n' "$service" "$image" "$status" "${running#sha256:}" | cut -c1-140
  done
  echo
  echo "App version: $("${COMPOSE[@]}" exec -T app cat version_sha1 2>/dev/null || echo unknown)"
  echo
  versions
  return $failed
}

case "${1:-update}" in
  update) update; verify ;;
  verify) verify ;;
  *) echo "Usage: $0 [update|verify]" >&2; exit 2 ;;
esac
