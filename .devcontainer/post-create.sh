#!/bin/bash
# Runs once when the dev container is created: installs dependencies, writes app/.env, builds assets
# and prepares the MySQL database.
set -euo pipefail
cd "$(dirname "$0")/../app"

# The vendor/node_modules volumes are created as root.
sudo chown "$(id -u):$(id -g)" vendor node_modules
# Claude Code's config/login lives in a named volume (see devcontainer.json "mounts") so it survives rebuilds
# and is shared by all checkouts.
sudo chown -R "$(id -u):$(id -g)" ~/.claude

if [ ! -f .env ]; then
  sed 's#^APP_URL=.*#APP_URL=http://localhost:8000#' ../.laravel.env.development.example > .env
  cat >> .env <<'EOF'

DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=playground
DB_USERNAME=playground
DB_PASSWORD=playground
EOF
fi
# `php artisan dusk` swaps this file in as .env for the duration of the run (APP_ENV=local).
cp ../.laravel.env.dusk.example .env.dusk.local
mkdir -p storage/testing && touch storage/testing/testing.sqlite

composer install --no-interaction
composer install --no-interaction --working-dir=../tools/php-cs-fixer
# Corepack (which provides yarn) asks before its first download, which blocks a non-interactive build.
export COREPACK_ENABLE_DOWNLOAD_PROMPT=0
yarn install --frozen-lockfile
yarn dev

grep -q '^APP_KEY=.\+' .env || php artisan key:generate

# Seed only on a fresh database; the dbdata volume survives container rebuilds.
if php artisan migrate:status >/dev/null 2>&1; then
  php artisan migrate --force
else
  php artisan migrate --seed --force
fi
