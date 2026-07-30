# Lion Roaring — Release History

Living changelog for the team and Cursor agents. **Newest entries first.**

When you ask for change logs, the agent should: read this file for the last versions → propose the next bump → return PMA paste text → **update this file**.

Keep in sync with the mobile app copy: `lion-roaring-app/CHANGELOG.md`.

---

## Current versions

| Platform | Version | Status | Updated |
|----------|---------|--------|---------|
| Web | v2.2.0 | draft (ready to publish) | 2026-07-30 |
| Mobile App | v1.0.38 | draft (ready to publish; app build `1.0.38+43`) | 2026-07-28 |

Last **published** in PMA / Site Settings: Web `v2.1.1` · Mobile `v1.0.37`.

---

## Entries

### Web — v2.2.0

- **Status:** draft (ready to publish)
- **Type:** feature
- **Date:** 2026-07-30
- **Title:** New translation system (Google Cloud Translation)
- **Description:**
  - Replaced the free Google Translate widget with Google Cloud Translation, so translation is reliable across the website, PMA, e-Store and e-Learning
  - Switching language no longer reloads the page, and "Original" restores the source text instantly
  - Personal names, usernames and icons are never translated anywhere, including content loaded after the page opens
  - Translations are cached permanently, so each phrase is translated once and repeat visits are instant
  - Admin panel remains English-only by design

### Mobile App — v1.0.38

- **Status:** draft (ready to publish)
- **Type:** feature
- **Date:** 2026-07-28
- **Title:** Support Reports, Change Logs & chat media
- **Description:**
  - Members can browse published Change Logs in the app (web and mobile), with clearer HTML/bullet formatting
  - Members can submit and track Support Reports from the app
  - Chat videos play in-app; images open fullscreen, with download from the viewer/player toolbar
  - File downloads work more reliably on Android and iOS (including Files app access on iOS)

### Web — v2.1.2

- **Status:** draft (ready to publish)
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
