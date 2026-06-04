# Quickstart: Verifying the Phase 3 Cache

**Feature**: 003-cache-shipping-zones | **Date**: 2026-06-04

Behavior-based verification (constitution Principle VII). No automated test suite is
required for this phase; the checks below confirm the contract. Run them after
implementing the change in `includes/CustomShippingZones.php`.

## Prerequisites

- WordPress + WooCommerce active, plugin installed on branch `csz-wp7-readiness`.
- Admin user with `manage_woocommerce`.
- A persistent object cache is **not** required (the cache is request-scoped).

## 0. Static check

```powershell
php -l includes/CustomShippingZones.php
```

Expect: `No syntax errors detected`. No `npm run build` is needed (no JS change).

## 1. Output is unchanged — with regions (C1, SC-003)

1. Go to **WooCommerce → Settings → Custom Shipping Zones**.
2. Add a custom region to a country (e.g. `US` → code `US-TEST`, name `Test Region`).
3. On a page where WooCommerce lists states for that country (e.g. a shipping-zone
   "states" selector, or the checkout state dropdown), confirm `Test Region` appears.

Expect: the custom region shows up exactly as before this change.

## 2. Output is unchanged — without regions (C6)

1. On a country with **no** custom regions, view its state list.

Expect: WooCommerce's default states only; no error, no missing/extra entries.

## 3. Save reflects immediately (C3)

1. Add a new region and save.
2. Without clearing any cache, reload a page that builds the states list.

Expect: the new region is present on the next build.

## 4. Delete reflects immediately (C4)

1. Delete a region you added.
2. Reload a page that builds the states list.

Expect: the deleted region is gone on the next build.

## 5. Add/delete still work end-to-end (FR-010)

Repeat add → verify → delete → verify a couple of times.

Expect: each operation succeeds and is reflected; no stale entries linger.

## 6. (Optional) Read-count check (C2, SC-001 / SC-002)

Use **Query Monitor**, a profiler, or a *temporary* counter to count
plugin-attributable `get_option('{cc}_custom_shipping_zones')` reads on a page that
triggers the `woocommerce_states` filter more than once.

- **Before**: reads scale with (countries-with-regions × filter invocations).
- **After**: reads occur during the first lookup only; later invocations add none.

> If you add a temporary counter to measure this, remove it before committing
> (constitution Principle V — zero debug output in production).

## 7. Version / release hygiene

- `custom-shipping-zones.php`: `Version: 1.0.5` and
  `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.5'`.
- `readme.txt`: `Stable tag: 1.0.5` + a changelog entry for the performance fix.
- If Phases 1/2 have not yet landed, bump to the highest shipped version instead
  (see plan Release Hygiene).

## Pass criteria

All of: PHP lints clean; states output identical with and without regions; add and
delete reflected immediately without external cache clearing; (optional) per-request
region reads no longer scale with filter invocations.
