# Implementation Plan: Readme, Messaging & Repositioning (Phase 4)

**Branch**: `csz-wp7-readiness` (work branch) · spec dir `004-readme-repositioning` | **Date**: 2026-06-04 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/004-readme-repositioning/spec.md`

## Summary

Phase 4 corrects the plugin's public and in-product messaging so it matches what
the plugin actually does: it adds custom **states/regions** to a country via the
`woocommerce_states` filter, which the store owner then targets inside WooCommerce's
own shipping zones. It does **not** manage WooCommerce shipping zones directly. This
is the "Option A reposition" — change words, not behavior.

After auditing the live tree, the misleading copy lives in four places:

1. **`readme.txt`** — short description ("create custom shipping zones beyond the
   default zones", line 11), long description/features (lines 15–21), usage steps
   (lines 23–34), FAQ (lines 42–46), and stale metadata (`Stable tag: 1.0.2`,
   `Tested up to: 6.9`).
2. **`readme.md` / `README.md`** — the GitHub mirror repeats the same "create and
   manage custom shipping zones" framing.
3. **`includes/Strings.php`** — admin UI strings delivered to React via
   `wp_localize_script('…','cszStrings', …)` (CustomShippingZones.php:58). Editing
   the **values** here changes the admin UI with **no JS rebuild**. Several use
   "zones" for the thing the user creates (`current_custom_shipping_zones`,
   `reload_page`, `navigate_to_woocommerce_settings`, and FAQ strings).
4. **`custom-shipping-zones.php`** — the plugin header `Description:` (line 4) shown
   on the Plugins screen also says "add custom shipping zones to WooCommerce".

The WooCommerce settings **tab label** is set in PHP
(`CustomShippingZones.php:179`, value `__('Custom Shipping Zones', …)`); the array
**key** `custom_shipping_zones` drives the tab hook names and MUST stay unchanged
(backward compatibility). Only the human-readable label value changes.

**The one JS touch**: the export/import FAQ entry is the only user-facing
export/import copy that actually renders. `src/ExportImport.js` exists but is **not
imported** by `src/App.js`, so its strings are already unsurfaced. However
`src/App.js` (lines 84–87) **does** render an FAQ entry built from
`faq_export_import` / `faq_export_import_description` ("…will be supported in a
future update…"). Removing that entry from the `faqs` array is a JavaScript content
change and therefore requires `npm run build`. This is the single JS change in the
phase and it removes content only — no logic.

Ships as **1.0.6** (assumes Phases 1–3 land first; the live tree is still 1.0.2 —
see Release Hygiene).

## Technical Context

**Language/Version**: PHP 7.4 (constitution Principle IV) for `readme.txt`,
`readme.md`, `includes/Strings.php`, `custom-shipping-zones.php`. One JavaScript
(JSX, ES2015+ via `@wordpress/scripts`) content edit in `src/App.js`, rebuilt to
`build/index.js` with `npm run build`.

**Primary Dependencies**: WordPress i18n (`__()`), `wp_localize_script` for
delivering `cszStrings` to the React admin app. No new dependencies (Principle III).
Build via `wp-scripts build` (existing pipeline — unchanged).

**Storage**: None touched. No options, no `woocommerce_states` contract change, no
schema change (Principle I). This phase reads and rewrites text only.

**Testing**: `php -l` on every changed PHP file
(`custom-shipping-zones.php`, `includes/Strings.php`). `npm run build` must succeed
after the `src/App.js` edit. Behavior verification per `quickstart.md`: admin screen
loads, add/save/delete a state still work, the export/import FAQ no longer appears,
and all surfaced copy reads in states/regions terms. Behavior-based per Principle VII.

**Target Platform**: WooCommerce store admin (WooCommerce → Settings → Custom Shipping
Zones tab) and WordPress.org / GitHub readme readers. PHP 7.4+, WooCommerce 9.x,
WordPress up to 7.0.

**Project Type**: Single WordPress/WooCommerce plugin (PHP backend + prebuilt React
admin bundle). This phase is copy/messaging with one content-only rebuild.

**Performance Goals**: N/A — no runtime behavior change (SC-007: states output and
admin actions behave identically before and after).

**Constraints**: Minimum viable surface (Principle II) — change text only; do not
rename option keys, the WC settings tab array key, or the `woocommerce_states`
signature; do not add/remove the export/import functionality (only remove its
rendered FAQ copy). Retain the word "zone" where it correctly refers to
WooCommerce's native shipping zones (FR-009): e.g. `state_is_in_use`
("…being used in a shipping zone…") and the second clause of
`faq_cant_delete_description` are correct and must be preserved.

**Scale/Scope**: Four PHP/text files + one JS file + the rebuilt bundle:
`readme.txt`, `readme.md` (and the case-variant `README.md` if distinct on the host
FS), `includes/Strings.php`, `custom-shipping-zones.php` (Description + Version +
version constant → 1.0.6), `src/App.js` (drop export/import FAQ entry), and the
regenerated `build/index.js` (+ `build/index.asset.php`).

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| # | Principle | Assessment |
|---|-----------|------------|
| I | Backward Compatibility | **PASS** — No stored option key, no `woocommerce_states` signature, and no WC settings-tab array **key** (`custom_shipping_zones`) changes. Only the tab's human label value and assorted display/text strings change. The plugin slug/Text Domain (`custom-shipping-zones`) is untouched, so translations keep loading. |
| II | Minimum Viable Surface | **PASS** — Text edits plus exactly one content-only JS edit (remove the rendered export/import FAQ entry). The unused `ExportImport.js` component and its unsurfaced strings are **left as-is** (already not rendered) to keep the diff minimal; no speculative cleanup. |
| III | WooCommerce-First | **PASS** — No data access changes. Guidance still points users to WooCommerce → Settings → Shipping (the canonical place to use the produced states). No direct DB queries, no private hooks. |
| IV | PHP 7.4 Baseline | **PASS** — Only string literal values and header comments change in PHP; no new syntax. |
| V | Zero Debug Output | **PASS** — No `console.log`/`error_log`/`var_dump` added. The `npm run build` output is a production bundle; no debug statements introduced. |
| VI | Security by Default | **PASS** — No AJAX handler, nonce, or capability check is touched. Copy-only. |
| VII | Test the Contract | **PASS** — Acceptance is behavior-based: admin loads, add/save/delete still work, export/import FAQ gone, copy reads in states/regions terms. No assertion about internal structure. |
| VIII | Branch Policy | **PASS** — All work on `csz-wp7-readiness` per the constitution Workflow and the user's explicit instruction. No per-phase implementation branch. |

**Result**: All gates pass. No violations → Complexity Tracking left empty.

## Project Structure

### Documentation (this feature)

```text
specs/004-readme-repositioning/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   └── messaging-contract.md
├── checklists/
│   └── requirements.md  # Created by /speckit-specify
└── tasks.md             # Phase 2 output (/speckit-tasks — NOT created here)
```

### Source Code (repository root)

```text
custom-shipping-zones.php      # header Description reposition; Version + version constant → 1.0.6
readme.txt                     # short desc, long desc, features, usage, FAQ; Stable tag → 1.0.6;
                               #   Tested up to → 7.0; changelog 1.0.6 entry
readme.md                      # mirror the readme.txt repositioning (GitHub copy)
README.md                      # case-variant mirror — update if it is a distinct file on the host FS
includes/
└── Strings.php                # reword "zones" → states/regions in SURFACED strings; remove
                               #   faq_export_import + faq_export_import_description
includes/CustomShippingZones.php  # line 179: settings-tab LABEL value → states/regions wording
                                  #   (array KEY 'custom_shipping_zones' unchanged)
src/
└── App.js                     # remove the export/import FAQ entry (faqs array, ~lines 84–87)
build/
├── index.js                   # regenerated by `npm run build`
└── index.asset.php            # regenerated by `npm run build`
```

Files explicitly **NOT** touched: `src/ExportImport.js` (already unrendered — left
in place), other `src/**` components, AJAX handlers and validation in
`includes/CustomShippingZones.php` (other than the one tab-label line), option keys,
the `woocommerce_states` filter, `vendor/**`.

**Structure Decision**: Keep the existing flat plugin layout. The repositioning is
overwhelmingly PHP/text value changes that propagate without a build (admin strings
ship via `wp_localize_script`). The single exception — removing the export/import
FAQ entry — lives in `src/App.js` and so requires one `npm run build`.

## Controlled Vocabulary (the messaging contract)

The full term-by-term mapping lives in
[`contracts/messaging-contract.md`](./contracts/messaging-contract.md). Summary:

- **"custom states / regions"** — what *this plugin* adds to a country. Use this for
  the things the user creates and manages on the plugin screen.
- **"region"** — friendly synonym for "state" where "state" reads awkwardly.
- **"shipping zone"** — reserved for *WooCommerce's native* feature the produced
  states are later used within. Keep "zone" only in that sense (FR-009).
- Never imply the plugin "creates/manages WooCommerce shipping zones".

## Concrete Changes (reference for /speckit-tasks)

### 1. `readme.txt` — public description, usage, FAQ, metadata

- **Short description (line 11)**: replace "A powerful tool … to create custom
  shipping zones beyond the default zones …" with copy that says the plugin lets
  store owners **add custom states/regions to a country** so those regions become
  selectable when configuring WooCommerce shipping zones.
- **Description / Features (lines 15–21)**: rewrite to describe defining custom
  states/regions that appear in WooCommerce's state list; remove any claim of direct
  shipping-zone management.
- **Usage (lines 23–34)**: reframe "Adding/Managing Custom Shipping Zones" as adding
  and managing custom states/regions, then **using them** under WooCommerce →
  Settings → Shipping.
- **FAQ (lines 42–46)**: reword the "custom shipping zone" questions to states/regions
  framing; ensure no FAQ advertises export/import.
- **Metadata**: `Stable tag: 1.0.2 → 1.0.6`; `Tested up to: 6.9 → 7.0`; add a
  `= 1.0.6 =` **Changelog** entry noting the repositioning. (Leave `Requires PHP`,
  `Requires at least` as-is unless an earlier phase already bumped them.)

### 2. `readme.md` / `README.md` — GitHub mirror

- Mirror the same repositioning in the prose ("create and manage custom shipping
  zones" → custom states/regions; features; usage). Keep code/links intact. If
  `readme.md` and `README.md` are the same inode on the host, edit once.

### 3. `includes/Strings.php` — admin UI strings (no rebuild)

Reword the **surfaced** strings; values only:

- `current_custom_shipping_zones`: "Your Existing Custom Shipping Zones" → states/regions
  wording (e.g. "Your Existing Custom States / Regions").
- `reload_page`: "Reload page and add new zones" → "… add new states/regions".
- `navigate_to_woocommerce_settings`: keep the literal
  `WooCommerce → Settings → Shipping` substring (App.js splits on it, line 38) but
  change "newly added custom zones" → "newly added custom states/regions".
- FAQ strings: `faq_cant_delete` and `faq_cant_delete_description` →
  reword the *plugin's item* to state/region, **but preserve** the second clause that
  refers to WooCommerce's "shipping zone" (FR-009); `faq_woocommerce_settings` and
  `faq_woocommerce_settings_description` → states/regions framing.
- **Remove** `faq_export_import` and `faq_export_import_description` (paired with the
  App.js change below).

Leave `state_is_in_use` ("…being used in a shipping zone…") **unchanged** — correct
WooCommerce-zone usage. Leave the unsurfaced `export_*` / `import_*` strings as-is
(minimum surface; already not rendered).

### 4. `includes/CustomShippingZones.php` — settings-tab label only

- Line 179: change the **value** `__('Custom Shipping Zones', …)` to states/regions
  wording (e.g. `__('Custom States / Regions', …)`). **Do not** change the array key
  `custom_shipping_zones` — the hooks `woocommerce_settings_tabs_custom_shipping_zones`
  and the enqueue guard (`$_GET['tab'] === 'custom_shipping_zones'`) depend on it.

### 5. `custom-shipping-zones.php` — header description + version

- Line 4 `Description:` → reposition to "…add custom states/regions to a country for
  use in WooCommerce shipping zones" (or similar accurate phrasing).
- Header `Version: 1.0.2 → 1.0.6` and
  `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.6';` (line 23).

### 6. `src/App.js` — remove export/import FAQ entry (the one JS change)

- Delete the `faqs` array object built from `strings.faq_export_import` /
  `strings.faq_export_import_description` (lines 84–87). No other logic changes.
- Run `npm run build`; commit the regenerated `build/index.js` and
  `build/index.asset.php`.

### 7. Release Hygiene / dependency note

- The live tree is still **1.0.2** (Phases 1–3 are specced but not yet implemented in
  code). If those ship first this is a clean bump to **1.0.6**; if phases are combined,
  land at the highest version actually shipped. `/speckit-tasks` should treat the
  version bump (and the `npm run build`) as the **final** tasks so they reflect the
  real shipped state.

## Complexity Tracking

> No constitution violations — section intentionally empty.
