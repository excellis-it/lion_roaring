---
title: Organization Center
updated: 2026-07-24
status: ready
sidebar_key: organization_center
---

# Organization Center

## Overview

Organization Center CMS (public `features/{slug}` pages under an organization).

**Controller:** `User\Admin\OrganizationCenterController`

## Features

### Centers CRUD

- Create/list/edit/delete organization centers linked in the public hierarchy.

## Permissions and conditions

- Gates: Manage/Create/Edit/Delete Organization Center.
- Access still requires `/user` membership + agreement unless Super Admin / excluded.