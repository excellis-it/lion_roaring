---
title: Chatbot Assistant
updated: 2026-07-24
status: ready
sidebar_key: chatbot
---

# Chatbot Assistant

## Overview

PMA tools for the site chatbot. Behavior depends on `CHATBOT` env (`AI` vs `NORMAL`).

**Controller:** `User\Admin\ChatbotController`  
**Routes:** `user.admin.chatbot.*`  
**Public:** `/chatbot/*` widget/API routes

## Features

### Dashboard / Keywords / History

- If `CHATBOT=AI`: sidebar Dashboard links to external `https://chatbot.lionroaring.us/`; in-app keyword/history may be hidden.
- Else: Dashboard, Keywords CRUD/bulk, Conversation history inside PMA.
- Seed also includes `View Chatbot Analytics`.

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