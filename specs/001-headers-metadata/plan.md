# Implementation Plan: Plugin Metadata & Dependency Declaration (Phase 1)

**Branch**: `csz-wp7-readiness` | **Date**: 2026-06-04 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/001-headers-metadata/spec.md`

## Summary

Phase 1 is a pure metadata change. Make the plugin formally declare its
dependencies and compatibility so WordPress.org and site admins have accurate
information before WP 7.0 ships: declare WooCommerce as a required plugin,
declare PHP 7.4 minimum, declare WordPress "Tested up to: 7.0", declare the
WooCommerce compatibility range (9.x), and bump the release to `1.0.3`
consistently across the plugin header and the public readme. No functional code,
options, filters, hooks, UI behavior, or assets change.

## Technical Context

**Language/Version**: PHP 7.4 (baseline per constitution); metadata is read by
WordPress core and WooCommerce, no runtime logic added.

**Primary Dependencies**: WordPress 6.0+ (tested to 7.0), WooCommerce 9.x
(declared as a required plugin dependency).

**Storage**: N/A — no options, schema, or persisted data touched.

**Testing**: `php -l` on the changed PHP file; manual behavior verification
(plugin activates cleanly with WooCommerce active; dependency notice shows when
WooCommerce is inactive/missing). Aligns with constitution Principle VII.

**Target Platform**: WordPress plugin running on PHP 7.4+, WP 6.0–7.0,
WooCommerce 9.x.

**Project Type**: WordPress/WooCommerce plugin (single project, PHP + a built JS
admin bundle — the JS bundle is **not** touched this phase).

**Performance Goals**: N/A — no runtime path changes.

**Constraints**: Additive metadata only; declarations must not break activation
or display on older WordPress that does not understand the dependency header.
Version string must be identical everywhere it appears.

**Scale/Scope**: Two files. Roughly 6 header lines added/changed in the plugin
file, one version constant, and ~5 readme fields plus one changelog entry.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| # | Principle | Assessment |
|---|-----------|------------|
| I | Backward Compatibility | **PASS** — No stored option keys or `woocommerce_states` filter signature touched. Adding `Requires Plugins: woocommerce` formalizes an already-real requirement; installs that run WooCommerce (the only working configuration) are unaffected. No migration needed. |
| II | Minimum Viable Surface | **PASS** — Exactly two files (`custom-shipping-zones.php`, `readme.txt`). No refactoring. |
| III | WooCommerce-First | **PASS** — Uses the WordPress-canonical `Requires Plugins` dependency mechanism and standard WC header fields; no DB queries, no private hooks. |
| IV | PHP 7.4 Baseline | **PASS** — Only a version-string constant changes; no new syntax/functions. |
| V | Zero Debug Output | **PASS** — No `console.log`/`error_log`/`var_dump` added. |
| VI | Security by Default | **PASS (N/A)** — No AJAX handlers touched. |
| VII | Test the Contract | **PASS** — Acceptance is behavior-based: dependency notice appears, plugin activates, version is consistent, `php -l` passes. |
| VIII | Branch Policy | **PASS** — All work on `csz-wp7-readiness`. |

**Result**: All gates pass. No violations → Complexity Tracking left empty.

## Project Structure

### Documentation (this feature)

```text
specs/001-headers-metadata/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   └── metadata-contract.md
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created here)
```

### Source Code (repository root)

```text
custom-shipping-zones.php   # Main plugin file — header comment block + version constant
readme.txt                  # WordPress.org distribution metadata + changelog
```

Files explicitly NOT touched this phase: `includes/**` (PHP logic), `src/**`
(React/JS source), `build/**` (compiled assets), `vendor/**`.

**Structure Decision**: Existing flat WordPress-plugin layout — the main plugin
file at the repo root carries the canonical header; `readme.txt` carries the
WordPress.org distribution metadata. Phase 1 edits only these two files; no new
directories or build steps are introduced.

## Concrete Changes (reference for /speckit-tasks)

### `custom-shipping-zones.php` — header block (lines ~3–14) and version constant (line ~23)

Add/adjust these recognized WordPress + WooCommerce header fields:

| Field | Current | Target | Notes |
|-------|---------|--------|-------|
| `Version` | `1.0.2` | `1.0.3` | Must match readme Stable tag |
| `Requires at least` | *(absent)* | `6.0` | WP minimum; matches readme |
| `Requires PHP` | *(absent)* | `7.4` | Matches readme |
| `Requires Plugins` | *(absent)* | `woocommerce` | WP 6.5+ dependency header → drives "WooCommerce required" notice |
| `WC requires at least` | *(absent)* | `9.0` | WooCommerce minimum (9.x series) |
| `WC tested up to` | `9.7` | `9.7` | Keep — already a 9.x value |

Also update the version constant: `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION` →
`'1.0.3'` (line ~23). This is the only PHP statement that changes.

> **Note**: `Tested up to` is **not** a recognized *plugin-file* header (WordPress
> reads it only from `readme.txt`). So the WP 7.0 "Tested up to" value lives in
> `readme.txt`, not the plugin header — this corrects a conflation in the
> source plan's acceptance list while satisfying its intent (FR-003).

### `readme.txt` — header fields (lines 1–9) and changelog (line ~53)

| Field | Current | Target |
|-------|---------|--------|
| `Tested up to` | `6.9` | `7.0` |
| `Stable tag` | `1.0.2` | `1.0.3` |
| `Requires at least` | `6.0` | `6.0` (keep) |
| `Requires PHP` | `7.4` | `7.4` (keep) |
| `WC requires at least` | *(absent)* | `9.0` (add) |
| `WC tested up to` | *(absent)* | `9.7` (add) |

Add a changelog entry:

```text
= 1.0.3 =
* Declared WooCommerce as a required plugin dependency.
* Declared minimum PHP 7.4 and WooCommerce 9.0 in the plugin header.
* WordPress 7.0 compatibility (Tested up to: 7.0).
```

## Complexity Tracking

> No constitution violations — section intentionally empty.
