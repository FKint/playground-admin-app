#!/bin/bash
# Run the Dusk suite in a cloud sandbox (Docker daemon required: selenium and, if the host PHP is too old, PHP 8.5 run in containers).
# Starts the CI selenium image (host network) plus `php artisan serve`, then runs Dusk.
# The app needs PHP >= 8.5: if the host PHP is older, serve and Dusk run in a php:8.5.12-cli container
# with host networking (the Docker daemon must be running and `vendor` installed on the host).
# Usage: scripts/dusk-sandbox.sh [dusk args, e.g. --filter=LoginTest]
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT/app"

# Same selenium image as CI (single source of truth: the pin in ci.yml), so screenshots match the visual baselines.
IMAGE=$(grep -oE 'selenium/standalone-chrome:[^ ]+' "$ROOT/.github/workflows/ci.yml" | head -1)

# DuskTestCase hardcodes http://selenium:4444 and the env file uses http://app-container:8000.
grep -q 'app-container' /etc/hosts || echo "127.0.0.1 selenium app-container" >> /etc/hosts

[ -d vendor ] || composer install --no-interaction --ignore-platform-req=php
[ -d node_modules ] || yarn install --frozen-lockfile
[ -f public/build/manifest.json ] || yarn build
cp ../.laravel.env.dusk.example .env

docker rm -f dusk-selenium >/dev/null 2>&1 || true
docker run -d --name dusk-selenium --network host --shm-size=2g "$IMAGE" >/dev/null
trap 'docker rm -f dusk-selenium >/dev/null 2>&1 || true; kill ${SERVE_PID:-} 2>/dev/null || true' EXIT
for i in $(seq 1 60); do curl -sf http://selenium:4444/wd/hub/status >/dev/null && break; [ "$i" = 60 ] && { echo "selenium did not become ready" >&2; docker logs dusk-selenium 2>&1 | tail -20 >&2; exit 1; }; sleep 1; done

if php -r 'exit(PHP_VERSION_ID >= 80500 ? 0 : 1);'; then
  php artisan serve --host=app-container --port=8000 >/tmp/artisan-serve.log 2>&1 &
  SERVE_PID=$!
  sleep 3
  php artisan dusk "$@"
else
  docker run --rm --network host -e UPDATE_VISUAL_BASELINES --add-host app-container:127.0.0.1 --add-host selenium:127.0.0.1 \
    -v "$PWD:/app" -w /app php:8.5.12-cli \
    sh -c 'php artisan serve --host=0.0.0.0 --port=8000 >/tmp/serve.log 2>&1 & sleep 3; php artisan dusk "$@"' sh "$@"
fi
