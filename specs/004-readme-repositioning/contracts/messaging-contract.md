# Messaging Contract: Controlled Vocabulary

The "contract" for a copy phase is the vocabulary every surface must obey. Each row
maps a concept to the approved term and records where it applies. `/speckit-tasks` and
implementation should treat the **MUST keep / MUST change** columns as acceptance gates.

## Term mapping

| Concept | Approved term | Never say | Notes |
|---|---|---|---|
| The thing this plugin adds to a country | **custom state / region** ("state", "region", "state/region") | "custom shipping zone" (for the plugin's item) | Mirrors WooCommerce's `states` list that `woocommerce_states` feeds. |
| Friendlier word for "state" in non-US locales | **region** | — | Use where "state" reads awkwardly (county, governorate, district). |
| WooCommerce's native geographic shipping feature | **shipping zone** | — | "zone" is correct ONLY here (FR-009). |
| What the plugin does, in one line | "adds custom states/regions to a country so you can target them in WooCommerce shipping zones" | "create custom shipping zones beyond the default zones" | Root misrepresentation being fixed. |
| Where the user applies the result | "WooCommerce → Settings → Shipping" | — | Real hand-off; keep this literal string in `navigate_to_woocommerce_settings` (App.js splits on it). |
| Export / import | *(not advertised)* | "export/import is available" / "coming in a future update" | Remove the rendered FAQ entry (FR-008). |

## String-level acceptance gates

### `includes/Strings.php`

| Key | Action | Must keep |
|---|---|---|
| `current_custom_shipping_zones` | Reword "Custom Shipping Zones" → states/regions | — |
| `reload_page` | "add new zones" → "add new states/regions" | — |
| `navigate_to_woocommerce_settings` | "newly added custom zones" → "…states/regions" | literal `WooCommerce → Settings → Shipping` substring |
| `faq_cant_delete` | Reword the plugin's item → state/region | — |
| `faq_cant_delete_description` | Reword the plugin's item → state/region | the WooCommerce **"shipping zone"** references in the answer |
| `faq_woocommerce_settings` | "custom shipping zones" → states/regions | — |
| `faq_woocommerce_settings_description` | Reword to states/regions framing | the link target `WooCommerce → Settings → Shipping` |
| `faq_export_import` | **Remove** | — |
| `faq_export_import_description` | **Remove** | — |
| `state_is_in_use` | **No change** | correct WooCommerce-zone usage |
| `export_*` / `import_*` (unrendered) | **No change** | minimum surface; already unsurfaced |

### `includes/CustomShippingZones.php`

| Location | Action | Must keep |
|---|---|---|
| Line 179 tab label value | Reword to states/regions | array **key** `custom_shipping_zones` unchanged; hook names depend on it |

### `src/App.js`

| Location | Action | Must keep |
|---|---|---|
| `faqs` array, export/import entry (~lines 84–87) | **Remove** the object | all other FAQ entries and logic unchanged |

### `custom-shipping-zones.php`

| Location | Action |
|---|---|
| Header `Description:` (line 4) | Reposition to states/regions phrasing |
| Header `Version:` (line 6) + constant (line 23) | → `1.0.6` |

### `readme.txt` / `readme.md` / `README.md`

| Section | Action |
|---|---|
| Short description | Reposition (states/regions) |
| Long description / Features | Remove direct-zone-management claims |
| Usage / Installation | Describe real flow → WooCommerce → Settings → Shipping |
| FAQ | Accurate; no export/import advertised |
| Metadata (readme.txt) | `Stable tag` → 1.0.6; `Tested up to` → 7.0; add `= 1.0.6 =` changelog |

## Contract verification

- **Consistency**: No surfaced string calls the plugin's own item a "shipping zone".
- **Preservation**: Every WooCommerce-native "shipping zone" reference (FR-009) is
  intact: `state_is_in_use`, `faq_cant_delete_description` (answer), and any
  usage/FAQ guidance pointing to WooCommerce shipping.
- **Removal**: No rendered surface mentions export/import.
- **Stability**: `custom_shipping_zones` tab key, `custom-shipping-zones` text domain,
  and option keys are unchanged.
