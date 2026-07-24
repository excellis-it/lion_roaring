---
title: Bulletins
updated: 2026-07-24
status: ready
sidebar_key: bulletins
---

# Bulletins

## Overview

Community publishing tools: Bulletin Board (read), Create Bulletins, Job Posting, Meeting Schedule, Live Events, Private Collaboration.

**Controllers:** `BulletinBoardController`, `BulletinController`, `JobpostingController`, `MeetingSchedulingController`, `LiveEventController`, `PrivateCollaborationController`

## Features

### Bulletin Board / Create Bulletins

- Board lists posts for members; Create Bulletins manages posts with country / user-type scoping.
- Title/description may be server-translated on the board when language cookie is set (see ContentTranslationService).

### Job Posting / Meeting Schedule / Live Events

- CRUD with View/Create/Edit/Delete permission variants.
- Edit/delete often **creator + permission OR Super Admin**.
- Events: RSVP (`confirmed` / `pending`), notifications; Meetings/PC may use Zoom signature endpoints.

### Private Collaboration

- Invitation/accept flow; eligible users loaded by country (Super Admin can pick country).

## Permissions and conditions

| Gate family | Menu |
|-------------|------|
| `Manage Bulletin` (+ Create/Edit/Delete) | Bulletins |
| `Manage Job Postings` (+ View/Create/Edit/Delete) | Jobs |
| `Manage Meeting Schedule` (+ View/Create/Edit/Delete) | Meetings |
| `Manage Event` (+ Create/Edit) | Live Events |
| `Manage Private Collaboration` (+ View/Create/Edit/Delete) | Private Collaboration |

Country scoping follows Global vs Regional patterns used elsewhere in the panel.