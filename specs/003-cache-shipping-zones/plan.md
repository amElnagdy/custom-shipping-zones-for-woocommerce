# Implementation Plan: Cache `get_custom_shipping_zones()` (Phase 3)

**Branch**: `csz-wp7-readiness` (work branch) · spec dir `003-cache-shipping-zones` | **Date**: 2026-06-04 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/003-cache-shipping-zones/spec.md`

## Summary

Phase 3 removes the redundant database reads that `get_custom_shipping_zones()`
performs every time WooCommerce builds its states list. No features, no UI, no JS.

After auditing the live code (`includes/CustomShippingZones.php`):

- `get_custom_shipping_zones()` (lines 108–121) loops over **every** WooCommerce
  country (~250) and, for each country that has stored regions, calls
  `get_option()` **twice** — once in the `if` test and again to assign the value.
- It is invoked from two places: `enqueue_scripts()` (line 48, once per admin page
  load) and `modify_woocommerce_states()` (line 159), which is hooked to the
  `woocommerce_states` filter. WooCommerce can fire that filter many times within a
  single request (each `WC()->countries->get_states()` call re-applies it), so the
  full country loop — and its `get_option()` storm — repeats per invocation.

The fix is a **request-scoped static cache** inside the class: the first call runs
the country loop once and memoizes the assembled result (including an empty result);
every later call in the same request returns the memoized array. The two write
handlers (`save_states()`, `delete_state()`) clear the cache after they
`update_option()`, so a save or delete is reflected immediately. The double
`get_option()` per country is collapsed to a single read in the same edit, since it
is part of the same hot loop and directly serves the "at most one lookup" goal.

Ships as **1.0.5** (assumes Phases 1/1.0.3 and 2/1.0.4 land first; see Release
Hygiene — the live tree is still 1.0.2).

## Technical Context

**Language/Version**: PHP 7.4 (constitution Principle IV). No JavaScript change in
this phase — the built React bundle is untouched and `npm run build` is **not** run.

**Primary Dependencies**: WordPress Options API (`get_option`, `update_option`),
WooCommerce (`WC()->countries->get_countries()`, the `woocommerce_states` filter).
No new dependencies (Principle III).

**Storage**: WordPress options only — one option per country, key
`{lowercase-country-code}_custom_shipping_zones`, value
`array<stateCode, stateName>`. **No schema change, no new options, no new transient
or persisted cache key** (constitution Principle I). The cache lives only in PHP
memory for the duration of the request.

**Testing**: `php -l includes/CustomShippingZones.php` must pass. Behavior
verification per `quickstart.md`: states output unchanged with/without regions;
add and delete still work end-to-end; (optional) read-count check via a profiler
or a temporary counter confirms `get_option` per-country reads do not scale with
the number of filter invocations. Behavior-based per Principle VII.

**Target Platform**: WooCommerce store front end and admin (WooCommerce → Settings
→ Custom Shipping Zones), PHP 7.4+, WooCommerce 9.x.

**Project Type**: Single WordPress/WooCommerce plugin (PHP backend + prebuilt React
admin bundle). This phase is backend-only.

**Performance Goals**: Per-request, plugin-attributable `get_option` reads for
custom regions become **constant** regardless of how many times the
`woocommerce_states` filter fires (SC-001); ≥90% reduction in those reads on a
shipping-heavy page for a store that has regions (SC-002). States output
byte-identical to current behavior (SC-003).

**Constraints**: Minimum viable surface (Principle II) — touch only
`get_custom_shipping_zones()`, add one private static property and one small
`clear_cache()` method, and add one invalidation call to each of the two existing
write handlers. Do **not** rename option keys, change the `woocommerce_states`
signature, add transients, alter validation, or touch JS. The cache must be
request-scoped so concurrent requests never share state (FR-008).

**Scale/Scope**: One source file plus a version bump:
`includes/CustomShippingZones.php` (cache property, memoized getter, `clear_cache()`,
two invalidation calls); `custom-shipping-zones.php` (Version header + version
constant → 1.0.5); `readme.txt` (Stable tag + changelog entry).

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| # | Principle | Assessment |
|---|-----------|------------|
| I | Backward Compatibility | **PASS** — Option keys and the `woocommerce_states` signature are unchanged. The cache is in-memory only; no stored data, transient, or persisted cache key is added or migrated. `get_custom_shipping_zones()` returns the same array it does today. |
| II | Minimum Viable Surface | **PASS** — One method memoized, one tiny `clear_cache()` added, two one-line invalidation calls. No refactor of unrelated code; the optional object-cache (`wp_cache_*`) wrapper is **declined** as out-of-scope for the stated goal (see research). |
| III | WooCommerce-First | **PASS** — Country list still comes from `WC()->countries->get_countries()`; reads still go through the Options API. No direct DB queries, no private hooks. |
| IV | PHP 7.4 Baseline | **PASS** — A `private static` property, a static method, and an early-return are all 7.4-safe. No new syntax. |
| V | Zero Debug Output | **PASS** — No `console.log`/`error_log`/`var_dump` added. (Any read-counter used to verify SC-002 is a temporary test aid, not shipped.) |
| VI | Security by Default | **PASS** — No change to the nonce/capability checks on `save_states()` / `delete_state()`; invalidation is added *after* the existing checks and the `update_option()` call, so it cannot run for a rejected request. |
| VII | Test the Contract | **PASS** — Acceptance is behavior-based: identical states output, add/delete still work, change reflected immediately. The cache is an internal detail, not asserted directly. |
| VIII | Branch Policy | **PASS** — All work on `csz-wp7-readiness` (constitution Workflow). Per-phase branch hook created `003-cache-shipping-zones` for spec tracking; implementation commits land on `csz-wp7-readiness` consistent with Phases 1–2. |

**Result**: All gates pass. No violations → Complexity Tracking left empty.

## Project Structure

### Documentation (this feature)

```text
specs/003-cache-shipping-zones/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   └── caching-contract.md
├── checklists/
│   └── requirements.md  # Created by /speckit-specify
└── tasks.md             # Phase 2 output (/speckit-tasks — NOT created here)
```

### Source Code (repository root)

```text
custom-shipping-zones.php             # Plugin header Version + version constant → 1.0.5
readme.txt                            # Stable tag + changelog entry for 1.0.5
includes/
└── CustomShippingZones.php           # add private static $zones_cache;
                                      #   memoize get_custom_shipping_zones();
                                      #   add clear_cache(); collapse double get_option;
                                      #   call clear_cache() in save_states() & delete_state()
```

Files explicitly **NOT** touched: `includes/Strings.php`, `src/**`, `build/**`
(no JS change, no rebuild), `vendor/**`, `includes/admin/**`.

**Structure Decision**: Keep the existing flat plugin layout. The cache is a single
`private static` property on the existing `CustomShippingZones` class — no new file,
class, option, or transient. Invalidation is co-located with the existing
`update_option()` calls in the two write handlers.

## Concrete Changes (reference for /speckit-tasks)

### 1. `includes/CustomShippingZones.php` — add request-scoped cache

Add a class property (near the top of the class, after the opening brace):

```php
/** Request-scoped memo of get_custom_shipping_zones(); null = not yet built. */
private static $zones_cache = null;
```

Memoize the getter (replaces lines ~108–121). Note this also collapses the
duplicate per-country `get_option()` into one read:

```php
public function get_custom_shipping_zones()
{
    if (self::$zones_cache !== null) {
        return self::$zones_cache;
    }

    $countries = WC()->countries->get_countries();
    $customShippingZones = array();

    foreach ($countries as $countryCode => $countryName) {
        $optionName = strtolower($countryCode) . '_custom_shipping_zones';
        $zones = get_option($optionName);
        if ($zones) {
            $customShippingZones[$countryCode] = $zones;
        }
    }

    self::$zones_cache = $customShippingZones;
    return self::$zones_cache;
}
```

Add a small invalidation helper (anywhere in the class):

```php
/** Clears the request-scoped zones cache so the next read re-queries options. */
public static function clear_cache(): void
{
    self::$zones_cache = null;
}
```

### 2. `includes/CustomShippingZones.php` — invalidate on write

- In `save_states()`, immediately after the existing `update_option($optionName, $updatedStates);`
  (line ~103) and before `wp_send_json_success();`, add:
  ```php
  self::clear_cache();
  ```
- In `delete_state()`, immediately after the existing `update_option($optionName, $existingStates);`
  (line ~146) and before `wp_send_json_success();`, add:
  ```php
  self::clear_cache();
  ```

Both calls sit *after* the handlers' existing nonce + capability checks and after the
successful `update_option`, so a rejected request never clears the cache (FR-009).

### 3. Version bump (Release Hygiene)

- `custom-shipping-zones.php`: header `Version: 1.0.5` and
  `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.5';`.
- `readme.txt`: `Stable tag: 1.0.5` + changelog entry (e.g. "Performance: cache
  custom region lookups so the `woocommerce_states` filter no longer re-queries the
  database on every invocation.").
- **Dependency note**: the live tree is still 1.0.2 (Phases 1/1.0.3 and 2/1.0.4 are
  specced but not yet implemented). If earlier phases ship first this is a clean
  1.0.4 → 1.0.5 bump; if phases are combined, land at the highest version.
  `/speckit-tasks` should treat the version bump as the final task so it reflects
  whatever actually shipped.

## Complexity Tracking

> No constitution violations — section intentionally empty.
