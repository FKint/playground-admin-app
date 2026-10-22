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
- `cd app && vendor/bin/phpunit` (untested)
- Local via Docker: `docker compose -f docker/docker-compose.yml.dusk-testing --project-name dusk-tests --project-directory . up --build`, then `docker exec -it dusk-tests-app-container-1 ./vendor/phpunit/phpunit/phpunit` (untested)

### Dusk (browser tests)
- Needs a selenium container (`selenium/standalone-chrome`) and `php artisan serve --host=app-container`; see `docker/docker-compose.yml.dusk-testing` and the `test-dusk` job in `.circleci/config.yml`.
- Run: `docker exec -it dusk-tests-app-container-1 php artisan dusk` (untested)

### MySQL
- Dev DB runs via `docker/docker-compose.yml.development`; PHPMyAdmin on host port 21001, app on 21000.
- `php artisan migrate:refresh --seed` builds schema + seed data (in the app container). Migrations must work on MySQL 8.4.
- No `mysql`/`mysqld` binary in the cloud sandbox as of first check; record here how (if) it was made to work.

### Lint
- `composer install --working-dir=tools/php-cs-fixer && tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff app` (CI runs this; untested here)

## Sandbox notes
- Present: `php`, `composer`, `docker`, `node`, `yarn`. `app/vendor` not installed by default. Docker daemon availability unverified.
