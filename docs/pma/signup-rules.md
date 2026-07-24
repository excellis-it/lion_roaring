---
title: Signup Rules
updated: 2026-07-24
status: ready
sidebar_key: signup_rules
---

# Signup Rules

## Overview

Configurable validation rules applied during registration/signup flows.

**Controller:** `User\SignupRuleController`  
**Routes:** `user.signup-rules.*`, `toggle-status`, `check-status`

## Features

### Rule management

- Fields: `field_name`, `rule_type`, `rule_value` (regex validated), `is_active`, `is_critical`, `priority`.
- Ordered by priority descending.
- Toggle active status; check-status helper endpoint.

## Permissions and conditions

- Gates: `Manage|Create|Edit|Delete Signup Rules` (`Gate::check` + `abort_unless`).
- Critical rules block signup when violated; inactive rules are ignored.