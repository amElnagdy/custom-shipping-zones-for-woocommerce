# Contract: Admin AJAX Endpoints (Phase 2)

The plugin's external interface for this feature is two `admin-ajax.php` actions,
registered only when `is_admin() && DOING_AJAX`. Both are **write** endpoints and
both require a valid nonce **and** the `manage_woocommerce` capability. Responses
use WordPress's standard `wp_send_json_success` / `wp_send_json_error` envelope:

- Success: `{ "success": true, "data": null }`
- Error:   `{ "success": false, "data": "<error_code_or_message>" }`

The nonce is created server-side as `wp_create_nonce('csz_nonce')` and delivered to
the client as `cszAjax.nonce`; the AJAX URL as `cszAjax.ajax_url`.

---

## `csz_save_states` — add custom states/regions to a country

**Request** (`POST`, `application/x-www-form-urlencoded`):

| Field | Required | Description |
|-------|----------|-------------|
| `action` | yes | `csz_save_states` |
| `nonce` | yes | `cszAjax.nonce` (action `csz_nonce`) |
| `countryCode` | yes | A country code present in `WC()->countries->get_countries()` |
| `states` | yes | JSON array of `{ "name": string, "code": string }` |

**Server checks, in order** (first failure wins; nothing is persisted on failure):

| # | Check | Failure response (`data`) |
|---|-------|---------------------------|
| 1 | `nonce` present & valid for `csz_nonce` | `"Nonce verification failed"` |
| 2 | current user can `manage_woocommerce` | `"Not allowed!"` |
| 3 | `countryCode` non-empty & a valid WC country *(NEW)* | `"invalid_country"` |
| 4 | every state `code` matches `^[A-Za-z0-9-]{1,10}$` & `name` non-empty *(NEW)* | `"invalid_state_code"` |

**Success**: states merged into option `{cc}_custom_shipping_zones`; returns
`{ "success": true }`.

**Client handling (NEW)**: `App.js` MUST branch on `result.success`. On `false`,
map `invalid_country` → `strings.invalid_country`, `invalid_state_code` →
`strings.invalid_state_code`, else `strings.failed_to_save_states`, and show it via
`message.error`. The success screen MUST render only when `result.success === true`.

---

## `csz_delete_state` — delete one custom state/region

**Request** (`POST`, `application/x-www-form-urlencoded`):

| Field | Required | Description |
|-------|----------|-------------|
| `action` | yes | `csz_delete_state` |
| `nonce` | yes | `cszAjax.nonce` (action `csz_nonce`) |
| `countryCode` | yes | Country code of the state to remove |
| `stateCode` | yes | Code of the state to remove |

**Server checks, in order**:

| # | Check | Failure response (`data`) |
|---|-------|---------------------------|
| 1 | `nonce` present & valid for `csz_nonce` | `"Nonce verification failed"` |
| 2 | current user can `manage_woocommerce` | `"Not allowed!"` |
| 3 | state not currently used by a WC shipping zone | `"state_is_in_use"` |

**Success**: removes the entry from `{cc}_custom_shipping_zones`; returns
`{ "success": true }`.

**Client handling (existing, unchanged)**: `CurrentStates.js` maps `state_is_in_use`
→ `strings.state_is_in_use`, else `strings.failed_to_delete_state`.

---

## Contract invariants (Phase 2 acceptance)

- **INV-1**: A request failing any check causes **no** change to any
  `{cc}_custom_shipping_zones` option (FR-005, FR-011).
- **INV-2**: The set of valid countries equals `WC()->countries->get_countries()`
  at request time (FR-003) — no independent list.
- **INV-3**: Check ordering is nonce → capability → input validation on both
  endpoints.
- **INV-4**: Response envelope and the existing error codes
  (`state_is_in_use`, `Not allowed!`, `Nonce verification failed`) are preserved
  (backward compatibility, Principle I); only the new `invalid_country` /
  `invalid_state_code` codes are added.
