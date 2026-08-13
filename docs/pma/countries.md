---
title: Countries
updated: 2026-08-13
status: ready
sidebar_key: countries
---

# Countries

## What this is

**Countries** is where staff maintain the country records that drive:

- Which **domain / host** belongs to which country  
- **CMS content** country codes  
- **Languages** offered on the public site and chatbot for that context  
- Much of **Global vs Regional** behavior  

Deep host and user-type rules live in **Global & Regional Domains**. This page explains the Countries admin screen itself.

---

## Who can use it

Permission: **Manage Countries**.

---

## What you can edit

For each country: name, code, domain, languages, flag, status, and related fields used across redirects and scoping.

### Languages

Languages assigned to a country (including the special **Global / GL** country) drive:

- Public language switcher options for that context  
- Chatbot language list for that context  

Global does **not** automatically expose every language in the whole catalog — only languages linked to GL (plus Original where the UI adds it).

---

## Critical rules

1. **Global country code is `GL`.**  
2. You **cannot delete** or **toggle status** of the global (`is_global`) country.  
3. Public country dropdowns **exclude** the Global row — visitors pick real countries, not GL.  
4. Changing a country’s **domain** changes which website host is treated as that country’s instance. Coordinate with main/URL configuration before editing live domains.  
5. Create Member and partner forms also exclude GL from the normal country picker — people are assigned a real country, not GL.

---

## Related documentation

- **Global & Regional Domains** — full domain / user-type guide  
- **Pages (CMS)** — content per country code  
- **Chatbot Assistant** — languages come from country assignments  
- **Create Member** — country on new people
