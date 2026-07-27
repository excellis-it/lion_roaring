# Support Reports & Change Logs — Design Spec

**Date:** 2026-07-27  
**Status:** Approved  
**Approach:** B — Native Laravel module with email notifications

---

## Overview

Add two new features to the User PMA panel:

1. **Support Reports** — All users can submit support reports (subject + message + optional attachment). Users with the `Manage Support Reports` permission can view all reports, update status, and reply with admin notes.
2. **Change Logs** — All users can read platform update / release notes. Users with the `Manage Change Logs` permission can create, edit, and delete change log entries.

Both features appear as separate top-level sidebar menu items visible to all authenticated users.

---

## Database

### `support_reports` table

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK → users | submitter |
| `subject` | string | required |
| `message` | text | required |
| `attachment` | string nullable | stored file path |
| `status` | enum: `open`, `in_progress`, `resolved`, `closed` | default `open` |
| `admin_notes` | text nullable | management reply visible to submitter |
| `resolved_by` | FK → users nullable | manager who last updated |
| `resolved_at` | timestamp nullable | when status changed to resolved/closed |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `change_logs` table

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `created_by` | FK → users | who published |
| `version` | string | e.g. `v2.4.1` |
| `title` | string | short heading |
| `description` | longText | rich text / markdown |
| `type` | enum: `feature`, `improvement`, `bugfix`, `security` | badge colour label |
| `published_at` | timestamp | can be set to future date |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## Permissions (Spatie)

Two new permissions registered in the system:

- `Manage Support Reports` — grants access to management view of all reports.
- `Manage Change Logs` — grants access to create/edit/delete change logs.

These are added via a seeder/migration and appear in the existing Role Permissions UI.

---

## User-Facing Features (All Authenticated Users)

### Support Reports

- **My Reports** `GET /user/support-reports` — paginated list of own reports with status badges.
- **Submit Report** `GET /user/support-reports/create` — form: Subject (text), Message (textarea), Attachment (optional, images/pdf/doc, max 5MB).
- **Store** `POST /user/support-reports` — validates, saves, sends notification email to all `Manage Support Reports` users, redirects to My Reports.
- **View Report** `GET /user/support-reports/{id}` — read-only detail: subject, message, attachment (download link), status badge, admin notes (if set).
- Authorization: user can only view their own reports (policy check).

### Change Logs

- **Change Logs List** `GET /user/change-logs` — reverse-chronological list of all published entries (where `published_at <= now()`). Shows version, type badge, title, date.
- **Change Log Detail** `GET /user/change-logs/{id}` — full description rendered as HTML.

---

## Management Features (Role-Permission Gated)

### Support Reports Management

Gated by `Gate::check('Manage Support Reports')`.

- **All Reports** `GET /user/support-reports/manage` — list of ALL reports by any user. Filterable by status (Open / In Progress / Resolved / Closed). Paginated.
- **View & Respond** `GET /user/support-reports/manage/{id}` — full detail + status dropdown + admin notes textarea.
- **Update** `PUT /user/support-reports/manage/{id}` — saves status + admin notes, records `resolved_by` and `resolved_at` when status is `resolved` or `closed`. Sends email notification to submitter.
- No delete — reports are only closeable via status change.

### Change Logs Management

Gated by `Gate::check('Manage Change Logs')`.

- **Create** `GET /user/change-logs/create` — form: Version, Title, Type (dropdown), Description (textarea/rich text), Publish Date.
- **Store** `POST /user/change-logs` — validates and saves.
- **Edit** `GET /user/change-logs/{id}/edit` — pre-populated form.
- **Update** `PUT /user/change-logs/{id}` — saves changes.
- **Delete** `DELETE /user/change-logs/{id}` — soft confirm, then delete.

Sidebar shows "Add New" or manage link next to Change Logs only when `Gate::check('Manage Change Logs')`.

---

## Email Notifications

### On new report submitted
- **Recipients:** all users who have the `Manage Support Reports` permission.
- **Content:** submitter name, subject preview, link to management detail view.
- **Mailable:** `App\Mail\SupportReportSubmittedMail`

### On report status updated
- **Recipient:** the original submitter.
- **Content:** new status, admin notes (if provided), link to their report.
- **Mailable:** `App\Mail\SupportReportStatusUpdatedMail`

### On change log published
- No automatic email (passive feature — users visit when they want).

---

## Sidebar

Two new top-level sidebar items in `resources/views/user/includes/sidebar.blade.php`:

1. **Support Reports** — always visible to all users. Shows management sub-link when `Gate::check('Manage Support Reports')`.
2. **Change Logs** — always visible to all users. Shows "Add / Manage" link when `Gate::check('Manage Change Logs')`.

Both use `Helper::getMenuName()` for menu label (consistent with existing items).

---

## File Structure

```
app/Http/Controllers/User/
    SupportReportController.php
    SupportReportManageController.php
    ChangeLogController.php

app/Models/
    SupportReport.php
    ChangeLog.php

app/Mail/
    SupportReportSubmittedMail.php
    SupportReportStatusUpdatedMail.php

database/migrations/
    YYYY_MM_DD_create_support_reports_table.php
    YYYY_MM_DD_create_change_logs_table.php
    YYYY_MM_DD_add_support_reports_change_logs_permissions_seeder.php

resources/views/user/
    support-reports/
        index.blade.php
        create.blade.php
        show.blade.php
        manage/
            index.blade.php
            show.blade.php
    change-logs/
        index.blade.php
        show.blade.php
        create.blade.php
        edit.blade.php

routes/web.php  (new route group entries)
```

---

## PMA Documentation

- Update `docs/pma/user-pma.md` — add Support Reports and Change Logs to the Major Menu Groups section.
- Create `docs/pma/support-reports.md` — document the Support Reports feature.
- Create `docs/pma/change-logs.md` — document the Change Logs feature.
- Add both to `config/pma_documentation.php` hubs if applicable.
