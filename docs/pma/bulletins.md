---
title: Bulletins
updated: 2026-07-28
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
- **Non-English languages:** post title/description translate with the rest of the page via Google Website Translator (same as other PMA pages). They are not marked `notranslate`.
- **English:** Google Translate is neutralized for the UI, so Spanish/other posts are server-translated into English via `ContentTranslationService` when `content_lang=en`.
- **Original:** no machine translation; posts stay in the author’s language.
- Author display names are never translated (`no_translate` / `notranslate`), consistent with site-wide person-name protection.

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