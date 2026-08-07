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
- Assistant config: none yet — not asked (revisit at Phase 2 close)
- Models: n/a — no per-role model map set up yet
- Keel baseline: v5.12.0
- Website intent: no (D-006)
- Client budget: no
- User guide: TBD (Phase 6)
- Docs theme: n/a until Phase 6
- Test-first policy: pure-logic (D-009, default accepted)
- Durability: git remote origin https://github.com/joseconti/product-export-filters-for-woocommerce.git (verified present)
- Autonomy: automatic / issues: after-sprint / Issue sweep interval: 24h / Issue capture: off
- Branches: develop (active, created from unborn main this session) — nothing yet awaiting merge to main
- Notify: Claude app push notification (PushNotification tool) — re-probed every session
- Chaining: start (requested by user, D-007) — resolves to its full behavior once the Phase 5 scaffold creates the single-lane lock + scripts/keel-continue + scripts/keel-handoff-verify; until then every session-end still writes and shows the continuation prompt as usual

## Phase status
| Phase | Status | Key artifacts |
|-------|--------|---------------|
| 1 Discovery | done | docs/00-competitive-landscape.md, docs/01-discovery.md, docs/estimate.md (v1 preliminary), docs/token-ledger.md, docs/keel-conformance.md |
| 2 Functional spec | done | docs/02-functional-spec.md, docs/03-technical-plan.md, docs/threat-model.md, docs/flows/date-filter-export.md, docs/rubrics/hooks-and-extensibility.md, docs/estimate.md (v2 firm) |
| 3 Design handoff | n/a — no UI design needed (D-006) | — |
| 4 Faithful build | n/a — no UI design needed (D-006) | — |
| 5 Development | pending | — |
| 6 Documentation | pending | — |
| 7 Release | pending | — |
| 8 Website | n/a — no website intent (D-006) | — |

## Current position
- Phase: 5 — Development. Not yet started (Phases 3–4 skipped per D-006).
- Next action: Open Phase 5 (`references/phase-5-development.md`): scaffold the plugin (bootstrap file, `.wp-env.json`, Playwright config, `scripts/keel-verify` / `scripts/keel-doctor` / `scripts/keel-handoff-verify` / `scripts/keel-continue`, single-lane lock — required before `Chaining: start` can actually fire), then Sprint 1: implement `EFWC_Date_Filter` (test-first for the pure-logic pieces per D-009), unit tests, the Playwright e2e spec, and run the real playground test point.

## Open items
- Unresolved user questions: none outstanding.
- Open Design Requests: none — no design phase for this project (D-006)
- Unverified external steps/assets: none
- Forge issues in progress: none

### Deferred items (consciously postponed work)
- Tag filter, stock-status filter, delimiter choice, never-sold filter, no-SKU filter, price-range filter, product-status filter, featured filter, batch-size control, brand/attribute filters, saved filter profiles — all recorded as the "Later" (v1.1+) roadmap in `docs/01-discovery.md`; review trigger: "revisit when scoping v1.1, after v1 ships"
- Scheduled/remote delivery — deferred as a future premium-tier candidate, not part of the free-tier roadmap; review trigger: "revisit if a premium tier is ever pursued"

Last updated: 2026-08-07 — Phase 2 closed, entering Phase 5 (Phases 3–4 skipped, D-006)
