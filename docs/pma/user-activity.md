---
title: User Activity
updated: 2026-07-24
status: ready
sidebar_key: user_activity
---

# User Activity

## Overview

Activity dashboard and detailed activity list for visits/logins and related aggregates.

**Controller:** `User\UserActivityController`  
**Routes:** `user-activity` resource, `user-activity-get-list`, AJAX by country/user/type, active members/countries

## Features

### Activity Dashboard

- Charts/stats for engagement over date ranges.

### Activity List

- Tabular activity feed with filters.

## Permissions and conditions

- Gates: `Manage User Activity` (+ View/Create/Edit/Delete in seed).
- Super Admin: global stats (no extra country filter in dashboard).
- Driven by `userActivity` middleware collecting activity on panel requests.