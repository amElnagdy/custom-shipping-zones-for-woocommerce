# Quickstart: Verifying Phase 1 (Metadata & Dependency Declaration)

This phase changes only two files: `custom-shipping-zones.php` and `readme.txt`.
Use the steps below to confirm the change is correct and complete. All checks
are behavior-based per the constitution.

## 1. Lint the changed PHP

```powershell
php -l custom-shipping-zones.php
```

Expected: `No syntax errors detected in custom-shipping-zones.php`.

## 2. Confirm version consistency (should all print 1.0.3)

```powershell
Select-String -Path custom-shipping-zones.php -Pattern 'Version:\s*1\.0\.3'
Select-String -Path custom-shipping-zones.php -Pattern "ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION\s*=\s*'1\.0\.3'"
Select-String -Path readme.txt -Pattern 'Stable tag:\s*1\.0\.3'
```

Expected: a match for each of the three.

## 3. Confirm the declared compatibility fields

```powershell
Select-String -Path custom-shipping-zones.php -Pattern 'Requires Plugins:\s*woocommerce'
Select-String -Path custom-shipping-zones.php -Pattern 'Requires PHP:\s*7\.4'
Select-String -Path custom-shipping-zones.php -Pattern 'Requires at least:\s*6\.0'
Select-String -Path custom-shipping-zones.php -Pattern 'WC requires at least:\s*9\.0'
Select-String -Path readme.txt -Pattern 'Tested up to:\s*7\.0'
Select-String -Path readme.txt -Pattern 'WC requires at least:\s*9\.0'
```

Expected: a match for each. The header `Requires PHP` must equal the readme
`Requires PHP` (both `7.4`).

## 4. Confirm scope is contained (only two files changed)

```powershell
git diff --name-only
```

Expected: exactly `custom-shipping-zones.php` and `readme.txt` (plus the
`specs/` planning docs, which are not plugin code).

## 5. Behavior check — WooCommerce dependency notice

1. In a WP 7.0 test site, deactivate WooCommerce.
2. Go to **Plugins** and locate Custom Shipping Zones for WooCommerce.
3. Expect WordPress to show that the required plugin **WooCommerce** is not
   active and to guard activation.
4. Reactivate WooCommerce → the notice clears and the plugin activates cleanly.

## 6. Behavior check — no functional regression

1. With WooCommerce active and the plugin activated, open the plugin's admin
   screen.
2. Add a custom state/region → it saves.
3. Delete that custom state/region → it is removed.

Expected: identical to pre-Phase-1 behavior. No console errors introduced.

---

**Done when**: steps 1–4 pass mechanically and steps 5–6 confirm the dependency
notice works and no behavior regressed.
