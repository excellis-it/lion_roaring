---
title: Change Logs
updated: 2026-08-13
status: ready
sidebar_key: change_logs
---

# Change Logs

## What this is

**Change Logs** are the official **release notes** for the platform, split into:

- **Web Version** — website / PMA web updates  
- **Mobile App Version** — Flutter app updates  

All signed-in PMA users can **read published** notes. Managers (or Super Admin) can create, edit, delete, and update the current version numbers.

---

## Who sees the menu

**Everyone** in the PMA can open Change Logs to read published entries.

Managing entries needs **Manage Change Logs** **or** Super Admin.

---

## For every member

- List of published entries (newest first), full description on the list (no separate detail page).
- Tabs for Web vs Mobile App.
- Header chip shows the **current** version for the selected platform (from stored version settings).
- Profile menu on web shows **Web Version** only (with a link here). Mobile version is not shown in the web profile menu.

Regular users never see unpublished or future-dated items.

---

## For managers

| Tool | Purpose |
|------|---------|
| **Update Versions** | Set current Web and Mobile version numbers (stored with Site Settings data; **not** edited on the Site Settings form) |
| **Create / Edit / Delete** | Maintain release note entries |
| Platform | Web or Mobile App |
| Fields | Version, title, type, rich description, publish date |

### Publish date rules (common confusion)

- Entries **publish as soon as you save** — there is no “schedule for next week” publisher.
- Publish date may be **now** or a **past** date (backdating a note is allowed).
- **Future** dates are blocked.
- Blank publish date means now (published).
- Timezones: the form uses the manager’s profile timezone sensibly so a past local time is not accidentally treated as unpublished.

### Entry types

| Type | Meaning |
|------|---------|
| feature | New capability |
| improvement | Enhancement |
| bugfix | Fix |
| security | Security fix |

---

## Mobile app

Members can read published logs in the app (Web/Mobile tabs). They cannot manage entries or versions there.

---

## Related documentation

- **Site Settings** — versions are stored with settings data but edited here  
- **User PMA** — where the menu sits  
- **Mobile App** hub — app-side behavior
