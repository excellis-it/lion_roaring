---
title: Site Settings
updated: 2026-07-24
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

### Menu Names

- Updates `menu_items.name` by key via `Helper::getMenuName()`.

## Permissions and conditions

- Gates: `Manage Site Settings`, `Manage Menu Settings`.
- MenuController relies on sidebar gating (no extra Gate inside controller).