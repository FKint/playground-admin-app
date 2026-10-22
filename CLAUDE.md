# Playground admin app (Laravel)

Laravel app in `app/`; Docker setup in `docker/`; CI in `.circleci/config.yml`. See `readme.md` for full dev/prod docs.

## Maintaining this file

Keep this file current. When you discover or verify something a future session would otherwise have to rediscover, add it here **before finishing the task**:

- working commands (tests, MySQL, Dusk, linting, build, deploy helpers) and the exact invocation that worked
- environment quirks (missing tools in the cloud sandbox, workarounds, required env vars)
- gotchas, failed approaches, and why

Rules: one line per fact, put it under the matching section below, mark anything unverified as `(untested)`, never record secrets, and delete entries that turn out to be wrong.

## Commands

Source of truth is `readme.md` / CI; entries marked `(untested)` have not been run in a cloud session.

### PHPUnit (SQLite, `DB_CONNECTION=sqlite_testing_phpunit` via `app/phpunit.xml`)
- Verified in sandbox (PHP 8.3.6, no Docker needed): `cd app && composer install --no-interaction && vendor/bin/phpunit` -> 78 tests OK, ~30s.
- Local via Docker: `docker compose -f docker/docker-compose.yml.dusk-testing --project-name dusk-tests --project-directory . up --build`, then `docker exec -it dusk-tests-app-container-1 ./vendor/phpunit/phpunit/phpunit` (untested)

### Dusk (browser tests)
- Verified in sandbox: `scripts/dusk-sandbox.sh` (optionally `--filter=LoginTest`) -> 23 tests OK, ~3 min. No Docker daemon exists in the sandbox (`docker` CLI only), so the script replaces the selenium container.
- What it does: builds assets if missing (`yarn install --frozen-lockfile && yarn dev`, needed or pages lack JS/CSS), copies `.laravel.env.dusk.example` to `app/.env`, adds `selenium`/`app-container` to `/etc/hosts` (hardcoded in `tests/DuskTestCase.php` and the env file), runs `php artisan serve --host=app-container`, and runs chromedriver on :4444 with `--url-base=/wd/hub`.
- Gotcha: the preinstalled `chromedriver` (v147) does not match Playwright's Chromium (v141) -> "session not created". The script downloads the matching driver from `storage.googleapis.com/chrome-for-testing-public/<version>/linux64/chromedriver-linux64.zip` (reachable through the proxy). Chromium is `/opt/pw-browsers/chromium`; chromedriver finds it via a `/usr/bin/google-chrome` symlink.
- CI/Docker equivalent (untested here): selenium container + `docker exec -it dusk-tests-app-container-1 php artisan dusk`; see the `test-dusk` job in `.circleci/config.yml`.
- `app/.env`, `public/js`, `public/css`, `vendor`, `node_modules` are generated/ignored; check `git status` before committing.

### MySQL
- Dev DB runs via `docker/docker-compose.yml.development`; PHPMyAdmin on host port 21001, app on 21000.
- `php artisan migrate:refresh --seed` builds schema + seed data (in the app container). Migrations must work on MySQL 8.4.
- Docker works in the sandbox if the daemon is started by hand: `(containerd &); (dockerd --containerd=/run/containerd/containerd.sock &)`, wait ~10s, then `docker pull mysql:8.4.13` works through the proxy. (`dockerd` alone times out waiting for its managed containerd.)
- Verified: `deployment-scripts/migrate-mysql-auth.sh <compose-file> [user] [host]` against `mysql:8.4.13` started with `--mysql-native-password=ON --authentication-policy=mysql_native_password` and a native account: migrates to `caching_sha2_password`, is idempotent, login works afterwards, and `NEW_PASSWORD` with quotes/backslashes works. It needs a compose file with a `database` service; MySQL's image entrypoint itself breaks on a `'` in `MYSQL_PASSWORD`.
- No `mysql`/`mysqld` binary on the sandbox host; use the container (`docker compose ... exec -T database mysql -uroot -p...`).

### Lint
- `composer install --working-dir=tools/php-cs-fixer && tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff app` (CI runs this; untested here)

## Sandbox notes
- Present: `php` 8.3.6 (project targets 8.5.12 in Docker, `^8.3` in composer.json; works fine), `composer`, `node`/`yarn`, `chromedriver`, Playwright Chromium. `docker` CLI exists; the daemon is not running by default (see MySQL section for starting it).
- `app/vendor` and `node_modules` are not installed by default; run `composer install` / `yarn install --frozen-lockfile` in `app/`.
