# Quickstart: Verify Phase 4 (Readme, Messaging & Repositioning)

Behavior-based verification (constitution Principle VII). Phase 4 changes copy only;
the goal is to confirm the message is accurate **and** that nothing functional moved.

## 0. Prerequisites

- WordPress (≤ 7.0) + WooCommerce 9.x with the plugin active.
- Node installed for the one rebuild (`npm install` already run).

## 1. Static checks (must pass)

```bash
php -l custom-shipping-zones.php
php -l includes/Strings.php
npm run build   # required only because src/App.js changed
```

- `php -l` → "No syntax errors detected" for each file.
- `npm run build` → completes; `build/index.js` and `build/index.asset.php` regenerated.

## 2. Admin screen — terminology & FAQ

Go to **WooCommerce → Settings** and open the plugin tab.

- [ ] The settings tab reads in **states/regions** terms (not "Custom Shipping Zones").
- [ ] The "Your existing …" section header reads in **states/regions** terms.
- [ ] The FAQ accordion has **no** "How do I export / import …" entry.
- [ ] No surfaced label calls the plugin's own item a "shipping zone".
- [ ] Any guidance still references **WooCommerce → Settings → Shipping** as the place
      to use the produced states.

## 3. Behavior unchanged (regression guard)

- [ ] Select a country, add a state/region, click **Save** → success result appears.
- [ ] The success screen's link still points to **WooCommerce → Settings → Shipping**
      and the reload button works.
- [ ] The newly added state/region appears in WooCommerce's state list for that country
      (e.g. when editing a WooCommerce shipping zone or an address state field).
- [ ] Delete the state/region → it is removed; a state still in use is still blocked
      with the existing "used in a shipping zone" message (this WooCommerce-zone wording
      is intentionally retained).

## 4. readme.txt — public description & metadata

Open `readme.txt` and read top to bottom.

- [ ] Short description says the plugin **adds custom states/regions to a country**;
      it does **not** claim it creates/manages WooCommerce shipping zones.
- [ ] Long description / Features make only true claims.
- [ ] Usage/Installation describe defining states/regions, then using them under
      WooCommerce → Settings → Shipping.
- [ ] FAQ entries are accurate; none advertise export/import.
- [ ] `Stable tag: 1.0.6`, `Tested up to: 7.0`, and a `= 1.0.6 =` changelog entry are
      present and consistent with the plugin header `Version` / version constant.

## 5. GitHub mirror

- [ ] `readme.md` (and `README.md` if distinct) carry the same repositioning.

## 6. Backward-compatibility spot check

- [ ] The WooCommerce tab still loads (the array **key** `custom_shipping_zones` and
      the `$_GET['tab']` guard are unchanged).
- [ ] Existing saved states/regions still display and still surface in WooCommerce —
      no data was touched.

## Success = all boxes checked

Matches Success Criteria SC-001…SC-007: message is accurate, no "zones" mislabeling of
the plugin's items, no export/import advertised, metadata consistent at 1.0.6, and zero
functional change.
