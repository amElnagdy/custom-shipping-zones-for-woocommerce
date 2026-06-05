---
description: "Task list for Phase 3 — Performance: Cache get_custom_shipping_zones()"
---

# Tasks: Cache `get_custom_shipping_zones()` (Phase 3)

**Input**: Design documents from `/specs/003-cache-shipping-zones/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/caching-contract.md, quickstart.md
**Branch**: `csz-wp7-readiness`

**Tests**: No automated test tasks — acceptance is behavior-based per constitution
Principle VII and `quickstart.md`. (The repo has a `jest` script, but no Phase-3 unit
tests were requested.)

## How to use this file (read first — written for the Kimi implementer)

Every task is **self-contained**: it names the exact file, the exact anchor in the
current code, and the exact code to add or replace. Apply tasks in numeric order.
Key facts about this phase:

- **This phase is PHP-only.** The single source file changed is
  `includes/CustomShippingZones.php`. There is **no** JavaScript change and **no**
  `npm run build` — do not touch `src/**` or `build/**`.
- **All code edits are in one file and one class** (`ANCSZ\CustomShippingZones\CustomShippingZones`).
  Apply them in order so each edit lands on the result of the previous one. Do **not**
  run the same-file tasks in parallel.
- A failed check in the AJAX handlers uses `wp_send_json_error()`, which calls
  `wp_die()` internally and halts the request — that is why invalidation is placed
  **after** the existing checks and the successful `update_option()`.
- **Do not** rename option keys (`{cc}_custom_shipping_zones`), change the
  `woocommerce_states` filter signature, add a transient/option/object-cache key, or
  refactor anything outside the listed anchors (constitution Principle II — minimum
  viable surface). The cache is in PHP memory only, for one request.
- The current relevant line anchors in `includes/CustomShippingZones.php`:
  class opening brace at line ~5; `get_custom_shipping_zones()` at lines ~108–121;
  `save_states()` `update_option(...)` at line ~103; `delete_state()`
  `update_option(...)` at line ~146.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different file, no dependency on an unfinished task)
- **[Story]**: US1 = read amortization (caching); US2 = immediate invalidation on save/delete

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Confirm the file currently parses, so a later `php -l` failure is clearly
caused by your edit and not a pre-existing problem.

- [X] T001 From the repo root, run `php -l includes/CustomShippingZones.php` and confirm
  it prints `No syntax errors detected`. This is a baseline check only — do not change or
  commit anything. (No `npm` step is needed in this phase; there is no JS change.)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Add the in-memory cache storage and its invalidator. **Both user stories
depend on these**: US1 reads/writes the property; US2 calls the invalidator. These are
two edits to the same class in `includes/CustomShippingZones.php` — do T002 then T003.

**⚠️ CRITICAL**: Complete this phase before US1 (T004) and US2 (T005, T006).

- [X] T002 Add the request-scoped cache property to the `CustomShippingZones` class in
  `includes/CustomShippingZones.php`. The class opens at line ~4–5:
  ```php
  class CustomShippingZones
  {

      public function __construct()
  ```
  Insert the property **immediately after the opening `{`** (before `public function __construct()`),
  so it reads:
  ```php
  class CustomShippingZones
  {
      /**
       * Request-scoped memo of get_custom_shipping_zones().
       * null = not built yet this request; an array (possibly empty) = built.
       *
       * @var array<string, array<string, string>>|null
       */
      private static $zones_cache = null;

      public function __construct()
  ```
  Change nothing else. This is the only state the cache needs (data-model: the
  `null`-vs-array sentinel distinguishes "not built" from "built and empty", FR-007).

- [X] T003 Add a static cache-invalidator method to the same class in
  `includes/CustomShippingZones.php`. Depends on T002 (uses `self::$zones_cache`). Place
  it directly **after** the `get_custom_shipping_zones()` method (which ends at line ~121
  with its closing `}`), before `delete_state()`:
  ```php
  /**
   * Clears the request-scoped zones cache so the next read re-queries options.
   * Called by the write handlers after a successful save or delete.
   */
  public static function clear_cache(): void
  {
      self::$zones_cache = null;
  }
  ```
  Then run `php -l includes/CustomShippingZones.php` and confirm it parses.

**Checkpoint**: The cache property and its invalidator exist and the file parses. No
behavior has changed yet (nothing reads or clears the property until T004–T006).

---

## Phase 3: User Story 1 - Repeated state lookups read from cache (Priority: P1) 🎯 MVP

**Goal**: Within a single request, `get_custom_shipping_zones()` runs its country loop
**at most once**; later calls in the same request return the memoized result without
touching the database. The states output is identical to today (FR-001, FR-002, FR-003).

**Independent Test**: On a page that triggers the `woocommerce_states` filter more than
once (e.g. a checkout / shipping page), the per-country `get_option` reads occur during
the first lookup only — later filter invocations add none — and the resulting states
list is unchanged from before this change (quickstart steps 1, 2, 6).

- [X] T004 [US1] Memoize `get_custom_shipping_zones()` in
  `includes/CustomShippingZones.php`. Depends on T002 (the `$zones_cache` property must
  exist). Replace the **entire current method** (lines ~108–121):
  ```php
  public function get_custom_shipping_zones()
  {
      $countries = WC()->countries->get_countries();
      $customShippingZones = array();

      foreach ($countries as $countryCode => $countryName) {
          $optionName = strtolower($countryCode) . '_custom_shipping_zones';
          if (get_option($optionName)) {
              $customShippingZones[$countryCode] = get_option($optionName);
          }
      }

      return $customShippingZones;
  }
  ```
  with the memoized version below. Note two changes: (a) an early return when the cache
  is already built; (b) each country's option is read **once** into `$zones` instead of
  twice (this halves the reads even on the first, cache-miss call):
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
  Behavior is preserved: the truthiness test (`if ($zones)`) and the stored value are
  identical to before; only redundant reads are removed and the result is memoized
  (including an empty result, satisfying FR-007). Then run
  `php -l includes/CustomShippingZones.php`.

**Checkpoint**: US1 verifiable independently — states output unchanged with and without
regions; repeated lookups in one request no longer re-run the country loop. (No rebuild;
PHP only.)

---

## Phase 4: User Story 2 - Saving or deleting a region reflects immediately (Priority: P1)

**Goal**: After a successful save (add/update) or delete, the next states build reflects
the change with no external cache clearing (FR-004, FR-005, FR-006). A rejected write
never disturbs the cache (FR-009).

**Independent Test**: Add a region → next states build includes it; delete a region →
next states build omits it; no cache plugin / manual clear involved (quickstart steps
3, 4, 5).

> Both tasks below edit `includes/CustomShippingZones.php` and depend on T003
> (`clear_cache()` must exist). They are in **different methods** but the **same file** —
> apply T005 then T006 sequentially; do not parallelize same-file edits.

- [X] T005 [US2] Invalidate the cache after a successful save in `save_states()` in
  `includes/CustomShippingZones.php`. Depends on T003. Find the tail of the method
  (line ~103):
  ```php
          // Update the option
          update_option($optionName, $updatedStates);

          wp_send_json_success();
  ```
  Insert `self::clear_cache();` **between** the `update_option(...)` line and
  `wp_send_json_success();`:
  ```php
          // Update the option
          update_option($optionName, $updatedStates);

          self::clear_cache();

          wp_send_json_success();
  ```
  This sits after the handler's existing nonce + capability checks and after the
  successful `update_option`, so a rejected/unauthorized request never reaches it
  (FR-009). Change nothing else.

- [X] T006 [US2] Invalidate the cache after a successful delete in `delete_state()` in
  `includes/CustomShippingZones.php`. Depends on T003 (and T005 only for same-file
  ordering). Find the tail of the method (line ~146):
  ```php
      update_option($optionName, $existingStates);

      wp_send_json_success();
  ```
  Insert `self::clear_cache();` between them:
  ```php
      update_option($optionName, $existingStates);

      self::clear_cache();

      wp_send_json_success();
  ```
  Change nothing else, then run `php -l includes/CustomShippingZones.php`.

**Checkpoint**: US2 verifiable — adding and deleting a region are reflected on the next
states build immediately; a rejected write leaves stored data and cache untouched.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Lint, bump the version, update the readme, and run the acceptance pass.

- [X] T007 From the repo root run `php -l includes/CustomShippingZones.php` and confirm
  `No syntax errors detected`. (This is the only PHP file changed by the logic tasks.)

- [X] T008 [P] Bump the version in `custom-shipping-zones.php`: set the header `Version:`
  (line ~6) to `1.0.5` and the constant
  `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.5';` (line ~23). If Phases 1 (1.0.3)
  and 2 (1.0.4) have not shipped yet, still land at the highest current version — see
  plan Release Hygiene. Then run `php -l custom-shipping-zones.php`.

- [X] T009 [P] Update `readme.txt`: set `Stable tag:` to `1.0.5` (line ~7) and add a
  changelog entry under `== Changelog ==`:
  ```text
  = 1.0.5 =
  * Performance: cache custom region lookups for the duration of a request so the
    woocommerce_states filter no longer re-queries the database on every invocation.
  * Cache is cleared automatically when a custom region is saved or deleted.
  ```

- [X] T010 Run the acceptance pass in `specs/003-cache-shipping-zones/quickstart.md`
  (steps 1–6): states output unchanged with regions and without regions; a saved region
  appears on the next build; a deleted region disappears on the next build; add/delete
  still work end-to-end; and (optional) confirm per-request `get_option` reads for custom
  regions no longer scale with the number of `woocommerce_states` invocations. If you add
  a temporary read counter to check the last point, remove it before committing
  (constitution Principle V — zero debug output).
  **Behavior checks requiring a live WP site deferred to release QA.** Mechanical
  verification passed: lint clean, version consistent, diff scope correct (only
  `includes/CustomShippingZones.php`, `custom-shipping-zones.php`, `readme.txt`),
  cache property and clear_cache() present, memoization and invalidation calls
  in place.

---

## Dependencies & Execution Order

### Phase dependencies

- **Setup (T001)** → no dependencies; run first.
- **Foundational (T002 → T003)** → T003 depends on T002 (uses the property). Both edit
  `includes/CustomShippingZones.php`; do them in order.
- **US1 (T004)** → depends on T002 (reads/writes `$zones_cache`). Independent of US2.
- **US2 (T005 → T006)** → both depend on T003 (`clear_cache()` must exist); apply T005
  then T006 (same-file ordering). Independent of US1 in behavior.
- **Polish (T007–T010)** → after all code tasks. T010 (quickstart) runs last.

### Same-file constraint (important for the Kimi model)

`includes/CustomShippingZones.php` is edited by **T002 → T003 → T004 → T005 → T006 in
that exact order**. Never edit it in parallel branches; apply each task on top of the
previous edit. T007 just re-lints the final result.

### Parallel opportunities

This phase is deliberately small and mostly sequential (one source file). The only
genuinely parallel tasks touch different files in Polish:

```text
# Different files, no shared dependency — safe to do together:
T008  Version bump in custom-shipping-zones.php
T009  Stable tag + changelog in readme.txt
```

---

## Implementation Strategy

### MVP first (User Story 1)

1. T001 (Setup) → T002, T003 (Foundational) → **T004 (US1)**. Stop and validate US1:
   states output is identical, and repeated lookups in one request no longer re-run the
   country loop. This alone delivers the performance win on read-heavy pages.
2. Add **US2 (T005, T006)** so admin save/delete is reflected immediately — this makes
   the cache safe in any in-request ordering and future-proofs it.
3. Finish with Polish (T007–T010): lint, version bump to 1.0.5, readme changelog, full
   quickstart pass.

### Incremental delivery checkpoints

- After US1: redundant reads eliminated (PHP only, no rebuild).
- After US2: save/delete invalidation in place — no stale data within a request.
- After Polish: 1.0.5 ready to ship.

---

## Task summary

- **Total tasks**: 10 (T001–T010)
- **By phase**: Setup 1 (T001) · Foundational 2 (T002–T003) · US1 1 (T004) ·
  US2 2 (T005–T006) · Polish 4 (T007–T010)
- **Parallelizable**: T008, T009 (different files, Polish only)
- **Files touched**: `includes/CustomShippingZones.php` (logic),
  `custom-shipping-zones.php` + `readme.txt` (release hygiene). No JS, no build.
- **No automated tests** — behavior verification via `quickstart.md` (Principle VII).

## Notes

- The cache is in PHP memory and request-scoped (FR-008) — do not add a transient,
  option, or `wp_cache_*` key in this phase (object-cache integration was deliberately
  deferred; see research R2).
- Do not introduce new `console.log` / `var_dump` / `error_log`, no new dependencies,
  use `$_POST`/`$_GET` explicitly (cross-phase rules).
- Commit after each logical group (e.g. one commit per phase) on `csz-wp7-readiness`.
