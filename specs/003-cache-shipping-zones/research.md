# Phase 0 Research: Cache `get_custom_shipping_zones()`

**Feature**: 003-cache-shipping-zones | **Date**: 2026-06-04

This phase has no open `NEEDS CLARIFICATION` markers from the spec. The research
below records the technical decisions that shape the design.

## R1 — Confirm the problem: where the reads come from

**Decision**: The hot path is `get_custom_shipping_zones()` →
`modify_woocommerce_states()` on the `woocommerce_states` filter.

**Findings** (`includes/CustomShippingZones.php`):
- `get_custom_shipping_zones()` (lines 108–121) iterates **all** countries from
  `WC()->countries->get_countries()` (~250 entries) and, for each country that has
  a stored option, calls `get_option()` **twice** — once in `if (get_option($optionName))`
  and again in `$customShippingZones[$countryCode] = get_option($optionName);`.
- It is called from `modify_woocommerce_states()` (line 159), hooked to
  `woocommerce_states` (registered line 21), and from `enqueue_scripts()` (line 48).
- WooCommerce applies the `woocommerce_states` filter every time
  `WC()->countries->get_states()` is consulted. On checkout / shipping-calculation
  pages this happens multiple times per request, so the full loop re-runs each time.

**Rationale**: Confirms the spec's premise — the cost multiplies with filter
invocations, not with the number of stored regions. The fix must make the loop run
at most once per request.

**Alternatives considered**: Reading WC source to see whether WC already caches
`get_states()` — even where it does, the plugin's filter callback still re-runs and
re-loops on each application, so plugin-side memoization is still required.

## R2 — Caching mechanism: static property vs. object cache vs. transient

**Decision**: A single `private static` class property memoizing the assembled
array for the duration of the request, plus a static `clear_cache()` invalidator.

**Rationale**:
- **Request-scoped and correct by construction (FR-008)**: static state lives only
  for the current PHP request, so two concurrent requests can never see each other's
  cache or each other's pre-invalidation value.
- **Minimum viable surface (Principle II)**: one property, one early-return, one
  tiny method, two call sites. No new option, transient, or persisted key — so
  nothing to migrate and nothing that can go stale across requests (Principle I).
- **Sufficient for the goal**: the spec's measurable outcome is "reads do not scale
  with filter invocations *within a request*" (SC-001). A static memo achieves
  exactly that. Cross-request persistence is explicitly out of scope (spec
  Assumptions).
- **Empty-result memoization (FR-007)**: a `null` sentinel distinguishes "not built
  yet" from "built and empty", so an empty store is cached too and the loop does not
  re-run on a no-regions site.

**Alternatives considered**:
- **`wp_cache_get` / `wp_cache_set` with group `csz_zones`** — gives cross-request
  reuse when a persistent object cache (Redis/Memcached) is installed. *Deferred*:
  it adds an invalidation surface (must clear on every write, and correctness now
  depends on external cache behavior) for a benefit beyond the stated goal. The plan
  source notes the static approach "is sufficient for Phase 3." Can be layered on
  later without changing the public contract.
- **Transient (`set_transient`)** — persisted, needs explicit expiry/invalidation,
  and re-introduces DB writes; contradicts the goal of *fewer* DB operations and the
  minimum-surface principle. Rejected.

## R3 — Invalidation strategy

**Decision**: Clear the static cache after each successful `update_option()` in the
two write handlers — `save_states()` and `delete_state()`.

**Rationale**:
- These are the only code paths that mutate the stored region data (spec
  Assumptions), so they are the complete set of invalidation points.
- Placing `clear_cache()` *after* the existing nonce + capability checks and *after*
  the successful `update_option()` guarantees a rejected or unauthorized request
  never disturbs the cache (FR-009).
- Even though save/delete arrive as separate AJAX requests (so a static cache would
  also self-heal on the next request), explicit invalidation keeps the behavior
  correct for any in-request read ordering and makes the design robust if an
  object-cache layer is added later.

**Alternatives considered**: Hooking `updated_option` / `added_option` globally —
rejected as over-broad (fires for unrelated options) and harder to reason about than
two explicit calls at the known write sites (Principle II).

## R4 — Collapsing the duplicate per-country `get_option()`

**Decision**: Read each country's option once into a local variable inside the loop
instead of calling `get_option()` twice.

**Rationale**: This is inside the exact method being changed and directly serves the
"at most one lookup per country per request" requirement (FR-002); it halves the
reads even on the first (cache-miss) call. It is behavior-preserving — the truthiness
test and the stored value are identical. Including it does not widen the surface
beyond the method already being edited.

**Alternatives considered**: Leaving the double read as-is — rejected; it is the
clearest, lowest-risk part of the performance fix and lives in the same five lines.

## R5 — No JavaScript / build impact

**Decision**: No `src/**` or `build/**` change; do **not** run `npm run build`.

**Rationale**: The change is entirely server-side PHP. The admin bundle consumes
`current_custom_zones` via `wp_localize_script` (line 48) exactly as before — the
value is identical, only assembled more cheaply. Touching the bundle would violate
Principle II.
