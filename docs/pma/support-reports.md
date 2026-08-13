---
title: Support Reports
updated: 2026-08-13
status: ready
sidebar_key: support_reports
---

# Support Reports

## What this is

**Support Reports** is the in-panel help desk: any signed-in PMA user can submit a report and track its status. Staff with management access (or Super Admin) can see everyone’s reports, update status, and leave admin notes.

---

## Who sees the menu

**Everyone** who can use the PMA sees Support Reports. No special permission is required to submit your own reports.

---

## For every member

| Action | What it does |
|--------|----------------|
| **My Reports** | List of reports you submitted, with status badges |
| **Submit Report** | Subject, message, optional attachment (images/PDF/Word; size limited) |
| **View Report** | Your report detail, admin notes, attachment download |

You only see **your own** reports unless you are a manager or Super Admin.

---

## For managers (Manage Support Reports or Super Admin)

- Tabs such as **All Reports** and **My Reports**, plus status filters.
- Open any report and use the manage panel to change status and add notes.
- Saving a status/note **emails the submitter**.
- New submissions **email** users who have Manage Support Reports **and** Super Admins.

Super Admin always has full manage access even without the Spatie permission.

---

## Status flow

Reports move through:

**open → in_progress → resolved → closed**

There is **no delete**. Closing is how you finish a ticket.

---

## Mobile app

Members can submit and view their own reports in the app. Management UI stays on the web PMA. If the API is not deployed yet, the app may show Coming soon.

---

## Related documentation

- **User Activity** — automatic activity tracking (different from tickets)  
- **Change Logs** — product release notes (not support tickets)  
- **User PMA** — who can enter the panel
