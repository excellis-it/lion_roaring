---
title: Our Governance
updated: 2026-07-24
status: ready
sidebar_key: our_governance
---

# Our Governance

## Overview

CMS entries for public Our Governance pages (`/our-governance/{slug}`).

**Controller:** `User\Admin\OurGovernanceController`  
**Routes:** `user.admin.our-governances.*` + reorder

## Features

### Governance entries

- CRUD + reorder within country.
- `order_no` resequenced on delete/country change.

## Permissions and conditions

- Gates: Manage/Create/Edit/Delete Our Governance.
- Country code: Global vs regional pattern.