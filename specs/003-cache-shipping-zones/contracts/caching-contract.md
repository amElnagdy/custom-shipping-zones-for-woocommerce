# Contract: Custom Region Caching Behavior

**Feature**: 003-cache-shipping-zones | **Date**: 2026-06-04

This phase exposes no new external interface. The relevant contract is the
**observable behavior** of the existing public surface, which MUST be preserved while
redundant reads are eliminated. Contracts are stated behaviorally (Principle VII) so
they survive the internal cache implementation.

## C1 — `woocommerce_states` filter output is unchanged

- **Surface**: the `woocommerce_states` filter callback (`modify_woocommerce_states()`).
- **Contract**: for any given set of stored region options, the filtered states array
  returned to WooCommerce is **identical** to the pre-Phase-3 output — same country
  keys, same state codes, same names, same merge behavior with WooCommerce defaults.
- **Verify**: capture the filtered states for a fixture store before and after the
  change; assert equality (SC-003). Repeat for: no regions, one country, multiple
  countries.

## C2 — Read amortization within a request

- **Surface**: `get_custom_shipping_zones()` (called directly and via the filter).
- **Contract**: within a single request, the per-country option reads happen **at
  most once**. The 1st call MAY read options; the 2nd…Nth calls MUST NOT add
  per-country reads. Total plugin-attributable region reads are independent of how
  many times the `woocommerce_states` filter fires (SC-001).
- **Verify**: request a page that triggers the filter ≥2 times; count
  plugin-attributable `get_option` calls (Query Monitor, a profiler, or a temporary
  in-method counter). Reads do not grow with invocation count.

## C3 — Save reflects immediately

- **Surface**: `save_states()` AJAX handler followed by any states rebuild.
- **Contract**: after a successful save (add/update), the next states build includes
  the saved region; no external cache clear is required (FR-004, FR-006).
- **Verify**: add a region, rebuild states, confirm the region is present.

## C4 — Delete reflects immediately

- **Surface**: `delete_state()` AJAX handler followed by any states rebuild.
- **Contract**: after a successful delete, the next states build no longer includes
  the deleted region (FR-005, FR-006).
- **Verify**: delete a region, rebuild states, confirm the region is absent.

## C5 — Rejected writes do not corrupt the cache

- **Surface**: `save_states()` / `delete_state()` when the nonce or capability check
  fails, or when no `update_option()` occurs.
- **Contract**: stored data is unchanged AND no rejected value appears in any later
  states build (FR-009). Invalidation only runs after a successful write.
- **Verify**: issue a write without a valid nonce/capability; confirm stored data and
  subsequent states output are unchanged.

## C6 — Empty store stays cheap

- **Surface**: `get_custom_shipping_zones()` on a store with no regions.
- **Contract**: WooCommerce defaults are returned unchanged, and repeated calls
  within a request do not re-run the full country loop (FR-007).
- **Verify**: with no regions stored, trigger the filter multiple times; confirm
  no error and that the country loop runs at most once.

## Non-goals (explicitly NOT part of this contract)

- Cross-request / persistent caching (object cache, transients) — out of scope.
- Any change to option keys, the option value shape, or the filter signature.
- Any client-side / build artifact change.
