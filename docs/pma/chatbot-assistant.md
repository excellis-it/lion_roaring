---
title: Chatbot Assistant
updated: 2026-08-03
status: ready
sidebar_key: chatbot
---

# Chatbot Assistant

## Overview

PMA tools for the site chatbot. Behavior depends on `CHATBOT` env (`AI` vs `NORMAL`).

For the full Multi-Tenant RAG Chatbot suite (user/admin/AI/developer guides), open **Chatbot Guides**.

**Controller:** `User\Admin\ChatbotController`  
**Routes:** `user.admin.chatbot.*`  
**Public:** `/chatbot/*` widget/API routes

## Features

### Dashboard / Keywords / History

- If `CHATBOT=AI`: sidebar Dashboard links to external `https://chatbot.lionroaring.us/`; in-app keyword/history may be hidden.
- Else: Dashboard, Keywords CRUD/bulk, Conversation history inside PMA.
- Seed also includes `View Chatbot Analytics`.

### Conversation language

- `chatbot_conversations.language` is `VARCHAR(20)` (not 10) so values like `__original__` from the language switcher can be stored on init without truncation errors.
- `__original__` is stored as-is (UI sentinel for “Original”); chatbot machine-translation skips that value and leaves content unchanged.
- `POST /chatbot/language` uses `firstOrCreate` on `session_id` so the RAG widget can change language even if `/init` has not finished (avoids a 404 when the conversation row is missing).
- `GET /chatbot/languages` uses `Helper::getVisitorCountryLanguages()` — on Global (`GL`) this is the languages assigned to the Global country in Country Management (plus `Original`), not the full TranslateLanguage catalog.

### Mobile app sidebar (Chatbot)

- Label: **Chatbot** (chat icon). Driven by `CHATBOT` + `MOBILE_CHATBOT_URL` (config `lion_roaring.*`).
- Exposed on `/api/v3/cms/site-settings` as `chatbot_mode` and `mobile_chatbot_url` (no DB migration).
- `CHATBOT=AI` and non-empty URL → Flutter opens that URL in a JS WebView (AppBar + back).
- `CHATBOT=NORMAL` (or empty URL) → existing in-app chat assistant screen.

## Permissions and conditions

- Gates: `Manage Chatbot`, `Manage Chatbot Keywords`, `View Chatbot History`.
- RAG env vars when AI mode: `RAG_WIDGET_URL`, `RAG_API_BASE`, `RAG_BOT_ID`, `RAG_AUTH_TOKEN`.
- Mobile WebView URL: `MOBILE_CHATBOT_URL`.
- Frontend/ecom/elearning layouts include chatbot partials with AI widget fallback timeout behavior.