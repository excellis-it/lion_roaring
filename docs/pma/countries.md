---
title: Countries
updated: 2026-07-24
status: ready
sidebar_key: countries
---

# Countries

## Overview

Admin CRUD for country records that drive multi-domain routing and CMS scoping. Full behavior is documented in **Global & Regional Domains**.

**Controller:** `User\Admin\CountryController`  
**Routes:** `user.admin.admin-countries.*`

## Features

### Country CRUD

- Create/edit name, code, domain, languages, flag, status.
- Lists used across frontend redirects and PMA scoping.

## Permissions and conditions

- Gate: `Manage Countries`.
- Cannot **delete** or **toggle status** of `is_global` country.
- Global code is **`GL`**; public dropdowns exclude global row (`Helper::getCountries()`).
- Changing `domain` affects which host is treated as that country’s instance — coordinate with `MAIN_URL` / `LION_ROARING_USA` fallbacks.