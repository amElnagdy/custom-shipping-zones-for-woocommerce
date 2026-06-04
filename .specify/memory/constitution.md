<!--
SYNC IMPACT REPORT
==================
Version change: (template / unratified) → 1.0.0
Bump rationale: First concrete ratification of the project constitution. All
  placeholder tokens replaced with binding principles. MINOR/PATCH not applicable
  because no prior ratified version existed; initial adoption is 1.0.0.

Modified principles (placeholder → concrete):
  [PRINCIPLE_1_NAME] → I. Backward Compatibility
  [PRINCIPLE_2_NAME] → II. Minimum Viable Surface
  [PRINCIPLE_3_NAME] → III. WooCommerce-First
  [PRINCIPLE_4_NAME] → IV. PHP 7.4 Baseline
  [PRINCIPLE_5_NAME] → V. Zero Debug Output in Production
  (added)           → VI. Security by Default
  (added)           → VII. Test the Contract, Not the Implementation

Added sections:
  - Additional Constraints (technology + compatibility envelope)
  - Development Workflow & Quality Gates (includes branch policy for csz-wp7-readiness)

Removed sections: none (all template slots filled or repurposed)

Templates requiring updates:
  ✅ .specify/templates/plan-template.md  — Constitution Check gate references this file generically; no edit required
  ✅ .specify/templates/spec-template.md  — behavior-based acceptance aligns with Principle VII; no edit required
  ✅ .specify/templates/tasks-template.md — tests OPTIONAL note compatible with Principle VII; no edit required

Follow-up TODOs: none
-->

# Custom Shipping Zones for WooCommerce Constitution

## Core Principles

### I. Backward Compatibility

No change MUST break stored option keys or the `woocommerce_states` filter
signature without shipping a migration path in the same change. Existing
installs that upgrade MUST retain their saved data and continue to function
without manual intervention. Renaming or removing a persisted option key,
or altering the parameters/return shape of a public filter or hook, is a
breaking change and is PROHIBITED unless accompanied by a documented,
automatic migration.

**Rationale**: This is an upgrade of a live plugin with existing users.
Silent data loss or a changed contract on update erodes trust and generates
support load that dwarfs any refactoring benefit.

### II. Minimum Viable Surface

Each phase MUST touch only what that phase requires. Speculative refactoring,
"while we're here" cleanups, and unrelated file churn are PROHIBITED. A change
that is not traceable to the current phase's acceptance criteria does not
belong in the change.

**Rationale**: A narrow diff is reviewable, reversible, and low-risk. Scope
creep in a compatibility-sensitive plugin multiplies the regression surface.

### III. WooCommerce-First

All interaction with WooCommerce data MUST go through official WooCommerce
APIs — `WC()`, `WC_Data` and its subclasses, the `woocommerce_states` filter,
and other documented public hooks. Direct database queries against WooCommerce
or WordPress core tables and reliance on private/internal hooks are PROHIBITED
where an official API exists.

**Rationale**: Official APIs are the only surface WooCommerce commits to keep
stable. Bypassing them couples the plugin to internals that change without
notice and breaks on the next WooCommerce release.

### IV. PHP 7.4 Baseline

Code MUST run on PHP 7.4. Syntax, functions, or language features introduced
after PHP 7.4 are PROHIBITED unless a later phase explicitly raises the
baseline. When the baseline is raised, the plugin's declared `Requires PHP`
header MUST be updated in the same change.

**Rationale**: A meaningful share of WooCommerce stores still run PHP 7.4. A
fatal parse error on activation locks those users out entirely.

### V. Zero Debug Output in Production

`console.log`, `error_log`, `var_dump`, `print_r`-to-output, and equivalent
debug statements MUST NOT reach a production build. Diagnostic output is
permitted only behind an explicit debug guard (e.g. `WP_DEBUG`) or must be
removed before the change is considered complete.

**Rationale**: Stray debug output leaks internal state, pollutes logs, can
break AJAX/JSON responses, and is a public signal of an unfinished release.

### VI. Security by Default

Every AJAX handler MUST verify a valid nonce AND the acting user's capability
before performing any action or returning any privileged data. Handlers that
mutate state MUST reject the request when either check fails. Input MUST be
sanitized on the way in and output escaped on the way out.

**Rationale**: Admin-facing AJAX endpoints are a primary attack surface for
WordPress plugins. Nonce + capability is the non-negotiable baseline that
prevents CSRF and privilege escalation.

### VII. Test the Contract, Not the Implementation

Acceptance criteria MUST be expressed as observable behavior — the admin
screen loads, a state saves, a state deletes — not as assertions about
internal structure, function names, or call sequences. Tests and acceptance
checks MUST remain valid across any refactor that preserves behavior.

**Rationale**: Behavior-based criteria survive internal change, keep the door
open for Principle II's small diffs, and verify what users actually experience.

## Additional Constraints

- **Platform envelope**: Target WordPress + WooCommerce current and the
  immediately prior major release; PHP 7.4 minimum per Principle IV.
- **Data contract**: Stored option keys and the `woocommerce_states` filter
  signature are part of the public contract and are governed by Principle I.
- **No new runtime dependencies** may be introduced without an explicit phase
  decision; prefer WooCommerce and WordPress core APIs (Principle III).

## Development Workflow & Quality Gates

- **Branch policy**: All changes for this upgrade MUST be committed to the
  current branch `csz-wp7-readiness`. Work is not merged elsewhere without an
  explicit decision recorded as a constitution amendment or phase note.
- **Phase discipline**: Work proceeds phase by phase; each phase's diff is
  scoped per Principle II and verified against behavior-based acceptance
  criteria per Principle VII.
- **Pre-merge gate**: Before a change is considered complete it MUST satisfy
  every Core Principle — backward compatibility preserved, surface minimal,
  WooCommerce APIs used, PHP 7.4-clean, debug-output-free, AJAX secured, and
  behavior verified.

## Governance

This constitution supersedes ad-hoc practice for the Custom Shipping Zones for
WooCommerce upgrade. When a principle and convenience conflict, the principle
wins.

- **Amendments**: Any change to a principle, constraint, or workflow rule MUST
  be made by editing this file, bumping the version per the policy below, and
  recording the change in the Sync Impact Report at the top.
- **Versioning policy** (semantic):
  - **MAJOR**: Backward-incompatible governance change — a principle removed or
    materially redefined.
  - **MINOR**: A new principle or section added, or existing guidance materially
    expanded.
  - **PATCH**: Clarifications, wording, and non-semantic refinements.
- **Compliance review**: Every change is reviewed against the Core Principles
  before it is considered complete. Deviations MUST be justified in writing in
  the relevant plan's Complexity Tracking section or rejected.

**Version**: 1.0.0 | **Ratified**: 2026-06-04 | **Last Amended**: 2026-06-04
