---
title: Project Documentation
updated: 2026-07-24
status: ready
sidebar_key: documentation
---

# Project Documentation

Start with the five product surfaces below, then open **Detailed topics** for menu-by-menu rules.

## How to use

1. Open a **hub** card (Website Frontend, User PMA, E-Learning, E-Store, Mobile App).
2. Read Overview, domain rules, and feature blocks.
3. Use **Detailed topics** for sidebar menus and shared domain rules.
4. When behavior changes, update the matching `docs/pma/*.md` file and bump `updated`.

## Surfaces

| Hub | Path / app | Audience |
|-----|------------|----------|
| Website Frontend | `/` | Public visitors |
| User PMA | `/user/*` | Authenticated members & admins |
| E-Learning | `/e-learning` + PMA admin | Logged-in users |
| E-Store | `/e-store` + PMA admin | Members (membership required) |
| Mobile App | Flutter + `/api/v3` | App users |

## Must-read shared rules

Open **Global & Regional Domains** for `MAIN_URL` / regional hosts, Global vs Regional vs `G_R` user types, instance middleware, visitor country, and CMS `content_country_code`.