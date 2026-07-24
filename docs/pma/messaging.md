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

### Team

- Team chat spaces; seed also includes `Create Team`, `Delete Team`.
- Membership-based team participation.

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