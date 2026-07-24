---
title: Policy & Guidance
updated: 2026-07-24
status: ready
sidebar_key: policy_guidance
---

# Policy & Guidance

## Overview

Same document-library pattern as Strategy for policy and guidance files.

**Controller:** `User\PolicyGuidenceController`  
**Routes:** `policy-guidence.*`

## Features

### Document library

- Upload, list, view, download, delete policy documents.
- Country-scoped like Strategy.

## Permissions and conditions

- Gates: `Manage Policy`, `Upload|Download|View|Delete Policy`.
- Super Admin unscoped; Regional/Global scoping for others.