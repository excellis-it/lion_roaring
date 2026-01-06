---
applyTo: "**"
---

# Project Copilot Guidelines (Laravel)

This file defines practical, actionable rules for contributing to this Laravel project. Keep instructions brief and follow repository conventions.

## High-level rules

-   Work in small, reviewable commits and include meaningful commit messages.
-   Never commit `.env` or sensitive credentials. Update `.env.example` if env vars change.
-   Follow PSR-12 coding style and Laravel conventions.

## API-related workflow (required)

-   Only modify controllers for API features inside `app/Http/Controllers/Api/` unless the task explicitly requires otherwise.
-   Do NOT modify models unless explicitly requested and approved.
-   Prefer using Artisan generators and framework commands (don't hand-write scaffolding when a command exists):

    -   Create migrations: `php artisan make:migration create_users_table --create=users`
    -   Create models + migration + factory + seeder + controller: `php artisan make:model ModelName --migration --factory --seeder --controller`
    -   Create API controller: `php artisan make:controller Api/MyController --api`
    -   Create resource controllers: `php artisan make:controller Api/ThingController --resource --api`
    -   Create requests: `php artisan make:request StoreThingRequest`
    -   Create jobs, events, listeners, notifications with `php artisan make:*` equivalents.

-   Migrations: prefer run/test with Artisan commands (don't create migration files by hand unless necessary):
    -   Run locally: `php artisan migrate`
    -   Rollback last batch: `php artisan migrate:rollback`
    -   Fresh + seed: `php artisan migrate:fresh --seed`
    -   Use `--path` when you need to run a specific migration file.

## Responses, errors, and HTTP codes

-   API endpoints MUST return JSON with a boolean `status` key along with data or error message.
-   Use semantic HTTP codes: `200` for OK, `201` for created resources, `204` for no content, `400/422` for client errors, `401` for unauthorized, `403` for forbidden, `500` for server errors.
-   Wrap controllers or service calls in try/catch only where needed; handle validation with Form Requests and rely on exceptions for unexpected errors. When catching, return appropriate error code and message.

## Scribe documentation (required for API methods)

-   Add Scribe annotations for every public API controller method you add or change. At minimum include:
    -   `@group` (module name)
    -   short feature description
    -   `@authenticated` when auth is required
    -   `@bodyParam`, `@queryParam`, `@urlParam` examples
    -   `@response 200` success example
    -   `@response 4xx|5xx` failure example(s)

## Country code rule

-   For tables that include a `country_code` column: accept an optional `country_code` parameter in the API. If omitted, default to `US`. Include the example `US` in Scribe annotations.

## Tests & CI

-   Write or update PHPUnit/Pest tests for any controller, model, or service logic you add or change.
-   Run tests locally before pushing: `php artisan test` or `vendor/bin/phpunit`.
-   End-to-end or browser tests (Playwright) should run against `http://127.0.0.1:8000`. Use existing Playwright config and add tests under `tests/` as appropriate.

## Frontend / assets

-   Use npm scripts already present in the repo: `npm install` then `npm run dev` for development, `npm run prod` for production builds.

## Database seeding & fixtures

-   Use factories and seeders for test data. Create them via Artisan (`php artisan make:factory`, `php artisan make:seeder`).

## Database migrations caution

-   Prefer `php artisan make:migration` and let Artisan set filenames and timestamps. Avoid manually editing migration timestamps or filenames unless fixing a specific conflict.

## Security & maintenance

-   Always run `composer install` and `composer dump-autoload` after adding packages.
-   Run `php artisan config:cache` and `php artisan route:cache` only in production or when explicitly needed.

## Playwright / project-view-test command

-   If a user asks via `project-view-test`, follow the repo's test workflow: run Playwright in headed Chrome, exercise the requested feature, report concise pass/fail and attach traces/screenshots. Start the local dev server at `http://127.0.0.1:8000`.
-   Ask for explicit confirmation before making any DB/schema changes or destructive edits when running automated tests.

## Small checklist for PRs

-   Include tests for behavior changes.
-   Update `README.md` or module docs when adding features or env variables.
-   Run static analysis (if configured) and include test output when relevant.

***
