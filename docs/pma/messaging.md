---
title: Messaging
updated: 2026-07-30
status: ready
sidebar_key: messaging
---

# Messaging

## Overview

PMA sidebar parent for **Chats**, **Team**, and **Mail**. Visible when the user has any of: `Manage Chat`, `Manage Team`, `Manage Email`.

**Controllers:** `User\ChatController`, `User\TeamChatController`, `User\SendMailController`  
**Routes:** `chats.*`, `team-chats.*`, `mail.*`

## Features

### Chats

- One-to-one messaging among eligible panel users.
- Recipients: `status = 1` and user role type in `{1, 2, 3}`.
- Soft "deleted for me" flags; messages are not hard-deleted for the other party by default.
- **Image attachments** accept common formats: `jpg`, `jpeg`, `jfif`, `png`, `gif`, `webp`, `heic`, `heif` (plus `bmp`/`svg` when provided). Web uploads convert HEIC/HEIF to JPEG when possible (browser `heic2any`, or Imagick on the server) and treat JFIF as JPEG so previews and lightbox work across browsers. If HEIC cannot be converted, it is stored and shown as a downloadable attachment (most browsers cannot render raw HEIC). Mobile file pickers allow the same image extensions.
- Video attachments (`mp4`, `mkv`, `avi`, `mov`, `wmv`, `webm`, `flv`, `mpeg`/`mpg`, `m4v`, `3gp`, `ogv`) show as a compact video card with a play overlay (format badge). Clicking opens an in-app video player lightbox. Formats the browser/device cannot decode still open the player UI and show a clear message that only **Download** is available. Videos are **not** wrapped in the global `a.file-download` hijack. Mobile mirrors this: in-app player on tap, download from the player toolbar when playback fails. (`.ogg` audio stays audio; use `.ogv` for Ogg video.)
- Image lightbox: height-first (`max-height ≈ 78vh`) so tall images show in full; centered on a dark stage so left/right gaps are expected.
- Chat image URLs prefer the **original** file via `Helper::chatMediaUrl()` — older Intervention v3 `resize(2000,2000)` compressions squashed portraits into squares; new uploads use `scaleDown()` and keep aspect ratio.
- Chat list / chat header / app header user avatars resolve via `Helper::publicStorageUrl()` and fall back to `profile_dummy.png` (including `onerror` for missing local files).
- Header notification badge sits on the top-right of the bell (does not use legacy `.round-note` margin).
- Display names use `no_translate` / `.GroupName` so Google Website Translator does not alter person names.

### Team

- Team chat spaces; seed also includes `Create Team`, `Delete Team`.
- Membership-based team participation.
- Same image/video attachment formats and preview behavior as one-to-one Chats.
- Group list/header avatars resolve via `Helper::publicStorageUrl()` (falls back to the original file if a compressed asset is missing, otherwise the default `group.jpg`). Broken URLs also fall back client-side via `onerror`.

### Mail

- Compose/send to active users (`status = true`).
- Star, trash, restore flows in the mail UI (`user/mail` views + mail sidebar partial).

## Permissions and conditions

| Permission | Area |
|------------|------|
| `Manage Chat` | Chats |
| `Manage Team` | Team |
| `Manage Email` | Mail |

### Super Admin

- Chat list can include all eligible users; non-Super Admin uses `visibleToAuthUser()` and may only see Super Admins who messaged first.
- Country/visibility helpers still apply for non-SA users.