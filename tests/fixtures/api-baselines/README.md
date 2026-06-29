# API contract baselines (Laravel 8 pre-upgrade)

Captured during Step 0 on branch `laravel13-upgrade`. PHPUnit suite `tests/Feature/Api/*` asserts:

| Endpoint | Method | Auth | Contract |
|----------|--------|------|----------|
| `/api/v3/register-meta` | GET | — | `{status, generated_id_part}` |
| `/api/v3/cms/site-settings` | GET | — | `{status, message, ...}` |
| `/api/v3/cms/menu` | GET | — | `{status, ...}` |
| `/api/v3/register-agreement` | POST | — | `{status, message, data?}` |
| `/api/v3/login` | POST | — | `{status, message, user, otp}` on success |
| `/api/v3/verify-otp` | POST | — | `{status, message, token}` on success |
| `/api/v3/user/profile` | POST | Bearer | `{status, message, in_app_membership, data}` |
| `/api/v3/user/update-fcm-token` | POST | Bearer | `{status, message}` |
| `/api/v3/user/fcm/update-token` | POST | Bearer | `{status, message}` |

Date fields in profile `data` use ISO 8601 strings (`created_at`, `updated_at`).

Re-run after each upgrade hop:

```bash
vendor/bin/phpunit --testsuite Feature --filter Api
```
