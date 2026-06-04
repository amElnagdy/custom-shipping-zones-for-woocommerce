# Implementation Plan: Critical Bug Fixes & Security Hardening (Phase 2)

**Branch**: `csz-wp7-readiness` | **Date**: 2026-06-04 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/002-bugfix-security-hardening/spec.md`

## Summary

Phase 2 closes correctness and security gaps in the existing admin flow without
adding features. After auditing the live code, the four spec stories resolve to:

- **US1 (AJAX security)** — *already satisfied in code*. Both `save_states()` and
  `delete_state()` already verify `current_user_can('manage_woocommerce')` and
  `wp_verify_nonce(..., 'csz_nonce')`, and `wp_send_json_error()` halts via
  `wp_die()`. This story becomes **verify-and-protect-from-regression**, with one
  low-risk hardening (run the nonce check before the capability check).
- **US2 (country validation)** — *genuinely missing*. `save_states()` never checks
  the submitted country against WooCommerce's country list. Add an authoritative
  server-side check using `WC()->countries->get_countries()`.
- **US3 (state-code validation)** — *genuinely missing*. `save_states()` never
  validates submitted state codes. Add a server-side rule: letters, digits, and
  hyphens only, 1–10 characters.
- **US4 (debug output)** — remove `console.log(current_states)` at
  `src/CurrentStates.js:10` and rebuild the JS bundle.

One **additional in-scope bug** surfaced during the audit: `App.js`
`handleSaveStates` ignores the AJAX response and shows the success screen
unconditionally (`src/App.js:29-32`). With server validation now able to reject a
save, the UI must read `result.success` and surface the error — otherwise FR-005
and FR-007 (rejection must be visible to the admin) cannot be met end-to-end.

Ships as **1.0.4** (assumes Phase 1 / 1.0.3 lands first; see Release Hygiene).

## Technical Context

**Language/Version**: PHP 7.4 (constitution Principle IV); JavaScript (React 18
via `@wordpress/element`), built with `@wordpress/scripts` (wp-scripts) into
`build/index.js`.

**Primary Dependencies**: WordPress (admin AJAX, nonces, options API, i18n),
WooCommerce (`WC()->countries->get_countries()`, `woocommerce_states` filter,
`WC_Shipping_Zones`), Ant Design (`message` for user-facing notifications).

**Storage**: WordPress options only — one option per country, key
`{lowercase-country-code}_custom_shipping_zones`, value `array<stateCode,stateName>`.
No schema change, no new options (constitution Principle I).

**Testing**: `php -l` on changed PHP; `npm run build` must succeed; manual
behavior verification per `quickstart.md` (add valid state, add invalid country
"ZZ", add malformed state code, delete state, console is clean). Behavior-based
per constitution Principle VII. (Repo has a `jest` script but no Phase-2 unit
tests are required.)

**Target Platform**: WordPress plugin admin screen (WooCommerce → Settings →
Custom Shipping Zones), PHP 7.4+, WooCommerce 9.x.

**Project Type**: Single WordPress/WooCommerce plugin — PHP backend plus a built
React admin bundle. Server is the authoritative validation/authorization boundary
(FR-006); client validation/feedback is convenience only.

**Performance Goals**: N/A — no hot-path change. (Caching is Phase 3.)

**Constraints**: Minimum viable surface (Principle II) — do not refactor the
already-correct security in `delete_state()`, do not rename the `csz_nonce`
action, do not change option keys or the `woocommerce_states` signature. Stricter
validation must reject only *new* bad input; existing stored data and
auto-generated codes (which already match the new rule) must remain valid.

**Scale/Scope**: ~4 source files plus a rebuilt bundle and a version bump:
`includes/CustomShippingZones.php` (validation in `save_states()`),
`includes/Strings.php` (a few error strings), `src/CurrentStates.js` (remove log),
`src/App.js` (handle save response), `build/index.js` (regenerated),
`custom-shipping-zones.php` + `readme.txt` (version/changelog).

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| # | Principle | Assessment |
|---|-----------|------------|
| I | Backward Compatibility | **PASS** — Option key scheme and `woocommerce_states` signature unchanged. New validation rejects only new invalid input; existing stored data is read unchanged, and the app's auto-generated codes (`US-XY-123` form) already satisfy the 1–10 char `[A-Za-z0-9-]` rule, so no legitimate existing data is invalidated. No migration needed. |
| II | Minimum Viable Surface | **PASS** — Touches only the files listed in Scale/Scope. `delete_state()` security is already correct and is left as-is; the `csz_nonce` action is **not** renamed (the cross-phase `csz_{action}_nonce` suggestion is declined as out-of-scope churn). |
| III | WooCommerce-First | **PASS** — Country validity is taken from `WC()->countries->get_countries()`, the canonical source; no direct DB queries, no private hooks. |
| IV | PHP 7.4 Baseline | **PASS** — Uses `array_key_exists`, `preg_match`, `strlen` — all 7.4-safe. No new syntax. |
| V | Zero Debug Output | **PASS** — Removes `console.log(current_states)`. The two `console.error(...)` calls in `catch` blocks are genuine error reporting (not state leakage) and are retained per the research decision. |
| VI | Security by Default | **PASS** — Nonce + capability already enforced on both write handlers and confirmed terminating via `wp_die()`. Phase hardens ordering (nonce before capability) and adds input validation/sanitization; output to the client is JSON via `wp_send_json_*`. |
| VII | Test the Contract | **PASS** — Acceptance is behavior-based (invalid country rejected & nothing stored, malformed code rejected, valid add/delete still work, console clean). |
| VIII | Branch Policy | **PASS** — All work on `csz-wp7-readiness`; per-phase branch hook intentionally skipped (matches Phase 1). |

**Result**: All gates pass. No violations → Complexity Tracking left empty.

## Project Structure

### Documentation (this feature)

```text
specs/002-bugfix-security-hardening/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   └── ajax-endpoints.md
├── checklists/
│   └── requirements.md  # Created by /speckit-specify
└── tasks.md             # Phase 3 output (/speckit-tasks — NOT created here)
```

### Source Code (repository root)

```text
custom-shipping-zones.php             # Plugin header Version + version constant → 1.0.4
readme.txt                            # Stable tag + changelog entry for 1.0.4
includes/
├── CustomShippingZones.php           # save_states(): add country + state-code validation;
│                                     #   reorder nonce-before-capability (both handlers)
└── Strings.php                       # add invalid_country / invalid_state_code /
                                      #   failed_to_save_states user-facing strings
src/
├── CurrentStates.js                  # remove console.log(current_states) (line 10)
└── App.js                            # handleSaveStates: read result.success, show error on failure
build/
└── index.js                          # regenerated by `npm run build`
```

Files explicitly **NOT** touched: `includes/admin/admin.php`, `src/CountrySelector.js`
(country comes from a fixed Select of valid WC countries — always valid client-side),
`src/StateAdder.js` / `src/AddedStates.js` (codes are auto-generated and disabled in
the form), `src/ExportImport.js` / `src/faq.js`, `vendor/**`.

**Structure Decision**: Keep the existing flat plugin layout. Server-side
validation lives in the one handler that writes new zones (`save_states()`); the
client change is limited to honoring the server's response so rejections are
visible. No new files, classes, build steps, or dependencies.

## Concrete Changes (reference for /speckit-tasks)

### 1. `includes/CustomShippingZones.php` — `save_states()` (lines ~74–106)

Order of operations becomes: **nonce → capability → parse → validate → persist.**

- **Reorder** the existing nonce check above the capability check (defense-in-depth
  ordering; both already terminate via `wp_send_json_error()`/`wp_die()`). Apply the
  same reorder to `delete_state()` (lines ~123–135) for consistency — no other
  change to `delete_state()`.
- **Country validation (US2 / FR-003)**: after reading `$countryCode`, reject if it
  is not a key in `WC()->countries->get_countries()`:
  ```php
  $valid_countries = WC()->countries->get_countries();
  if ($countryCode === '' || ! array_key_exists($countryCode, $valid_countries)) {
      wp_send_json_error('invalid_country');
  }
  ```
- **State-code validation (US3 / FR-004)**: while building `$statesFormatted`,
  validate each entry; reject the whole request on the first bad one (all-or-nothing,
  FR-005) before any `update_option`:
  ```php
  $code = isset($state['code']) ? sanitize_text_field($state['code']) : '';
  $name = isset($state['name']) ? sanitize_text_field($state['name']) : '';
  if ($name === '' || ! preg_match('/^[A-Za-z0-9-]{1,10}$/', $code)) {
      wp_send_json_error('invalid_state_code');
  }
  ```
  Build `$statesFormatted` only after all entries pass, so nothing is persisted on
  failure.

### 2. `includes/Strings.php` — add user-facing error strings (after line ~18)

```php
'invalid_country'        => __('That country is not recognized. Nothing was saved.', 'custom-shipping-zones'),
'invalid_state_code'     => __('State codes may use only letters, numbers and hyphens (max 10 characters).', 'custom-shipping-zones'),
'failed_to_save_states'  => __('Failed to save states. Please check your entries and try again.', 'custom-shipping-zones'),
```

### 3. `src/App.js` — `handleSaveStates` (lines ~16–33)

Read the response and branch (mirrors the existing pattern in `CurrentStates.js`'s
`handleDelete`). On `result.success` show the success screen; otherwise show an
Ant Design `message.error` mapping `invalid_country` / `invalid_state_code` to the
matching string, falling back to `failed_to_save_states`. Import `message` from
`antd`.

### 4. `src/CurrentStates.js` — remove debug output (line 10)

Delete `console.log(current_states);`. Leave the `console.error` in the delete
`catch` block (legitimate error reporting).

### 5. Rebuild + version bump (Release Hygiene)

- `npm run build` → regenerate `build/index.js` (and `build/index.asset.php`).
- `custom-shipping-zones.php`: header `Version: 1.0.4` and
  `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.4';`.
- `readme.txt`: `Stable tag: 1.0.4` + changelog entry.
- **Dependency note**: the live tree is still at 1.0.2 (Phase 1's 1.0.3 is specced
  but not yet implemented). If Phase 1 ships first, this is a clean 1.0.3 → 1.0.4
  bump; if phases are combined, land at the highest version. `/speckit-tasks`
  should treat the version bump as the final task so it reflects whatever shipped.

## Complexity Tracking

> No constitution violations — section intentionally empty.
