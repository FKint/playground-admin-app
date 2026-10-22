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
| Runtime / images (#200) | PHP 8.5.12 (Dockerfile and CircleCI), composer 2.10.3, nginx 1.30.6, MySQL 8.4.13 LTS, Redis 8.10.2, phpMyAdmin 5.2.3 |
| Linter | php-cs-fixer 3.95 (CI job on `cimg/php:8.5.12`) |
| Database indexes | `UNIQUE (year_id, id)` for the composite foreign keys (#193, #203); redundant indexes dropped (#203, #207) |
| MySQL 8.4 migrations (#202) | a new database can be created under 8.4's default `restrict_fk_on_non_standard_key` |
| PHPUnit SQLite connection (#201) | foreign key enforcement re-enabled; `CloneYearTest` data bug fixed |
| MySQL authentication (#204, #209) | compose files start MySQL with `--authentication-policy=caching_sha2_password` (plus `--mysql-native-password=ON`); `deployment-scripts/migrate-mysql-auth.sh` converts an existing account (documented in the readme) |
| Deploy helper (#205) | `deploy_prod.sh update` / `verify` check that running containers use the latest images |
| Dev tooling (#208) | `CLAUDE.md` and `scripts/dusk-sandbox.sh` (Dusk without Docker) |

## Not adopted, and why

| Component | Why |
|---|---|
| `webpack` stays at 5.94 | laravel-mix 6 nests its own copy: 5.95+ breaks its progress plugin, 5.107+ removed `lib/SizeFormatHelpers` |
| `webpack-cli` 7 | needs webpack >= 5.101 |
| `@babel/*` 8 | laravel-mix 6 passes a `helpers` option to plugin-transform-runtime that Babel 8 removed |
| jQuery 4 | Bootstrap 3's JS, bootstrap-datepicker and corejs-typeahead use APIs jQuery 4 removed |

All four are blocked by laravel-mix 6 or Bootstrap 3. Replacing laravel-mix (for example with Vite) would lift the first three and clear the remaining 20 `yarn audit` advisories; it is a larger migration and has not been started. Bootstrap 3 itself is end-of-life and is the reason for jQuery 3.

## Not verified yet

- An in-place data-directory upgrade of the real MySQL 8.2 database to 8.4 (only a fresh 8.4 data directory was tested). 8.4 data directories cannot be downgraded: take a backup first.
- PHP 8.5 beyond what CircleCI covers (no PHP 8.5 binary in the sandbox where this was developed).
- Whether production still has `mysql_native_password` accounts (step 1 below needs the production database).

## Plan: stop using `mysql_native_password`

**Why.** MySQL 8.4 disables the `mysql_native_password` plugin by default and MySQL 9 removes it. The compose files therefore start the server with `--mysql-native-password=ON`. That flag is a bridge: it keeps accounts that were created long ago working. If the production data directory was first created by an older MySQL (the project dates from 2017), the app user and root may still be on the old plugin; that is unverified and is what step 1 checks. The goal is to move every account to `caching_sha2_password`, the 8.4 default, and then delete the flag.

**Already in place.**
- New accounts use `caching_sha2_password` (`--authentication-policy`, #204). The image entrypoint creates `MYSQL_USER` / `MYSQL_ROOT_PASSWORD` only when the data directory is first initialised, so existing accounts keep their old plugin across upgrades; a fresh dev volume needs nothing.
- `deployment-scripts/migrate-mysql-auth.sh [compose-file] [user] [host]` (#209) converts an account with the same password, is idempotent, and reads credentials inside the container. Verified against `mysql:8.4.13`: converts, can be re-run, login works afterwards, passwords with quotes/backslashes work. Set `NEW_PASSWORD` for accounts other than the app account (e.g. root).
- Clients are fine: PHP 8.3 PDO and mysqli (same mysqlnd as Laravel and phpMyAdmin 5.2.3) connect to MySQL 8.4.13 with a `caching_sha2_password` account over plain TCP, with no TLS or extra options.

**Steps remaining (production access needed).**
1. **Inventory** (production): `SELECT user, host, plugin FROM mysql.user;`. If no account is on `mysql_native_password`, skip to step 4.
2. **Convert** each such account with the script (or `ALTER USER ... IDENTIFIED WITH caching_sha2_password BY '<password>'`), root last. Passwords stay unchanged, so no env file changes. Revert with `IDENTIFIED WITH mysql_native_password`.
3. **Verify:** the app loads and `php artisan migrate:status` works, phpMyAdmin logs in, `mysqldump` with the app account succeeds. Back up before the 8.2 to 8.4 upgrade; keep the flag for that first deploy as a safety net.
4. **Remove `--mysql-native-password=ON`** from both compose files in a second change, once the inventory shows no native accounts.

## Open work

### Known gaps
- Replace laravel-mix 6 (for example with Vite): unblocks webpack 5.95+, `webpack-cli` 7, Babel 8 and the remaining 20 `yarn audit` advisories. Not started. Bootstrap 3 (end-of-life) is the reason for jQuery 3.
- Production steps of the MySQL plan above, and the 8.2 to 8.4 upgrade.
- `readme.md` (Certbot section) still says "TODO: update the docker-compose script" for the certbot pre/post hooks.

### Code TODOs
Only the first two touch application code:

| Where | TODO |
|---|---|
| `app/app/Http/Controllers/Internal/RegistrationsController.php:187` | check other registrations of the child in the same week (e.g. through other families); the only one that could change behaviour |
| `app/app/Models/FamilyWeekRegistration.php:173` | verify that the `'registered' = false` default is needed |
| `app/tests/Browser/` (Dusk, 20 TODOs in 9 files) | more precise selectors and row-scoped assertions (`within`), waiting instead of fixed delays, a date picker in tests, a visual diff in `LoginTest`, a test for today's registrations in `InternalDashboardTest` |
