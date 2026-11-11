# Playground admin app (Laravel)

Laravel app in `app/`; Docker setup in `docker/`; CI in `.github/workflows/ci.yml`. See `readme.md` for full dev/prod docs.

## Maintaining this file

Keep this file current. When you discover or verify something a future session would otherwise have to rediscover, add it here **before finishing the task**:

- working commands (tests, MySQL, Dusk, linting, build, deploy helpers) and the exact invocation that worked
- environment quirks (missing tools in the cloud sandbox, workarounds, required env vars)
- gotchas, failed approaches, and why

Rules: one line per fact, put it under the matching section below, mark anything unverified as `(untested)`, never record secrets, and delete entries that turn out to be wrong.

## Commands

Source of truth is `readme.md` / CI; entries marked `(untested)` have not been run in a cloud session.

### PHPUnit (SQLite, `DB_CONNECTION=sqlite_testing_phpunit` via `app/phpunit.xml`)
- `composer.json` requires PHP `^8.5` (Symfony 8), but the sandbox PHP is 8.3.6: `composer install` needs `--ignore-platform-req=php` and PHPUnit must run in Docker. Verified: start the daemon (see MySQL section), `docker pull php:8.5.12-cli`, then `docker run --rm -v $PWD/app:/app -w /app php:8.5.12-cli vendor/bin/phpunit` -> 78 tests OK, ~30s. The container has no network, so install `vendor` on the host first. `composer:2.10.3` image: `docker cp` its `/usr/bin/composer` out if you need composer in a container.
- Local via Docker: `docker compose -f docker/docker-compose.yml.dusk-testing --project-name dusk-tests --project-directory . up --build`, then `docker exec -it dusk-tests-app-container-1 ./vendor/phpunit/phpunit/phpunit` (untested)

### Dusk (browser tests)
- Verified in sandbox: Docker daemon running (see MySQL section), `vendor` installed on the host, then `scripts/dusk-sandbox.sh` (optionally `--filter=LoginTest`) -> 30 tests OK, ~3 min. The script replaces the selenium container; because the sandbox PHP (8.3) is too old, it runs `artisan serve` and `artisan dusk` in `php:8.5.12-cli` with `--network host`. (The previous host-PHP path is untested since the PHP `^8.5` bump.)
- What it does: builds assets if missing (`yarn install --frozen-lockfile && yarn build`, needed or pages lack JS/CSS), copies `.laravel.env.dusk.example` to `app/.env`, adds `selenium`/`app-container` to `/etc/hosts` (hardcoded in `tests/DuskTestCase.php` and the env file), runs `php artisan serve --host=app-container`, and runs chromedriver on :4444 with `--url-base=/wd/hub`.
- Gotcha: the preinstalled `chromedriver` (v147) does not match Playwright's Chromium (v141) -> "session not created". The script downloads the matching driver from `storage.googleapis.com/chrome-for-testing-public/<version>/linux64/chromedriver-linux64.zip` (reachable through the proxy). Chromium is `/opt/pw-browsers/chromium`; chromedriver finds it via a `/usr/bin/google-chrome` symlink.
- Export buttons (PDF/CSV/Excel) are client-side DataTables buttons; `tests/Browser/ExportButtonsTest.php` clicks each and checks the generated blob (intercepts `URL.createObjectURL`, so it works with a remote selenium too). DataTables 3: `:visible.export` is an invalid column selector (use a function), the search box is `div.dt-search` (use `table.search()`), and `dom: 'Blfrtip'` is required for buttons to render.
- Verified in the dev container (selenium + `app-container` already resolve, no docker CLI): `cd app && (php artisan serve --host=0.0.0.0 --port=8000 &) ; php artisan dusk` -> 30 tests OK, ~2 min. Check `curl http://selenium:4444/wd/hub/status` first; kill the server afterwards.
- CI/Docker equivalent (untested here): selenium container + `docker exec -it dusk-tests-app-container-1 php artisan dusk`; see the `test-dusk` job in `.github/workflows/ci.yml`.
- `app/.env`, `public/build`, `vendor`, `node_modules` are generated/ignored; check `git status` before committing.

### MySQL
- Dev DB runs via `docker/docker-compose.yml.development`; PHPMyAdmin on host port 21001, app on 21000.
- `php artisan migrate:refresh --seed` builds schema + seed data (in the app container). Migrations must work on MySQL 8.4.
- Docker works in the sandbox if the daemon is started by hand: `(containerd &); (dockerd --containerd=/run/containerd/containerd.sock &)`, wait ~10s, then `docker pull mysql:8.4.13` works through the proxy. (`dockerd` alone times out waiting for its managed containerd.)
- Verified: `deployment-scripts/migrate-mysql-auth.sh <compose-file> [user] [host]` against `mysql:8.4.13` started with `--mysql-native-password=ON --authentication-policy=mysql_native_password` and a native account: migrates to `caching_sha2_password`, is idempotent, login works afterwards, and `NEW_PASSWORD` with quotes/backslashes works. It needs a compose file with a `database` service; MySQL's image entrypoint itself breaks on a `'` in `MYSQL_PASSWORD`.
- No `mysql`/`mysqld` binary on the sandbox host; use the container (`docker compose ... exec -T database mysql -uroot -p...`).

### Dev container
- `.devcontainer/` (compose: app + mysql 8.4.13 + phpmyadmin + selenium), setup in `.devcontainer/post-create.sh`; the app service has network alias `app-container` so Dusk works unchanged. Don't set `DB_*` as container env vars: they would override the Dusk `.env` swap. Verified: container builds and Dusk passes inside it (see Dusk section). `.devcontainer/devcontainer-lock.json` pins the feature versions; commit it when it changes.
- Gotcha: never give the compose file a fixed `name:`. Dev Containers finds containers by compose project + service, so a shared name made a second checkout attach to the first checkout's container (wrong files, broken git in worktrees). `initialize.sh` (host-side) writes `.devcontainer/.env` so the checkout and the shared `.git` are mounted at their host paths; a worktree's `.git` file holds absolute host paths. (untested in a container: written without Docker access)

### Lint
- `composer install --working-dir=tools/php-cs-fixer && tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff app` (CI runs this; untested here)

## CI
- GitHub Actions (`.github/workflows/ci.yml`): lint, build, test, test-dusk, publish (master push only). Publish uses the "Docker Publishing" environment (job has `environment: Docker Publishing`), which must hold secrets `DOCKER_HUB_USERNAME` and `DOCKER_HUB_PASSWORD` (if they are environment variables rather than secrets, use `vars.` instead of `secrets.`). (untested until first run on GitHub)
- CI images are built via `docker/docker-bake.hcl` (`docker buildx bake -f docker/docker-bake.hcl test|prod`); `build-test` and `build-prod` jobs run in parallel, separate GHA cache scopes (`docker-test`/`docker-prod`, each reads both). Version files (`version_sha1` etc.) are NOT written into `app/` in CI: they come from `VERSION_*` build args into a tiny last `version` Dockerfile stage, so a new commit does not bust the composer/COPY layers; empty args (local builds) create no files. The source stage runs `composer dump-autoload` (not `install`) after `COPY app .`. `bake --print` validates the file locally; real builds only run in CI.
- The main checkout's local `.git` can be a broken worktree pointer (host paths); use the GitHub API (`gh api`) for remote info (default branch is `master`).

## Sandbox notes
- macOS Claude sandbox: `gh`/Go TLS fails (`x509: OSStatus -26276`) unless `sandbox.enableWeakerNetworkIsolation: true` is set (see untracked `.claude/settings.local.json`); also list github/registry hosts in `sandbox.network.allowedDomains`.
- macOS sandbox + yarn 1: the proxy randomly truncates registry JSON (~60KB) -> "malformed response from registry"; and writes to `.idea/` dirs are denied (EPERM when linking `iconv-lite`). Workaround that worked: run a patched copy of yarn's `cli.js` that retries when `params.json` body is a string, and `find <yarn cache>/v6 -name .idea -prune -exec rm -rf {} +` before install. Plain `yarn install` works outside the sandbox.
- Present: `php` 8.3.6 (project requires `^8.5` and runs 8.5.12 in Docker; the sandbox PHP is too old to run the app), `composer`, `node`/`yarn`, `chromedriver`, Playwright Chromium. `docker` CLI exists; the daemon is not running by default (see MySQL section for starting it; start containerd and dockerd once only, a second containerd locks boltdb and dockerd fails with a stale `/var/run/docker.pid`: `pkill containerd; pkill dockerd; rm -f /var/run/docker.pid /var/run/docker.sock` and start again).
- `app/vendor` and `node_modules` are not installed by default; run `composer install` / `yarn install --frozen-lockfile` in `app/`.

## Frontend build (Vite)
- Vite 8 + `laravel-vite-plugin` (`app/vite.config.js`); `yarn build` writes `public/build` + `manifest.json`, `yarn watch` = `vite build --watch`, `yarn dev` = Vite dev server (`public/hot`, HMR). Verified: build ~1.5s, Dusk 30 tests OK; `yarn dev` starts and serves the entry (not tried in a browser).
- The layout loads the bundle with `@vite('resources/assets/js/app.js')` (module script; CSS comes from the Less import in `app.js`). Module scripts are deferred, so the inline scripts in the views are `<script type="module">` too (they then run after the bundle, in document order).
- Gotchas of module scripts: strict mode (a server-rendered `08` is a syntax error: use `new Date('{{ $date->format('Y-m-d') }}T00:00:00')`, not `{{ format('m') }}` as a number) and no implicit globals: a function used from another view's script must be set on `window` (`window.formatPrice`, `closeAllModals`, `show*Modal`).
- jQuery plugins (DataTables 3, bootstrap-datepicker, typeahead) must load after `window.jQuery` is set: `globals.js` is imported first and the plugins are ES imports; `require()` of `datatables.net-buttons` broke (`undefined.Buttons`).
- Less: no `~` prefix for package imports (`bootstrap-less/...`); `@icon-font-path` is `bootstrap-less/fonts/`. The Dutch DataTables language JSON is imported into the bundle (no copy step).
- Dusk writes browser console logs to `app/tests/Browser/console/*.log`; grep them for `Uncaught` after a failing run.
