# Phase 0 Research: Critical Bug Fixes & Security Hardening (Phase 2)

All open questions resolved by auditing the live code against the spec and the
constitution. No external/unknown technologies — this is a fix to existing flows.

## R1 — Is AJAX nonce + capability (US1 / FR-001, FR-002) already enforced?

- **Decision**: Yes. Treat US1 as *verify and protect from regression*, plus a
  single low-risk hardening: run the nonce check **before** the capability check
  in both handlers.
- **Rationale**: `save_states()` and `delete_state()` both already call
  `current_user_can('manage_woocommerce')` and
  `wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'csz_nonce')`.
  `wp_send_json_error()` calls `wp_die()` internally, so a failed check terminates
  before any write. The constitution's Principle VI is therefore already met; the
  responsible change is to keep it met (no regression) rather than rebuild it.
  Verifying the nonce first is the conventional order (reject forged requests
  before doing capability work) and is essentially free.
- **Alternatives considered**:
  - *Switch to `check_ajax_referer('csz_nonce', 'nonce')`*: functionally
    equivalent to the current manual `wp_verify_nonce`; rejected as needless churn
    (Principle II). The existing manual check is correct and explicit.
  - *Rename the nonce action to `csz_save_states_nonce` / `csz_delete_state_nonce`*
    (the cross-phase note's `csz_{action}_nonce` suggestion): rejected for this
    phase — it is a coordinated PHP+JS rename with no behavioral benefit and is out
    of the phase's acceptance criteria (Principle II). The single `csz_nonce`
    action is created and verified consistently today.

## R2 — Authoritative source of valid country codes (US2 / FR-003)

- **Decision**: Validate the submitted country against
  `WC()->countries->get_countries()` using `array_key_exists()`; reject empty or
  unknown codes with `wp_send_json_error('invalid_country')`.
- **Rationale**: This is the same list WooCommerce itself uses and the same list
  already fed to the client `CountrySelector` (Principle III, WooCommerce-First).
  An unknown code such as `ZZ` would otherwise create an orphan option
  `zz_custom_shipping_zones` that `get_custom_shipping_zones()` never reads — a
  silent data-integrity defect. Validating at the write boundary is authoritative
  (FR-006).
- **Alternatives considered**: maintaining a hard-coded country list (rejected —
  duplicates WC and drifts); validating only client-side (rejected — client is not
  the security/integrity boundary).

## R3 — State-code format rule (US3 / FR-004)

- **Decision**: Accept `^[A-Za-z0-9-]{1,10}$` (letters, digits, hyphens; 1–10
  characters). Reject the whole submission on the first violation, before any
  `update_option`. State *name* must be non-empty after `sanitize_text_field`.
- **Rationale**: Matches the WP 7.0 readiness plan's stated rule (alphanumeric +
  hyphen, max 10). The app's own auto-generated codes look like `US-XY-123`
  (country prefix + initials + 3 digits), which are ≤ ~9 chars and already satisfy
  the rule — so legitimate existing and future codes pass, preserving backward
  compatibility (Principle I). Hyphen is required because generated codes contain
  it. All-or-nothing rejection satisfies FR-005 (no partial writes).
- **Alternatives considered**:
  - *Allow underscores / colons*: rejected — WooCommerce uses `country:state`
    location codes, and a colon inside a state code would collide with that parsing
    (see `is_state_in_use()` which splits on `:`). Excluding `:` is deliberate.
  - *Silently truncate/normalize invalid codes*: rejected — silent fixups violate
    the spec's "reject and store nothing" behavior and hide user error.

## R4 — Debug output policy (US4 / FR-008, Principle V)

- **Decision**: Remove `console.log(current_states)` (`src/CurrentStates.js:10`).
  Retain the two `console.error(...)` calls that sit inside `.catch()` blocks
  (`CurrentStates.js` delete handler, and the App save flow after the fix).
- **Rationale**: Principle V targets *debug* output that leaks internal state; the
  `console.log` dumps the full zones object on every page load and must go.
  `console.error` inside a catch is standard, deliberate error reporting for an
  actual failure, not state leakage, and removing it would reduce diagnosability of
  genuine AJAX errors. SC-005 ("no plugin-originated debug messages during normal
  use") is satisfied because `console.error` fires only on error paths, not normal
  use.
- **Alternatives considered**: guarding the log behind a debug flag (rejected —
  simpler to delete; no value in keeping it); removing all `console.*` (rejected —
  loses legitimate error reporting).

## R5 — Making server rejections visible to the admin (FR-005, FR-007)

- **Decision**: Fix `src/App.js` `handleSaveStates` to read `result.success` and,
  on failure, show an Ant Design `message.error` keyed off the returned error code
  (`invalid_country`, `invalid_state_code`) with a generic fallback. Add the
  corresponding strings to `includes/Strings.php`.
- **Rationale**: Currently `handleSaveStates` resolves `.then(() => { ... setSavedSuccessfully(true) })`
  **without inspecting the response**, so even a `wp_send_json_error` from the new
  validation would still render the success screen — making the validation
  invisible and violating FR-005/FR-007. The fix mirrors the existing, correct
  response-handling pattern already used in `CurrentStates.js` `handleDelete`
  (which maps `state_is_in_use` to a friendly string). Reusing that pattern keeps
  the change small and idiomatic (Principle II).
- **Alternatives considered**: client-side pre-validation only (rejected — server
  is authoritative and tampered requests must still surface an error); a generic
  "failed" message with no specifics (rejected — less helpful; the server already
  distinguishes the two failure codes cheaply).

## R6 — Build & verification toolchain

- **Decision**: Build with `npm run build` (`wp-scripts build`); verify PHP with
  `php -l` on changed files; verify behavior manually per `quickstart.md`.
- **Rationale**: `package.json` defines `build` as `wp-scripts build`, emitting
  `build/index.js` + `build/index.asset.php` (the enqueued bundle). No bundler
  change is permitted (Principle II). No new npm packages are introduced
  (`message` is already imported from the existing `antd` dependency).
- **Alternatives considered**: adding jest unit tests this phase (rejected —
  acceptance is behavior-based per Principle VII and the existing flows have no
  test harness wired; out of minimum scope).
