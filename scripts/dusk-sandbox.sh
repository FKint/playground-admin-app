#!/bin/bash
# Run the Dusk suite in a cloud sandbox without a selenium container.
# Starts a chromedriver matching the installed Chromium plus `php artisan serve`, then runs Dusk.
# The app needs PHP >= 8.5: if the host PHP is older, serve and Dusk run in a php:8.5.12-cli container
# with host networking (the Docker daemon must be running and `vendor` installed on the host).
# Usage: scripts/dusk-sandbox.sh [dusk args, e.g. --filter=LoginTest]
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT/app"

CHROME=${CHROME:-/opt/pw-browsers/chromium}
VER=$("$CHROME" --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+')
DRV_DIR=${DRV_DIR:-/tmp/chromedriver-$VER}

# DuskTestCase hardcodes http://selenium:4444 and the env file uses http://app-container:8000.
grep -q 'app-container' /etc/hosts || echo "127.0.0.1 selenium app-container" >> /etc/hosts
# chromedriver looks for the browser at these paths.
ln -sf "$CHROME" /usr/bin/google-chrome

if [ ! -x "$DRV_DIR/chromedriver-linux64/chromedriver" ]; then
  mkdir -p "$DRV_DIR"
  curl -sSf -o "$DRV_DIR/cd.zip" "https://storage.googleapis.com/chrome-for-testing-public/$VER/linux64/chromedriver-linux64.zip"
  unzip -oq "$DRV_DIR/cd.zip" -d "$DRV_DIR"
fi

[ -d vendor ] || composer install --no-interaction --ignore-platform-req=php
[ -d node_modules ] || yarn install --frozen-lockfile
[ -f public/build/manifest.json ] || yarn build
cp ../.laravel.env.dusk.example .env

"$DRV_DIR/chromedriver-linux64/chromedriver" --port=4444 --url-base=/wd/hub --allowed-ips= --allowed-origins='*' >/tmp/chromedriver.log 2>&1 &
CD_PID=$!
trap 'kill $CD_PID ${SERVE_PID:-} 2>/dev/null || true' EXIT

if php -r 'exit(PHP_VERSION_ID >= 80500 ? 0 : 1);'; then
  php artisan serve --host=app-container --port=8000 >/tmp/artisan-serve.log 2>&1 &
  SERVE_PID=$!
  sleep 3
  php artisan dusk "$@"
else
  docker run --rm --network host --add-host app-container:127.0.0.1 --add-host selenium:127.0.0.1 \
    -v "$PWD:/app" -w /app php:8.5.12-cli \
    sh -c 'php artisan serve --host=0.0.0.0 --port=8000 >/tmp/serve.log 2>&1 & sleep 3; php artisan dusk "$@"' sh "$@"
fi
