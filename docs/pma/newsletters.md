---
title: Newsletters
updated: 2026-07-24
status: ready
sidebar_key: newsletters
---

# Newsletters

## Overview

Admin Portal newsletter subscriber management (public site / portal signups). Separate e-learning newsletter tools exist under E-Learning CMS.

**Controller:** `User\Admin\NewsletterController`  
**Routes:** `user.admin.newsletters.*`

## Features

### Subscriber list

- List/fetch/delete newsletter rows.
- Public POST `newsletter` on frontend; e-store/e-learning also have newsletter endpoints.

## Permissions and conditions

- Gates: `Manage Newsletters`, `Delete Newsletters`.
- Country: Global all; else own `country_id`.