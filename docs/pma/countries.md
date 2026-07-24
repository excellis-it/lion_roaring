---
title: Countries
updated: 2026-07-24
status: ready
sidebar_key: countries
---

# Countries

## Overview

Manage country records used for multi-domain / multi-instance CMS and scoping.

**Controller:** `User\Admin\CountryController`  
**Routes:** `user.admin.admin-countries.*`, toggle-status, fetch-data, delete

## Features

### Country CRUD

- Create/edit countries, domains, languages sync, status toggle.

## Permissions and conditions

- Gate: `Manage Countries`.
- Cannot delete or toggle status of an `is_global` country.
- Drives public visitor country resolution and PMA content scoping.