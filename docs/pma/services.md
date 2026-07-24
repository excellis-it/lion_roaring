---
title: Services
updated: 2026-07-24
status: ready
sidebar_key: services
---

# Services

## Overview

Per-organization services CMS. Sidebar builds one link per organization from `Helper::getOrganzations()`.

**Controller:** `User\Admin\ServiceContoller`  
**Routes:** `user.admin.services.index` (+ slug)

## Features

### Service management

- Manage services shown on public `/service/{slug}`.

## Permissions and conditions

- Gate: `Manage Services`.
- Organization list drives dynamic sidebar children.