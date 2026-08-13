---
title: User PMA
updated: 2026-08-13
status: ready
sidebar_key: user_pma
---

# User PMA

## What this is

The **User PMA** is the signed-in control panel at `/user/…`. Members use it for everyday work (messaging, education, bulletins). Staff and Super Admins also use it to manage people, membership, website content, and support.

This hub explains panel-wide rules that apply **before** any single menu. Open the topic cards below this page for each sidebar module.

For shared domain behavior (which website host you must use), see **Global & Regional Domains**.

---

## Who can enter the panel

Almost every PMA page requires all of the following:

1. **Logged in** with an **active** account.
2. **Membership access** — a non-expired subscription, **or** the account is marked **excluded from membership**, **or** the user is **Super Admin**.
3. **Register agreement signed** — unless the user is **Super Admin**.
4. **Correct website** for their User Type (Global vs Regional vs G_R). Wrong host can log them out.

If someone can log in on the public site but cannot use the PMA, check membership expiry, exclusion flag, agreement signature, and whether they opened the Global site vs their country site.

---

## How the sidebar works

- Menus appear only when the person has the matching **permission** (for example Manage Chat), or in a few cases a **role** check (Super Admin).
- **Support Reports** and **Change Logs** appear for everyone in the panel; managing other people’s items needs extra permission or Super Admin.
- **Membership** (self-service) appears for people who are **not** Super Admin and **not** membership-excluded.
- **Restore** and **Documentation** appear only for **Super Admin**.
- **E-Store**, **Warehouse Store**, and **E-Learning** admin menus are documented under the E-Store and E-Learning hubs.

---

## Menu map (what each area is for)

| Area | Plain-language purpose |
|------|------------------------|
| **Messaging** | Chats, Team spaces, Mail |
| **Education** | Topics, becoming tracks, files |
| **Bulletins** | Board, jobs, meetings, events, private collaboration |
| **Role Permission** | Role templates and what each role can do |
| **Membership Management** | Plans, subscriber list, payments, promos, settings |
| **All Members** | Directory of partners/members; edit; export; audit |
| **Create Member** | Add a new person (from All Members → Add Members) |
| **User Activity** | Who did what in the panel |
| **Signup Rules** | Rules for **public** registration only |
| **Strategy / Policy & Guidance** | Country-scoped document libraries |
| **Membership** (self-service) | Buy, renew, or manage **your own** plan |
| **Restore** | Recycle bin (Super Admin) |
| **Donations / Newsletters / Testimonials / Governance / Orgs / Services / Pages** | Website content and public-site data |
| **Countries** | Country records, domains, languages |
| **Site Settings** | Logos, contact keys, sidebar menu labels |
| **Super Admin** | List of Super Admin accounts |
| **Chatbot Assistant** | Chatbot admin tools |
| **Support Reports** | Help tickets |
| **Change Logs** | Release notes for web and mobile |
| **Documentation** | This documentation UI (Super Admin) |

---

## User Types — why people get confused

| User Type | Simple meaning |
|-----------|----------------|
| **Global** | Uses the Global site; not a single regional country host |
| **Regional** | Belongs to one country instance |
| **G_R** | May use Global **or** their assigned regional country — not other regionals |
| **Super Admin** | Full operator role; bypasses membership, agreement, and many list filters |

Creating the wrong User Type for a new member is a top support issue. See **Create Member** and **Global & Regional Domains**.

---

## Global vs Regional staff — what they see

### Global users

- Must use the Global domain.
- Partner lists: typically Global and G_R people.
- Website CMS editors often get a **Content Country** picker (default US).
- Education / strategy-style libraries on the global server often use the **GL** country scope.

### Regional users

- Must use their country host; wrong host → logout.
- CMS locked to their country.
- Partner lists: same-country Regional and G_R.
- Education/files scoped to their country; ecclesia admins may see a narrower house list.

### G_R users

- Allowed on Global or their assigned regional.
- On Global, often treated like Global for library scope.
- On regional, own-country scope.

### Super Admin

- Bypasses membership, agreement, and instance locks.
- Usually sees unscoped lists and can pick countries.
- Only role with **Restore** and **Documentation**.

---

## Language and names

- The panel can use the same translation tools as the public site.
- **Person names and usernames should stay untranslated** — the product protects them on purpose.
- A short **Translating…** badge may appear while a page is converting language.

---

## Documentation UI (this area)

- Last item in the sidebar for Super Admins.
- Index shows product hubs; each hub can list deeper topics.
- When product behavior changes, these pages should be updated so staff are not trained on old rules.

---

## Related topics

Start with **Create Member** if you are onboarding people, **All Members** for the directory, **Membership Management** for plans, and **Global & Regional Domains** for host/login rules.
