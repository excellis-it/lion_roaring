---
title: Strategy
updated: 2026-07-24
status: ready
sidebar_key: strategy
---

# Strategy

## Overview

Upload, view, download, and delete Strategy documents in the PMA panel.

**Controller:** `User\StrategyController`  
**Routes:** `strategy.index|upload|store|delete|download|fetch-data|view`

## Features

### Document library

- Index/list with country scoping.
- Upload with optional `country_id` for Super Admin.
- View/download/delete with permission checks.

## Permissions and conditions

- Gates: `Manage Strategy`, `Upload|Download|View|Delete Strategy`.
- Global/Regional country + author status scoping; ecclesia admin filters on regional lists.
- Super Admin: unscoped list; chooses country on upload.