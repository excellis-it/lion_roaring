# PMA Super Admin Documentation — Design

**Date:** 2026-07-24  
**Status:** Approved (product direction); pending engineer review of this spec  
**Scope:** Super Admin–only Documentation menu in the PMA user panel (`/user`), covering sidebar features from Messaging through the last menu item

## Problem

The PMA user panel has many permission-gated menus (Messaging → Chatbot and Admin Portal CMS). There is no single, Super Admin–facing place that documents each area’s purpose, pages, features, rules, and conditions. When features change, knowledge stays in code and chats instead of a maintained project document. Cursor/Claude are not required to announce or update documentation when work starts.

## Goal

1. Add a **Documentation** sidebar item visible **only to Super Admin**, placed **after the last existing menu**.
2. Provide a **modern, hybrid** documentation UI: scannable overview cards, then per-section detail with expandable feature/rules blocks.
3. Store content as **version-controlled Markdown** under `docs/pma/` so agents and developers update it with feature work.
4. Enforce an **always-apply Cursor rule** (and Claude guidance if added later): at work start announce doc impact; on feature/rule/condition changes, update the matching markdown in the same change.

## Non-goals

- In-panel WYSIWYG editing by Super Admin
- Documenting the separate `/admin` site or Flutter app in v1
- Filling every section’s full detail on day one (stubs + index ship first; content grows menu-by-menu)
- Public/API docs (Scribe `/docs` remains separate)

## Decisions (locked)

| Topic | Choice |
|-------|--------|
| Content storage | Markdown files in repo (`docs/pma/`) |
| Depth | Hybrid: overview on index; full detail on section pages |
| Rollout | Ship menu + UI + agent rule first; fill sections incrementally |
| Sidebar placement | Last item after all existing menus |
| Approach | Laravel Markdown viewer + modern Blade UI |

## Architecture

| Piece | Responsibility |
|-------|----------------|
| `config/pma_documentation.php` | Ordered registry: slug, title, summary, icon, markdown path, status (`ready` \| `coming_soon`), sidebar key |
| `docs/pma/*.md` | One file per major sidebar area + `index.md` overview; YAML frontmatter |
| `User\DocumentationController` | Index + show; Super Admin only; render Markdown → HTML |
| `league/commonmark` | Convert Markdown to HTML (already present via Composer lock; require explicitly in `composer.json` if not a direct dep) |
| Routes | `/user/documentation`, `/user/documentation/{section}` under user middleware + `super_admin` |
| Sidebar | Last link; `@if (Auth::user()->hasNewRole('SUPER ADMIN'))` |
| Views | `resources/views/user/documentation/index.blade.php`, `show.blade.php` (+ partials) |
| CSS | Dedicated styles under `public/user_assets/css/` (or scoped section in existing assets) for modern docs chrome inside `user/layouts/master` |
| `.cursor/rules/pma-documentation.mdc` | Always-apply: announce at work start; update docs when PMA features/rules change |

### Access

- Middleware: existing `super_admin` (`SuperAdminMiddleware` → `hasNewRole('SUPER ADMIN')`)
- Direct URL without Super Admin → 403
- Non–Super Admin never sees the menu item

### Frontmatter (per markdown file)

```yaml
---
title: Messaging
updated: 2026-07-24
status: ready   # or coming_soon
sidebar_key: messaging
---
```

### Section registry (v1 stubs)

Ordered to match sidebar from Messaging through end (including Admin Portal): Messaging, Education, Bulletins, E-Store, Warehouse Store, E-Learning, Role Permission, Membership Management, All Members, User Activity, Signup Rules, Strategy, Policy & Guidance, Membership (self-service), Restore, Donations, Newsletters, Testimonials, Our Governance, Our Organizations, Organization Center, Services, Pages (CMS), Countries, Site Settings, Super Admin, Chatbot Assistant.

Status for all stubs at ship: `coming_soon` except `index` overview (`ready`). First real write-up when that area is next touched (prefer Messaging if documenting in the same release).

### Markdown → HTML flow

1. Resolve `{section}` against config; unknown slug → 404
2. Load file from `base_path('docs/pma/{file}')`
3. Parse frontmatter; convert body with CommonMark
4. Pass HTML + meta to Blade; accordion headings driven by `##` / `###` structure in markdown (or explicit HTML markers agreed in the writing plan)

## UX

### Index

- Title: “PMA Project Documentation”
- Short intro + client-side search filtering section cards
- Card grid: icon, title, one-line summary, Ready / Coming soon badge
- Click → section page

### Section page

- Sticky left mini-nav of all sections (current highlighted)
- Main column: overview, then expandable accordion for each sub-feature/page (rules, conditions, permissions, edge cases)
- “Last updated” from frontmatter
- Coming soon: friendly placeholder, not blank

### Visual direction

- Modern, readable docs UI inside existing PMA master layout
- Clear hierarchy, soft accents, generous spacing
- Avoid generic purple-gradient “AI template” look; align with PMA brand colors already in `user_assets`

## Agent / Cursor workflow

Always-apply rule content (summary):

1. **At work start:** state whether the task affects PMA documentation and which section(s), or that no doc update is needed.
2. **On feature / rule / condition change** for any documented sidebar area: update the matching `docs/pma/*.md` (and frontmatter `updated`) in the same change set.
3. **On new sidebar menu:** add config entry, markdown stub, and index card.
4. Do not invent undocumented product rules; document only what the code and product behavior actually enforce.

## Testing

- Super Admin sees Documentation as last sidebar item and can open index + a stub section
- Non–Super Admin: menu hidden; `/user/documentation` returns 403
- Unknown section slug returns 404
- Markdown with frontmatter renders; Coming soon status shows placeholder
- Search on index filters cards (manual / light JS)

## Out of scope follow-ups

- Full prose for every section in one pass
- Flutter / public site documentation
- In-app edit/publish workflow
- Automated “docs drift” CI checks
