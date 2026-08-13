---
title: Signup Rules
updated: 2026-08-13
status: ready
sidebar_key: signup_rules
---

# Signup Rules

## What this is

**Signup Rules** configure validation for **public self-registration** (website / app sign-up). Examples: required fields, formats, regex checks, priority order, and whether a rule is **critical** (blocks signup).

These rules do **not** run when staff use **All Members → Add Members** (**Create Member**). Admin-created members skip this engine.

---

## Who can use it

- Sidebar: **Manage Signup Rules**
- Create / edit / delete rules need the matching Create / Edit / Delete Signup Rules permissions where your roles define them.

---

## How rules behave

| Concept | Meaning |
|---------|---------|
| **Active** | Only active rules are applied |
| **Priority** | Higher priority runs with more weight in ordering (highest first) |
| **Critical** | Failing this rule **blocks** registration |
| **Non-critical** | May warn or apply softer validation depending on configuration |
| **Field / type / regex** | What is checked on the signup payload |

Inactive rules are ignored.

---

## What staff should tell support

1. “Why did public signup reject this email?” → check Signup Rules.  
2. “Why did Create Member allow it?” → Create Member does not use Signup Rules.  
3. Register page legal text / agreements are under **Pages (CMS)** (Register Page Agreements), separate from these validation rules.

---

## Related documentation

- **Create Member** — admin onboarding path  
- **Pages (CMS)** — register agreements and public pages  
- **All Members** — after someone exists in the directory
