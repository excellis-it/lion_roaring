---
title: Site Settings
updated: 2026-07-27
status: ready
sidebar_key: site_settings
---

# Site Settings

## Overview

Global site settings and dynamic sidebar menu display names.

**Controllers:** `User\Admin\SettingsController`, `User\Admin\MenuController`  
**Routes:** `user.admin.settings.edit|update|toggle-status`, `user.admin.menu.index|update`

## Features

### Settings

- Edit site settings keys used across frontend and panel (logos, contact email, etc.).
- Web / Mobile app version numbers (`WEB_APP_VERSION`, `MOBILE_APP_VERSION`) are stored on Site Settings but edited from **Change Logs** (managers with `Manage Change Logs`), not from this page.

### Menu Names

- Updates `menu_items.name` by key via `Helper::getMenuName()`.

## Permissions and conditions

- Gates: `Manage Site Settings`, `Manage Menu Settings`.
- MenuController relies on sidebar gating (no extra Gate inside controller).
