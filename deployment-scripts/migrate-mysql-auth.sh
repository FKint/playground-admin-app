#!/bin/bash
# Migrates an existing MySQL account from mysql_native_password to caching_sha2_password.
#
# Usage: deployment-scripts/migrate-mysql-auth.sh [compose-file] [user] [host]
#   compose-file  default: docker/docker-compose.yml.production
#   user          default: the app account (MYSQL_USER of the database container)
#   host          default: %
#
# The account's current password (MYSQL_PASSWORD) is reused for the app account; for any other
# user, set NEW_PASSWORD in the environment. Credentials are read inside the container, so they
# never appear on the host's command line.
set -euo pipefail

COMPOSE_FILE="${1:-docker/docker-compose.yml.production}"
ACCOUNT_USER="${2:-}"
ACCOUNT_HOST="${3:-%}"

# Works with both Compose v2 ("docker compose") and v1 ("docker-compose").
if docker compose version >/dev/null 2>&1; then
  COMPOSE=(docker compose)
else
  COMPOSE=(docker-compose)
fi
COMPOSE+=(-f "$COMPOSE_FILE" --project-name playgroundadminapp --project-directory .)

"${COMPOSE[@]}" exec -T -e NEW_PASSWORD="${NEW_PASSWORD:-}" database sh -eu -s -- "$ACCOUNT_USER" "$ACCOUNT_HOST" <<'INNER'
user="${1:-$MYSQL_USER}"
host="$2"
password="${NEW_PASSWORD:-}"
if [ -z "$password" ]; then
  if [ "$user" != "$MYSQL_USER" ]; then
    echo "Set NEW_PASSWORD when migrating an account other than $MYSQL_USER" >&2
    exit 1
  fi
  password="$MYSQL_PASSWORD"
fi

# Escape for use inside single-quoted SQL string literals.
esc() { printf '%s' "$1" | sed -e "s/\\\\/\\\\\\\\/g" -e "s/'/\\\\'/g"; }
user_sql="$(esc "$user")"
host_sql="$(esc "$host")"
password_sql="$(esc "$password")"

export MYSQL_PWD="$MYSQL_ROOT_PASSWORD"
plugin() {
  mysql -uroot -N -B -e "SELECT plugin FROM mysql.user WHERE User='$user_sql' AND Host='$host_sql'"
}

current="$(plugin)"
if [ -z "$current" ]; then
  echo "Account '$user'@'$host' not found" >&2
  exit 1
fi
echo "Current plugin for '$user'@'$host': $current"
if [ "$current" = "caching_sha2_password" ]; then
  echo "Already using caching_sha2_password; nothing to do."
  exit 0
fi

mysql -uroot -e "ALTER USER '$user_sql'@'$host_sql' IDENTIFIED WITH caching_sha2_password BY '$password_sql'"
echo "New plugin for '$user'@'$host': $(plugin)"
INNER
