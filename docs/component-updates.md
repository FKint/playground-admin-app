# Open component and maintenance work (2026-10-22)

Only what is still to do, each item checked against `master` on 2026-10-22. Everything else (Laravel 13, PHP 8.5.12, MySQL 8.4, dependency majors, index cleanups, the account migration script, ...) is merged and not listed.

## 1. Stop using `mysql_native_password` (needs production access)

`master` still starts MySQL with `--mysql-native-password=ON` in `docker/docker-compose.yml.development` and `docker/docker-compose.yml.production`. MySQL 9 removes the plugin. New accounts already use `caching_sha2_password`, and `deployment-scripts/migrate-mysql-auth.sh` converts existing ones, so what remains is running it:

1. **Inventory** (production, still on 8.2): `SELECT user, host, plugin FROM mysql.user;`. If no account is on `mysql_native_password`, skip to step 4.
2. **Convert** each such account, root last: `deployment-scripts/migrate-mysql-auth.sh [compose-file] [user] [host]` (set `NEW_PASSWORD` for accounts other than the app account). Passwords stay unchanged.
3. **Verify:** the app loads, `php artisan migrate:status` works, phpMyAdmin logs in, `mysqldump` with the app account works. Back up before the 8.2 to 8.4 upgrade (8.4 data directories cannot be downgraded); keep the flag for that first deploy.
4. **Remove `--mysql-native-password=ON`** from both compose files.

## 2. Verify the production upgrade path

- In-place upgrade of the real MySQL 8.2 data directory to 8.4: only a fresh 8.4 data directory has been tested.
- PHP 8.5.12 beyond CircleCI: the sandbox has PHP 8.3.6 only.

## 3. Replace laravel-mix 6 (not started)

`app/package.json` pins `webpack ~5.94.0` (also in `resolutions`) and still depends on `laravel-mix ^6.0.39`, `@babel/* ^7`, `jquery ^3`. These cannot move while laravel-mix 6 and Bootstrap 3 are in use:

| Held back | Blocker |
|---|---|
| `webpack` > 5.94 | laravel-mix 6 nests its own copy: 5.95+ breaks its progress plugin, 5.107+ removed `lib/SizeFormatHelpers` |
| `webpack-cli` 7 | needs webpack >= 5.101 |
| `@babel/*` 8 | laravel-mix 6 passes a `helpers` option to plugin-transform-runtime that Babel 8 removed |
| jQuery 4 | Bootstrap 3's JS, bootstrap-datepicker and corejs-typeahead use APIs jQuery 4 removed (Bootstrap 3 is end-of-life) |

Replacing laravel-mix (for example with Vite) lifts the first three and should clear the `yarn audit` advisories that remain in the build tooling (20 when last measured; not re-measured, `yarn audit` could not run here).

## 4. Code and docs TODOs

| Where | TODO |
|---|---|
| `app/app/Http/Controllers/Internal/RegistrationsController.php:187` | check other registrations of the child in the same week (e.g. through other families); the only one that could change behaviour |
| `app/app/Models/FamilyWeekRegistration.php:173` | verify that the `'registered' = false` default is needed |
| `readme.md:34` | "update the docker-compose script" (certbot hooks) |
| `app/tests/Browser/` (20 TODOs in 9 files) | row-scoped assertions (`within`) and more precise selectors, waiting instead of fixed delays, date picker in tests, visual diff in `LoginTest`, a test for today's registrations in `InternalDashboardTest` |
