# Feature Specification: Critical Bug Fixes & Security Hardening (Phase 2)

**Feature Branch**: `csz-wp7-readiness`

**Created**: 2026-06-04

**Status**: Draft

**Input**: User description: "Fix four critical issues in Custom Shipping Zones for WooCommerce that affect production correctness and security — Phase 2 of the WP 7.0 readiness plan (Critical Bug Fixes & Security Hardening only): remove debug output, validate country codes, validate state codes, and harden all data-writing AJAX handlers with nonce + capability checks."

## User Scenarios & Testing *(mandatory)*

<!--
  Phase 2 scope: correctness and security only. No new end-user features.
  The four user journeys below are independently testable slices; shipping any
  one of them on its own measurably reduces risk for the store.
-->

### User Story 1 - Block unauthorized or forged data changes (Priority: P1)

A developer reviewing the site's security posture wants assurance that no visitor — and no logged-in user without store-management permission — can add or remove custom states/regions by replaying or forging a request. Every action that writes data must prove both that it came from the legitimate admin screen and that the acting user is allowed to manage the store.

**Why this priority**: This is the highest-severity issue. An unprotected write endpoint lets unauthorized users silently corrupt the data that feeds WooCommerce's state/region list, affecting checkout and shipping for real customers. Security gaps must close before any other fix ships.

**Independent Test**: Issue an add/delete request (a) with no/invalid security token, and (b) as a logged-in user lacking the store-management permission. Both must be rejected with an error and must leave stored data unchanged. A legitimate admin performing the same action must still succeed.

**Acceptance Scenarios**:

1. **Given** a request to add or delete a custom state with a missing or invalid security token, **When** the request is processed, **Then** it is rejected with an error response and no data is created, changed, or deleted.
2. **Given** a logged-in user who does not have the store-management permission, **When** they submit an add or delete request with an otherwise valid token, **Then** the request is rejected and no data is changed.
3. **Given** an authenticated store manager on the admin screen with a valid token, **When** they add or delete a custom state, **Then** the action succeeds.

---

### User Story 2 - Reject invalid country codes (Priority: P2)

A store admin entering a custom state/region picks the country it belongs to. If the country identifier does not correspond to a real country recognized by the store, the entry must be refused so that malformed data never reaches WooCommerce's state/region list.

**Why this priority**: Invalid country data silently pollutes the state list WooCommerce relies on, which can break shipping-zone configuration and checkout. It is a data-integrity defect with customer-facing impact, second only to the open security hole.

**Independent Test**: Submit an entry whose country identifier is not a recognized country (e.g. `ZZ`). The system must return a validation error and store nothing. Submitting an entry with a recognized country must succeed.

**Acceptance Scenarios**:

1. **Given** an entry whose country identifier is not a recognized country, **When** it is submitted, **Then** the system returns a validation error and stores nothing.
2. **Given** an entry whose country identifier is a recognized country, **When** it is submitted with otherwise valid data, **Then** it is accepted and saved.

---

### User Story 3 - Reject malformed state/region codes (Priority: P3)

A store admin types a code for the custom state/region. If that code contains characters WooCommerce cannot use, or is unreasonably long, the admin must see a validation error and the entry must be refused.

**Why this priority**: A malformed state code can corrupt the state list and cause display or matching problems in shipping zones. It is a data-integrity issue, but lower impact and frequency than an invalid country, hence P3.

**Independent Test**: Submit a state/region code containing illegal characters, or one longer than the allowed maximum. The system must return a validation error and store nothing. A well-formed code must be accepted.

**Acceptance Scenarios**:

1. **Given** a state/region code containing characters other than letters, digits, and hyphens, **When** it is submitted, **Then** the system returns a validation error and stores nothing.
2. **Given** a state/region code longer than the allowed maximum length, **When** it is submitted, **Then** the system returns a validation error and stores nothing.
3. **Given** a well-formed state/region code (letters, digits, hyphens; within the length limit), **When** it is submitted with otherwise valid data, **Then** it is accepted and saved.

---

### User Story 4 - No debug output in production (Priority: P4)

A developer auditing the site opens the browser console on the Custom Shipping Zones admin screen and expects to see no diagnostic output from the plugin. The plugin must not leak its internal state to anyone viewing the page.

**Why this priority**: Debug output is a hygiene and professionalism issue rather than a data or security risk, so it is lowest priority — but it is trivially verifiable and must not reach a production build.

**Independent Test**: Open the admin screen with the browser console visible and exercise the normal add/delete flows. No diagnostic/debug messages originating from the plugin should appear.

**Acceptance Scenarios**:

1. **Given** the Custom Shipping Zones admin screen is open with the browser console visible, **When** the page loads and the admin adds or deletes a state, **Then** no plugin-originated debug output appears in the console.

---

### Edge Cases

- **Missing input**: A submission with an empty country or empty state/region code is rejected with a validation error.
- **Whitespace / casing**: Leading/trailing whitespace around a code is trimmed before validation; a code that is only whitespace is treated as empty and rejected.
- **Boundary length**: A state/region code at exactly the maximum allowed length is accepted; one character over the maximum is rejected.
- **Valid country, invalid state (and vice-versa)**: When one field is valid and the other is not, the whole submission is rejected and nothing is stored.
- **Expired/stale security token**: A request whose security token has expired is treated the same as an invalid token and rejected.
- **Authorization vs. authentication**: A logged-in but under-privileged user is rejected just as an anonymous request is; the response must not reveal whether data exists.
- **No partial writes**: If any validation or authorization check fails, no data is created, modified, or deleted (all-or-nothing).
- **Existing stored data**: Previously saved valid states/regions are unaffected by the new validation rules.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST reject any request that adds or deletes a custom state/region unless it carries a valid, unexpired security token tied to the admin screen.
- **FR-002**: The system MUST reject any request that adds or deletes a custom state/region unless the acting user holds the store-management permission.
- **FR-003**: The system MUST validate a submitted country identifier against the set of countries recognized by the underlying commerce platform, and reject any identifier not in that set.
- **FR-004**: The system MUST validate a submitted state/region code so that it contains only letters, digits, and hyphens, and does not exceed the maximum allowed length; any code violating these rules MUST be rejected.
- **FR-005**: When any validation or authorization check fails, the system MUST return a clear error response and MUST NOT create, modify, or delete any stored data.
- **FR-006**: Server-side validation and authorization MUST be authoritative; any client-side checks are a convenience for the admin and MUST NOT be relied upon as the security or integrity boundary.
- **FR-007**: The system MUST surface a human-readable validation/permission error to the admin when a submission is rejected.
- **FR-008**: The plugin MUST NOT emit diagnostic or debug output (to the browser console or server logs) in a production build.
- **FR-009**: An authorized admin MUST be able to add a valid custom state/region end-to-end, with no regression in existing behavior.
- **FR-010**: An authorized admin MUST be able to delete a valid custom state/region end-to-end, with no regression in existing behavior.
- **FR-011**: The change MUST NOT alter or lose any existing valid stored states/regions.

### Key Entities *(include if feature involves data)*

- **Custom State/Region**: A user-defined sub-region of a country, represented by a code and a display name, that is injected into the commerce platform's recognized state/region list so it can be targeted by shipping rules. Subject to the code-format and length rules in FR-004.
- **Country**: The parent geographic entity a custom state/region belongs to, identified by a country code. The set of valid countries is owned by the underlying commerce platform (FR-003).
- **Data-modifying request**: An admin-initiated action that adds or deletes a Custom State/Region. Every such request is the subject of the authorization and token rules (FR-001, FR-002).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of add/delete requests that lack a valid security token are rejected with no change to stored data.
- **SC-002**: 100% of add/delete requests from users without store-management permission are rejected with no change to stored data.
- **SC-003**: 100% of submissions with an unrecognized country identifier are rejected and persist no data.
- **SC-004**: 100% of submissions with a state/region code containing illegal characters or exceeding the maximum length are rejected and persist no data.
- **SC-005**: Zero plugin-originated debug messages appear in the browser console on the admin screen during normal use.
- **SC-006**: An authorized admin can add and then delete a valid custom state/region successfully on the first attempt (no functional regression).
- **SC-007**: No previously stored valid state/region is altered or lost after the change is applied.

## Assumptions

- The authoritative list of valid country identifiers is the country list provided by the underlying commerce platform (WooCommerce); the plugin does not maintain its own country list.
- "Store-management permission" maps to WooCommerce's standard store-management capability (`manage_woocommerce`).
- The maximum state/region code length is 10 characters, and the allowed character set is letters, digits, and hyphens, per the WP 7.0 readiness plan.
- "Production build" means the distributed plugin package delivered to end users; debug statements are acceptable only in local development, never in the shipped build.
- This phase introduces no new end-user features, no new stored options or schema, and no new third-party dependencies — it only fixes correctness and security defects in existing flows.
- Existing add and delete flows keep their current user-visible behavior except for the addition of validation/permission error feedback.
- Duplicate-code handling is out of scope for this phase unless an existing duplicate-prevention behavior already exists, in which case it is preserved.
