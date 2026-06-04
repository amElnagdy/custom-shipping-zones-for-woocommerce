# Phase 1 Data Model: Plugin Metadata & Dependency Declaration

This phase introduces **no runtime data** — no options, no database schema, no
persisted state. The only "entities" are declarative metadata records read by
WordPress core, WooCommerce, and compatibility tooling. They are documented here
because consistency between them is a hard requirement (SC-004).

## Entity: Plugin Header Metadata

**Location**: comment block at the top of `custom-shipping-zones.php`.
**Consumed by**: WordPress core (Plugins screen, dependency resolution),
WooCommerce (compatibility checks), automated compatibility/CI checkers.

| Field | Value (target) | Required by | Validation rule |
|-------|----------------|-------------|-----------------|
| `Plugin Name` | Custom Shipping Zones for WooCommerce | (unchanged) | Non-empty |
| `Version` | `1.0.3` | FR-005 | Semver; MUST equal version constant and readme Stable tag |
| `Requires at least` | `6.0` | FR-006 | Valid WP version ≤ Tested up to |
| `Requires PHP` | `7.4` | FR-002 | Exactly `7.4`; MUST equal readme `Requires PHP` |
| `Requires Plugins` | `woocommerce` | FR-001 | Valid WordPress.org plugin slug |
| `WC requires at least` | `9.0` | FR-004 | 9.x value ≤ WC tested up to |
| `WC tested up to` | `9.7` | FR-004 | 9.x value |

## Entity: Version Constant

**Location**: `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION` in `custom-shipping-zones.php`.
**Consumed by**: the plugin's own asset/versioning logic at runtime.

| Field | Value (target) | Validation rule |
|-------|----------------|-----------------|
| `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION` | `'1.0.3'` | MUST equal header `Version` and readme Stable tag |

## Entity: Readme Distribution Metadata

**Location**: header block + changelog of `readme.txt`.
**Consumed by**: WordPress.org plugin directory, the in-dashboard plugin
details modal, compatibility tooling.

| Field | Value (target) | Required by | Validation rule |
|-------|----------------|-------------|-----------------|
| `Requires PHP` | `7.4` | FR-002 | MUST equal header `Requires PHP` |
| `Requires at least` | `6.0` | FR-006 | MUST equal header `Requires at least` |
| `Tested up to` | `7.0` | FR-003 | Valid WP version ≥ Requires at least |
| `Stable tag` | `1.0.3` | FR-005 | MUST equal header `Version` and version constant |
| `WC requires at least` | `9.0` | FR-004 | MUST equal header value |
| `WC tested up to` | `9.7` | FR-004 | MUST equal header value |
| Changelog `= 1.0.3 =` entry | present | FR-005 | Describes the metadata changes |

## Cross-Entity Consistency Invariants

These invariants are the heart of the phase's contract (SC-004):

1. **Version equality**: header `Version` == `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION`
   == readme `Stable tag` == `1.0.3`.
2. **PHP equality**: header `Requires PHP` == readme `Requires PHP` == `7.4`.
3. **WP minimum equality**: header `Requires at least` == readme
   `Requires at least` == `6.0`.
4. **WC range equality**: header `WC requires at least` == readme
   `WC requires at least` == `9.0`; header `WC tested up to` == readme
   `WC tested up to` == `9.7`.
5. **Ordering**: `Requires at least` (6.0) ≤ `Tested up to` (7.0); `WC requires
   at least` (9.0) ≤ `WC tested up to` (9.7).

## State Transitions

None. Metadata is static declaration; there are no lifecycle states.
