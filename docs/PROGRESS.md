# PROGRESS — Export Filters for WooCommerce

> Living state. Read this FIRST in every session. Keep current and compact.

## Project card
- Name / one-line purpose: Export Filters for WooCommerce (slug `export-filters-for-woocommerce`) — adds filtering options (starting with dates) to WooCommerce's native product CSV exporter via hooks.
- Project type: WordPress plugin / WooCommerce extension / secondary: none
- Stack & target platform(s): PHP, WordPress admin (WooCommerce, extends the native exporter via hooks — verified against WC 11.0.0) — full matrix in docs/03-technical-plan.md (Phase 2)
- License: GPL-3.0-or-later (D-004)
- Docs language: English (token economy) — announced to the user; conversation stays in Spanish
- Security profile: references/security/wordpress.md
- Accessibility: WCAG 2.2 AA floor (references/accessibility.md) — WP admin surface, native controls
- i18n: multi — base English, locales en_US + es_ES for v1, mechanism WP text domain (`export-filters-for-woocommerce`) + .pot
- Installed base: fresh v1
- Design system: n/a — native WP/WC admin styling only, no custom design (D-006)
- Keel portability: lock + embedded v5.12.0 (.claude/skills/keel/, .agents/skills/keel/)
- Assistant config: full (tools: claude) — rules + agents + permissions + pre-commit gate + CI (D-018)
- Models: orchestrator=session model (claude-sonnet-5, not set by Keel) / reviewer=claude-sonnet-5 / mechanical=claude-haiku-4-5-20251001 (D-018)
- Keel baseline: v5.12.0
- Website intent: no (D-006)
- Client budget: no
- User guide: declined for v1 (D-015) — readme.txt + docs/usage/ cover it; reversible later
- Docs theme: n/a — guide declined (D-015)
- Docs indexing: n/a — no guide published
- Test-first policy: pure-logic (D-009, default accepted)
- Durability: git remote origin https://github.com/joseconti/product-export-filters-for-woocommerce.git (verified present)
- Autonomy: automatic / issues: after-sprint / Issue sweep interval: 24h / Issue capture: off
- Branches: develop (active, created from unborn main this session) — nothing yet awaiting merge to main
- Notify: Claude app push notification (PushNotification tool) — re-probed every session
- Chaining: start (requested by user, D-007) — the single-lane lock, scripts/keel-continue and scripts/keel-handoff-verify are all built and verified (Phase 5 scaffold complete); the refuse-to-fire path is verified live, the real macOS Terminal-launch has not fired yet (D-014, not a gap — an unexercised mechanism, not a blocked one)

## Phase status
| Phase | Status | Key artifacts |
|-------|--------|---------------|
| 1 Discovery | done | docs/00-competitive-landscape.md, docs/01-discovery.md, docs/estimate.md (v1 preliminary), docs/token-ledger.md, docs/keel-conformance.md |
| 2 Functional spec | done | docs/02-functional-spec.md, docs/03-technical-plan.md, docs/threat-model.md, docs/flows/date-filter-export.md, docs/rubrics/hooks-and-extensibility.md, docs/estimate.md (v2 firm) |
| 3 Design handoff | n/a — no UI design needed (D-006) | — |
| 4 Faithful build | n/a — no UI design needed (D-006) | — |
| 5 Development | done — Sprint 1 closed | export-filters-for-woocommerce.php, includes/class-efwc-date-filter.php, tests/ (17 unit + 5 e2e, all passing), docs/05-test-points.md, docs/playground.md, docs/sprints/sprint-1.md, scripts/keel-verify + keel-doctor + keel-handoff-verify + keel-continue (all passing/verified) |
| 6 Documentation | done | docs/architecture.md, docs/api/README.md + INDEX.md, docs/reference/, docs/usage/, docs/security.md, docs/accessibility.md, README.md, LICENSE; guide/ declined for v1 (D-015) |
| 7 Release | **candidate ready on `develop` — not tagged/merged/published** | docs/07-release.md, docs/threat-model.md (all controls IN PLACE with real evidence), the real distributable ZIP built and installed-tested live |
| 8 Website | n/a — no website intent (D-006) | — |

## Current position
- Phase: 7 — Release. The v1.0.0 candidate is fully prepared and verified on
  `develop`: 17/17 unit tests, 5/5 e2e tests, phpcs clean, i18n verified
  (including a real compiled `.mo` injected into the package — `git archive`
  alone can't include a gitignored generated file), `keel-verify` and
  `keel-doctor --check` both green, the WordPress security/anti-patterns
  self-audit run with real evidence per row, `docs/threat-model.md`'s
  controls all moved to `IN PLACE`, and — the real test that matters most —
  the actual packaged ZIP installed fresh into the wp-env playground and
  smoke-verified working. Full detail in `docs/07-release.md`.
- **A critical incident occurred and was fully recovered**: verifying the
  uninstall lifecycle by running `wp plugin uninstall` against the
  bind-mounted dev plugin directory deleted this repository's real files on
  disk (wp-env mounts `.` directly — L-005). Nothing was lost (everything
  was already pushed to `origin/develop`); the working tree was re-cloned
  and restored, verified identical, and `node_modules`/`vendor` reinstalled.
  The uninstall lifecycle was then re-verified the safe way (read-only
  inspection — the plugin creates zero options/tables/transients/events, so
  there is nothing to clean up).
- Next action: **entirely the user's call, per the unbreakable version and
  git-flow rules — Keel prepares and stops here.** Merging `develop` → `main`
  (this repo has no `main` commit yet — the first release is also the first
  time `main` gets one), tagging `v1.0.0`, and publishing (wordpress.org
  and/or a GitHub Release) all require the user's explicit instruction.

## Open items
- Unresolved user questions: **the release itself** — merge to `main` +
  tag `v1.0.0` + publish, whenever the user is ready. Nothing else is
  blocking.
- Open Design Requests: none — no design phase for this project (D-006)
- Unverified external steps/assets: none
- Forge issues in progress: none

### Deferred items (consciously postponed work)
- Tag filter, stock-status filter, delimiter choice, never-sold filter, no-SKU filter, price-range filter, product-status filter, featured filter, batch-size control, brand/attribute filters, saved filter profiles — all recorded as the "Later" (v1.1+) roadmap in `docs/01-discovery.md`; review trigger: "revisit when scoping v1.1, after v1 ships"
- Scheduled/remote delivery — deferred as a future premium-tier candidate, not part of the free-tier roadmap; review trigger: "revisit if a premium tier is ever pursued"
- Real assistive-technology (screen reader) pass — optional, not gating (native WP admin controls only); review trigger: "before wordpress.org submission, offer to the user"
- `scripts/keel-continue`'s real `start` launch (opening a Terminal window) — implemented, verified on its refuse-to-fire path, but never fired live (D-014); review trigger: "the next sprint close is free to be the first real firing"
- A dedicated permission-denied Playwright test (low-privilege user cannot see the export screen) — currently relies on WooCommerce core's own gating, verified by code reading rather than a driven test of this plugin specifically; review trigger: "v1.1, alongside the next filter"
- End-user `guide/` — declined for v1 (D-015), reversible; review trigger: "if the user later wants the full HTML guide once the canonical theme can be vendored"

Last updated: 2026-08-07 — Phase 7 candidate ready on develop; stopped for the user's explicit release decision, per SKILL.md "Git flow" and the version-change policy
