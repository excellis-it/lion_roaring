# Lion Roaring — Laravel 13 Production Deployment Guide

This guide covers deploying the **Laravel 8 → 13** upgrade to production. Work was completed on branch `laravel13-upgrade`.

## Final stack (verified locally)

| Component | Version |
|-----------|---------|
| PHP | **8.3+** (required) |
| Laravel | **13.2.0** (pinned `<13.3` in `composer.json`) |
| Laravel Passport | **13.x** |
| PHPUnit (dev) | **12.x** |
| Node (assets) | Laravel Mix 6 |

---

## Before you deploy — hard requirements

1. **Production PHP must be 8.3 or newer.** Laravel 13 will not run on PHP 8.2.
2. **Back up everything** before touching production:
   - Full database dump
   - `storage/oauth-private.key` and `storage/oauth-public.key` (Passport signing keys)
   - `.env` file
   - `storage/app/public/firebase-adminsdk.json` (FCM)
3. **Do not regenerate Passport keys** — existing Flutter app tokens must stay valid.
4. Schedule a maintenance window (15–30 minutes minimum).

---

## Files and folders to upload

Upload the **entire project** except items listed in [Do NOT upload](#do-not-upload). Typical deployment uses Git pull or rsync.

### Must upload (changed by upgrade)

| Path | Why |
|------|-----|
| `composer.json` / `composer.lock` | Laravel 13 + all dependency versions |
| `bootstrap/app.php` | New L11+ application bootstrap |
| `bootstrap/providers.php` | App service providers |
| `artisan` | L11+ CLI entry |
| `public/index.php` | L11+ HTTP entry |
| `app/Providers/AppServiceProvider.php` | API rate limiter (moved from RouteServiceProvider) |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Removed RouteServiceProvider dependency |
| `app/Services/FCMService.php` | Kreait Firebase v7 API (`toToken()` instead of `withTarget()`) |
| `config/app.php` | RouteServiceProvider removed from providers |
| `tests/` | API contract tests (optional on prod, recommended in CI) |
| `vendor/` | **Or** run `composer install` on server (preferred) |
| `public/js/`, `public/css/` | Rebuilt Mix assets |

### Removed (delete on server if still present)

| Path | Replaced by |
|------|-------------|
| `app/Http/Kernel.php` | `bootstrap/app.php` → `withMiddleware()` |
| `app/Console/Kernel.php` | `bootstrap/app.php` → `withSchedule()` |
| `app/Exceptions/Handler.php` | `bootstrap/app.php` → `withExceptions()` |
| `app/Providers/RouteServiceProvider.php` | `bootstrap/app.php` → `withRouting()` |

### Do NOT upload

- `.env` — edit production `.env` in place (see below)
- `node_modules/`
- `.git/` (unless deploying via git pull)
- `storage/logs/*`, `storage/framework/cache/*`, `storage/framework/sessions/*`, `storage/framework/views/*` (keep directory structure)
- Local-only test artifacts

### Never overwrite on production

- `storage/oauth-private.key`
- `storage/oauth-public.key`
- Production `.env`
- User-uploaded files under `storage/app/`

---

## Step-by-step production deployment

### Phase 1 — Prepare the server

```bash
# 1. Verify PHP version
php -v
# Must show 8.3.x

# 2. Verify required PHP extensions
php -m | grep -E 'pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|zip|gd|curl'

# 3. Put site in maintenance mode (from current release directory)
php artisan down --render="errors::503" --retry=60
```

### Phase 2 — Deploy code

**Option A — Git (recommended)**

```bash
cd /path/to/lion_roaring
git fetch origin
git checkout laravel13-upgrade   # or merge into your production branch
git pull origin laravel13-upgrade
```

**Option B — rsync from build machine**

```bash
rsync -avz --delete \
  --exclude '.env' \
  --exclude 'node_modules' \
  --exclude 'storage/logs' \
  --exclude 'storage/framework/cache/data' \
  --exclude 'storage/oauth-private.key' \
  --exclude 'storage/oauth-public.key' \
  ./ user@server:/path/to/lion_roaring/
```

### Phase 3 — Install dependencies

```bash
cd /path/to/lion_roaring

# PHP dependencies (production, no dev packages)
composer install --no-dev --optimize-autoloader --no-interaction

# Frontend assets (on build machine or server if Node is available)
npm ci
npm run production
```

### Phase 4 — Fix Passport key permissions

Passport 13 requires restrictive key permissions:

```bash
chmod 600 storage/oauth-private.key storage/oauth-public.key
chown www-data:www-data storage/oauth-private.key storage/oauth-public.key
# Replace www-data with your PHP-FPM / Apache user
```

### Phase 5 — Update `.env` (edit in place)

Review and update these values on production. **Do not replace the whole file.**

```dotenv
APP_ENV=production
APP_DEBUG=false

# No change needed if already set — verify they exist:
# APP_KEY=...
# APP_URL=https://your-production-domain.com

# PHP 8.3 compatible — no changes required for most keys
# Verify mail, database, Stripe, Firebase paths still correct

TELESCOPE_ENABLED=false
```

No new mandatory `.env` keys were introduced for this upgrade. Firebase credentials path remains `storage/app/public/firebase-adminsdk.json`.

### Phase 6 — Database migrations

**Important:** Review pending migrations before running. Passport oauth tables already exist — do **not** drop or recreate them.

```bash
# Preview pending migrations
php artisan migrate:status

# Run only new migrations (if any)
php artisan migrate --force
```

If Passport migration files were published in a prior environment, ensure they are not duplicated. Existing `oauth_*` tables must remain intact.

### Phase 7 — Clear and rebuild caches

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Phase 8 — Storage link and permissions

```bash
php artisan storage:link

# Writable directories
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
chmod 600 storage/oauth-private.key storage/oauth-public.key
```

### Phase 9 — Scheduler (cron)

The scheduler moved from `app/Console/Kernel.php` to `bootstrap/app.php`. **Cron entry is unchanged:**

```cron
* * * * * cd /path/to/lion_roaring && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled commands (unchanged behavior):

- `delete:job` — daily
- `mails:update-deleted-status` — daily
- `subscription:send-reminder` — daily

### Phase 10 — Queue workers (if used)

Restart queue workers after deploy:

```bash
php artisan queue:restart
# Then restart supervisor/systemd workers
```

### Phase 11 — Bring site back up

```bash
php artisan up
```

---

## Post-deploy verification checklist

Run these in order after `php artisan up`:

### 1. Application boots

```bash
php artisan about
# Laravel Version should show 13.x
# PHP Version should show 8.3+
```

### 2. API contract (on server or staging)

```bash
vendor/bin/phpunit --testsuite Feature --filter Api
# All 10 tests should pass (requires DB access from test env)
```

### 3. Manual API smoke test

1. `POST /api/v3/login` — receives OTP
2. `POST /api/v3/verify-otp` — receives Bearer token
3. `POST /api/v3/user/profile` with `Authorization: Bearer {token}` — profile JSON
4. `GET /api/v3/cms/site-settings` — public CMS response
5. `POST /api/v3/user/update-fcm-token` — FCM token update

### 4. Web admin smoke test

- Log in to admin panel
- Open a Blade page that uses PDF export (dompdf v3)
- Open a page with Excel export (maatwebsite/excel)
- Confirm Telescope is inaccessible to non–super-admins (or disabled)

### 5. Flutter app

Point the app at production API (or staging mirror):

- Login → OTP → home screen
- One authenticated data screen
- Push token registration

### 6. Monitor logs

```bash
tail -f storage/logs/laravel.log
```

Watch for Passport, FCM, or permission errors in the first hour.

---

## Rollback plan

If critical issues occur:

1. `php artisan down`
2. Restore previous code release (Git tag / backup)
3. Restore database backup **only if** migrations caused data issues
4. Restore Passport keys from backup if accidentally overwritten
5. `composer install` for the old `composer.lock`
6. `php artisan config:cache && php artisan route:cache`
7. `php artisan up`

---

## What changed (summary for ops)

| Area | Change |
|------|--------|
| PHP | Minimum **8.3** |
| Framework | 8.83 → **13.2** |
| Skeleton | Legacy Kernel/Handler → `bootstrap/app.php` |
| CORS | `fruitcake/laravel-cors` removed → framework `HandleCors` |
| Mail | SwiftMailer removed (Symfony Mailer since L9) |
| Passport | v10 → **v13**; keys preserved; chmod **600** required |
| Spatie Permission | v5 → **v6**; middleware namespace `Middleware\` |
| Firebase / FCM | `kreait/laravel-firebase` **v7**; `FCMService` API updated |
| DomPDF | v2 → **v3** |
| laravelcollective/html | **Removed** (was unused in views) |
| doctrine/dbal | **Removed** (unused) |
| Telescope | v5 (L12/13 compatible) |
| Tinker | **v3** (L13 requirement) |
| PHPUnit | **12** (dev only) |

---

## Future: removing the Laravel 13.2 pin

`composer.json` pins `laravel/framework` to `^13.0 <13.3` to avoid Symfony 8 / PHP 8.4 requirements in patches ≥13.3.

When production moves to **PHP 8.4**:

1. Update `composer.json`: `"laravel/framework": "^13.0"`
2. `composer update laravel/framework --with-all-dependencies`
3. Re-run API tests and smoke tests

---

## Support references

- Upgrade plan: `docs/laravel-13-upgrade-plan.md`
- API baselines: `tests/fixtures/api-baselines/README.md`
- Branch: `laravel13-upgrade`
