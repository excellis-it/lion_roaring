---
title: Chatbot Guides
updated: 2026-08-03
status: ready
sidebar_key: chatbot_guides
---

# Chatbot Guides

**Lion Roaring link:** When `CHATBOT=AI`, PMA Dashboard opens the external chatbot portal (`https://chatbot.lionroaring.us/`). See also **Chatbot Assistant**.

## 1. User Guide

### 1.1 Introduction

The Multi-Tenant RAG Chatbot System is an enterprise-grade SaaS platform designed to let multiple business tenants construct, train, customize, and deploy AI support assistants on their websites. Each business operates in complete database and vector store isolation, ensuring data privacy.

The primary purpose is to simplify customer support, lead capture, and website-specific information retrieval. By crawling a business's public website or importing custom Q&A items, the system builds a semantic knowledge base. A customized chat widget embedded on their pages uses this knowledge to respond to visitor inquiries instantly. If the AI cannot resolve the query, the chat seamlessly routes to human support representatives.

### 1.2 Onboarding & Registration

New tenants must register through the system registration page to provision isolated environments. Registration requires entering a name, email address, username, and a password of at least six characters.

Upon registration, the system dynamically provisions a dedicated MongoDB database, a custom folder for vector embeddings, and generates API endpoints.

### 1.3 User Login

Once registered, business owners can log in using their username or email address and password. Upon successful validation, the backend issues a JSON Web Token (JWT) that manages user sessions and secures dashboard API calls.

### 1.4 Dashboard Overview

The User Dashboard page acts as the central control hub. It lists total websites created, total conversations logged, leads captured, and active crawler schedulers. Business owners can review their websites, check scraping status, configure options, customize chat widget colors, or view recent leads in real-time.

### 1.5 Website Management & Scraping

Tenants train the chatbot by indexing website URLs. Adding a website requires configuring a start URL and optional sitemap URL. Tenants can adjust settings such as depth limits (how deep the crawler follows internal links) and limit the maximum links crawled per page. For sites built on modern JavaScript frameworks, the Playwright rendering option compiles the page layout before extracting text. Scrapes can be triggered immediately or scheduled to run every two hours to keep the bot updated.

### 1.6 Crawling & Installation Workflow

From the Websites list, click the gear actions icon to open the Website Actions page. Three primary workflows:

- **Run Crawl** — Initiate website crawling. The crawler parses pages and adds extracted content to the knowledge base.
- **Add Knowledge** — Open the Knowledge Base page to manually add custom Q&A items.
- **Install Widget** — Open the installation script pop-up for web and mobile deployment.

#### Installing the Chatbot Widget

Clicking **Install Widget** displays a modal with widget configuration and an HTML script snippet. Copy this snippet and paste it into the website HTML just before the closing `</body>` tag. The snippet loads the widget, passes the bot ID and authentication token, and integrates the chatbot on the site.

Lion Roaring wires the same RAG widget via env: `RAG_WIDGET_URL`, `RAG_API_BASE`, `RAG_BOT_ID`, `RAG_AUTH_TOKEN` (and mobile `MOBILE_CHATBOT_URL`).

#### Adding Manual Knowledge

If certain information is not on the website, or to override default answers, use **Add Knowledge**. Custom Q&A pairs are indexed instantly alongside scraped pages.

**Important limitation:** The crawler ignores document downloads exceeding 5 megabytes. It cannot bypass CAPTCHAs or pages protected by login screens.

### 1.7 Live Support Agent Management

Business owners can delegate chats to support representatives. Under settings, tenants create sub-accounts (name, email, username, password). Agents log in to their own dashboard to handle live chat handoffs.

### 1.8 Chat Widget Customization & Embedding

Tenants can customize widget title, header colors, background shades, message bubble colors, and upload a custom avatar. After customization, copy the auto-generated HTML snippet before the closing body tag.

### 1.9 Voice Chat Integration

Visitors can use browser-native Speech-to-Text to speak messages. Text-to-Speech reads answers aloud, with male/female voice options.

### 1.10 Live Support Handoff & Messaging Queue

By default the chatbot answers visitor questions. If the visitor requests a human agent, the widget triggers a handoff. When agents are online and available, the conversation is queued. When an agent accepts, status changes to human mode and messages flow in real time via Socket.IO.

Agents can enable file uploads (up to 5MB). Sound notifications play on the agent console for new messages.

**Platform limitations:** No group chats, message reactions, mentions, or message modifications/deletions. Audio/video calling, screen sharing, call recordings, and live captions are not implemented.

## 2. Admin Guide

### 2.1 System Administration

Platform administrators share the same portal with system-wide access. The Admin Dashboard shows consolidated metrics: registered business accounts, system-wide chatbots, active schedulers, database connection pool statistics, and audit logs.

### 2.2 Tenant Provisioning & Limits

Administrators adjust bot and agent limits. Defaults: max bots per tenant 1 (max 10); max agent accounts 0 (max 50). Suspending a tenant deactivates all logins; deleting a tenant removes associated database records, scheduler processes, and vector folders.

### 2.3 System Audit & Action Logs

Logged events include registrations, login/logout, crawler status triggers, and agent updates. Logs are immutable in the primary database.

### 2.4 Maintenance & Configuration Settings

Administrators manage configurations via environment variables (database connections, backend ports, token secrets, fallback thresholds). When a tenant updates their knowledge base, administrators can trigger vector reloads to refresh bot memory cache.

## 3. AI Usage Guide

### 3.1 Retrieval-Augmented Generation (RAG)

Pipeline:

1. Visitor sends a query.
2. System vectorizes the query with the semantic embedding model.
3. Similarity search in the tenant's isolated database retrieves matching passages.
4. A reranking model ranks passages for the most relevant context.
5. Top passages plus a system prompt are sent to the LLM (Anthropic Claude).
6. The LLM generates a response with source citations.

### 3.2 Key Fallbacks & Performance

Primary model: Anthropic Claude. Fallback: Google Gemini if Claude hits rate limits. Typical latency 1.2–3.0 seconds. Reranking can be disabled to speed responses.

### 3.3 Data Privacy

Tenant data is isolated (separate vector files and databases). Embedding models run locally so raw content is not shared with external APIs for vectorization.

## 4. System Architecture

### 4.1 Architecture Stack

- **Frontend:** React SPA (dashboards and widget rendering)
- **Backend:** Express API (auth, Socket.IO messaging, database routing)
- **RAG Bot Engine:** FastAPI (vector embeddings and LLM completions)
- **Scraper Worker:** Python background scraping service

### 4.2 Multi-Tenant Resource Isolation

Database connections are pooled and cached; closed after 60 seconds of inactivity. Vector files live in separate filesystem directories per tenant.

## 5. Developer Guide

### 5.1 Environment Configuration

Configure database connections, token secrets, and API credentials via environment file.

### 5.2 Local Installation

Requires Node.js and Python. Install dependencies in a Python venv; run backend, bot, worker, and frontend on dedicated ports.

### 5.3 Docker Compose Deployments

Docker Compose builds the React frontend and starts all services. Production typically uses Nginx to route backend, frontend, and bot containers.

## 6. AI Prompt Guide

### 6.1 Synthesis Prompt

Instructs the LLM to act as a customer support assistant, restrict answers to provided context, enforce a short response limit, and redirect to human agents when requested.

### 6.2 Matcher & Translation Prompts

- **Matcher** — maps visitor queries to the most relevant page URL.
- **Translation** — translates system messages into the visitor's language (cached).

## 7. FAQ & Troubleshooting

**Q: Why is my website crawl status stuck on "Running"?**  
A: The crawler may be stuck in a redirection loop. Check the log file in the tenant's vector directory, and reduce the crawl depth setting.

**Q: The bot replies that it doesn't know the answer. How can I fix this?**  
A: The bot only answers from indexed content. Verify the website crawled successfully, or add manual Q&A under the knowledge tab.

## 8. Glossary

- **RAG (Retrieval-Augmented Generation)** — retrieves relevant context from a database to generate accurate answers.
- **ChromaDB** — vector database for page embeddings.
- **Handoff** — transferring a visitor chat session from AI to a live agent.
