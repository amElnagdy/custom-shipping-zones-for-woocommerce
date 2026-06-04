# spec-kit Plan: Custom Shipping Zones for WooCommerce — WP 7.0 Readiness

> **Plugin:** `custom-shipping-zones-for-woocommerce` v1.0.2  
> **Target:** WordPress 7.0, PHP 7.4 minimum, latest WooCommerce  
> **Methodology:** Spec-Driven Development via [spec-kit](https://github.com/github/spec-kit)  
> **Date:** June 3, 2026

---

## How to use this plan with spec-kit

```bash
# 1. Install specify CLI (requires uv)
uv tool install specify-cli --from git+https://github.com/github/spec-kit.git@latest

# 2. Init in your plugin repo with your agent of choice
specify init . --integration claude   # or copilot, cursor, etc.

# 3. For each phase below, run the spec-kit workflow:
#    /speckit.constitution  → once, before Phase 1
#    /speckit.specify       → paste the Phase Spec block
#    /speckit.clarify       → catch any ambiguity
#    /speckit.plan          → provide tech constraints block
#    /speckit.tasks         → generate task breakdown
#    /speckit.implement     → execute
```

---

## Constitution (run once before any phase)

Run this first to establish governing principles for the whole upgrade:

```
/speckit.constitution

Governing principles for the Custom Shipping Zones for WooCommerce upgrade:

1. Backward compatibility: no breaking changes to stored option keys or the
   woocommerce_states filter signature without a migration path.
2. Minimum viable surface: touch only what each phase requires — no speculative
   refactoring.
3. WooCommerce-first: always use official WC APIs (WC(), WC_Data, woocommerce_states)
   rather than direct DB queries or private hooks.
4. PHP 7.4 baseline: no syntax or functions beyond PHP 7.4 unless explicitly
   introduced in a later phase.
5. Zero debug output in production: console.log, error_log, and var_dump calls
   must not reach a production build.
6. Security by default: every AJAX handler verifies nonce + capability before acting.
7. Test the contract, not the implementation: acceptance criteria are behavior-based
   (admin screen loads, state saves, state deletes) not implementation-based.
```

---

## Phase 1 — Headers, Metadata & Dependency Declaration

**Goal:** Make the plugin correctly declare its dependencies and compatibility so
WordPress.org and site admins have accurate information before WP 7.0 drops.  
**Target version bump:** 1.0.3  
**Estimated effort:** 30–60 min

### `/speckit.specify` prompt

```
/speckit.specify

Update the Custom Shipping Zones for WooCommerce plugin metadata so it is accurate
for WordPress 7.0.

User stories:

1. As a site admin, when I visit Plugins → Add New and search for this plugin,
   I want WordPress to warn me if WooCommerce is not active, so I do not install
   a broken plugin.

2. As a developer running wp-env or Local, I want the plugin header to declare
   its minimum PHP version as 7.4, so automated compatibility checkers pass
   without false negatives.

3. As a store owner on WP 7.0, I want the "Tested up to" value to read 7.0 so
   I have confidence the plugin is maintained.

Acceptance criteria:
- Plugin header contains: Requires Plugins: woocommerce
- Plugin header contains: Requires PHP: 7.4
- Plugin header contains: Tested up to: 7.0
- Plugin header WC requires at least references the current WooCommerce stable
  (9.x series).
- PHP lint (php -l) passes on all PHP files.
- No other functional code is changed in this phase.
```

### `/speckit.plan` tech constraints

```
/speckit.plan

This is a pure metadata change. The only files touched are:
- custom-shipping-zones-for-woocommerce.php  (plugin header comment)
- readme.txt  (Tested up to, Requires at least, Stable tag, WC tested up to)

No JavaScript build step is required for this phase.
Verify changes with: php -l custom-shipping-zones-for-woocommerce.php
```

---

## Phase 2 — Critical Bug Fixes & Security Hardening

**Goal:** Fix the bugs that cause data loss, silent failures, or console noise
in production. No new features.  
**Target version bump:** 1.0.4  
**Estimated effort:** 2–3 hours

### `/speckit.specify` prompt

```
/speckit.specify

Fix four critical issues in Custom Shipping Zones for WooCommerce that affect
production correctness and security.

User stories:

1. As a developer auditing the site, when I open the browser console on the
   Custom Shipping Zones admin screen, I want to see zero debug output, so
   the plugin is not leaking internal state to end users.

2. As a store admin, when I type an invalid country code (e.g. "ZZ") into
   the country field, I want to see a validation error and the entry to be
   rejected, so invalid data never reaches the woocommerce_states filter.

3. As a store admin, when I type a state code that contains illegal characters
   or exceeds a reasonable length, I want to see a validation error, so
   WooCommerce does not receive malformed state data.

4. As a developer reviewing AJAX handlers, I want every handler that writes
   data to verify a nonce and check the manage_woocommerce capability, so
   unauthorized users cannot modify shipping zone data.

Acceptance criteria:
- console.log(current_states) is removed from src/CurrentStates.js.
- Submitting country code "ZZ" returns a WP_Error / JSON error response and
  stores nothing.
- Valid country codes are those returned by WC()->countries->get_countries().
- State codes are validated: alphanumeric + hyphen only, max 10 characters.
- All existing AJAX handlers call check_ajax_referer() and current_user_can('manage_woocommerce').
- Adding a valid custom state still works end-to-end.
- Deleting a valid custom state still works end-to-end.
- PHP lint passes.
- JS build (npm run build) passes.
```

### `/speckit.plan` tech constraints

```
/speckit.plan

Stack:
- PHP 7.4+, WordPress 7.0, WooCommerce 9.x
- React/JSX for admin UI (existing build pipeline — do not change bundler)
- Validation on both client (JS) and server (PHP) sides — server is authoritative

Specific file targets:
- src/CurrentStates.js           → remove console.log line
- src/[component with add form]  → add client-side country/state validation
- includes/[ajax handler file]   → add check_ajax_referer + capability check
- includes/[main class]          → add server-side country validation against
                                   WC()->countries->get_countries()
                                 → add state code sanitize_text_field + regex check

Do not introduce new npm packages. Use native fetch() for any new JS calls —
do not add jQuery calls.
```

---

## Phase 3 — Performance: Cache `get_custom_shipping_zones()`

**Goal:** Eliminate the O(n × countries) `get_option()` storm that fires on
every `woocommerce_states` invocation.  
**Target version bump:** 1.0.5  
**Estimated effort:** 1–2 hours

### `/speckit.specify` prompt

```
/speckit.specify

Improve the runtime performance of Custom Shipping Zones for WooCommerce so
that the woocommerce_states filter does not cause database thrashing on
high-traffic stores.

User stories:

1. As a store owner on a busy site, when WooCommerce calls the woocommerce_states
   filter repeatedly during a single page request (e.g. on a checkout page with
   many shipping calculations), I want the plugin to read from in-memory cache
   rather than calling get_option() for every country, so database queries are
   not multiplied.

2. As a store admin, when I save or delete a custom state/region in the admin,
   I want the cache to be invalidated immediately, so the updated zones are
   reflected on the front end without a cache-busting plugin.

Acceptance criteria:
- get_custom_shipping_zones() calls get_option() at most once per page request
  (use a static variable or WP object cache).
- Cache is cleared when any custom zone is saved or deleted.
- Adding and deleting zones still work correctly after the cache change.
- No visible behavior change to end users.
- PHP lint passes.
```

### `/speckit.plan` tech constraints

```
/speckit.plan

Implementation:
- Use a static class property or static local variable inside
  get_custom_shipping_zones() to hold results for the duration of the request.
- On save/delete AJAX handlers, set the static cache to null (or call a
  clear_cache() method) so the next call re-queries.
- Optionally: wrap with wp_cache_get/wp_cache_set using a group like
  'csz_zones' for object-cache compatibility — but the static variable
  approach is sufficient for Phase 3.

No JS changes required in this phase.
No schema changes. No new options.
```

---

## Phase 4 — Readme, Messaging & Repositioning

**Goal:** Align the public readme.txt and admin UI copy with what the plugin
actually does (custom states/regions, not full WooCommerce shipping zone
management). This is the "Option A reposition" recommended in the task spec.  
**Target version bump:** 1.0.6  
**Estimated effort:** 30–60 min

### `/speckit.specify` prompt

```
/speckit.specify

Reposition the public description of Custom Shipping Zones for WooCommerce to
accurately reflect what it does: it allows store owners to define custom
states/regions within a country so those regions become available when
configuring WooCommerce shipping zones.

The current public description implies the plugin manages WooCommerce shipping
zones directly ("create custom shipping zones beyond the default zones"), which
is misleading — it actually injects custom state/region codes via the
woocommerce_states filter.

User stories:

1. As a store owner browsing WordPress.org, I read the plugin description and
   immediately understand that this plugin adds custom states or sub-regions to
   a country so I can target them in WooCommerce shipping zone rules.

2. As a store owner who installed the plugin, the admin screen heading and
   field labels use the words "states" or "regions" — not "zones" — so the UI
   matches WooCommerce's own terminology for what the plugin produces.

3. As a store owner reading the FAQ, every answered question is accurate and no
   question references features that do not exist (or the question is removed).

Acceptance criteria:
- readme.txt short description accurately describes the states/regions feature.
- readme.txt long description does not claim the plugin manages WooCommerce
  shipping zones directly.
- Admin screen page title/heading reflects "custom states/regions" language.
- Any FAQ entry referencing export/import is removed, as that feature is not
  being shipped.
- No PHP or JS functional changes in this phase.
- readme.txt Stable tag, Changelog entry, and Tested up to are updated
  to match the new version.
```

### `/speckit.plan` tech constraints

```
/speckit.plan

Files to edit:
- readme.txt            → short description, long description, FAQ section
- readme.md (if exists) → mirror changes
- Admin screen PHP/JS   → update heading/label strings only; no logic changes

No build step required unless label strings are in a JS i18n file.
If strings are in wp_localize_script, update the PHP string values only.
Run php -l to confirm no accidental PHP syntax errors.
```

---

## Phase Summary

| Phase | What ships | Version | Effort |
|-------|-----------|---------|--------|
| 1 | Headers & dependency declaration | 1.0.3 | ~1 hr |
| 2 | Bug fixes: debug log, input validation, AJAX security | 1.0.4 | 2–3 hr |
| 3 | Performance: cache `get_custom_shipping_zones()` | 1.0.5 | 1–2 hr |
| 4 | Readme & admin copy repositioning | 1.0.6 | ~1 hr |

**Total estimated effort: 5–7 hours across all phases.**

All four phases should ship before WP 7.0 goes stable.

---

## Notes for the agent across all phases

- Always run `php -l` on every changed PHP file before marking a task done.
- Always run `npm run build` (or equivalent) after any JS change.
- Do not introduce new `console.log`, `var_dump`, or `error_log` calls.
- Do not use `$_REQUEST`; use `$_POST` or `$_GET` explicitly.
- Nonce actions should be named consistently: `csz_{action}_nonce`.
- When in doubt about a WooCommerce API, prefer reading the WC source over
  guessing — WC()'s `countries` object is the canonical source for valid codes.
