# Feature Specification: Cache `get_custom_shipping_zones()`

**Feature Branch**: `003-cache-shipping-zones`

**Created**: 2026-06-04

**Status**: Draft

**Input**: User description: "Phase 3 — Performance: Cache `get_custom_shipping_zones()`. Improve the runtime performance of Custom Shipping Zones for WooCommerce so that the `woocommerce_states` filter does not cause database thrashing on high-traffic stores."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Repeated state lookups read from cache, not the database (Priority: P1)

A store owner runs a busy storefront. During a single page request — for example a checkout page that recalculates shipping several times, or any admin/front-end screen that asks WooCommerce for its list of states more than once — the plugin must answer from an in-request cache after the first lookup instead of re-reading every country's stored custom regions from the database again and again.

**Why this priority**: This is the entire point of the phase. Today, each time WooCommerce asks for states the plugin walks the full country list (~250 countries) and issues a separate database read for every country that has custom regions, then reads it a second time to use it. On a page that triggers the states filter many times, those reads multiply and become a measurable load on the database. Fixing the repeated work is the value; everything else supports it.

**Independent Test**: Load a page that triggers the WooCommerce states filter multiple times in one request and observe the number of option/database reads attributed to the plugin. With caching, the per-country option reads occur during the first lookup only; subsequent lookups in the same request add none. The list of states returned is identical to before.

**Acceptance Scenarios**:

1. **Given** a store with custom regions defined for one or more countries, **When** the states list is requested twice within the same page request, **Then** the plugin performs its per-country option reads at most once and returns the same complete states list both times.
2. **Given** a store with custom regions, **When** a single page request triggers the states filter many times, **Then** the total plugin-attributable option reads do not grow with the number of filter invocations.
3. **Given** a store with no custom regions defined for any country, **When** the states list is requested, **Then** the returned states are unchanged from WooCommerce's defaults and no error occurs.

---

### User Story 2 - Saving or deleting a region is reflected immediately (Priority: P1)

A store admin adds a new custom region to a country, or deletes an existing one, from the plugin's admin screen. The change must be visible the next time the states list is built — without clearing any external cache, deactivating a caching plugin, or reloading after a delay.

**Why this priority**: An in-request cache that is never invalidated would serve stale data within the same request after an edit, silently dropping or resurrecting regions. Correct invalidation is what makes the cache safe to ship; without it the feature is a regression, so it shares top priority with the cache itself.

**Independent Test**: Add a region and then, in a flow that rebuilds the states list, confirm the new region appears. Delete a region and confirm it no longer appears. No external cache tooling is involved.

**Acceptance Scenarios**:

1. **Given** the cache has been populated during a request, **When** a custom region is saved (added or updated), **Then** the next states lookup reflects the saved region rather than the pre-save cached value.
2. **Given** the cache has been populated during a request, **When** a custom region is deleted, **Then** the next states lookup no longer includes the deleted region.
3. **Given** a region is added and then deleted in the same session, **When** the states list is rebuilt afterward, **Then** it matches the final saved data with no orphaned entries.

---

### Edge Cases

- **No regions stored at all**: the cached result is an empty set; this empty result is itself cached so the country loop does not run again in the same request.
- **First lookup after invalidation**: once the cache is cleared by a save or delete, the very next lookup repopulates it from current stored data.
- **Concurrent independent requests**: the cache is scoped to a single request, so two simultaneous requests never see each other's cached or pre-invalidation state.
- **A save/delete that fails validation**: if a write is rejected (invalid input, capability/nonce failure), stored data is unchanged and a stale cache must not cause the rejected value to appear in later lookups.
- **Large number of countries with regions**: caching must hold the full result set for every country that has regions, not just the first one encountered.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST return the complete set of custom regions, grouped by country, exactly as it does today — caching MUST NOT change which regions appear in the WooCommerce states list.
- **FR-002**: Within a single page request, the system MUST perform its per-country lookups of stored custom regions at most once, regardless of how many times the states list is requested during that request.
- **FR-003**: The system MUST serve all states-list requests after the first (within the same request) from the in-request cache.
- **FR-004**: The system MUST invalidate (clear) the cache whenever a custom region is saved (added or updated).
- **FR-005**: The system MUST invalidate (clear) the cache whenever a custom region is deleted.
- **FR-006**: After invalidation, the system MUST repopulate the cache from current stored data on the next states-list request.
- **FR-007**: The system MUST cache an empty result when no custom regions exist, so the absence of regions does not trigger repeated full lookups within the same request.
- **FR-008**: Caching MUST be scoped to a single request such that one request's cached data cannot be served to a different request.
- **FR-009**: A save or delete operation that is rejected MUST leave stored data unchanged and MUST NOT cause the rejected value to appear in any subsequent states-list request.
- **FR-010**: Adding a region and deleting a region MUST continue to work end-to-end exactly as before this change.

### Key Entities *(include if feature involves data)*

- **Custom region set**: the collection of custom regions defined across all countries, grouped by country code. This is the value that is read repeatedly to build the WooCommerce states list and is the subject of caching.
- **In-request cache entry**: a transient, single-request holding of the custom region set. It has two states — populated or empty/unset — and is reset on any save or delete.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: When the states list is requested N times in one page request, the number of plugin-attributable stored-region reads stays constant as N grows (it does not scale with N).
- **SC-002**: On a page request that triggers the states filter repeatedly, plugin-attributable database reads for custom regions are reduced by at least 90% compared with the current behavior on a store that has custom regions defined.
- **SC-003**: The states list returned to WooCommerce is byte-for-byte identical to the pre-change output for the same stored data, in 100% of tested scenarios (with regions, without regions, single country, multiple countries).
- **SC-004**: After adding or deleting a region, the change is reflected in the next states-list build 100% of the time, with no external cache clearing required.
- **SC-005**: End users and store admins observe no behavioral difference other than improved responsiveness on high-traffic, shipping-heavy pages.

## Assumptions

- "Cache" here means per-request, in-memory caching for the duration of a single page request; cross-request persistence is out of scope for this phase. (A request-scoped static holder satisfies the requirements; object-cache integration is an optional enhancement, not a requirement.)
- The save and delete entry points that mutate stored custom regions are the only places that can change the data during a request, so invalidating the cache at those points is sufficient.
- The set of countries and the mapping of stored regions to countries does not change mid-request except through the plugin's own save/delete operations.
- No change to stored data format, option keys, or the `woocommerce_states` filter contract is made in this phase — behavior is preserved, only redundant reads are eliminated.
- This phase introduces no user-interface changes and no front-end/client-side changes.
- Backward compatibility is maintained per the project constitution: no breaking changes to stored option keys or the states filter signature.
