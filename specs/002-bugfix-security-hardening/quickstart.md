# Quickstart: Verify Phase 2 (Critical Bug Fixes & Security Hardening)

Behavior-based verification (constitution Principle VII). No new test harness is
introduced this phase. Run from the repo root.

## Build & lint

```bash
# 1. Rebuild the admin bundle after JS edits
npm run build                 # must complete without errors → build/index.js

# 2. PHP syntax check on changed files
php -l custom-shipping-zones.php
php -l includes/CustomShippingZones.php
php -l includes/Strings.php
```

## Manual acceptance (WooCommerce → Settings → Custom Shipping Zones)

Open the admin screen as a user with the **manage_woocommerce** capability, with
the browser devtools **Console** tab open.

| # | Story | Steps | Expected |
|---|-------|-------|----------|
| 1 | US4 / SC-005 | Load the page; add and delete a state | **No** `console.log` of the zones object appears in the console during normal use |
| 2 | US2 / SC-003 | Tamper a save request so `countryCode=ZZ` (devtools/Network replay), or temporarily inject an invalid code | Response is `{success:false, data:"invalid_country"}`; an error message shows; **no** `zz_custom_shipping_zones` option is created |
| 3 | US3 / SC-004 | Submit a state with a code containing an illegal char (e.g. `US@1`) or > 10 chars | Response is `{success:false, data:"invalid_state_code"}`; an error message shows; nothing is stored |
| 4 | US1 / SC-001 | Replay a save/delete with the `nonce` field removed or altered | Request rejected (`Nonce verification failed`); no data change |
| 5 | US1 / SC-002 | As a user **without** `manage_woocommerce` (e.g. Subscriber), issue a save/delete | Request rejected (`Not allowed!`); no data change |
| 6 | US2/US3 happy path / SC-006 | Select a real country, add a state with the auto-generated code, Save | Success screen shows; the state appears under "Your Existing Custom Shipping Zones" |
| 7 | Delete happy path / SC-006 | Delete the state added in #6 | "State deleted successfully"; row disappears |
| 8 | FR-011 / SC-007 | Confirm a pre-existing valid state is still present after the above | Existing data unchanged / not lost |

## Notes

- Steps 2–5 exercise the **server** boundary (authoritative). The UI Select and
  auto-generated, disabled code field make it hard to produce invalid input through
  normal clicks, so use a request replay (devtools → Network → Edit & Resend, or
  curl with a valid nonce) to confirm server rejection.
- After step 2/3, also confirm via `wp option list --search='*_custom_shipping_zones'`
  (WP-CLI) or the options table that no bad option row was written.
- Reminder (cross-phase): do not introduce new `console.log` / `var_dump` /
  `error_log`; do not add npm packages; use `$_POST`/`$_GET` explicitly.
