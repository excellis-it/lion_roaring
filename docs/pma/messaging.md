---
title: Messaging
updated: 2026-07-24
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
- Video attachments (`mp4`, `webm`, `ogg`, `mov`, `m4v`) show as a compact thumbnail with a play overlay; clicking opens an in-app video player lightbox (download optional from the toolbar). Videos are **not** wrapped in the global `a.file-download` hijack.
- Image lightbox: height-first (`max-height ≈ 78vh`) so tall images show in full; centered on a dark stage so left/right gaps are expected.
- Chat image URLs prefer the **original** file via `Helper::chatMediaUrl()` — older Intervention v3 `resize(2000,2000)` compressions squashed portraits into squares; new uploads use `scaleDown()` and keep aspect ratio.
- Chat list / chat header / app header user avatars resolve via `Helper::publicStorageUrl()` and fall back to `profile_dummy.png` (including `onerror` for missing local files).
- Header notification badge sits on the top-right of the bell (does not use legacy `.round-note` margin).

### Team

- Team chat spaces; seed also includes `Create Team`, `Delete Team`.
- Membership-based team participation.
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