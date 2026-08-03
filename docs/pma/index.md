---
title: Project Documentation
updated: 2026-08-03
status: ready
sidebar_key: documentation
---

# Project Documentation

Start with the product surfaces below, then open Chatbot Guides, Improvement Performance Report, Automation Implementations, or E-Store & E-Learning Enhancement. Use **Detailed topics** under each product hub for menu-by-menu rules.

## How to use

1. Open a **hub** card (1–9 below).
2. Read Overview, domain rules, and feature blocks.
3. Use **Detailed topics** for sidebar menus and shared domain rules.
4. When behavior changes, update the matching `docs/pma/*.md` file and bump `updated`.

## Surfaces

| # | Hub | Path / app | Audience |
|---|-----|------------|----------|
| 1 | Website Frontend | `/` | Public visitors |
| 2 | User PMA | `/user/*` | Authenticated members & admins |
| 3 | E-Learning | `/e-learning` + PMA admin | Logged-in users |
| 4 | E-Store | `/e-store` + PMA admin | Members (membership required) |
| 5 | Mobile App | Flutter + `/api/v3` | App users |
| 6 | Chatbot Guides | RAG chatbot suite | Super Admins / AI chatbot ops |
| 7 | Improvement Performance Report | E-Store & E-Learning QA | Dev / ops |
| 8 | Automation Implementations | Account management automation | PMA admins |
| 9 | E-Store & E-Learning Enhancement | Store & learning enhancements | Dev / admins |

## Must-read shared rules

Open **Global & Regional Domains** for `MAIN_URL` / regional hosts, Global vs Regional vs `G_R` user types, instance middleware, visitor country, and CMS `content_country_code`.
