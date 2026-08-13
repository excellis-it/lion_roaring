---
title: User Activity
updated: 2026-08-13
status: ready
sidebar_key: user_activity
---

# User Activity

## What this is

**User Activity** shows engagement and actions people take while using the PMA (dashboard summaries and a detailed activity list). Staff use it to understand usage, investigate issues, or export activity for review.

---

## Who can use it

- Sidebar: **Manage User Activity**
- Related View / Create / Edit / Delete activity permissions may exist on roles for finer control
- **Export** requires Manage User Activity

---

## What you will find

### Activity Dashboard

High-level view of activity. Super Admin dashboards are typically **global** (not forced to one country filter).

### Activity List

Searchable/filterable list of activity rows, with export.

Export is designed for large sets (chunked downloads) and can be cancelled if a run is too large.

---

## Where the data comes from

Panel requests are tracked by activity middleware while people use `/user/…`. If someone never opens the PMA, they will not generate PMA activity here.

This is different from **Support Reports** (tickets people submit) and **All Members → Audit Logs** (role/permission/member field changes).

---

## Related documentation

- **All Members** — audit logs for member/permission changes  
- **Support Reports** — human-submitted tickets  
- **User PMA** — panel entry rules
