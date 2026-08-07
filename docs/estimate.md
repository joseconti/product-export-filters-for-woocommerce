# Estimate — Export Filters for WooCommerce

> Internal working estimate. AI-time based: AI session hours + vibe coder hours.
> Never based on traditional human development time.

## Estimate v1 (preliminary, Phase 1 close) — 2026-08-07

### Scope basis
v1 scope from `docs/01-discovery.md`: one filter (date created/modified, from/to
range, same-day supported) added to WooCommerce's native product CSV exporter via
hooks, using the already-drafted and WC-11.0.0-verified reference implementation in
`docs/spec-references/filtros-exportador-woocommerce.md` §6 as the starting point. No design phase
(native admin UI only). Counts: 1 filter, 3 extension points (`woocommerce_product_export_row`,
`admin_enqueue_scripts`, `woocommerce_product_export_product_query_args`), ~10
manual test scenarios (`docs/spec-references/filtros-exportador-woocommerce.md` §9).

### AI working hours (itemized)
| Segment (AI does) | Hours (low–high) | Basis |
|---|---|---|
| Phase 2 — functional spec, technical plan, i18n scaffolding | 1–2 h | Small, single-filter scope; most decisions already closed in Phase 1 |
| Phase 5 — scaffold (plugin bootstrap, playground/wp-env, build script if any) | 1–2 h | Standard WP plugin scaffold + wp-env |
| Phase 5 — slice: date filter implementation (adapt the drafted class, prefix rename, hook wiring) | 1–2 h | Reference code already exists and is verified against WC source |
| Phase 5 — test points (the 10 scenarios in §9, playground-driven) | 2–3 h | Real WooCommerce admin flows, several batch-size and edge-case runs |
| Phase 6 — documentation (hooks reference, API index, user guide) | 1–2 h | Small surface: one class, three hooks |
| Phase 7 — release prep (readme.txt, POT file, packaging, GPL headers) | 1 h | Standard wordpress.org packaging |
Total AI: **7–12 h**

### Vibe coder hours (itemized)
| Segment | What the developer does | Hours (low–high) |
|---|---|---|
| Decisions & review | Answering Keel's batched questions, reviewing the plan, approving the release | 1–2 h |
| Real-environment testing | Trying the exporter in a real WooCommerce store (assistant drives the playground; owner spot-checks on production-like data) | 0.5–1 h |
| wordpress.org submission | Account/SVN setup, listing text, screenshots | 1–2 h (mostly one-time platform overhead, not project-specific) |
Total developer: **2.5–5 h** → plan for ~5 h with margin

### Contingency: +20% → AI 8–14 h, developer 3–6 h with contingency

### Estimated calendar delivery
With the user available in short daily sessions: **1–2 working days** of elapsed
calendar time (the work itself is a few hours; delivery time is dominated by
review/testing loops, not raw AI throughput).

### AI cost
Mode: **subscription** (Claude Code / Claude subscription) — ≈0 marginal cost per
session; no per-token billing applies. If usage moves to API billing at any point,
this section will be recomputed with verified per-model token prices at that time.

### Assumptions & risks
- Assumes the reference implementation in `docs/spec-references/filtros-exportador-woocommerce.md`
  §6 needs only prefix/naming adaptation, not a rewrite — verified true as of WC
  11.0.0; a WooCommerce core change before release would require re-verification.
- Assumes a local wp-env playground can be stood up without licensing/privilege
  friction (no Docker Desktop paywall issue expected on this machine — to confirm
  at the Phase 5 scaffold).
- Does not include the "Later" roadmap items (tags, stock status, delimiter,
  never-sold, etc.) — those get their own estimate increment when scoped.

## Estimate v2 (firm, Phase 2 close) — 2026-08-07

### Scope basis
Firm scope from `docs/02-functional-spec.md` (2 features, 13 acceptance
criteria, 1 flow) and `docs/03-technical-plan.md` (1 PHP class, 1 bootstrap
file, wp-env playground, PHPUnit + Playwright test suites, no build pipeline,
no client budget). No design phase (D-006). Firm figures narrow the v1
preliminary range now that the exact test surface (13 ACs, 2 test frameworks,
1 e2e spec) and the scaffold requirements (wp-env, Playwright browsers) are
fixed.

### AI working hours (itemized)
| Segment (AI does) | Hours (low–high) | Basis |
|---|---|---|
| Phase 5 scaffold (plugin bootstrap, `.wp-env.json`, Playwright config, seed script, `keel-doctor`/`keel-verify`/playground docs) | 1.5–2.5 h | Standard WP-plugin + wp-env + Playwright scaffold, small project |
| Sprint 1 — `EFWC_Date_Filter` implementation (port + rename from the verified reference) | 1–1.5 h | Reference code exists; mostly renaming + file-splitting |
| Sprint 1 — unit tests (`clean_date`, args-building, test-first per D-009) | 1–1.5 h | 13 ACs, but most are covered by a handful of parametrized unit cases |
| Sprint 1 — Playwright e2e spec (AC-01, 02, 08, 09, 10, 12) incl. axe accessibility check | 1.5–2.5 h | Multi-batch scenario (AC-08) needs a real seeded catalog run, the slowest single case |
| Phase 6 — documentation (hooks reference, API index, user guide, readme.txt draft) | 1–1.5 h | Small surface |
| Phase 7 — release prep (readme.txt finalize, .pot/.po, packaging, GPL headers, "Tested up to") | 1 h | Standard |
Total AI: **7–9.5 h** (narrowed from the 7–12 h preliminary range now that the
test surface and scaffold are fixed precisely)

### Vibe coder hours (itemized)
| Segment | What the developer does | Hours (low–high) |
|---|---|---|
| Decisions & review | Reviewing Phase 2 artifacts, approving sprint plans/release | 1–1.5 h |
| Real-environment testing | Spot-checking the assistant-driven playground run on a real-feeling catalog; optional manual screen-reader pass (not gating, per `docs/03-technical-plan.md`) | 0.5–1 h |
| wordpress.org submission | Account/SVN setup, listing text, screenshots (mostly one-time platform overhead) | 1–2 h |
Total developer: **2.5–4.5 h** → plan for ~4.5 h with margin

### Contingency: +20% → AI 8.5–11.5 h, developer 3–5.5 h with contingency

### Estimated calendar delivery
1–2 working days of elapsed calendar time, matching the Phase 1 preliminary
estimate — the firm numbers confirm rather than change that projection.

### AI cost
Mode: subscription — ≈0 marginal cost per session; unchanged from Estimate v1.

### Assumptions & risks
- Same assumptions as v1, now narrower in range because the test suite (13
  fixed ACs) and the scaffold (`wp-env` + Playwright, both standard tooling)
  are fully specified rather than estimated from a rough scope description.
- Client budget: no — `docs/budget.md` is not produced (Phase 1 D-002/card
  `Client budget: no`).
