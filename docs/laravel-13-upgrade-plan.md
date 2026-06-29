# Upgrade Lion Roaring: Laravel 8 → 13

## Context

The application is currently on **Laravel 8.83 / PHP `^7.3|^8.0`** using the **legacy
skeleton** (`app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`,
classic `bootstrap/app.php`, providers in `config/app.php`). The goal is to reach the
**latest Laravel 13** (released Feb 2026, PHP 8.3+) with **no behavioral change** — the
public `/api/v3` surface consumed by the Flutter app (`lion-roaring-app`) must keep working
byte-for-byte, and the web admin must keep working as-is.

This is a **5-major-version jump** (8→9→10→11→12→13). Laravel upgrade guides are written one
major at a time, so the work is five sequential migrations. The hard part is 8→12; **12→13 is
deliberately a minor upgrade**.

**Decisions made with the user:**
- **Approach:** incremental, manual — one major per step, test the API after each, commit between hops.
- **Structure:** migrate to the modern **L11+ streamlined skeleton** (done at the 10→11 hop).
- **Runtime:** **PHP 8.3** in dev (MAMP has 8.3.14; XAMPP is only 8.2) and production. Pin
  `laravel/framework: "^13.0 <13.3"` to avoid the Symfony 8 / PHP 8.4 requirement in patch ≥13.3.
  Revisit the pin once production reaches PHP 8.4.

**Critical constraint — the mobile API uses Laravel Passport (OAuth2), not Sanctum.** Auth flow:
`POST /register` → `POST /login` (emails OTP) → `POST /verify-otp` (returns
`$user->createToken('authToken')->accessToken`). All protected routes use `['auth:api','user']`.
Existing `oauth_access_tokens` rows and the signing keys (`storage/oauth-private.key` /
`oauth-public.key`) **must be preserved** so already-issued app tokens stay valid.

---

## Current State (verified)

| Area | Finding |
|------|---------|
| Framework | `laravel/framework` `v8.83.29`; `php` constraint `^7.3\|^8.0` (lock resolved under PHP 8.x) |
| Skeleton | Legacy — `app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`, classic `bootstrap/app.php`, providers in `config/app.php` |
| API auth | **Passport** OAuth2, guard `api` driver `passport` in `config/auth.php`; no `Passport::routes()` call (relies on personal access tokens) |
| API prefix | `/api/v3` in `routes/api.php`; global `userActivity` middleware; `['auth:api','user']` on protected groups; 45 API controllers, **no API Resource classes** (manual JSON) |
| Middleware | ~15 custom aliases in `app/Http/Kernel.php` (`user`, `member.sovereign`, `admin`, `super_admin`, `agreement.signed`, `userActivity`, `api.member.access`, etc.) |
| Push | `kreait/laravel-firebase` ^4.2 (FCM); broadcasting driver = `null` |
| Build | **Laravel Mix 6** (not Vite) — keep Mix to minimize scope |
| Tests | PHPUnit 9 (`phpunit.xml`, `tests/Feature`, `tests/Unit`); plus `*.spec.ts` e2e tests (login/register/mail) — usable as API verification |
| Models | Clean: no `$dates`, class-based factories, `EventServiceProvider` uses `$listen` array — none of these block the upgrade |
| Constraint drift | `intervention/image ^3`, `telescope ^5`, `stripe ^13`, `doctrine/dbal ^3` already ahead of L8 → reconcile all constraints together, not piecemeal |

---

## Target State

- `php: "^8.3"`, `laravel/framework: "^13.0 <13.3"`, `phpunit/phpunit: "^12.0"`
- Modern skeleton: `bootstrap/app.php` (`withRouting`/`withMiddleware`/`withExceptions`),
  `bootstrap/providers.php`; `Kernel.php` + `Handler.php` removed
- First-party peers: **Passport 13, Sanctum 4, Telescope (matching), Tinker (matching)**
- `spatie/laravel-permission ^6`, replaced/maintained HTML form package, `spatie/laravel-ignition` (dev)
- `minimum-stability` returned to `stable` once all deps have stable releases

---

## Strategy & Sequencing

Work on a dedicated branch off `app-update`. **Commit after each hop only when the test suite +
API smoke test pass.** Run everything on **PHP 8.3 (MAMP)** from the start — the lock already
resolves under PHP 8.x, so moving the dev runtime to 8.3 first is safe and removes a variable.

### Step 0 — Prerequisites & safety net (before any version bump)
- Confirm production PHP can reach **8.3** (8.4 preferred). This is a hard gate for go-live.
- Point dev runtime at MAMP PHP 8.3.14; back up DB and the **Passport keys** in `storage/`.
- Build a **characterization safety net** for the API contract before changing anything:
  - Write Pest/PHPUnit feature tests covering `register → login → verify-otp → token`, one
    `auth:api` protected GET (e.g. profile), FCM token update, and a couple of public `/cms`
    endpoints. Assert **status code, `{status,message,data,token}` shape, and date formats**.
  - Confirm whether the `tests/*.spec.ts` Playwright tests run against a local server; if so,
    wire them as a second verification layer.
- Capture baseline JSON responses for ~10 representative endpoints (fixtures to diff against).

### Step 1 — Laravel 8 → 9
- `laravel/framework: ^9.0`, bump first-party (`telescope`, `tinker`, `sanctum`, `passport`) to L9-compatible.
- **Remove `fruitcake/laravel-cors`**; swap global middleware to `Illuminate\Http\Middleware\HandleCors::class` in `app/Http/Kernel.php`. Keep `config/cors.php` as-is (open `*` origins must stay).
- **Replace `facade/ignition` → `spatie/laravel-ignition`** (dev).
- Symfony 6 + Symfony Mailer (SwiftMailer removed): review `app/Mail/*` and `config/mail.php`. Flysystem 3 for storage.
- `passport` → v11 (it auto-registers routes; the missing `Passport::routes()` call is now correct). Verify personal-access client + keys intact; **do not regenerate keys**.
- Grep & fix: `dispatchNow(` → `dispatchSync(`.

### Step 2 — Laravel 9 → 10
- `laravel/framework: ^10.0`; PHP floor 8.1. Bump first-party + `nunomaduro/collision ^7`, Monolog 3.
- `doctrine/dbal ^3` still fine here. `predis`/Redis facade unaffected (not a direct dep).
- Mostly type-hint/skeleton changes — keeping the legacy skeleton means minimal churn this hop.

### Step 3 — Laravel 10 → 11  *(largest hop — includes skeleton migration)*
- `laravel/framework: ^11.0`; PHP floor 8.2. `nunomaduro/collision ^8`.
- **Skeleton migration to the streamlined structure:**
  - New `bootstrap/app.php` using `withRouting(web:, api:, commands:, channels:, apiPrefix: 'api')`, `withMiddleware(...)`, `withExceptions(...)`.
  - New `bootstrap/providers.php` listing the app providers currently in `config/app.php`
    (`AppServiceProvider`, `AuthServiceProvider`, `EventServiceProvider`, `RouteServiceProvider`,
    `TelescopeServiceProvider`, plus package providers as needed).
  - **Port all ~15 middleware aliases** from `app/Http/Kernel.php` into `withMiddleware(fn($m) => $m->alias([...]))`, and global middleware (`TrustProxies`, `HandleCors`, `PreventRequestsDuringMaintenance`, `ValidatePostSize`, `TrimStrings`, `ConvertEmptyStringsToNull`) into the appropriate group. Preserve the `api` group (`throttle:api`, `SubstituteBindings`) and the `userActivity` middleware on `/api/v3`.
  - **Port the scheduler** from `app/Console/Kernel.php::schedule()` into `routes/console.php` (or `withSchedule()` in `bootstrap/app.php`) — read the existing `schedule()` body and reproduce it exactly.
  - **Port exception handling** from `app/Exceptions/Handler.php` into `withExceptions(...)`. Preserve any custom API JSON error rendering so `{status:false,message}` error shape is unchanged.
  - Delete `app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`; remove the singleton bindings from `bootstrap/app.php`.
  - Trim `config/app.php` providers array (now driven by `bootstrap/providers.php`).
- **Passport → v12:** no longer auto-loads migrations — run `php artisan vendor:publish --tag=passport-migrations` and confirm the existing `oauth_*` tables are not duplicated/re-created. Keep keys.
- **Sanctum → v4:** publish its migration if enabling; it is currently unused, so a clean bump is low-risk.
- **`spatie/laravel-permission → ^6`:** republish `config/permission.php`, review v6 migration/breaking changes (wildcard perms, `model_morph_key`); **preserve existing roles/permissions data** — no destructive migration.
- `kreait/laravel-firebase → ^5`. `doctrine/dbal` no longer required by core schema — remove unless still used directly or by `maatwebsite/excel`.

### Step 4 — Laravel 11 → 12
- `laravel/framework: ^12.0`. Minor upgrade; mostly dependency bumps + small guide items.
- Align first-party (Telescope, Tinker), `spatie/*`, `knuckleswtf/scribe`, `stevebauman/location`, `barryvdh/laravel-dompdf ^3`, `maatwebsite/excel` (confirm L12-compatible release), `stripe/stripe-php` (latest).

### Step 5 — Laravel 12 → 13
- `laravel/framework: "^13.0 <13.3"`, `phpunit/phpunit: ^12`, `php: ^8.3`.
- Apply the three L13 guide items: **PreventRequestForgery origin config**, **cache `serializable_classes`**, **cache prefix default** — verify none change session/token/cache behavior the app relies on.
- Migrate `phpunit.xml` schema: `vendor/bin/phpunit --migrate-configuration`.
- Set `minimum-stability` back to `stable` if all deps now have stable releases.

---

## Dependency Replacement Map

| Package | Now | Action | Hop |
|---------|-----|--------|-----|
| `fruitcake/laravel-cors` | ^2.0 | **Remove** → framework `HandleCors` | 8→9 |
| `facade/ignition` (dev) | ^2.5 | **Replace** → `spatie/laravel-ignition` | 8→9 |
| `laravel/passport` | ^10.4 | → 11 → **12 (publish migrations)** → 13 | each |
| `laravel/sanctum` | ^2.11 | → ^4 (unused; clean bump) | 10→11 |
| `spatie/laravel-permission` | ^5.9 | → ^6 (republish config, preserve data) | 10→11 |
| `laravelcollective/html` | ^6.4 | **Abandoned — investigate** (see Risks); swap to maintained fork or remove | 10→11 |
| `doctrine/dbal` | ^3.6 | Remove if unused after L11 (else ^4) | 10→11 |
| `kreait/laravel-firebase` | ^4.2 | → ^5 | 10→11 |
| `barryvdh/laravel-dompdf` | ^2.2 | → ^3 (facade path unchanged) | 11→12 |
| `maatwebsite/excel` | ^3.1 | Confirm L12/13 release; republish `config/excel.php` if needed | 11→12 |
| `nunomaduro/collision` (dev) | ^5.10 | → ^7 → ^8 | per hop |
| `phpunit/phpunit` (dev) | ^9.5 | → ^12 (migrate config) | 12→13 |
| `knuckleswtf/scribe` (dev) | ^4.35 | Bump to latest | 11→12 |
| `stevebauman/location` | ^7.3 | Bump to L12/13-compatible | 11→12 |
| `stripe/stripe-php` | ^13 | Bump latest (API stable) | 11→12 |
| `intervention/image` | ^3.11 | Already v3 — keep | — |
| `spatie/laravel-fractal` | ^6.2 | Bump to L12/13-compatible | 11→12 |

---

## Mobile-App / API Compatibility Safeguards (apply at every hop)

1. **Never regenerate Passport keys** (`storage/oauth-*.key`) — existing app tokens must stay valid.
2. After each hop run the Step-0 auth flow test: `register → login → verify-otp → Bearer token → protected GET`.
3. Diff captured JSON fixtures for the ~10 baseline endpoints — watch **date serialization** and `{status,message,data,token}` envelope. If L-version changes default date format, pin it (e.g. `protected $casts`/`Model::serializeDate`) to match the current output.
4. Keep `config/cors.php` permissive (`allowed_origins: ['*']`, `supports_credentials: false`).
5. Keep `/api/v3` prefix and the `userActivity` + `['auth:api','user']` middleware behavior identical through the skeleton migration.
6. Verify FCM endpoints (`/api/v3/user/fcm/update-token`, etc.) after the `kreait/laravel-firebase` bump.

---

## Verification (end-to-end)

Per hop, in order:
1. `composer update` resolves with no conflicts on PHP 8.3.
2. `php artisan config:clear route:clear cache:clear view:clear` then `php artisan about` boots clean.
3. `vendor/bin/phpunit` (PHPUnit 12 at the final hop) — green, including the Step-0 auth feature tests.
4. **API smoke test**: run the auth flow + protected endpoints; diff JSON against baseline fixtures.
5. If wired up, run the `tests/*.spec.ts` e2e suite against a local `php artisan serve`.
6. Web admin smoke test (Blade views render — especially anything using the HTML form package).
7. `npm ci && npm run production` (Laravel Mix) builds assets without error.
8. Point the **Flutter app** at the upgraded local API; confirm login, a data screen, and push-token registration.

Final: confirm production PHP ≥ 8.3, deploy keys/`.env` unchanged, run `php artisan migrate` (review pending Passport/permission/Sanctum migrations first), monitor Telescope + logs.

---

## Risks & Open Items

- **`laravelcollective/html` is abandoned** — highest third-party risk. First task at the L11 hop: grep `resources/views` for `Form::`/`Html::` usage to size the blast radius, then choose a maintained fork (drop-in) or replace with plain Blade. Web-only; does not affect the API.
- **Passport 12 migration publish** can collide with existing `oauth_*` tables — review published migrations before running `migrate`; do not drop/recreate token tables.
- **spatie/laravel-permission v6** has breaking config/migration changes — preserve existing role/permission rows.
- **Constraint drift** in current `composer.json` (telescope ^5 / intervention ^3 alongside L8) implies the lockfile is already inconsistent — reconcile the whole `require` block per hop, don't trust individual pins.
- **`maatwebsite/excel`** L12/13 compatibility must be confirmed; may gate the 11→12 hop.
- **Symfony 8 / PHP 8.4**: the `<13.3` pin is temporary; plan a follow-up to move prod to PHP 8.4 and drop the pin.
