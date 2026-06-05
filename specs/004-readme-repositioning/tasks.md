---
description: "Task list for Phase 4 — Readme, Messaging & Repositioning"
---

# Tasks: Readme, Messaging & Repositioning (Phase 4)

**Input**: Design documents from `/specs/004-readme-repositioning/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/messaging-contract.md, quickstart.md
**Branch**: `csz-wp7-readiness`

**Tests**: No automated test tasks — this is a copy/messaging phase; acceptance is
behavior-based per constitution Principle VII and `quickstart.md`.

## How to use this file (read first — written for the Kimi implementer)

This phase changes **words, not behavior** (the "Option A reposition"). Every task
below gives the **exact file**, the **exact current text**, and the **exact replacement
text** so you never have to invent wording. Apply tasks in numeric order.

**Five rules that will save you from breaking the plugin:**

1. **Most edits are PHP/text and need NO build.** Admin strings reach the React app via
   `wp_localize_script('…','cszStrings', …)`, so editing the **values** in
   `includes/Strings.php` updates the UI with no rebuild. The **only** task that needs
   `npm run build` is the `src/App.js` edit (T014); run the build once at the end (T019).
2. **Never change an array KEY — only its displayed VALUE.** In particular keep the WC
   tab key `'custom_shipping_zones'` (T010) and the string keys in `Strings.php`. Hook
   names (`woocommerce_settings_tabs_custom_shipping_zones`) and the enqueue guard
   (`$_GET['tab'] === 'custom_shipping_zones'`) depend on that key. Changing it breaks
   the screen.
3. **Keep the literal substring `WooCommerce → Settings → Shipping`** inside
   `navigate_to_woocommerce_settings` (T009). `src/App.js` line 38 does
   `.split("WooCommerce → Settings → Shipping")` to build a link — if you change that
   substring, the link breaks.
4. **The word "zone" is still correct for WooCommerce's OWN shipping zones.** Reword it
   only when it names *this plugin's* item (a state/region). Strings you MUST leave with
   "shipping zone" intact: `state_is_in_use`, and the second clause of
   `faq_cant_delete_description` (FR-009). The full keep/change map is in
   `contracts/messaging-contract.md`.
5. **PHP single-quoted strings**: keep escaped apostrophes as `\'` (e.g. `can\'t`). Do
   not change the text domain `'custom-shipping-zones'` in any `__()` call.

**Controlled vocabulary** (full table in `contracts/messaging-contract.md`):
*this plugin's item* = **"custom state / region"** (use "states/regions");
*WooCommerce's native feature* = **"shipping zone"**. The plugin is NOT being renamed.

Note: git tracks the markdown mirror as **`README.md`**; on this Windows (case-
insensitive) checkout `readme.md` and `README.md` are the same file — edit `README.md`.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different file, no dependency on an unfinished task)
- **[Story]**: US1 = accurate public description · US2 = admin terminology ·
  US3 = FAQ/no-phantom-claims + accurate metadata

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish a clean baseline so a later failure is clearly caused by your edit.

- [X] T001 From the repo root, confirm the tree is clean and parses before editing:
  run `git status` (expect a clean working tree on `csz-wp7-readiness`),
  `php -l includes/Strings.php`, `php -l includes/CustomShippingZones.php`, and
  `php -l custom-shipping-zones.php` (each must print `No syntax errors detected`), and
  `npm run build` once (must succeed) so you know the build pipeline works on the
  untouched tree. Change/commit nothing in this task.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Internalize the terminology rules that govern every later edit.

**⚠️ CRITICAL**: Do this before any user-story task; it is the rulebook for all of them.

- [X] T002 Read `specs/004-readme-repositioning/contracts/messaging-contract.md` and the
  "Out of scope" list in `specs/004-readme-repositioning/data-model.md`. Note the binding
  rules: (a) call this plugin's item a **state/region**, never a "shipping zone";
  (b) keep "shipping zone" only where it refers to **WooCommerce's** native feature
  (strings `state_is_in_use` and the second clause of `faq_cant_delete_description` stay
  as-is); (c) never change array keys, the text domain, option keys, or the
  `WooCommerce → Settings → Shipping` literal. No file is edited in this task.

**Checkpoint**: You know which "zone" mentions to change and which to keep.

---

## Phase 3: User Story 1 - Accurate public description (Priority: P1) 🎯 MVP

**Goal**: A prospective reader of the readme understands the plugin adds custom
states/regions to a country (not that it manages WooCommerce shipping zones).
(FR-001, FR-002, FR-003)

**Independent Test**: Read only the short description + opening of the long description
of `readme.txt` (and `README.md`); a new reader correctly says "it adds custom
states/regions to a country," not "it builds shipping zones" (quickstart step 4).

> T003–T005 all edit **`readme.txt`** (same file) in separate, non-overlapping blocks —
> apply them in order. T006 edits a different file (`README.md`) and is `[P]`.

- [X] T003 [US1] Replace the **short description** in `readme.txt` (line 11). Current:
  ```text
  A powerful tool for WooCommerce store owners to create custom shipping zones beyond the default zones provided by WooCommerce.
  ```
  Replace with:
  ```text
  Add custom states and regions to any country so they become available when you set up WooCommerce shipping zones, taxes, and addresses.
  ```

- [X] T004 [US1] Replace the **Description + Features** block in `readme.txt` (lines
  13–21). Current:
  ```text
  == Description ==

  Custom Shipping Zones for WooCommerce allows store owners to extend the flexibility of WooCommerce shipping zones. With this plugin, you can create detailed shipping zones based on specific criteria that are not supported out of the box by WooCommerce.

  ### Features

  - Create custom shipping zones tailored to specific needs.
  - Easily manage and edit custom zones from the WooCommerce settings panel.
  - Integrate seamlessly with existing WooCommerce shipping methods.
  ```
  Replace with:
  ```text
  == Description ==

  Custom Shipping Zones for WooCommerce lets you add custom states/regions to any country. WooCommerce ships with a fixed list of states for each country — this plugin lets you define your own (for example a district, governorate, county, or any sub-region). Once added, your custom states appear in WooCommerce's own state list, so you can target them when building shipping zones, setting up tax rates, or restricting selling and shipping locations.

  This plugin does not replace WooCommerce's shipping zone manager — it feeds it. You keep using WooCommerce → Settings → Shipping exactly as before; you simply have extra states/regions to choose from.

  ### Features

  - Add custom states/regions to any WooCommerce country.
  - Manage and delete your custom states/regions from a dedicated WooCommerce settings tab.
  - Your custom states appear anywhere WooCommerce uses states — shipping zones, tax rates, and address fields.
  - Lightweight: no extra database tables; it uses WooCommerce's official state list.
  ```

- [X] T005 [US1] Replace the **Usage + Installation** block in `readme.txt` (lines
  23–39). Current:
  ```text
  ## Usage

  ### Adding Custom Shipping Zones

  1. Navigate to WooCommerce → Settings → Custom Shipping Zones.
  2. Use the intuitive interface to define new shipping zones based on your specific requirements.
  3. Assign shipping methods and rates to your custom zones.

  ### Managing Zones

  - Visit the Custom Shipping Zones tab to edit or delete existing zones.
  - Adjust zone settings and shipping methods as needed to adapt to changing business needs.

  == Installation ==
  1. Upload the `custom-shipping-zones` folder to the `/wp-content/plugins/` directory.
  2. Activate the plugin through the `Plugins` menu in WordPress.
  3. Configure and manage your shipping zones via WooCommerce → Settings → Custom Shipping Zones tab.
  ```
  Replace with:
  ```text
  ## Usage

  ### Adding custom states/regions

  1. Go to WooCommerce → Settings → Custom States / Regions.
  2. Select a country and add one or more custom states/regions (each gets an auto-generated code).
  3. Save. Your new states are now part of WooCommerce's state list for that country.

  ### Using them in WooCommerce

  - Go to WooCommerce → Settings → Shipping and add or edit a zone — your custom states are now selectable there (and in tax and address settings).
  - Return to the Custom States / Regions tab anytime to add or delete states/regions.

  == Installation ==
  1. Upload the `custom-shipping-zones` folder to the `/wp-content/plugins/` directory.
  2. Activate the plugin through the `Plugins` menu in WordPress.
  3. Add and manage your custom states/regions via WooCommerce → Settings → Custom States / Regions, then use them under WooCommerce → Settings → Shipping.
  ```

- [X] T006 [P] [US1] Mirror the repositioning in **`README.md`** (the GitHub copy).
  Apply these replacements:
  - Line 3 (intro), current:
    ```text
    Custom Shipping Zones for WooCommerce is a powerful and lightweight extension that allows WooCommerce store owners to create and manage custom shipping zones beyond the default zones provided by WooCommerce.
    ```
    →
    ```text
    Custom Shipping Zones for WooCommerce is a lightweight extension that lets WooCommerce store owners add custom states/regions to any country. Your custom states appear in WooCommerce's own state list, so you can target them when building shipping zones, tax rates, and address rules. It does not replace WooCommerce's shipping zone manager — it feeds it.
    ```
  - Features (lines 7–9), current:
    ```text
    - **Custom Zone Creation:** Define shipping zones based on specific criteria that are not natively supported by WooCommerce.
    - **Ease of Management:** Manage and edit your custom zones from the WooCommerce settings panel.
    - **Integration:** Works seamlessly with existing WooCommerce shipping methods.
    ```
    →
    ```text
    - **Add custom states/regions:** Define your own states or sub-regions for any WooCommerce country.
    - **Ease of management:** Add and delete your custom states/regions from a dedicated WooCommerce settings tab.
    - **Native integration:** Your custom states appear anywhere WooCommerce uses states — shipping zones, taxes, and address fields.
    ```
  - Line 18, current: `4. Navigate to WooCommerce → Settings → Custom Shipping Zones tab to configure your custom shipping zones.`
    → `4. Navigate to WooCommerce → Settings → Custom States / Regions to add your custom states/regions.`
  - Line 25, current: `4. Go to WooCommerce → Settings → Shipping to start creating your custom shipping zones.`
    → `4. Add your states/regions under WooCommerce → Settings → Custom States / Regions, then use them under WooCommerce → Settings → Shipping.`
  - Usage block (lines 27–35), current:
    ```text
    ## Usage

    ### Adding Custom Shipping Zones

    Navigate to WooCommerce → Settings → Custom Shipping Zones to add and configure new shipping zones. Here, you can define the geographical areas and set specific shipping rules and rates.

    ### Managing Custom Zones

    Manage your zones through the Custom Shipping Zones tab in the WooCommerce settings. You can edit or delete zones as needed to adapt to your business requirements.
    ```
    →
    ```text
    ## Usage

    ### Adding custom states/regions

    Go to WooCommerce → Settings → Custom States / Regions, select a country, and add one or more custom states/regions. Each gets an auto-generated code and becomes part of WooCommerce's state list for that country.

    ### Using them in WooCommerce

    Once added, your custom states are selectable under WooCommerce → Settings → Shipping (and in tax and address settings). Return to the Custom States / Regions tab anytime to add or delete states/regions.
    ```
  - Leave line 16 (`search for "Custom Shipping Zones"`) unchanged — it is the plugin's
    name in the WordPress search box, which is not being renamed.

**Checkpoint**: The public readme (both files) describes states/regions accurately. No
build needed (text files only).

---

## Phase 4: User Story 2 - Admin screen speaks WooCommerce's terminology (Priority: P2)

**Goal**: The plugin's admin tab label, section header, and guidance use states/regions
wording, not "zones". (FR-004, FR-005, FR-006)

**Independent Test**: Open WooCommerce → Settings → (the plugin tab); the tab name and
the "Your existing…" header read in states/regions terms; the post-save guidance still
links to WooCommerce → Settings → Shipping (quickstart steps 2, 3). No rebuild — these
are PHP value edits delivered via `wp_localize_script`.

> T007–T009 all edit **`includes/Strings.php`** (same file, distinct lines) — apply in
> order. T010 edits a different file and is `[P]`.

- [X] T007 [US2] In `includes/Strings.php` (line 23) change the section-header **value**
  only (keep the key `current_custom_shipping_zones`). Current:
  ```php
  'current_custom_shipping_zones' => __('Your Existing Custom Shipping Zones', 'custom-shipping-zones'),
  ```
  →
  ```php
  'current_custom_shipping_zones' => __('Your Existing Custom States / Regions', 'custom-shipping-zones'),
  ```

- [X] T008 [US2] In `includes/Strings.php` (line 13) reword `reload_page`. Current:
  ```php
  'reload_page' => __('Reload page and add new zones', 'custom-shipping-zones'),
  ```
  →
  ```php
  'reload_page' => __('Reload page and add new states/regions', 'custom-shipping-zones'),
  ```

- [X] T009 [US2] In `includes/Strings.php` (line 30) reword
  `navigate_to_woocommerce_settings`, **keeping the exact substring**
  `WooCommerce → Settings → Shipping` (App.js splits on it). Current:
  ```php
  'navigate_to_woocommerce_settings' => __('Now you can go to WooCommerce → Settings → Shipping to use the newly added custom zones.', 'custom-shipping-zones'),
  ```
  →
  ```php
  'navigate_to_woocommerce_settings' => __('Now you can go to WooCommerce → Settings → Shipping to use the newly added custom states/regions.', 'custom-shipping-zones'),
  ```

- [X] T010 [P] [US2] In `includes/CustomShippingZones.php` (line 179) change the settings
  **tab label value only** — DO NOT change the array key `'custom_shipping_zones'`.
  Current:
  ```php
  $settings_tabs['custom_shipping_zones'] = __('Custom Shipping Zones', 'custom-shipping-zones');
  ```
  →
  ```php
  $settings_tabs['custom_shipping_zones'] = __('Custom States / Regions', 'custom-shipping-zones');
  ```
  Then run `php -l includes/CustomShippingZones.php`.

**Checkpoint**: The admin tab and labels read in states/regions terms; the tab still
loads (key unchanged); the save-success link still works (substring preserved).

---

## Phase 5: User Story 3 - No false or phantom claims (Priority: P3)

**Goal**: Every FAQ/answer is accurate and uses states/regions wording; no surfaced copy
advertises export/import; the plugin-header description is accurate.
(FR-007, FR-008, FR-009)

**Independent Test**: Read every FAQ entry in the admin app and `readme.txt`; each
describes shipped behavior; the "export / import" FAQ is gone; the Plugins-screen
description is accurate (quickstart steps 2, 4).

> T011–T013 edit **`includes/Strings.php`**; T016 edits **`readme.txt`**; T014 edits
> **`src/App.js`**; T015 edits **`custom-shipping-zones.php`**. Same-file tasks
> (T011→T012→T013) apply in order; T014/T015/T016 are different files.

- [X] T011 [US3] In `includes/Strings.php` (lines 46–47) reword the "can't delete" FAQ —
  change the **plugin's item** to state/region but **keep** the WooCommerce "shipping
  zone" references (FR-009). Current:
  ```php
  'faq_cant_delete' => __('Why can\'t I delete a custom shipping zone?', 'custom-shipping-zones'),
  'faq_cant_delete_description' => __('You cannot delete a custom shipping zone if it is being used in a shipping zone. Remove it from the shipping zone first.', 'custom-shipping-zones'),
  ```
  →
  ```php
  'faq_cant_delete' => __('Why can\'t I delete a custom state/region?', 'custom-shipping-zones'),
  'faq_cant_delete_description' => __('You cannot delete a custom state/region while it is being used in a WooCommerce shipping zone. Remove it from the shipping zone first, then delete it here.', 'custom-shipping-zones'),
  ```

- [X] T012 [US3] In `includes/Strings.php` (lines 50–51) reword the "how to use in
  WooCommerce" FAQ. Current:
  ```php
  'faq_woocommerce_settings' => __('How do I use the custom shipping zones in WooCommerce?', 'custom-shipping-zones'),
  'faq_woocommerce_settings_description' => __('After adding custom shipping zones, go to WooCommerce → Settings → Shipping to use the newly added zones.', 'custom-shipping-zones'),
  ```
  →
  ```php
  'faq_woocommerce_settings' => __('How do I use my custom states/regions in WooCommerce?', 'custom-shipping-zones'),
  'faq_woocommerce_settings_description' => __('After adding your custom states/regions, go to WooCommerce → Settings → Shipping to use them in a shipping zone (they also appear in tax and address settings).', 'custom-shipping-zones'),
  ```

- [X] T013 [US3] In `includes/Strings.php` **delete the two export/import FAQ lines**
  (lines 48–49) entirely — this feature is not shipped (FR-008). Remove:
  ```php
  'faq_export_import' => __('How do I export / import custom shipping zones?', 'custom-shipping-zones'),
  'faq_export_import_description' => __('Export/import functionality will be supported in a future update, allowing you to easily transfer settings between stores. For the time being, you\'ll need to create them on the other website.', 'custom-shipping-zones'),
  ```
  Leave the other (unrendered) `export_*` / `import_*` strings untouched — they are not
  surfaced (the `ExportImport` component is not mounted), so per FR-008 they are already
  "left unsurfaced"; removing them is out-of-scope cleanup (Principle II). After this
  edit run `php -l includes/Strings.php`.

- [X] T014 [US3] In `src/App.js` remove the export/import FAQ entry from the `faqs`
  array (currently lines ~84–87) so it pairs with T013. Delete exactly:
  ```javascript
      {
        question: strings.faq_export_import,
        answer: strings.faq_export_import_description,
      },
  ```
  Leave every other `faqs` entry and all logic unchanged. (This is the only JS change in
  the phase; it takes effect only after the rebuild in T019.)

- [X] T015 [P] [US3] In `custom-shipping-zones.php` (line 4) reposition the plugin-header
  **Description** shown on the Plugins screen. Current:
  ```text
   * Description: Lightweight, yet powerful WooCommerce extension that allows you to add custom shipping zones to WooCommerce
  ```
  →
  ```text
   * Description: Lightweight WooCommerce extension that lets you add custom states/regions to any country for use in WooCommerce shipping zones, taxes, and address fields.
  ```
  (Do not touch other header lines in this task; the version bump is T017.)

- [X] T016 [US3] In `readme.txt` reword the **FAQ** and **Screenshots** sections.
  FAQ (lines 41–46), current:
  ```text
  == Frequently Asked Questions ==
  = What is a custom shipping zone? =
  A custom shipping zone is a geographical area where specific shipping methods and rates apply. This plugin allows you to define these areas based on unique criteria not typically handled by default WooCommerce settings.

  = How do I set up a new shipping zone? =
  Go to WooCommerce → Settings → Custom Shipping Zones and define your zones with custom criteria and shipping methods.
  ```
  →
  ```text
  == Frequently Asked Questions ==
  = What does this plugin actually add? =
  It adds custom states/regions to a country. WooCommerce ships with a fixed list of states per country; this plugin lets you add your own (a district, county, governorate, or any sub-region) so they appear in WooCommerce's state list.

  = How do I use my custom states in a shipping zone? =
  First add your states/regions under WooCommerce → Settings → Custom States / Regions. Then go to WooCommerce → Settings → Shipping and create or edit a zone — your custom states are now selectable, just like WooCommerce's built-in ones.

  = Does this replace WooCommerce shipping zones? =
  No. It does not manage shipping zones for you. It only adds the states/regions that you then use inside WooCommerce's own shipping zone (and tax/address) settings.
  ```
  Screenshots (lines 48–50), current:
  ```text
  == Screenshots ==
  1. Interface for adding a new custom shipping zone.
  2. Managing existing custom zones.
  ```
  →
  ```text
  == Screenshots ==
  1. Interface for adding a new custom state/region.
  2. Managing existing custom states/regions.
  ```

**Checkpoint**: No FAQ or description anywhere advertises a phantom or wrong feature;
WooCommerce-zone references remain only where correct.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Bump the version, finish readme metadata, rebuild the bundle, lint, and run
the acceptance pass.

- [X] T017 [P] Bump the version in `custom-shipping-zones.php`: header `Version:`
  (line 6) `1.0.2` → `1.0.6`, and the constant (line 23)
  `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.2';` → `'1.0.6'`. If Phases 1–3
  (1.0.3/1.0.4/1.0.5) have not shipped yet, still land at the highest current version —
  see plan Release Hygiene. Then run `php -l custom-shipping-zones.php`.

- [X] T018 Update `readme.txt` metadata (same file as T003–T005, T016 — non-overlapping
  lines): set `Tested up to: 6.9` (line 6) → `Tested up to: 7.0`; set
  `Stable tag: 1.0.2` (line 7) → `Stable tag: 1.0.6`; and add a changelog entry directly
  under the `== Changelog ==` line (line 52), above `= 1.0.2 =`:
  ```text
  = 1.0.6 =
  * Repositioned the plugin description and admin labels to accurately reflect what it does: adding custom states/regions to a country for use in WooCommerce shipping zones, taxes, and addresses. No functional change.
  * Removed the "export / import" FAQ entry for a feature that is not yet available.
  ```

- [X] T019 Rebuild the admin bundle (required only because of the `src/App.js` edit in
  T014): from the repo root run `npm run build` and confirm it succeeds and regenerates
  `build/index.js` (and `build/index.asset.php`). Commit the regenerated `build/` files
  together with the source change. Do not edit `build/` by hand.

- [X] T020 Final lint: run `php -l includes/Strings.php`,
  `php -l includes/CustomShippingZones.php`, and `php -l custom-shipping-zones.php` —
  each must print `No syntax errors detected`. Confirm no new `console.log` /
  `var_dump` / `error_log` was introduced (constitution Principle V).

- [X] T021 Run the acceptance pass in `specs/004-readme-repositioning/quickstart.md`
  (sections 2–6): admin tab/labels read in states/regions terms; the export/import FAQ
  is gone; add → save → delete a state still works and the success link points to
  WooCommerce → Settings → Shipping; `readme.txt` short/long/usage/FAQ are accurate with
  `Stable tag 1.0.6`, `Tested up to 7.0`, and a `1.0.6` changelog entry; `README.md`
  mirrors the repositioning; and the WC tab still loads with existing saved states
  intact (backward-compatible — key unchanged).
  **Behavior checks requiring a live WP site deferred to release QA.** Mechanical
  verification passed: lint clean, build succeeds, diff scope correct, no debug output
  introduced, version consistent, tab key unchanged, Shipping substring preserved,
  state_is_in_use unchanged, export/import FAQ removed from App.js and Strings.php.

---

## Dependencies & Execution Order

### Phase dependencies

- **Setup (T001)** → first; no dependencies.
- **Foundational (T002)** → after T001; blocks all user stories (it is the rulebook).
- **US1 (T003–T006)**, **US2 (T007–T010)**, **US3 (T011–T016)** → each depends only on
  T002. The three stories touch mostly different files and are independently testable;
  they may be done in any order (P1 → P2 → P3 recommended).
- **Polish (T017–T021)** → after the story edits. T019 (build) depends on T014.
  T021 (quickstart) runs last.

### Same-file constraints (important for the Kimi model)

Apply edits to a shared file in listed order; they target non-overlapping blocks:

- `readme.txt`: T003 → T004 → T005 (US1) → T016 (US3) → T018 (Polish).
- `includes/Strings.php`: T007 → T008 → T009 (US2) → T011 → T012 → T013 (US3).
- `custom-shipping-zones.php`: T015 (description) → T017 (version).

`src/App.js` (T014) and `includes/CustomShippingZones.php` (T010) are each edited once.

### Parallel opportunities

```text
# Different files, no shared dependency — safe to run together:
T006  README.md mirror            (parallel with the readme.txt / Strings.php edits)
T010  WC tab label in CustomShippingZones.php
T015  Plugin-header Description in custom-shipping-zones.php
T017  Version bump in custom-shipping-zones.php   (after T015, same file)
```

---

## Implementation Strategy

### MVP first (User Story 1)

1. T001 (Setup) → T002 (vocabulary) → **T003–T006 (US1)**. Stop and validate: the public
   readme now describes states/regions accurately. This alone fixes the headline
   misrepresentation — shippable as the MVP of this phase.
2. Add **US2 (T007–T010)** so the admin UI matches the readme.
3. Add **US3 (T011–T016)** to clean up FAQs/phantom claims and the header description.
4. Finish with **Polish (T017–T021)**: version → 1.0.6, readme metadata + changelog,
   `npm run build`, lint, full quickstart.

### Incremental delivery checkpoints

- After US1: WordPress.org/GitHub description is accurate (text only, no build).
- After US2: in-product labels match (still no build).
- After US3 + T019: export/import FAQ removed in the live bundle; header accurate.
- After Polish: 1.0.6 ready to ship.

---

## Task summary

- **Total tasks**: 21 (T001–T021)
- **By phase**: Setup 1 (T001) · Foundational 1 (T002) · US1 4 (T003–T006) ·
  US2 4 (T007–T010) · US3 6 (T011–T016) · Polish 5 (T017–T021)
- **Parallelizable**: T006, T010, T015 (and T017 after T015) — different files.
- **Files touched**: `readme.txt`, `README.md`, `includes/Strings.php`,
  `includes/CustomShippingZones.php` (tab label only), `src/App.js` (one FAQ entry),
  `custom-shipping-zones.php` (description + version), and regenerated `build/**`.
- **Build**: one `npm run build` (T019), required only by the `src/App.js` edit.
- **No automated tests** — behavior verification via `quickstart.md` (Principle VII).

## Notes

- This phase is **text-only**: change no plugin logic, option keys, AJAX handlers,
  nonce/capability checks, or the `woocommerce_states` filter (FR-011, Principle II).
- Keep every array **key** and the text domain `'custom-shipping-zones'` unchanged.
- Keep the literal `WooCommerce → Settings → Shipping` substring in
  `navigate_to_woocommerce_settings` (App.js link depends on it).
- Keep "shipping zone" where it names WooCommerce's own feature (`state_is_in_use`, the
  answer in `faq_cant_delete_description`) — FR-009.
- Do not introduce `console.log` / `var_dump` / `error_log`; do not add dependencies.
- Commit per logical group (e.g. one commit per phase) on `csz-wp7-readiness`.
