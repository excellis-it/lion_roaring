---
title: Site Settings
updated: 2026-08-13
status: ready
sidebar_key: site_settings
---

# Site Settings

## What this is

**Site Settings** holds global configuration keys used across the website and PMA (logos, contact email, and similar), plus **Menu Names** — the display labels shown in the sidebar.

---

## Who can use it

| Sub-menu | Permission |
|----------|------------|
| Settings | **Manage Site Settings** |
| Menu Names | **Manage Menu Settings** |

Parent appears if either permission is present.

---

## Settings tab

Edit the shared settings values your organization uses for branding and contact.

### App version numbers — important

`WEB_APP_VERSION` and `MOBILE_APP_VERSION` are **stored** with Site Settings data, but staff are expected to change them from **Change Logs → Update Versions**, not from this Settings form.

If versions look wrong in the profile menu or Change Logs header, fix them under Change Logs (needs **Manage Change Logs** or Super Admin).

---

## Menu Names tab

Changes only the **label text** people see in the sidebar (for example renaming “All Members”). It does **not** change permissions. Someone can still be blocked by missing Manage Partners even if the label says something friendlier.

---

## Related documentation

- **Change Logs** — update web/mobile version numbers  
- **Role Permission** — real access control  
- **User PMA** — how menus appear
