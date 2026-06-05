---
description: "Task list for Phase 1 — Plugin Metadata & Dependency Declaration"
---

# Tasks: Plugin Metadata & Dependency Declaration (Phase 1)

**Input**: Design documents from `specs/001-headers-metadata/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md
**Branch**: `csz-wp7-readiness` (all commits go here — Constitution Principle VIII)

**Tests**: No automated test tasks are included. The specification did not
request tests, and the project has no PHP test harness. Per Constitution
Principle VII, acceptance is verified by behavior (dependency notice appears,
plugin activates, version is consistent) plus `php -l`. Verification tasks are
in Phase 6.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different file, no dependency on an incomplete task)
- **[Story]**: US1 / US2 / US3 — maps to the user stories in spec.md
- Exact file paths and exact text are given in every editing task

## ⚠️ Scope guardrails (read before editing — Constitution Principle II)

- The ONLY two files you may modify are:
  - `custom-shipping-zones.php` (repo root) — the plugin header comment block and one version constant
  - `readme.txt` (repo root) — the header fields and the changelog
- Do NOT touch `includes/**`, `src/**`, `build/**`, `vendor/**`, or any JS.
- Do NOT add `console.log`, `error_log`, or `var_dump` (Principle V).
- Do NOT run any JS build — there is no JS change in this phase.
- All values are fixed: PHP `7.4`, WP tested `7.0`, WP min `6.0`, WC min `9.0`,
  WC tested `9.7`, release version `1.0.3`.

---

## Phase 1: Setup

**Purpose**: Confirm environment and starting point before editing.

- [X] T001 Confirm the working branch is `csz-wp7-readiness` by running `git rev-parse --abbrev-ref HEAD`; if it is not, stop and switch to it. Do not create a new branch.
- [X] T002 Confirm the PHP CLI is available for linting by running `php -v`; note the version (must be 7.4+). If `php` is unavailable, flag it — task T020 (lint) cannot be completed without it.

**Checkpoint**: On `csz-wp7-readiness`, PHP CLI available.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Capture the exact current state so edits are precise and the final scope check is meaningful.

- [X] T003 Open `custom-shipping-zones.php` and confirm the header block (lines ~3–14) currently contains `* Version: 1.0.2`, `* Domain Path: /languages`, and `* WC tested up to: 9.7`, and that the constant `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.2';` exists (line ~23). These are the anchors used by later tasks.
- [X] T004 Open `readme.txt` and confirm the header (lines 1–9) currently contains `Tested up to: 6.9`, `Stable tag: 1.0.2`, `Requires PHP: 7.4`, and `Requires at least: 6.0`, and that the changelog (line ~52) starts with `= 1.0.2 =`. These are the anchors used by later tasks.

**Checkpoint**: Current values verified — proceed to user stories.

---

## Phase 3: User Story 1 — Warn before installing without WooCommerce (Priority: P1) 🎯 MVP

**Goal**: WordPress warns the admin that WooCommerce is required when it is missing/inactive, so nobody installs a broken plugin.

**Independent Test**: On a site with WooCommerce inactive, the Plugins screen shows a "requires WooCommerce" notice and guards activation; with WooCommerce active, the plugin activates with no warning. (Maps to contract C1.)

### Implementation for User Story 1

- [X] T005 [US1] In `custom-shipping-zones.php`, add the WooCommerce dependency header line. Insert a new line `* Requires Plugins: woocommerce` immediately after the existing line `* Domain Path: /languages` and before `* WC tested up to: 9.7`. Keep the ` * ` comment prefix and existing indentation.

**Checkpoint**: With WooCommerce deactivated, the Plugins screen shows the WooCommerce dependency notice (verified behaviorally in T023).

---

## Phase 4: User Story 2 — Accurate PHP requirement for compatibility checkers (Priority: P2)

**Goal**: The plugin header declares minimum PHP 7.4 (and the WP minimum) so compatibility tooling reads it correctly and consistently with the readme.

**Independent Test**: Reading the minimum PHP from the plugin header and from `readme.txt` both return `7.4`. (Maps to contract C2.)

### Implementation for User Story 2

- [X] T006 [US2] In `custom-shipping-zones.php`, add the line `* Requires PHP: 7.4` in the header block. Insert it immediately after `* Domain Path: /languages` (it may sit just above or below the `Requires Plugins` line added in T005 — order among header fields does not matter). Keep the ` * ` prefix.
- [X] T007 [US2] In `custom-shipping-zones.php`, add the line `* Requires at least: 6.0` in the header block, alongside the other `Requires` lines (after `* Domain Path: /languages`). Keep the ` * ` prefix.
- [X] T008 [US2] In `readme.txt`, confirm `Requires PHP: 7.4` is present and unchanged (line ~4). It already exists — this task only verifies the header value added in T006 matches the readme value (`7.4`). Make no edit if it already reads `Requires PHP: 7.4`.

**Checkpoint**: Plugin header and readme both declare `Requires PHP: 7.4`; header also declares `Requires at least: 6.0`.

---

## Phase 5: User Story 3 — Confidence the plugin is maintained for WP 7.0 (Priority: P3)

**Goal**: Public metadata declares "Tested up to: 7.0", a current WooCommerce support window, and a consistent `1.0.3` release version everywhere.

**Independent Test**: `readme.txt` "Tested up to" reads `7.0`; WC compatibility values reference 9.x; the version reads `1.0.3` in the plugin header, the version constant, and the readme Stable tag. (Maps to contracts C3, C4, C5.)

### Implementation for User Story 3

- [X] T009 [US3] In `custom-shipping-zones.php`, change the header line `* Version: 1.0.2` to `* Version: 1.0.3`.
- [X] T010 [US3] In `custom-shipping-zones.php`, change the constant `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.2';` to `const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.3';` (line ~23).
- [X] T011 [US3] In `custom-shipping-zones.php`, add the line `* WC requires at least: 9.0` in the header block, immediately before the existing `* WC tested up to: 9.7` line. Leave `* WC tested up to: 9.7` unchanged. Keep the ` * ` prefix.
- [X] T012 [P] [US3] In `readme.txt`, change `Tested up to: 6.9` to `Tested up to: 7.0` (line ~6).
- [X] T013 [P] [US3] In `readme.txt`, change `Stable tag: 1.0.2` to `Stable tag: 1.0.3` (line ~7).
- [X] T014 [P] [US3] In `readme.txt`, add a line `WC requires at least: 9.0` and a line `WC tested up to: 9.7` into the header block (immediately after the `Requires at least: 6.0` line, line ~5). These two `WC ...` values MUST match the plugin-header values from T011 and the existing header.
- [X] T015 [US3] In `readme.txt`, add a new changelog entry directly under the `== Changelog ==` heading and above the `= 1.0.2 =` entry (line ~53):
  ```
  = 1.0.3 =
  * Declared WooCommerce as a required plugin dependency.
  * Declared minimum PHP 7.4 and WooCommerce 9.0 in the plugin header.
  * WordPress 7.0 compatibility (Tested up to: 7.0).
  ```

**Checkpoint**: Public metadata reads WP 7.0, WC 9.0–9.7, version 1.0.3 consistently.

---

## Phase 6: Polish & Cross-Cutting Concerns (Verification)

**Purpose**: Prove the contract holds and the scope stayed minimal. These tasks make no source edits except to fix a failure they uncover.

- [X] T016 Verify the final plugin header block in `custom-shipping-zones.php` contains all of: `Version: 1.0.3`, `Requires at least: 6.0`, `Requires PHP: 7.4`, `Requires Plugins: woocommerce`, `WC requires at least: 9.0`, `WC tested up to: 9.7`. (Contract C1–C5.)
- [X] T017 Verify version consistency: `Version: 1.0.3` (header), `ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.3'` (constant), and `Stable tag: 1.0.3` (readme) all agree. (Contract C5 / SC-004.) Run: `Select-String -Path custom-shipping-zones.php,readme.txt -Pattern '1\.0\.3'`.
- [X] T018 Verify PHP/WP/WC value agreement between header and readme: `Requires PHP` = `7.4` in both; `WC requires at least` = `9.0` in both; `WC tested up to` = `9.7` in both. (Contracts C2, C4.)
- [X] T019 Verify `readme.txt` reads `Tested up to: 7.0` and `Requires at least: 6.0`, and that `6.0` ≤ `7.0`. (Contract C3.)
- [X] T020 Run `php -l custom-shipping-zones.php` and confirm output is `No syntax errors detected`. (Contract C6 / FR-008.)
- [X] T021 Run `git diff --name-only` and confirm the ONLY plugin files changed are `custom-shipping-zones.php` and `readme.txt` (the `specs/` planning docs may also appear and are acceptable; no file under `includes/`, `src/`, `build/`, or `vendor/` may appear). (Contract C6 / FR-009 / SC-006.)
- [X] T022 Confirm no `console.log`, `error_log`, or `var_dump` was introduced by this phase (the diff should contain none). (Constitution Principle V.)
- [X] T023 Behavior check (manual, if a WP 7.0 + WC 9.x test site is available): deactivate WooCommerce → the Plugins screen shows the "requires WooCommerce" notice and guards activation; reactivate WooCommerce → notice clears and the plugin activates cleanly. (Contract C1 / SC-001.) **Deferred to release QA** — no local test site available.
- [X] T024 Behavior check (manual, if a test site is available): with the plugin active, add a custom state/region (it saves) and delete it (it is removed) — confirming no functional regression. (Contract C6 / SC-005.) **Deferred to release QA** — no local test site available.

---

## Dependencies & Execution Order

### Phase dependencies

- **Setup (T001–T002)** → no dependencies; do first.
- **Foundational (T003–T004)** → after Setup; confirms anchors for all edits.
- **User Stories (T005–T015)** → after Foundational. All three stories are
  independently testable, but they edit two shared files, so the editing tasks
  are sequenced (not run blindly in parallel) to avoid overlapping edits — see
  the file-coupling note below.
- **Polish (T016–T024)** → after all editing tasks.

### File-coupling note (important for an automated implementer)

- T005, T006, T007, T009, T011 all edit the **header comment block of the same
  file** (`custom-shipping-zones.php`). Apply them **one at a time, in T-order**.
  Each uses a distinct, non-overlapping anchor line, so they will not collide,
  but they are NOT `[P]` with each other.
- T010 edits the version **constant** (a different region of the same file) — do
  it after the header edits to keep the file edits orderly.
- T012, T013, T014, T015 edit `readme.txt`. They are marked `[P]` because they
  are in a **different file** from the plugin PHP and can be done while the PHP
  edits happen — but among themselves, apply them in T-order since they share
  `readme.txt`.

### Story independence

- **US1 (P1)** — dependency notice — testable on its own (T023).
- **US2 (P2)** — PHP declaration — testable on its own (read header + readme).
- **US3 (P3)** — WP/WC/version metadata — testable on its own (read readme + header).

---

## Parallel Execution Example

Within User Story 3, the readme edits (different file from the in-progress PHP
edits) can proceed alongside the header work:

```text
# After T009–T011 (plugin header) are queued, the readme edits can run as a group:
Task T012: change "Tested up to: 6.9" → "Tested up to: 7.0" in readme.txt
Task T013: change "Stable tag: 1.0.2" → "Stable tag: 1.0.3" in readme.txt
Task T014: add "WC requires at least: 9.0" + "WC tested up to: 9.7" in readme.txt
Task T015: add the "= 1.0.3 =" changelog entry in readme.txt
# (Apply T012–T015 in order — they share readme.txt.)
```

---

## Implementation Strategy

### MVP First (User Story 1 only)

1. Phase 1 (Setup) → Phase 2 (Foundational) → Phase 3 (US1: add `Requires Plugins: woocommerce`).
2. Verify T020 (`php -l`) and T023 (dependency notice).
3. This alone delivers the headline value: admins are protected from installing without WooCommerce.

### Incremental delivery

1. Add US2 (PHP/WP minimums in header) → verify header/readme agreement.
2. Add US3 (WP 7.0 tested-up-to, WC window, version 1.0.3 everywhere) → verify consistency.
3. Run all Phase 6 verification tasks before considering the phase done.

### Definition of done

- T016–T022 pass mechanically.
- T020 (`php -l`) reports no syntax errors.
- T021 confirms only `custom-shipping-zones.php` and `readme.txt` changed.
- T023–T024 pass on a test site, or are explicitly deferred to release QA.

---

## Notes

- All values are fixed constants from research.md — do not invent or bump them.
- `Tested up to` is a `readme.txt` field only; do NOT add it to the plugin header.
- Keep the ` * ` comment prefix and alignment consistent with surrounding header lines.
- Commit to `csz-wp7-readiness` only.
