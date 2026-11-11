# Open component and maintenance work (2026-11-11)

Only what is still to do, each item checked against `master` on 2026-11-11. Everything else (Laravel 13, PHP 8.5.12, MySQL 8.4.13, dependency majors, index cleanups, the account migration script, the move from CircleCI to GitHub Actions, the webpack 5.104 and build-tooling security upgrade, the webpack-cli 7.3.1 bump, ...) is merged and not listed.

Production has run the current versions (PHP 8.5.12, MySQL 8.4, Redis 8) since 2026-11-04 with no detected issues. That confirms the in-place MySQL 8.2 to 8.4 upgrade and PHP 8.5.12 in production, so they are no longer open.

## 1. Stop using `mysql_native_password` (needs production access)

`master` still starts MySQL with `--mysql-native-password=ON` in `docker/docker-compose.yml.development` and `docker/docker-compose.yml.production`. MySQL 9 removes the plugin. New accounts already use `caching_sha2_password`, and `deployment-scripts/migrate-mysql-auth.sh` converts existing ones. The production accounts have not been converted yet, so the flag must stay until they are. What remains:

1. **Inventory** (production, now on 8.4): `SELECT user, host, plugin FROM mysql.user;`. Expect the existing accounts to be on `mysql_native_password`; if none is, skip to step 3.
2. **Convert** each such account, root last: `deployment-scripts/migrate-mysql-auth.sh [compose-file] [user] [host]` (set `NEW_PASSWORD` for accounts other than the app account). Passwords stay unchanged. Verify: the app loads, `php artisan migrate:status` works, phpMyAdmin logs in, `mysqldump` with the app account works.
3. **Remove `--mysql-native-password=ON`** from both compose files and deploy.

## 2. Replace laravel-mix 6 (not started)

`app/package.json` pins `webpack ~5.104.1` (also in `resolutions`) and still depends on `laravel-mix ^6.0.39`, `@babel/* ^7`, `jquery ^3`. These cannot move while laravel-mix 6 and Bootstrap 3 are in use:

| Held back | Blocker |
|---|---|
| `webpack` > 5.104 | laravel-mix 6 nests its own copy: 5.107+ removed `lib/SizeFormatHelpers` (5.95+ broke its progress plugin; the 5.104 pin is what `master` builds with) |
| `@babel/*` 8 | laravel-mix 6 passes a `helpers` option to plugin-transform-runtime that Babel 8 removed |
| jQuery 4 | Bootstrap 3's JS, bootstrap-datepicker and corejs-typeahead use APIs jQuery 4 removed (Bootstrap 3 is end-of-life) |

Replacing laravel-mix (for example with Vite) lifts the first three and should clear the 3 `yarn audit` advisories that remain (1 high `body-parser`, 1 moderate `minimatch`, 1 low `elliptic`, all reached through laravel-mix; measured 2026-11-10).

`webpack-cli` 7.3.1 is already merged and is not blocked by laravel-mix.

## 3. Code and docs TODOs

| Where | TODO |
|---|---|
| `app/app/Http/Controllers/Internal/RegistrationsController.php:187` | check other registrations of the child in the same week (e.g. through other families); the only one that could change behaviour |
| `app/app/Models/FamilyWeekRegistration.php:173` | verify that the `'registered' = false` default is needed |
| `readme.md:49` | "update the docker-compose script" (certbot hooks) |
| `app/tests/Browser/` (20 TODOs in 9 files) | row-scoped assertions (`within`) and more precise selectors, waiting instead of fixed delays, date picker in tests, visual diff in `LoginTest`, a test for today's registrations in `InternalDashboardTest` |
