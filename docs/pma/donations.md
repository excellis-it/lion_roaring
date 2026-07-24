---
title: Donations
updated: 2026-07-24
status: ready
sidebar_key: donations
---

# Donations

## Overview

PMA Admin Portal list of donations submitted from the public website Stripe donation form.

**Controller:** `User\Admin\DonationController`  
**Routes:** `user.admin.donations.*`

## Features

### Donation list

- View/fetch donation records; delete where permitted.
- Public charge created by `Frontend\DonationController` (USD Stripe Charge).

## Permissions and conditions

- Gate: `Manage Donations`.
- Country: Global users see all; others filtered to `country_id = user.country`.