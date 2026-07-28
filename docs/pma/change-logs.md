---
title: Change Logs
updated: 2026-07-28
status: ready
sidebar_key: change_logs
---

# Change Logs

## Overview

Platform release notes / update history. All authenticated PMA users can read published entries. Users with the `Manage Change Logs` permission can create, edit, and delete entries, and update current Web / Mobile app version numbers.

Entries are split by platform: **Web Version** and **Mobile App Version**.

## User Features (All Users)

- **Change Logs List** `/user/change-logs` — reverse-chronological list of published entries (where `published_at <= now()`).
- Tabs: **Web Version** (`?platform=web`) and **Mobile App Version** (`?platform=mobile`). Default tab is Web.
- Header chip shows **Current Web** or **Mobile App** version from Site Settings columns (`WEB_APP_VERSION` / `MOBILE_APP_VERSION`).
- Each list entry shows the **full description** (rich text: bold/italic, bullet and numbered lists). There is no separate detail page.
- PMA profile menu shows the current **Web Version** (only) below Log Out, with a link to Change Logs. Mobile app version is not shown in the web profile menu.

## Mobile App

- Member API: `GET /api/v3/user/change-logs?platform=web|mobile` (published only; includes `current_version` for the platform).
- Flutter drawer: top-level **Change Logs** with Web/Mobile tabs (default Web); no manage CRUD or version editing.
- If the API route is missing (HTTP 404), the app shows **Coming soon** until the endpoint is deployed.

## Management Features

Gated by `Manage Change Logs` permission, **or Super Admin** (always has full access).

- **Update Versions** — form on the Change Logs list to set `WEB_APP_VERSION` and `MOBILE_APP_VERSION` (stored on Site Settings; no longer edited under Site Settings UI).
- **Create** `/user/change-logs/create` — form with Platform (Web / Mobile App), Version, Title, Type, Description (lightweight text editor: bold, italic, underline, bullet/numbered lists), Publish Date.
- **Edit** `/user/change-logs/{id}/edit` — update any field including platform.
- **Delete** — delete button on list entries.

### Publish Date rules

- Entries **publish immediately on save**. There is no scheduled / future publishing.
- Publish Date may be left as the default (now) or set to a **past or current** datetime (e.g. backdating a release note).
- Future date/times are blocked in the form (`max` = now) and rejected by the server.
- Datetime is interpreted in the manager’s profile timezone, then stored in app timezone, so a past local time is not marked Unpublished due to UTC offset.
- Blank Publish Date defaults to now (published).

Managers and Super Admins see all entries for the selected platform. The Unpublished badge only appears when `published_at` is null (legacy) or somehow still after now. Regular users only see published entries (`published_at <= now()`).

Bullet and numbered lists in descriptions render with visible markers on the list page (and in the editor while composing).
## Platforms

| Platform | Label | Description |
|---|---|---|
| `web` | Web Version | Website / PMA web updates |
| `mobile` | Mobile App Version | Flutter mobile app updates |

## Entry Types

| Type | Badge Color | Description |
|---|---|---|
| `feature` | Blue | New feature added |
| `improvement` | Cyan | Enhancement to existing feature |
| `bugfix` | Yellow | Bug fix |
| `security` | Red | Security fix |

## Permissions

| Permission | Description |
|---|---|
| `Manage Change Logs` | Create, edit, delete entries; update Web/Mobile app version numbers |
| Super Admin | Full access without requiring the permission |
