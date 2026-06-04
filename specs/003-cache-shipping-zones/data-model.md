# Phase 1 Data Model: Cache `get_custom_shipping_zones()`

**Feature**: 003-cache-shipping-zones | **Date**: 2026-06-04

This phase introduces **no persisted data** and **no schema change**. The only new
entity is an in-memory, request-scoped cache. Stored data is documented for context
because the cache mirrors it.

## Persisted data (unchanged — for context only)

### Custom region option (existing)

- **Storage**: WordPress option, one per country with regions.
- **Key**: `{lowercase-country-code}_custom_shipping_zones` (e.g. `us_custom_shipping_zones`).
- **Value**: associative array `array<stateCode (string) => stateName (string)>`.
- **Owners (writers)**: `save_states()` (add/update), `delete_state()` (remove one entry).
- **Readers**: `get_custom_shipping_zones()` only.
- **Phase 3 impact**: none. Keys, value shape, and read/write semantics are unchanged
  (constitution Principle I).

## New in-memory entity

### Request-scoped zones cache

- **Representation**: a single `private static` property on `CustomShippingZones`
  (e.g. `self::$zones_cache`).
- **Holds**: the fully assembled return value of `get_custom_shipping_zones()` —
  `array<countryCode => array<stateCode => stateName>>` for every country that has
  stored regions (an empty `array()` when none do).
- **Lifetime**: a single PHP request. Not serialized, not persisted, not shared
  across requests (satisfies FR-008).
- **Sentinel**: `null` means "not yet built this request"; any array (including the
  empty array) means "built — serve this" (satisfies FR-007).

#### States

| State | Meaning | Entered when | Left when |
|-------|---------|--------------|-----------|
| **Unset** (`null`) | Cache not yet built this request | Start of request; after any invalidation | First call to `get_custom_shipping_zones()` builds it |
| **Populated** (array, possibly empty) | Memoized result for this request | Getter finishes building the array | A write handler calls `clear_cache()` → back to Unset |

#### Transitions

```text
            first get_custom_shipping_zones()
   Unset ───────────────────────────────────────▶ Populated
     ▲                                                  │
     │            save_states() / delete_state()        │
     └──────────────── clear_cache() ◀──────────────────┘
```

- **Build**: on the first read of the request, the country loop runs once and the
  result (empty or not) is stored.
- **Serve**: every subsequent read in the same request returns the stored array
  without touching the database (FR-002, FR-003).
- **Invalidate**: a successful save or delete sets the property back to `null`, so
  the next read rebuilds from current option values (FR-004, FR-005, FR-006).

## Validation / invariants

- The cache value MUST always equal what a fresh country-loop read would produce for
  the current option state — enforced by invalidating on every successful write.
- A rejected write (failed nonce/capability or no `update_option`) MUST NOT change
  the cache (FR-009): `clear_cache()` is only reached after a successful
  `update_option()`.
- The cache MUST NOT be populated from, or written to, any cross-request store
  (FR-008): it is plain PHP static memory only.
