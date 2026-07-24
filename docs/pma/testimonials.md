---
title: Testimonials
updated: 2026-07-24
status: ready
sidebar_key: testimonials
---

# Testimonials

## Overview

CMS for testimonials shown on the public website.

**Controller:** `User\Admin\TestimonialController`  
**Routes:** `user.admin.testimonials.*`

## Features

### Testimonial CRUD

- List, create, edit, delete testimonials.
- Content country code: Global defaults to US; Regional locked to own country code.

## Permissions and conditions

- Gates: `Manage Testimonials`, Create/Edit/Delete Testimonials.