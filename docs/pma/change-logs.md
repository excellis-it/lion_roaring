---
title: Change Logs
updated: 2026-07-27
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

## Management Features

Gated by `Manage Change Logs` permission, **or Super Admin** (always has full access).

- **Update Versions** — form on the Change Logs list to set `WEB_APP_VERSION` and `MOBILE_APP_VERSION` (stored on Site Settings; no longer edited under Site Settings UI).
- **Create** `/user/change-logs/create` — form with Platform (Web / Mobile App), Version, Title, Type, Description (lightweight text editor: bold, italic, underline, bullet/numbered lists), Publish Date.
- **Edit** `/user/change-logs/{id}/edit` — update any field including platform.
- **Delete** — delete button on list entries.

Managers and Super Admins also see unpublished/future-dated entries on the list (marked Unpublished). Regular users only see published entries (`published_at <= now()`).

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
