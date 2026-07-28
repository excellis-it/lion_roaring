---
title: Support Reports
updated: 2026-07-28
status: ready
sidebar_key: support_reports
---

# Support Reports

## Overview

Allows all authenticated PMA users to submit support reports and track their status. Users with the `Manage Support Reports` permission, **or Super Admin**, can view all reports, update status, and add admin notes from the main Support Reports pages.

## User Features (All Users)

- **My Reports** `/user/support-reports` — paginated list of own submitted reports with status badges (card UI).
- **Submit Report** `/user/support-reports/create` — form with Subject, Message, and optional attachment (jpg/png/gif/pdf/doc/docx, max 5MB).
- **View Report** `/user/support-reports/{id}` — detail including admin notes and attachment download. Authorization: own reports, or any report if manager/Super Admin.

## Mobile App

- Member APIs: `GET/POST /api/v3/user/support-reports`, `GET /api/v3/user/support-reports/{id}` (own reports only; optional attachment on create).
- Flutter drawer: top-level **Support Reports** (no manage UI).
- If the API route is missing (HTTP 404), the app shows **Coming soon** until the endpoint is deployed.

## Management Features

Gated by `Manage Support Reports` permission, **or Super Admin** (always has full access).

- On `/user/support-reports`, managers see tabs: **All Reports** (default) and **My Reports**, plus status filter.
- **Manage** (managers) / **View** (members) opens `/user/support-reports/{id}` with full report details. Managers also get a **Manage Report** side panel (status + admin notes). Saving emails the submitter.
- Legacy URLs `/user/support-reports/manage` and `/user/support-reports/manage/{id}` redirect into the main list/detail pages. Status updates still POST to `support-reports.manage.update`.

## Status Flow

`open` → `in_progress` → `resolved` → `closed`

Reports cannot be deleted — only moved to `closed` status.

## Email Notifications

- **On new submission:** email sent to all users with `Manage Support Reports` permission **and** Super Admins.
- **On status update:** email sent to the original submitter.

## Permissions

| Permission | Description |
|---|---|
| `Manage Support Reports` | Access all reports, update status, add notes |
| Super Admin | Full access without requiring the permission |
