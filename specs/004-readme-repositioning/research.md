# Phase 0 Research: Readme, Messaging & Repositioning

All "unknowns" here are messaging/scope decisions, not technical ones. The spec
carried three assumptions; this document confirms them against the live code and
records the rationale.

## Decision 1 — Repositioning, not a feature build ("Option A")

- **Decision**: Correct the words to match current behavior. Do **not** build a real
  WooCommerce shipping-zone manager and do **not** remove the underlying export/import
  code.
- **Rationale**: The plugin already does something real and useful — it injects custom
  state/region codes via `woocommerce_states`. The defect is the description, not the
  behavior. Per constitution Principle II (minimum viable surface), fixing the copy is
  the smallest change that removes the misrepresentation. Building a zone manager would
  be a new feature, out of scope for a WP 7.0 readiness pass.
- **Alternatives considered**: (a) Build actual shipping-zone management to match the
  old promise — rejected: large scope, new regression surface, not required for
  readiness. (b) Leave copy as-is — rejected: it is the root cause of user confusion
  and support load this phase exists to fix.

## Decision 2 — Controlled vocabulary: "states / regions" vs "zones"

- **Decision**: Use **"custom states / regions"** for what the plugin produces;
  reserve **"shipping zone"** for WooCommerce's native feature; use **"region"** as a
  friendly synonym for "state".
- **Rationale**: The plugin populates `WC()->countries`'s **states** list (the
  `woocommerce_states` filter). "States" is WooCommerce's own word for exactly this
  data, so it is the most accurate and least surprising term. "Region" softens cases
  where "state" reads oddly for non-US locales (counties, governorates, districts).
  WooCommerce's "shipping zones" remain a distinct, real concept the produced states
  feed into — so "zone" is correct *only* in that sense (FR-009).
- **Where "zone" is correctly retained** (verified in code):
  - `includes/Strings.php` → `state_is_in_use`: "…currently being used in a shipping
    zone…" — refers to the WooCommerce zone. **Keep.**
  - `includes/Strings.php` → second clause of `faq_cant_delete_description`: "…if it is
    being used in a shipping zone. Remove it from the shipping zone first." **Keep** the
    WooCommerce-zone references; reword only the plugin's own item.
- **Alternatives considered**: Standardize on a single noun ("state" only, or "region"
  only). Rejected as the default because "state/region" reads naturally across locales
  and matches the plugin's existing field labels ("State name", "State code"). Flagged
  in the spec checklist for `/speckit-clarify` if the maintainer wants one noun.

## Decision 3 — Export/import scope

- **Finding (verified)**: `src/ExportImport.js` exists but is **not imported** by
  `src/App.js` — the export/import controls do **not render**. The export_*/import_*
  strings in `Strings.php` are therefore **unsurfaced** today. The *only* rendered
  export/import copy is the **FAQ entry** in `src/App.js` (lines 84–87), built from
  `faq_export_import` ("How do I export / import custom shipping zones?") and
  `faq_export_import_description` ("…will be supported in a future update…").
- **Decision**: Remove the rendered export/import **FAQ entry** (App.js array item +
  the two `Strings.php` keys). Leave the unrendered `ExportImport.js` component and the
  already-unsurfaced `export_*/import_*` strings **in place** — they satisfy FR-008's
  "removed **or left unsurfaced**" by virtue of not rendering, and removing them is
  speculative cleanup that Principle II discourages.
- **Rationale**: This is the smallest change that satisfies the plan's explicit
  acceptance criterion ("Any FAQ entry referencing export/import is removed, as that
  feature is not being shipped") and FR-008, without deleting code that no user sees.
- **Consequence**: Removing the FAQ array entry is a **JavaScript** change to
  `src/App.js`, so `npm run build` is required and the regenerated `build/index.js`
  must be committed. This is the single JS touch in the phase and removes content only.
- **Alternatives considered**: (a) Also delete `ExportImport.js` and all export_*
  strings — rejected (out-of-scope cleanup; risk for no surfaced benefit). (b) Keep the
  FAQ but reword it to "not planned" — rejected: the plan asks for removal, and an
  accordion entry about an absent feature still adds noise.

## Decision 4 — Admin "heading/title" target

- **Finding (verified)**: `includes/admin/admin.php` renders only a React mount point
  (`<div id="csz_custom-shipping-zones"><h2>Loading…</h2></div>`) — no static heading.
  The user-visible "title" is the **WooCommerce settings tab label**
  (`CustomShippingZones.php:179`, value `__('Custom Shipping Zones', …)`). The most
  prominent in-app section header is the `current_custom_shipping_zones` string.
- **Decision**: Update the settings-tab **label value** and the
  `current_custom_shipping_zones` section header to states/regions wording. **Do not**
  change the tab array **key** `custom_shipping_zones`.
- **Rationale**: The key is referenced by the tab-render hook name
  (`woocommerce_settings_tabs_custom_shipping_zones`) and the enqueue guard
  (`$_GET['tab'] === 'custom_shipping_zones'`). Changing it would break the screen
  (Principle I). Changing only the displayed label is safe and satisfies FR-004.
- **Tradeoff noted**: The plugin is *named* "Custom Shipping Zones for WooCommerce",
  so a tab reading "Custom States / Regions" is clearer about function but slightly
  less obviously tied to the installed plugin name. Accepted: accuracy (FR-004) outranks
  brand-echo in the tab strip; the plugin name itself is not being renamed (out of
  scope).

## Decision 5 — Build & delivery mechanics

- **Finding (verified)**: Admin strings reach the React app via
  `wp_localize_script('custom-shipping-zone-admin', 'cszStrings', $this->get_strings())`
  (CustomShippingZones.php:58), which returns `Strings::strings()`. So editing
  `Strings.php` **values** changes the UI with **no rebuild**.
- **Decision**: Treat `Strings.php`, `readme.txt`, `readme.md`, and
  `custom-shipping-zones.php` as no-build text edits. Only the `src/App.js` FAQ removal
  needs `npm run build` (script: `wp-scripts build`, output `build/index.js` +
  `build/index.asset.php`).
- **Rationale**: Keeps the change reviewable and isolates the single build artifact to
  one well-understood content edit.

## Verification approach (feeds quickstart.md)

- `php -l custom-shipping-zones.php` and `php -l includes/Strings.php` must pass.
- `npm run build` must succeed after the App.js edit.
- Manual: open the WooCommerce → Settings → Custom States/Regions tab — heading and
  labels read in states/regions terms; the export/import FAQ is gone; add → save →
  delete a state still works; guidance still links to WooCommerce → Settings → Shipping.
- Read `readme.txt` top-to-bottom: short/long description, usage, FAQ, and metadata all
  accurate and consistent; `Stable tag`, `Tested up to`, and a `1.0.6` changelog entry
  present.

**Output**: No open NEEDS CLARIFICATION. All scope decisions resolved with reasonable,
constitution-aligned defaults; the single-noun terminology choice and any deeper
export/import cleanup remain optional `/speckit-clarify` topics.
