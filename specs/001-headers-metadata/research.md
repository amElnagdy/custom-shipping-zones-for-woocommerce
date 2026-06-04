# Phase 0 Research: Plugin Metadata & Dependency Declaration

The spec carried **no `[NEEDS CLARIFICATION]` markers** — the readiness plan
supplied concrete target values. Research here confirms the correct WordPress/
WooCommerce metadata conventions so the implementation maps cleanly to real,
recognized header fields.

---

## R1. How to declare a required WooCommerce dependency

**Decision**: Add `Requires Plugins: woocommerce` to the main plugin file
header comment.

**Rationale**: WordPress 6.5+ reads the `Requires Plugins` header (a
comma-separated list of WordPress.org plugin slugs). When a listed plugin is
missing or inactive, WordPress disables the Activate link and shows a dependency
notice — exactly the protective behavior User Story 1 (P1) requires. The slug
for WooCommerce on WordPress.org is `woocommerce`.

**Alternatives considered**:
- *Custom `admin_notices` + bail in code*: more code, duplicates a core feature,
  violates Principle II (minimum surface) and Principle III (WC/WP-first). The
  plugin already guards at runtime; the header is the declarative, tooling-
  visible contract.
- *Doing nothing*: leaves admins able to activate into a broken state. Rejected.

**Backward-compat note**: On WordPress < 6.5 the header is simply ignored — no
breakage. Installs that already run WooCommerce (the only functional setup) are
unaffected, so no migration is required (Principle I).

---

## R2. Where "Tested up to: 7.0" belongs

**Decision**: Set `Tested up to: 7.0` in `readme.txt` only. Do **not** add a
`Tested up to` line to the plugin file header.

**Rationale**: `Tested up to` is a WordPress.org **readme.txt** field. The main
plugin-file header does not recognize it — adding it there has no effect and
would be noise. The recognized plugin-file headers relevant here are
`Requires at least`, `Requires PHP`, and `Requires Plugins`. This corrects a
conflation in the source plan's acceptance list while fully satisfying its
intent (the public "Tested up to" reads 7.0 → FR-003, SC-003).

**Alternatives considered**:
- *Add `Tested up to` to the plugin header too*: rejected — not a real header,
  no benefit.

---

## R3. PHP and WordPress minimums in the plugin header

**Decision**: Add `Requires PHP: 7.4` and `Requires at least: 6.0` to the plugin
header, matching the values already in `readme.txt`.

**Rationale**: `readme.txt` already declares `Requires PHP: 7.4` and
`Requires at least: 6.0`, but the **plugin file header lacks both**. Many
compatibility checkers and hosts read the plugin-file header, not the readme.
Declaring them in both places makes the requirement authoritative and
consistent (FR-002, SC-002). `7.4` means "7.4 or newer" — it does not exclude
7.4 itself (edge case covered).

**Alternatives considered**:
- *Header-only or readme-only*: rejected — tooling reads both; consistency
  (SC-002, SC-004) requires they agree.

---

## R4. WooCommerce compatibility fields and values

**Decision**: Add `WC requires at least: 9.0` to both the plugin header and
readme; keep `WC tested up to: 9.7` (already present in the plugin header) and
mirror it into `readme.txt`.

**Rationale**: The readiness plan targets the current WooCommerce stable 9.x
series. The header already states `WC tested up to: 9.7`; pairing it with
`WC requires at least: 9.0` declares a clear, current 9.x support window
(FR-004, SC-003). These `WC ...` headers are WooCommerce's documented
compatibility convention.

**Alternatives considered**:
- *Bump `WC tested up to` higher than 9.7*: rejected for this phase — we should
  only claim a version actually tested against; 9.7 is the last value the
  project recorded and stays truthful. Bumping can happen in a later phase if a
  newer WC is verified.

---

## R5. Version bump and consistency

**Decision**: Set the release version to `1.0.3` in three places that must
agree: the plugin header `Version`, the `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION`
constant, and the readme `Stable tag`. Add a `= 1.0.3 =` changelog entry.

**Rationale**: The readiness plan assigns `1.0.3` to Phase 1. SC-004 requires
zero version disagreements. The constant is the only PHP statement that changes;
it is a string literal swap, safe under PHP 7.4 (Principle IV).

**Alternatives considered**:
- *Leave the constant at 1.0.2*: rejected — would create an internal version
  mismatch and fail SC-004.

---

## Summary of resolved values

| Item | Value | Files |
|------|-------|-------|
| Required dependency | `Requires Plugins: woocommerce` | plugin header |
| Min PHP | `Requires PHP: 7.4` | plugin header (+ readme already has it) |
| Min WP | `Requires at least: 6.0` | plugin header (+ readme already has it) |
| Tested WP | `Tested up to: 7.0` | readme only |
| Min WC | `WC requires at least: 9.0` | plugin header + readme |
| Tested WC | `WC tested up to: 9.7` | plugin header (keep) + readme (add) |
| Release version | `1.0.3` | plugin header `Version`, version constant, readme `Stable tag`, changelog |

All NEEDS CLARIFICATION resolved (none existed). Ready for Phase 1 design.
