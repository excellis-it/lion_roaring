---
title: All Members
updated: 2026-07-24
status: ready
sidebar_key: all_members
---

# All Members

## Overview

Partners / all-members directory (create, edit, status, export, agreement details).

**Controller:** `User\PartnerController`  
**Routes:** `partners` resource, `partners.change-status`, `partners.fetch-data`, export, agreement-details

## Features

### Directory

- List/filter partners; DataTables-style fetch.
- Create/edit partner profile, country, ecclesia, user type.
- Change status; export; view agreement details.

## Permissions and conditions

- Gates: `Manage Partners`, Create/Edit/Delete/View Partners.
- Super Admin: may see inactive users and manage broader type/country options.
- Non-SA: typically `status = 1`; Global → Global+G_R; Regional → same country Regional+G_R.
- Create/edit enforces same `user_type` as auth (non-SA) and country for Regional.
- Admin roles force `user_type = G_R`; new partners often `status = 1`, `is_accept = 1`.