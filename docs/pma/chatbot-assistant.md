---
title: Chatbot Assistant
updated: 2026-07-24
status: ready
sidebar_key: chatbot
---

# Chatbot Assistant

## Overview

PMA tools for the site chatbot. Behavior depends on `CHATBOT` env (`AI` vs normal).

**Controller:** `User\Admin\ChatbotController`  
**Routes:** `user.admin.chatbot.*`  
**Public:** `/chatbot/*` widget/API routes

## Features

### Dashboard / Keywords / History

- If `CHATBOT=AI`: sidebar Dashboard links to external `https://chatbot.lionroaring.us/`; in-app keyword/history may be hidden.
- Else: Dashboard, Keywords CRUD/bulk, Conversation history inside PMA.
- Seed also includes `View Chatbot Analytics`.

## Permissions and conditions

- Gates: `Manage Chatbot`, `Manage Chatbot Keywords`, `View Chatbot History`.
- RAG env vars when AI mode: `RAG_WIDGET_URL`, `RAG_API_BASE`, `RAG_BOT_ID`, `RAG_AUTH_TOKEN`.
- Frontend/ecom/elearning layouts include chatbot partials with AI widget fallback timeout behavior.