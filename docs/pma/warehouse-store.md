---
title: Warehouse Store
updated: 2026-07-24
status: ready
sidebar_key: warehouse_store
---

# Warehouse Store

## Overview

Sidebar block shown when `Auth::user()->warehouses->count() > 0`. Gives warehouse-scoped access to Warehouses, Warehouse Products, and Warehouse Orders without requiring the full E-Store admin permission set.

## Features

### Warehouse-scoped navigation

- Links reuse `ware-houses.index`, `products.index`, `user.store-orders.list` with warehouse admin capabilities.
- Controllers enforce warehouse admin checks (`isWarehouseAdmin()`) for product/size/color management where applicable.

## Permissions and conditions

- Visibility condition: user has one or more related warehouses.
- Still behind `/user` stack: login, membership (unless excluded/SA), agreement signed.
- See **E-Store** for full commerce and order rules.