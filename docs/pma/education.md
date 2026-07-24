---
title: Education
updated: 2026-07-24
status: ready
sidebar_key: education
---

# Education

## Overview

Education tracks and file library in the PMA panel: Topics, Becoming Sovereign, Becoming Christ Like, Becoming a Leader, and Files.

**Controllers:** `TopicController`, `BecomingSovereignController`, `BecomingChristLikeController`, `LeadershipDevelopmentController`, `FileController`  
**Routes:** `topics.*`, `becoming-sovereign.*`, `becoming-christ-link.*`, `leadership-development.*`, `file.*`

## Features

### Topics

- CRUD for education topics; scoped by Global vs regional country rules.

### Becoming tracks

- Becoming Sovereign / Christ Like / Leader: upload, edit, delete content filtered by `education_type`.
- Seed includes Upload/Edit/Delete variants (note historical typo permission `Manage Becomeing Sovereigns` may exist in DB).

### Files

- Upload/view/download education files; duplicate name checks per country scope.
- Delete often requires owner **or** Super Admin.

## Permissions and conditions

- Parent sidebar: education permissions **or** Super Admin (`hasNewRole('SUPER ADMIN')`).
- Gates include `Manage Topic` (+ Create/Edit/Delete), becoming-track manage/upload/edit/delete, `Manage File` (+ Upload/Edit/Delete/View).
- Super Admin: unscoped lists; may set `country_id` on upload.
- Non-SA: Global/`G_R` on global server vs own country Regional/`G_R`; ecclesia admins may further filter by managed ecclesia.