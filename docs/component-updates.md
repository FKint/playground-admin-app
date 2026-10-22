# Component update status (2026-10-22)

Where the dependency and runtime updates stand after the Laravel 13 upgrade, what is still open, what was deliberately not adopted, and the plan for the MySQL account passwords. Sources: `composer audit`, `yarn audit`, the lock files, Docker Hub tags.

## Done (in `master`)

| Component | Now |
|---|---|
| Laravel | 13.38 (was 10.48), PHPUnit 12.5, Dusk 8, dompdf 3, tinker 3, collision 8, `yajra/laravel-datatables-oracle` 13 |
| Composer advisories | none (`composer audit`; was 53) |
| JS | DataTables 3.1 / Buttons 4.1, pdfmake 0.3, `less-loader` 13, `cross-env` 10, `webpack-cli` 6; `vue-template-compiler` removed |
| JS advisories | 20 (5 low, 9 moderate, 6 high, 0 critical; was 185 with 20 critical), all in laravel-mix 6 build tooling |
| Docker asset build | node 24 LTS |
| PHP / base images | PHP 8.3.36, phpMyAdmin 5.2.3 |
| Database | `UNIQUE (year_id, id)` indexes for the composite foreign keys (#193) |

## In review

- **PHP 8.5.12, composer 2.10, nginx 1.30, MySQL 8.4 LTS, Redis 8, php-cs-fixer 3.95** (#200). Merge #202 first: on MySQL 8.4 a new database cannot be created from the current migrations.
- **MySQL 8.4 migrations** (#202): `restrict_fk_on_non_standard_key` is ON by default in 8.4, so the composite foreign keys need unique parent keys when they are created.
- **Re-enable foreign key enforcement for the PHPUnit SQLite connection** (#201): reverts the workaround from the Laravel 13 upgrade now that the unique indexes exist (one test data bug, `CloneYearTest`, surfaced).

## Not adopted, and why

| Component | Why |
|---|---|
| `webpack` stays at 5.94 | laravel-mix 6 nests its own copy: 5.95+ breaks its progress plugin, 5.107+ removed `lib/SizeFormatHelpers` |
| `webpack-cli` 7 | needs webpack >= 5.101 |
| `@babel/*` 8 | laravel-mix 6 passes a `helpers` option to plugin-transform-runtime that Babel 8 removed |
| jQuery 4 | Bootstrap 3's JS, bootstrap-datepicker and corejs-typeahead use APIs jQuery 4 removed |

All four are blocked by laravel-mix 6 or Bootstrap 3. Replacing laravel-mix (for example with Vite) would lift the first three and clear the remaining 20 `yarn audit` advisories; it is a larger migration and has not been started. Bootstrap 3 itself is end-of-life and is the reason for jQuery 3.

## Not verified yet

- An in-place data-directory upgrade of the real MySQL 8.2 database to 8.4 (only a fresh 8.4 data directory was tested).
- PHP 8.5 beyond what CircleCI covers (no PHP 8.5 binary in the sandbox where this was developed).

## Plan: stop using `mysql_native_password`

**Why.** MySQL 8.4 disables the `mysql_native_password` plugin by default and MySQL 9 removes it. The 8.4 bump (#200) therefore starts the server with `--mysql-native-password=ON`. That flag is a bridge: it keeps accounts that were created long ago working. If the production data directory was first created by an older MySQL (the project dates from 2017), the app user and root may still be on the old plugin; that is unverified and is what step 1 checks. The goal is to move every account to `caching_sha2_password`, the 8.4 default, and then delete the flag.

**What is already known.**
- The image entrypoint creates `MYSQL_USER` / `MYSQL_ROOT_PASSWORD` only when the data directory is first initialised, so existing accounts keep their old plugin across upgrades. New environments (a fresh dev volume) get the default plugin and need nothing.
- Passwords come from the environment: `DB_USERNAME` / `DB_PASSWORD` (app), `MYSQL_ROOT_PASSWORD` (production root), `DEV_DB_*` (dev app user, also used by phpMyAdmin via `PMA_USER` / `PMA_PASSWORD`).
- The clients are fine: PHP 8.3 PDO and mysqli (the same mysqlnd that Laravel and phpMyAdmin 5.2.3 use) connect to MySQL 8.4.13 with a `caching_sha2_password` account over plain TCP, on the first and second connection, with no TLS or extra options.

**Steps.**
1. **Inventory** (production, still on 8.2, read-only): `SELECT user, host, plugin FROM mysql.user;`. If every account is already `caching_sha2_password`, skip to step 5.
2. **Convert** each account on the old plugin, keeping the same password so no env file changes: `ALTER USER '<user>'@'<host>' IDENTIFIED WITH caching_sha2_password BY '<password>';`. Do root last. Feed the SQL through stdin (or `MYSQL_PWD` / a `--defaults-extra-file`), not as `-p<password>` on the command line, so the passwords stay out of the process list and shell history.
3. **Verify while still on 8.2:** the app loads and `php artisan migrate:status` works, phpMyAdmin logs in, a `mysqldump` with the app account succeeds. Reverting is another `ALTER USER ... IDENTIFIED WITH mysql_native_password ...`.
4. **Back up, then upgrade to 8.4** (#200). Keep `--mysql-native-password=ON` for this first deploy as a safety net (it is harmless), confirm everything works. MySQL 8.4 data directories cannot be downgraded: the rollback is the backup.
5. **Remove the flag** from both compose files in a second change, once `SELECT user, host, plugin FROM mysql.user;` shows no native accounts.

**What cannot be done from here.** Steps 1 to 4 need access to the production database. A small idempotent script (`deployment-scripts/`) that converts only the accounts still on the old plugin and prints before and after, plus the compose change for step 5, can be prepared in advance.
