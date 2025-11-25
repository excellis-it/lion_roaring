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
