---
title: Chatbot Assistant
updated: 2026-08-13
status: ready
sidebar_key: chatbot
---

# Chatbot Assistant

## What this is

**Chatbot Assistant** is the PMA area for operating the site chatbot. What you see depends on whether the site runs in **AI** mode or **NORMAL** mode (configured by operations with the `CHATBOT` setting).

For the full RAG chatbot manuals (user, admin, AI, developer), open the **Chatbot Guides** hub.

---

## Who can use it

| Area | Permission |
|------|------------|
| Parent / Dashboard | **Manage Chatbot** |
| Keywords | **Manage Chatbot Keywords** |
| History | **View Chatbot History** |

---

## AI mode vs NORMAL mode

| Mode | What staff see in PMA |
|------|------------------------|
| **AI** | Dashboard often opens the **external** chatbot admin (`chatbot.lionroaring.us`). In-app Keywords / History may be **hidden**. |
| **NORMAL** | Dashboard, Keywords, and History stay **inside** the PMA. |

If Keywords disappeared after a configuration change, check whether the site was switched to AI mode — that is expected, not a missing permission by itself.

---

## Languages

Chatbot language options follow the **visitor country / Global country language assignments** in **Countries** (including Original). On Global, this is **not** “every language in the database” — only languages linked to the Global country (plus Original).

Conversation language can be stored as Original (`__original__`), meaning “do not machine-translate.”

---

## Mobile app

The app shows a Chatbot entry driven by the same mode:

- **AI** + configured mobile URL → opens that URL in an in-app browser view  
- **NORMAL** (or empty URL) → built-in assistant screen  

Members do not use this PMA admin menu for that; they use the app/website widget.

---

## Related documentation

- **Chatbot Guides** — full suite  
- **Countries** — which languages appear  
- **Website Frontend** — public widget behavior  
- **Site Settings** — related configuration keys may live with ops/env
