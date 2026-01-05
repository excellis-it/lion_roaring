---
applyTo: "**"
---

# Project general coding standards

## For ONLY API-related code use these instructions

-   You are assisting in a Laravel API-only project
-   Work only inside app/Http/Controllers/Api/
-   Never modify models.
-   Use short code, avoid boilerplate.
-   Use try-catch with 200 for success/201 for failure response.
-   Follow existing module patterns.
-   Add proper Scribe annotations for every method.
-   Only generate API controllers and routes.
-   Every API method MUST return JSON responses and must have 'status' = true/false.
-   Also check for blade files in resources/views/ for any API-related code.
-   Also check views if any extra things are needed for API-related code.
-   Always check routes/api.php for route definitions.
-   Which db table have 'country_code' column, should ask for country code optionally in the API, otherwise filter by default country code 'US', add in scribe annotation with example: 'US'.
-   For every new or updated API method, you MUST add or update proper Scribe annotations above functions:
    -- @group (for main module name)
    -- \* Feature Name (for every feature inside the module)
    -- @authenticated (if required)
    -- @bodyParam
    -- @queryParam
    -- @urlParam
    -- @response 200 (for success example)
    -- @response 201 (for failure example)

## You are assisting in a Laravel project that contains major modules:

-   User Panel
-   E-commerce
-   E-learning
-   Admin Panel
-   Frontend

## Copilot command: project-view-test

-   When a user message begins with `project-view-test` followed by a short description (for example: `project-view-test the login feature and fix if any bugs`), Copilot should perform these steps:

    -   Launch Playwright in **headed Chrome** with DevTools open (use the `chrome-devtools` project and flags such as `--headed` and `--project=chrome-devtools`).
    -   Generate or update Playwright tests that exercise the described feature end-to-end, including visual checks (screenshots, DOM assertions) and edge cases where applicable.
    -   Start the local development server at `http://127.0.0.1:8000` (if not already running) and run the tests against it.
    -   If tests fail, attempt a minimal, safe fix to the app code (only when confident and non-destructive), re-run tests, and verify the fix.
    -   Report results concisely: what was tested, pass/fail summary, screenshots or traces on failure, and any code changes made.
    -   Before making changes that may affect production data, migrations, or sensitive configuration, ask for explicit confirmation from the user.

-   Keep operations auditable: add tests and any code edits with clear commit messages and include new/updated test files in the report.
