---
title: User Activity
updated: 2026-08-13
status: ready
sidebar_key: user_activity
---

# User Activity

## Overview

Activity dashboard and detailed activity list for visits/logins and related aggregates.

**Controller:** `User\UserActivityController`  
**Routes:** `user-activity` resource, `user-activity-get-list`, chunked export (`user-activity-export-start` / `chunk` / `cancel` / `download`), AJAX by country/user/type, active members/countries

## Features

### Activity Dashboard

- Charts/stats for engagement over date ranges.

### Activity List

- Tabular activity feed with filters (Name, Email, Country, Activity Type, Date From/To).
- **Export** opens a progress modal and builds a CSV of the currently filtered list in chunks (5,000 rows per request, no overall row cap). Excel is not used because a worksheet cannot hold millions of rows.
- The modal shows processed/total records and a **Cancel** button. Cancel stops the export and deletes the temp file. When complete, the CSV downloads automatically.
- Export requires `Manage User Activity`.

## Permissions and conditions

- Gates: `Manage User Activity` (+ View/Create/Edit/Delete in seed).
- Super Admin: global stats (no extra country filter in dashboard).
- Driven by `userActivity` middleware collecting activity on panel requests.