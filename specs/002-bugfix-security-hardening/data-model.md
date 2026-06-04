# Phase 1 Data Model: Critical Bug Fixes & Security Hardening (Phase 2)

No schema changes in this phase. This documents the existing data shapes and the
**validation rules** Phase 2 enforces at the write boundary.

## Entities

### Custom State/Region

A user-defined sub-region of a country, injected into WooCommerce's state list via
the `woocommerce_states` filter.

| Field | Type | Source | Validation (NEW in Phase 2) |
|-------|------|--------|------------------------------|
| `code` | string | client (auto-generated, e.g. `US-XY-123`) | MUST match `^[A-Za-z0-9-]{1,10}$` (FR-004). Trimmed via `sanitize_text_field`. |
| `name` | string | client (admin-entered) | MUST be non-empty after `sanitize_text_field` (edge case: empty/whitespace rejected). |
| `country` | string (country code) | client (Select of WC countries) | MUST be a key in `WC()->countries->get_countries()` (FR-003). |

Relationship: each Custom State/Region belongs to exactly one Country and is stored
under that country's option.

### Country

The parent geographic entity. **Owned by WooCommerce** — the valid set is
`WC()->countries->get_countries()`. The plugin maintains no country list of its own
(Principle III). Not persisted by the plugin.

## Storage (unchanged)

- **Option key**: `{strtolower(countryCode)}_custom_shipping_zones`
  (e.g. `us_custom_shipping_zones`).
- **Option value**: associative array `stateCode => stateName`.
- **Merge semantics**: `save_states()` merges new states into the existing option
  via `array_merge` (existing entries with the same code are overwritten by new
  values; other entries preserved). Unchanged by Phase 2.
- **Read path**: `get_custom_shipping_zones()` iterates WC countries and reads each
  `{cc}_custom_shipping_zones` option; `modify_woocommerce_states()` injects them
  into the `woocommerce_states` array. Unchanged by Phase 2.

## Validation lifecycle (write path — `save_states()`)

```text
request ──► nonce valid?  ──no──► error 'Nonce verification failed' (wp_die)
              │ yes
              ▼
           capability manage_woocommerce? ──no──► error 'Not allowed!' (wp_die)
              │ yes
              ▼
           country in WC countries? ──no──► error 'invalid_country'  (nothing stored)
              │ yes
              ▼
           every state code matches rule AND name non-empty?
              │ no ──► error 'invalid_state_code'  (nothing stored)
              │ yes
              ▼
           build $statesFormatted ► array_merge with existing ► update_option ► success
```

All failure branches return **before** any `update_option`, guaranteeing
all-or-nothing writes (FR-005) and that existing stored data is never altered on a
rejected request (FR-011).

## Delete path (`delete_state()`) — unchanged except check ordering

Nonce → capability → `is_state_in_use()` guard → `unset` + `update_option`. Phase 2
only reorders nonce-before-capability for consistency; no validation added (the
state code here is a lookup key, already `sanitize_text_field`-ed, and a non-match
simply unsets nothing).
