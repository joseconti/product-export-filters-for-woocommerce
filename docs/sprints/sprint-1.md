# Sprint 1 — Date filter for the native product exporter

- Scope: scaffold the plugin (bootstrap, wp-env playground, PHPUnit +
  Playwright + phpcs tooling) and implement the v1 date filter
  (`EFWC_Date_Filter`, ported from the verified reference per D-008).
- Acceptance: AC-01 through AC-13 from `docs/02-functional-spec.md` all
  covered by driven tests and passing; `scripts/keel-verify` passes;
  `.pot`/`.po`/`.mo` generate correctly.
- Status: **in progress** — implementation, unit tests, e2e tests, phpcs, and
  i18n are all done and verified live (see `docs/05-test-points.md`); the
  scaffold's remaining meta-tooling (`keel-doctor`, `keel-handoff-verify`,
  `keel-continue`, single-lane lock) is not yet built, so the sprint is not
  formally closed.
- Slices:
  | Slice | Status | Test point result | Notes |
  |---|---|---|---|
  | Plugin bootstrap + `EFWC_Date_Filter` class | done | php -l clean; phpcs clean (1 documented ignore) | Ported from `docs/filtros-exportador-woocommerce.md` §6 (D-008), renamed to `EFWC_` |
  | Unit tests (`clean_date`, `query_args`) | done | 17/17 pass, real WP core test suite | Test-first gap acknowledged (D-013) — ported code, not new logic |
  | wp-env playground + seed script | done | Boots; both plugins report `active`; 60 products seeded | L-001, L-002 fixed during verification |
  | Playwright e2e suite + axe accessibility | done | 5/5 pass headless, trace+video recorded | L-003 fixed during verification (axe scoping) |
  | i18n (`.pot`/`.po`/es_ES `.mo`) | done | `wp i18n make-pot` + `msgfmt` both verified | 11 msgids, all translated |
  | `scripts/keel-verify` | done | passes | Covers [E] paths, php -l, phpcs, version touchpoints, .gitignore hygiene |
  | `scripts/keel-doctor` / `keel-handoff-verify` / `keel-continue` / single-lane lock | **not started** | — | Needed before `Chaining: start` (D-007) can actually fire |
- Close-out: not yet closed — see "Current position" in `docs/PROGRESS.md`
  for what remains before this sprint formally closes.
