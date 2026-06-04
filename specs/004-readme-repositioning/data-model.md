# Phase 1 Data Model: Readme, Messaging & Repositioning

This phase changes **copy**, not data. There is no database entity, option, or schema
work. The "entities" below are the content surfaces being edited and the controlled
vocabulary governing them — modeled here so `/speckit-tasks` can map each requirement
to a concrete artifact.

## Entity: Public Description (readme)

- **Represents**: What prospective and current users read to evaluate the plugin.
- **Lives in**: `readme.txt` (WordPress.org), `readme.md` / `README.md` (GitHub).
- **Attributes (fields edited)**:
  - `short_description` — one-line summary (readme.txt:11).
  - `long_description` + `features` — what the plugin does (readme.txt:15–21; readme.md
    "Features").
  - `usage` / `installation` — the real flow: define states/regions → use under
    WooCommerce → Settings → Shipping (readme.txt:23–39; readme.md usage).
  - `faq` — questions/answers; no phantom features (readme.txt:42–46).
  - `metadata` — `Stable tag` (→ 1.0.6), `Tested up to` (→ 7.0), `Changelog` (add
    `= 1.0.6 =`).
- **Validation rules** (from requirements):
  - MUST NOT claim direct WooCommerce shipping-zone management (FR-001, FR-002).
  - Usage MUST describe the real hand-off to WooCommerce Shipping (FR-003).
  - FAQ entries MUST be accurate; no export/import advertised (FR-007, FR-008).
  - Metadata MUST be internally consistent and reflect 1.0.6 (FR-010, SC-006).

## Entity: Admin Screen Copy

- **Represents**: In-product text the installed user sees on the plugin tab.
- **Lives in**:
  - `includes/Strings.php` — string **values** delivered via `wp_localize_script`
    (`cszStrings`); editing values needs **no rebuild**.
  - `includes/CustomShippingZones.php:179` — WooCommerce settings-tab **label** value.
  - `src/App.js` — FAQ **structure** (which entries exist); editing needs a rebuild.
- **Attributes (fields edited)**:
  - `tab_label` — settings-tab display value (key `custom_shipping_zones` unchanged).
  - `section_header` — `current_custom_shipping_zones`.
  - `guidance` — `navigate_to_woocommerce_settings`, `reload_page`.
  - `faq_entries` — `faq_cant_delete[_description]`, `faq_woocommerce_settings[_description]`;
    **removed**: `faq_export_import[_description]` (Strings.php + App.js array item).
- **Validation rules**:
  - Heading/labels MUST use states/regions, not "zones" for the plugin's items
    (FR-004, FR-005, SC-003).
  - Post-create guidance MUST point to WooCommerce → Settings → Shipping (FR-006).
  - "zone" retained ONLY for WooCommerce-native references (FR-009) — see contract.
  - Tab array **key** MUST NOT change (backward compatibility, Principle I).

## Entity: Plugin Header

- **Represents**: The Plugins-screen description and version metadata.
- **Lives in**: `custom-shipping-zones.php` (header comment lines 3–13; version
  constant line 23).
- **Attributes (fields edited)**: `Description` (reposition), `Version` (→ 1.0.6),
  `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION` (→ 1.0.6).
- **Validation rules**: Description accurate (FR-001 spirit); `php -l` passes; version
  consistent with readme `Stable tag` (SC-006).

## Entity: Controlled Vocabulary (Terminology)

- **Represents**: The mapping that keeps every surface telling the same story.
- **Defined in**: [`contracts/messaging-contract.md`](./contracts/messaging-contract.md).
- **States**: a term is either (a) **plugin item** → "custom state/region", or
  (b) **WooCommerce native** → "shipping zone". Every edited string resolves to exactly
  one of these.

## Out of scope (explicitly not edited)

- Stored options, the `woocommerce_states` filter, AJAX handlers, nonce/capability
  checks, validation logic.
- `src/ExportImport.js` and the unrendered `export_*/import_*` strings (already
  unsurfaced).
- The plugin slug / Text Domain `custom-shipping-zones`, and the WC tab array key
  `custom_shipping_zones`.
