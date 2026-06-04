# Feature Specification: Plugin Metadata & Dependency Declaration (Phase 1)

**Feature Branch**: `csz-wp7-readiness`

**Created**: 2026-06-04

**Status**: Draft

**Input**: User description: "Update the Custom Shipping Zones for WooCommerce plugin metadata so it is accurate for WordPress 7.0 — Phase 1 of the WP 7.0 readiness plan (Headers, Metadata & Dependency Declaration only)."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Warn before installing without WooCommerce (Priority: P1)

A site administrator browses **Plugins → Add New**, finds this plugin, and is
about to install or activate it. Because the plugin formally declares that it
depends on WooCommerce, WordPress shows a notice that WooCommerce is required
and is not active — so the admin does not end up with a non-functional plugin.

**Why this priority**: A shipping-states plugin is inert without WooCommerce.
Declaring the dependency is the single highest-value, user-protecting metadata
change and is the headline reason for this phase.

**Independent Test**: On a site without WooCommerce active, view the plugin in
the admin and confirm WordPress surfaces a "requires WooCommerce" dependency
notice and blocks/guards activation. Delivers value entirely on its own.

**Acceptance Scenarios**:

1. **Given** a WordPress site where WooCommerce is installed but not active,
   **When** the admin views this plugin on the Plugins screen, **Then**
   WordPress displays a notice that the required plugin WooCommerce is not
   active.
2. **Given** a WordPress site where WooCommerce is not installed at all,
   **When** the admin views this plugin on the Plugins screen, **Then**
   WordPress indicates WooCommerce is a required, missing dependency.
3. **Given** a WordPress site where WooCommerce is installed and active,
   **When** the admin activates this plugin, **Then** no dependency warning
   appears and activation succeeds as before.

---

### User Story 2 - Accurate PHP requirement for compatibility checkers (Priority: P2)

A developer running the plugin under wp-env, Local, or an automated
compatibility/CI checker needs the plugin's minimum PHP version to be declared
in the canonical place (the plugin header) so tooling reads `7.4` and does not
produce a false negative or treat the requirement as unknown.

**Why this priority**: Tooling and hosts increasingly gate on the declared PHP
requirement. The value is real but secondary to protecting end users from a
missing WooCommerce dependency.

**Independent Test**: Inspect the plugin header and confirm it declares a
minimum PHP of 7.4; run a metadata/compatibility checker and confirm it reads
7.4 rather than reporting the requirement as unspecified.

**Acceptance Scenarios**:

1. **Given** the plugin files, **When** a tool reads the plugin's metadata,
   **Then** the declared minimum PHP version is `7.4`.
2. **Given** the plugin's distribution metadata, **When** a tool reads the
   minimum PHP version from both the plugin header and the public readme,
   **Then** the two values agree (`7.4`).

---

### User Story 3 - Confidence that the plugin is maintained for WP 7.0 (Priority: P3)

A store owner evaluating or already running WordPress 7.0 reads the plugin's
"Tested up to" value and sees `7.0`, giving confidence the plugin is actively
maintained for the current WordPress release. They can likewise see the plugin
is tested against the current stable WooCommerce.

**Why this priority**: Trust/marketing signal. Important for adoption and
perceived maintenance, but does not change runtime behavior, so it ranks below
the functional dependency and PHP declarations.

**Independent Test**: Read the public readme and confirm "Tested up to" reads
`7.0`, "Requires at least" is a supported WordPress version, and the WooCommerce
compatibility values reference the current WooCommerce stable line.

**Acceptance Scenarios**:

1. **Given** the public readme, **When** a store owner reads the compatibility
   section, **Then** "Tested up to" reads `7.0`.
2. **Given** the public readme and plugin metadata, **When** a store owner reads
   the WooCommerce compatibility values, **Then** they reference the current
   WooCommerce stable series (9.x).
3. **Given** the public readme, **When** a store owner reads the version/stable
   information, **Then** it reflects the Phase 1 release (`1.0.3`) consistently.

---

### Edge Cases

- **WooCommerce installed but inactive vs. not installed at all**: both states
  must result in a clear "required dependency" signal; the plugin must not
  silently activate into a broken state.
- **Older WordPress that does not understand the dependency declaration**: the
  declaration must be additive and must not break activation or display on a
  WordPress version that predates the dependency-header feature.
- **Version stated in two places**: the plugin header version and the readme
  stable tag must not disagree after this change.
- **Hosts on exactly PHP 7.4**: the declared minimum must not exclude PHP 7.4
  itself (7.4 is supported, not "above 7.4").

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The plugin MUST formally declare WooCommerce as a required plugin
  dependency such that WordPress warns the admin when WooCommerce is missing or
  inactive before the plugin is used.
- **FR-002**: The plugin MUST declare a minimum supported PHP version of `7.4`
  in its primary metadata so compatibility tooling reads it correctly.
- **FR-003**: The plugin's public distribution metadata MUST declare it is
  tested up to WordPress `7.0`.
- **FR-004**: The plugin MUST declare a minimum required WooCommerce version and
  a "tested up to" WooCommerce version, both referencing the current WooCommerce
  stable series (9.x).
- **FR-005**: The plugin MUST present a consistent release version for this
  phase (`1.0.3`) across all places a version is stated, with no disagreement
  between the plugin header and the public readme.
- **FR-006**: The declared minimum WordPress version ("requires at least") MUST
  be a currently supported WordPress version consistent with being tested up to
  `7.0`.
- **FR-007**: All metadata declarations MUST be additive and MUST NOT alter,
  remove, or break any existing functional behavior of the plugin.
- **FR-008**: The plugin's code MUST remain free of syntax errors after the
  metadata changes (the plugin loads and activates without fatal errors on a
  PHP 7.4 environment).
- **FR-009**: This phase MUST NOT change any functional code paths, options,
  filters, hooks, user-facing UI behavior, or assets beyond metadata/header and
  readme text.

### Key Entities *(include if feature involves data)*

- **Plugin header metadata**: The canonical declaration block read by WordPress
  and tooling — carries plugin version, required PHP, required WordPress,
  required WooCommerce, WooCommerce tested-up-to, and the WooCommerce dependency
  declaration.
- **Public readme metadata**: The distribution-facing compatibility block —
  carries "Requires at least", "Tested up to", "Requires PHP", stable
  tag/version, and WooCommerce compatibility values. Must stay consistent with
  the plugin header.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: On a site without WooCommerce active, 100% of attempts to use the
  plugin surface a clear "WooCommerce required" dependency notice (no silent
  activation into a broken state).
- **SC-002**: A metadata/compatibility checker reads the minimum PHP as exactly
  `7.4` — zero "unknown" or mismatched readings between the plugin header and
  the public readme.
- **SC-003**: The public "Tested up to" value reads `7.0`, and WooCommerce
  compatibility values reference the 9.x stable series.
- **SC-004**: The stated release version is identical (`1.0.3`) in every place a
  version appears — zero disagreements.
- **SC-005**: The plugin loads and activates with no fatal errors on a PHP 7.4 +
  WordPress 7.0 + WooCommerce 9.x environment, and all pre-existing behavior
  (adding and deleting custom states) continues to work unchanged.
- **SC-006**: No functional code, options, filters, hooks, or assets are changed
  by this phase — the only differences are in metadata/header and readme text.

## Assumptions

- The scope of Phase 1 is strictly metadata: the plugin's primary header block
  (in `custom-shipping-zones.php`) and the public `readme.txt`. No other files
  are in scope.
- "Current WooCommerce stable" is the 9.x series, as referenced in the WP 7.0
  readiness plan; the existing header already states "WC tested up to: 9.7".
- The Phase 1 target release version is `1.0.3`, per the readiness plan's
  version bump for this phase.
- WordPress's plugin-dependency declaration mechanism ("requires the WooCommerce
  plugin") is the intended way to express the WooCommerce requirement; on older
  WordPress versions that do not support it, the declaration is simply ignored
  and does not break the plugin.
- A supported "Requires at least" WordPress baseline is acceptable to keep at or
  above the current declared value (`6.0`) as long as it remains consistent with
  being tested up to `7.0`; raising it is out of scope unless required for
  consistency.
- Verification that "the plugin loads without errors" is treated as a
  behavior-level check (activates cleanly, screen loads), consistent with the
  constitution's "test the contract, not the implementation" principle.

## Out of Scope

- Any bug fixes, input validation, AJAX security hardening, performance/caching,
  or admin-copy repositioning — these belong to later phases (2–4).
- Introducing new options, filters, hooks, dependencies, or build steps.
- Any change to JavaScript, React components, or the asset build pipeline.
