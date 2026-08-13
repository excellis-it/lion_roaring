---
title: Pages (CMS)
updated: 2026-08-13
status: ready
sidebar_key: pages
---

# Pages (CMS)

## What this is

**Pages** is the Admin Portal group of editors that power most **public website** content: home, about, FAQs, gallery, legal pages, footer, register agreements, and related pages.

Visitors never edit these screens. Staff edit here; the website reads the saved country-specific rows.

---

## Who sees Pages

The parent menu appears if you have **any** of the Manage-* page permissions (Home, Details, Organizations, About Us, FAQ, Gallery, Ecclesia Association, Principle and Business, Articles of Association, Footer, Register Agreements, PMA Terms, Privacy Policy, Terms and Conditions, and related).

Each child page still needs its own Manage permission. Create/Edit/Delete variants apply where the product defines them (for example FAQ and Gallery).

**Note:** Some permissions (such as Member Privacy Policy) may exist in the system without a matching child link in the current sidebar. Contact Us CMS may be commented out in the sidebar even if code exists.

---

## Content country — the rule everyone must understand

Public pages are stored **per country code** (US, IN, …), not usually as a single global blob.

| Who you are | What you edit |
|-------------|----------------|
| **Regional** staff | Only **your country** |
| **Global** user, or **Super Admin on the Global domain** | A **Content Country** dropdown (default **US**) so you can maintain each country’s copy |
| Everyone | Saving for country X never silently overwrites country Y |

### “Why do I see US content for another country?”

If the country you selected has **no saved row yet**, the editor shows **US content as a starting draft** and explains that with a banner.

- What you see is a **preview**, not permission to edit the US row as US.
- When you **Save**, the system creates a **new** row for the selected country.
- The original US row is **not** overwritten.
- Images/files may be **reused by path** from US on first save if you did not upload new media (shared storage paths — US files are not deleted).

### Multi-row pages (FAQ, Gallery, lists)

If a non-US country has **no rows**, you may see US items as read-only **US drafts** (badge, no edit/delete) until that country has its own saved rows.

### Reordering

Drag-reorder ignores draft rows that do not exist in the database yet (important for **Our Governance** and similar lists).

---

## Pages you can edit (typical children)

| Child | Public purpose (plain language) |
|-------|----------------------------------|
| Home | Homepage content; banner visibility per country |
| Details | Details page content |
| Organization CMS | Organization-related CMS blocks |
| About Us | About page |
| FAQs | FAQ list |
| Gallery | Gallery items |
| Ecclesia Association | Ecclesia association page |
| Principle and Business Model | Principle / business model page |
| Articles of Association | Articles page |
| Footer | Footer logo, address, phone, email, newsletter title, copyright |
| Register Page Agreements | Agreements shown on registration |
| PMA Terms | PMA terms / disclaimer content |
| Privacy Policy | Privacy policy |
| Terms and Conditions | Terms |

### Home-specific notes

- **Show banner image for this country** controls whether the hero banner image appears for that country’s visitors.
- Some older Home fields (certain video/book sections) are no longer shown in the form because the public site does not use them; old database values may still exist unused.

### Footer-specific notes

- Footer admin includes logo, flag, title, address, phone, email, newsletter title, copyright.
- Play Store / App Store / social link rows are **not** shown in the current Footer admin because the public footer does not use them.

---

## Permissions (examples)

Parent visibility uses Manage permissions such as:

- Manage Home Page, Manage Details Page, Manage Organizations Page, Manage About Us Page  
- Manage Faq (+ Create/Edit/Delete), Manage Gallery (+ CRUD)  
- Manage Ecclesia Association Page, Manage Principle and Business Page, Manage Article of Association Page  
- Manage Footer, Manage Register Page Agreement Page, Manage PMA Terms Page  
- Manage Privacy Policy Page, Manage Terms and Conditions Page  

---

## Related documentation

- **Testimonials** / **Our Governance** / **Our Organizations** — same content-country patterns  
- **Countries** — country codes and domains  
- **Website Frontend** — what visitors see  
- **Global & Regional Domains** — who may pick content country
