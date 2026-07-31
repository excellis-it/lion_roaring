# Lion Roaring — Release History

Living changelog for the team and Cursor agents. **Newest entries first.**

When you ask for change logs, the agent should: read this file for the last versions → propose the next bump → return PMA paste text → **update this file**.

Keep in sync with the mobile app copy: `lion-roaring-app/CHANGELOG.md`.

---

## Current versions

| Platform | Version | Status | Updated |
|----------|---------|--------|---------|
| Web | v2.1.2 | published | 2026-07-28 |
| Mobile App | v1.0.39 | published (app build `1.0.39+44`) | 2026-07-31 |

Last **published** in PMA / Site Settings: Web `v2.1.2` · Mobile `v1.0.39`.

---

## Entries

### Mobile App — v1.0.39

- **Status:** published
- **Type:** improvement
- **Date:** 2026-07-31
- **Title:** Cancel downloads, wider chat media & ownership controls
- **Description:**
  - Cancel in-progress downloads from the progress dialog across Files, Chat, Mail, Policies, and related screens
  - Chat and Team Chat support more image and video formats (including HEIC/HEIF and JFIF), with clearer messages when a video cannot play
  - Edit and delete actions on lists (Bulletins, Jobs, Policies, Strategies, and similar) only show for your own records or Super Admin — matching the web PMA
  - Android storage permissions tightened for store compliance; previously stored passwords are cleared for security
  - More reliable live chat connections and safer drawer layout on notched devices

### Mobile App — v1.0.38

- **Status:** published
- **Type:** feature
- **Date:** 2026-07-28
- **Title:** Support Reports, Change Logs & chat media
- **Description:**
  - Members can browse published Change Logs in the app (web and mobile), with clearer HTML/bullet formatting
  - Members can submit and track Support Reports from the app
  - Chat videos play in-app; images open fullscreen, with download from the viewer/player toolbar
  - File downloads work more reliably on Android and iOS (including Files app access on iOS)

### Web — v2.1.2

- **Status:** published
- **Type:** improvement
- **Date:** 2026-07-28
- **Title:** Protect names from auto-translate
- **Description:**
  - Personal names and usernames stay untranslated when Google Website Translator is used across the site, PMA, and emails
  - Change Log create/edit forms are clearer for publishing release notes
  - Mobile APIs added so members can use Support Reports and Change Logs in the app

### Mobile App — v1.0.37

- **Status:** published
- **Type:** improvement
- **Date:** 2026-07-27
- **Title:** Stability & Version Alignment Release
- **Description:**
  - Version aligned with the PMA Change Logs system for clearer release tracking
  - General stability and performance improvements
  - Preparation for upcoming in-app release notes visibility

### Web — v2.1.1

- **Status:** published
- **Type:** feature
- **Date:** 2026-07-27
- **Title:** Support Reports, Change Logs & App Version Tracking
- **Description:**
  - Support Reports — all members can submit support reports with optional file attachments and track status
  - Support Reports Management — staff with permission (and Super Admin) can view all reports, update status, and reply with notes; email notifications on submit and status update
  - Change Logs — release notes with separate Web Version and Mobile App Version tabs
  - App Versions — set current Web and Mobile versions from Change Logs management
  - Profile menu shows current Web version with a link to Change Logs
  - Role permissions added: Manage Support Reports and Manage Change Logs
