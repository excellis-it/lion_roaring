---
title: PMA Project Documentation
updated: 2026-07-24
status: ready
sidebar_key: documentation
---

# PMA Project Documentation

Internal Super Admin reference for Lion Roaring surfaces: the **public website**, **E-Store**, **E-Learning**, and the **PMA user panel** (sidebar from Messaging through Chatbot).

## How to use

1. Browse section cards below (or the left nav on a section page).
2. Read **Overview**, then expand feature blocks for permissions, rules, and conditions.
3. When product behavior changes, update the matching file under `docs/pma/` and bump `updated`.

## Coverage map

| Area | Doc section | Surface |
|------|-------------|---------|
| Public marketing site | Website Frontend | `/` |
| Shop (customers) + PMA admin | E-Store | `/e-store` + `/user` store menus |
| Learning catalog + PMA admin | E-Learning | `/e-learning` + `/user` elearning menus |
| PMA sidebar Messaging → Chatbot | Matching section titles | `/user/*` |
| Super Admin restore / docs | Restore, Documentation UI | Super Admin only |

## Panel-wide rules (all `/user`)

- Middleware: `user` (logged in + `status == 1`), `member.access`, `agreement.signed`, plus activity / back-history guards.
- **Super Admin** and `membership_excluded` users bypass membership expiry checks.
- **Super Admin** bypasses the signed-agreement gate.
- Spatie permissions via `Gate::check` / `can()`; Super Admin Spatie role is seeded with all permissions.
- Country content is often scoped: Global / `G_R` on the global server vs regional `country` / `content_country_code` (US fallback on public CMS).