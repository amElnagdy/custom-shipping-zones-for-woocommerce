# Feature Specification: Readme, Messaging & Repositioning

**Feature Branch**: `csz-wp7-readiness`

**Created**: 2026-06-04

**Status**: Draft

**Input**: User description: "Phase 4 — Readme, Messaging & Repositioning. Reposition the public description of Custom Shipping Zones for WooCommerce to accurately reflect what it does: it allows store owners to define custom states/regions within a country so those regions become available when configuring WooCommerce shipping zones."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - A prospective user understands what the plugin actually does (Priority: P1)

A store owner is browsing WordPress.org (or the plugin's readme) looking for a way to add a sub-region — a county, district, governorate, or custom area — to a country so they can charge different shipping for it. They read the plugin's short description and the opening of its long description. From that text alone, before installing anything, they correctly understand that this plugin adds custom **states/regions** to a country, and that those regions then become selectable when they build their normal WooCommerce shipping zones. They do **not** come away believing the plugin replaces or re-implements WooCommerce's shipping-zone manager.

**Why this priority**: The entire phase exists because the current public description is misleading — it claims the plugin lets you "create custom shipping zones beyond the default zones," implying it manages WooCommerce shipping zones directly. In reality it injects custom state/region codes via the `woocommerce_states` filter. A user who installs based on the current promise will look for zone-management features that do not exist, leading to confusion, support load, and bad reviews. Correcting the first impression is the whole value of the phase.

**Independent Test**: Read only the readme's short description and the first paragraph of the long description. Confirm a reader with no prior knowledge can answer "what does this plugin add to my store?" with "custom states/regions inside a country" and not "a shipping-zone builder." No code or admin screen is involved.

**Acceptance Scenarios**:

1. **Given** the published readme, **When** a prospective user reads the short (one-line) description, **Then** it describes adding custom states/regions to a country and does not claim the plugin creates or manages WooCommerce shipping zones directly.
2. **Given** the published readme, **When** a prospective user reads the long description and feature list, **Then** every claim is something the plugin actually does (defining custom states/regions that surface in WooCommerce's state list), with no statement implying direct shipping-zone management.
3. **Given** the readme's usage/installation steps, **When** the user follows them, **Then** the steps describe defining states/regions and then using them inside WooCommerce's own Shipping settings — matching real behavior.

---

### User Story 2 - The admin screen speaks WooCommerce's terminology (Priority: P2)

A store owner who has installed the plugin opens its admin screen. The page heading and the field/section labels use the words "states" and/or "regions" — the same vocabulary WooCommerce uses for the thing this plugin produces — rather than "zones." Because the UI now matches what the plugin actually outputs (entries in WooCommerce's state list), the owner understands that after saving here they go to WooCommerce → Settings → Shipping to use those states in a zone.

**Why this priority**: A user who read an accurate readme but then sees "zones" everywhere in the UI is thrown back into the original confusion. Aligning the admin copy with the readme closes the loop. It ranks below the readme because the public description shapes the install decision first; the in-product copy reinforces it second.

**Independent Test**: Open the plugin's admin screen and read the heading and labels. Confirm the primary noun is "state"/"region," not "zone," for the things the user creates, and that guidance points to WooCommerce's own Shipping settings for actual zone use. No functional behavior is exercised.

**Acceptance Scenarios**:

1. **Given** the plugin admin screen, **When** the user reads the page title/heading, **Then** it refers to custom states/regions rather than "custom shipping zones."
2. **Given** the plugin admin screen, **When** the user reads the section that lists what they have created, **Then** it is labelled in states/regions terminology consistent with the heading.
3. **Given** the plugin admin screen, **When** the user finishes adding a state/region, **Then** any guidance text directs them to WooCommerce's Shipping settings to apply it, accurately describing the hand-off.

---

### User Story 3 - The FAQ and supporting copy contain no false or phantom claims (Priority: P3)

A store owner reads the FAQ and other explanatory copy (in the readme and on the admin screen). Every question and answer is accurate for the shipping version. No entry describes or promises a capability that the plugin does not ship — in particular, nothing in the user-facing copy advertises an export/import capability that is not part of this release. Where a question was written around the old "zones" framing, it is reworded to the states/regions framing or removed.

**Why this priority**: Inaccurate FAQ entries are lower-frequency than the headline description but still erode trust and generate support tickets when a promised feature cannot be found. It is P3 because it polishes an already-corrected message rather than fixing the core misrepresentation.

**Independent Test**: Read every FAQ entry and supporting blurb in the readme and admin screen. Confirm each describes real, shipped behavior and uses states/regions language; confirm no entry advertises export/import as an available feature in this release.

**Acceptance Scenarios**:

1. **Given** the readme FAQ, **When** the user reads each question and answer, **Then** each one accurately describes shipped behavior and uses states/regions terminology rather than the misleading "zones" framing.
2. **Given** any user-facing copy in this release, **When** the user looks for an export/import feature mentioned in that copy, **Then** no such feature is advertised as available (any reference to it is removed or not surfaced).
3. **Given** the readme metadata, **When** the user checks the version, supported-version, and changelog information, **Then** the Stable tag, the "Tested up to" value, and a changelog entry all reflect the new release and its repositioning.

---

### Edge Cases

- **Mixed terminology left behind**: a single overlooked "zone" string in the heading, a label, or a FAQ answer reintroduces the confusion the phase set out to remove; the repositioning is only complete when the user-facing copy is consistent.
- **WooCommerce's own "zones" remain valid**: the word "zone" is still correct when referring to WooCommerce's native shipping-zone feature that the custom states feed into. Copy must distinguish "the states/regions this plugin adds" from "the WooCommerce shipping zones you use them in," rather than scrubbing the word "zone" everywhere.
- **Export/import strings exist in the product but are not part of this release's promise**: user-facing copy must not advertise export/import as an available feature, even though related strings may exist internally; this is a messaging decision, not a code-removal task in this phase.
- **Translatable strings**: any reworded label is a translatable string; changing the source text affects existing translations, which will fall back to the (corrected) source text until re-translated.
- **Version/metadata drift**: if earlier phases have not yet been released, the readme's Stable tag, "Tested up to," and changelog must still be brought to the Phase 4 target version so the published metadata is internally consistent.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The readme's short (one-line) description MUST accurately describe the plugin as adding custom states/regions to a country and MUST NOT claim the plugin creates or manages WooCommerce shipping zones directly.
- **FR-002**: The readme's long description and feature list MUST contain only claims the plugin actually fulfils (defining custom states/regions that appear in WooCommerce's state list) and MUST NOT imply direct shipping-zone management.
- **FR-003**: The readme's usage and installation guidance MUST describe the real flow: define custom states/regions in the plugin, then apply them inside WooCommerce's own Shipping settings.
- **FR-004**: The plugin admin screen's page title/heading MUST use states/regions terminology rather than "custom shipping zones."
- **FR-005**: The plugin admin screen's section and field labels for the items the user creates MUST consistently use states/regions terminology aligned with the heading.
- **FR-006**: User-facing guidance after creating a state/region MUST direct the user to WooCommerce's Shipping settings to apply it, accurately describing the hand-off.
- **FR-007**: Every readme FAQ entry MUST describe shipped behavior accurately and use states/regions terminology; entries framed around the misleading "zones" concept MUST be reworded or removed.
- **FR-008**: No user-facing copy in this release MUST advertise an export/import capability as an available feature; any such reference MUST be removed or left unsurfaced.
- **FR-009**: Copy MUST continue to use the word "zone" only where it correctly refers to WooCommerce's native shipping-zone feature, preserving the distinction between the custom states/regions this plugin adds and the WooCommerce zones they are used in.
- **FR-010**: The readme's Stable tag, "Tested up to" value, and changelog MUST be updated to reflect this release (the Phase 4 target version) and note the repositioning.
- **FR-011**: This phase MUST change user-facing text only; it MUST NOT alter plugin logic, data storage, the `woocommerce_states` behavior, or any functional capability.

### Key Entities *(include if feature involves data)*

- **Public description (readme)**: the short description, long description, feature list, usage/installation steps, FAQ, changelog, and version metadata that prospective and current users read to understand and evaluate the plugin.
- **Admin screen copy**: the heading, section labels, field labels, and guidance strings shown inside the plugin's settings screen — the in-product counterpart to the readme that must tell the same accurate story.
- **Terminology**: the controlled vocabulary distinguishing the "custom states/regions" the plugin adds from the "WooCommerce shipping zones" those states are later used within.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A reader given only the repositioned short description and opening paragraph can correctly state that the plugin adds custom states/regions to a country (not that it manages shipping zones) in 100% of review checks.
- **SC-002**: Zero user-facing statements in the readme or admin screen claim the plugin creates or manages WooCommerce shipping zones directly.
- **SC-003**: 100% of the items a user creates are referred to with states/regions terminology in the admin heading and labels; no remaining label calls them "zones."
- **SC-004**: Zero user-facing copy advertises an export/import feature as available in this release.
- **SC-005**: 100% of readme FAQ entries describe behavior the plugin actually ships.
- **SC-006**: The readme's Stable tag, "Tested up to," and changelog all reflect the Phase 4 target version with a changelog entry describing the repositioning, with no version-metadata inconsistencies.
- **SC-007**: No functional behavior changes — the plugin's states output and admin actions behave identically before and after the phase, confirmed by the same checks passing.

## Assumptions

- **Repositioning, not feature change ("Option A")**: this phase corrects messaging to match current behavior; it does not add, remove, or alter any functional capability. Removing the misleading promise — rather than building a real shipping-zone manager — is the chosen direction.
- **Primary vocabulary**: "states/regions" is the controlled term for what the plugin adds, mirroring WooCommerce's own "states" wording (the `woocommerce_states` list the plugin populates). "Region" is used as the friendlier synonym where "state" reads awkwardly. The word "zone" is retained only for WooCommerce's native shipping zones.
- **Export/import scope**: although export/import strings exist in the product, this phase treats them as not part of the release's public promise and only ensures no user-facing copy advertises them; it does not add or remove the underlying functionality (that would be a functional change, which is out of scope here).
- **Target version**: the Phase 4 target version is 1.0.6, consistent with the upgrade plan's phase sequence; the readme metadata is brought to that version even if earlier phases have not yet been separately released.
- **No build/logic impact**: changes are confined to readme text and admin display strings. Where admin strings are defined as translatable source text, only the source string values change; no behavior, data, or filter contract is affected.
- **Translations**: existing translations are out of scope; reworded strings fall back to the corrected English source until re-translated.
- **Backward compatibility**: per the project constitution, no stored option keys or the `woocommerce_states` filter signature change in this phase.
