# Contract: Plugin Metadata Declaration

The "interface" this phase exposes is the metadata WordPress and WooCommerce
read about the plugin. The contract below is **behavior-based** (Constitution
Principle VII): each clause is verifiable from the admin UI or a metadata
reader, without asserting how the header lines are formatted internally.

## C1. WooCommerce dependency contract

- **Given** WooCommerce is **not active** (missing or deactivated),
  **when** an admin views this plugin on the Plugins screen,
  **then** WordPress shows a notice that the required plugin **WooCommerce** is
  not active, and does not present this plugin as ready to use.
- **Given** WooCommerce is **active**,
  **when** an admin activates this plugin,
  **then** activation succeeds with **no** dependency warning.

*Maps to*: FR-001, SC-001, User Story 1.

## C2. PHP requirement contract

- **When** any metadata reader (plugin header or readme) is queried for the
  minimum PHP version, **then** it returns exactly `7.4`, and the two sources
  **agree**.

*Maps to*: FR-002, SC-002, User Story 2.

## C3. WordPress compatibility contract

- **When** the public readme is queried for "Tested up to", **then** it returns
  `7.0`.
- **When** queried for "Requires at least", **then** it returns a supported WP
  version (`6.0`) that is **≤** "Tested up to".

*Maps to*: FR-003, FR-006, SC-003, User Story 3.

## C4. WooCommerce compatibility contract

- **When** the plugin header and readme are queried for WooCommerce
  compatibility, **then** both declare `WC requires at least: 9.0` and
  `WC tested up to: 9.7` (the 9.x stable series), and the two sources **agree**.

*Maps to*: FR-004, SC-003.

## C5. Version consistency contract

- **When** the release version is read from the plugin header `Version`, the
  `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION` constant, and the readme `Stable tag`,
  **then** all three return the identical value `1.0.3` — zero disagreements.
- The readme changelog **contains** a `= 1.0.3 =` entry describing the change.

*Maps to*: FR-005, SC-004.

## C6. No-regression contract

- **When** the plugin is loaded/activated on PHP 7.4 + WP 7.0 + WooCommerce 9.x,
  **then** there are no fatal errors, and the pre-existing behavior (adding and
  deleting a custom state) works exactly as before.
- **When** the changed PHP file is linted (`php -l`), **then** it reports no
  syntax errors.
- **No** file other than `custom-shipping-zones.php` and `readme.txt` is
  modified by this phase.

*Maps to*: FR-007, FR-008, FR-009, SC-005, SC-006.

## Verification matrix

| Contract | How to verify (behavior-based) |
|----------|-------------------------------|
| C1 | Deactivate WooCommerce → view Plugins screen → expect "requires WooCommerce" notice; reactivate → activate cleanly |
| C2 | Read minimum PHP from plugin header and from readme → both `7.4` |
| C3 | Read readme "Tested up to" (`7.0`) and "Requires at least" (`6.0`) |
| C4 | Read WC fields from header and readme → both `9.0` / `9.7` |
| C5 | Diff the three version locations → all `1.0.3`; grep changelog for `1.0.3` |
| C6 | `php -l custom-shipping-zones.php`; add+delete a custom state; `git diff --name-only` shows only the two files |
