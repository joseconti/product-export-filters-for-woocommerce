# Decisions — Export Filters for WooCommerce

> Append-only. A session NEVER re-opens a decision recorded here on its own initiative;
> only the user reverses a decision (append the reversal as a new entry).

## D-001 — Project type and scope entry point
- Date / phase: 2026-08-07 / Phase 1
- Decision: The project is a WordPress plugin / WooCommerce extension that adds filtering options to WooCommerce's native product CSV exporter via hooks, starting with a date filter (created / modified, range with same-day support). The WooCommerce importer is explicitly out of scope — only the exporter is extended.
- Why: The user already scoped and technically verified this against the WooCommerce 11.0.0 source (see `docs/filtros-exportador-woocommerce.md`), including the exact extension points, traps, and a working reference implementation.
- Alternatives rejected: Also touching the importer — rejected because the user stated it is not needed.
- Supersedes: none

## D-002 — Durability
- Date / phase: 2026-08-07 / Phase 1 (session setup)
- Decision: The project already lives in a Git repository with a remote (`origin` → `https://github.com/joseconti/product-export-filters-for-woocommerce.git`). Durability is satisfied without further action.
- Why: SKILL.md "Work never lives only on this machine" — checked mechanically before creating any file.
- Alternatives rejected: none needed.
- Supersedes: none

## D-003 — Autonomy, forge issues, issue capture, notifications
- Date / phase: 2026-08-07 / Phase 1 (session setup batch)
- Decision: Autonomy = automatic (Keel does not ask before routine actions; merges/pushes to `develop` itself; never touches `main` without explicit instruction). Forge issue review = after every sprint close, sweep interval 24h. Issue capture = off (bugs the user reports in conversation are worked directly, not opened as public issues — solo project). Notifications = via the Claude app push notification channel (PushNotification tool) when Keel blocks on the user.
- Why: User's explicit answers to the session-start setup batch.
- Alternatives rejected: manual mode (more dialogs, more friction); issue capture on (unnecessary for a solo-maintained project pre-launch).
- Supersedes: none

## D-004 — Name, slug, license, repo not renamed
- Date / phase: 2026-08-07 / Phase 1
- Decision: Plugin name "Export Filters for WooCommerce", slug/text-domain
  `export-filters-for-woocommerce`, license GPL-3.0-or-later. The GitHub repository
  keeps its existing name (`product-export-filters-for-woocommerce`) — it is NOT
  renamed, to avoid breaking the existing remote URL and any links to it.
- Why: Confirmed against the actual WooCommerce 11.0.0 source (`WC_Admin_Exporters`
  registers only one native exporter, `product_exporter`) that "Export Filters for
  WooCommerce" is accurate and leaves room to grow if WooCommerce ever adds another
  native exporter. GPL-3.0-or-later is required for wordpress.org distribution.
- Alternatives rejected: "Product Export Filters for WooCommerce" (more explicit
  but narrower); renaming the repo (disruptive, not requested).
- Supersedes: none

## D-005 — v1 scope: dates only, confrontation table applied wholesale
- Date / phase: 2026-08-07 / Phase 1, step 3a
- Decision: v1 = the date filter only (created/modified, from/to range, same-day
  supported). Every other functionality surfaced by the competitive confrontation
  (tags, stock status, delimiter, never-sold, no-SKU, price range, product status,
  featured, batch size, brand/attribute filters, saved profiles) goes to the Later
  (v1.1+) roadmap. Multi-format export, replacing the native exporter, and
  duplicating its meta/custom-field export are ruled out permanently as
  contradicting the project's architecture. Scheduled/remote delivery is reserved
  as a future premium-tier candidate, not part of the free-tier roadmap.
- Why: Matches the user's explicit instruction to start with dates; the assistant's
  recommendation (option 1 of 3) was applied wholesale per the user's choice. See
  the full confrontation table in `docs/01-discovery.md`.
- Alternatives rejected: row-by-row decision (more thorough but slower, user chose
  the recommendation instead); bundling all "almost free" filters into v1 (more
  scope now, not what the user asked for).
- Supersedes: none

## D-006 — No custom design, no project website for v1
- Date / phase: 2026-08-07 / Phase 1, steps 7–9
- Decision: The plugin's entire UI is two `<tr>` rows inside WooCommerce's native
  `Products > Export` admin screen, using WP admin's own styling. No custom visual
  design system is needed; Phases 3–4 (design handoff, faithful build) are skipped.
  No dedicated project website for v1 — distribution is via the wordpress.org
  plugin page.
- Why: User confirmed no custom design is needed beyond the native admin screens.
- Alternatives rejected: none proposed.
- Supersedes: none

## D-007 — Chaining: start (requested), gated until the Phase 5 scaffold
- Date / phase: 2026-08-07 / Phase 1, step 10
- Decision: `Chaining: start` recorded on the project card per the user's explicit
  choice, with its warning shown (CLI-only, unsupervised). It resolves in practice
  once the Phase 5 scaffold creates the single-lane lock and
  `scripts/keel-continue` / `scripts/keel-handoff-verify` — until then, every
  session-end still writes and shows the continuation prompt as usual.
- Why: User's explicit answer; macOS is verified and `claude` CLI is confirmed on
  PATH (`/Users/joseconti/.local/bin/claude`).
- Alternatives rejected: `prefill` (the otherwise-recommended default absent an
  explicit `start` request).
- Supersedes: none

## D-008 — Reference artifact: port the drafted date-filter class
- Date / phase: 2026-08-07 / Phase 2, step 1
- Decision: `docs/filtros-exportador-woocommerce.md` §6 (the `JC_Product_Export_Date_Filter`
  class) is used as a code-to-port reference artifact for Sprint 1, renamed to
  this project's `EFWC_` convention. Registered in `docs/02-functional-spec.md`
  under "Reference artifacts."
- Why: it is a working, WC-11.0.0-source-verified implementation the user
  already wrote and tested against the real traps (meta_query clobbering,
  variations bypass, date regex limits) — re-specifying it from scratch in
  prose would throw away that verification work for no benefit.
- Alternatives rejected: writing the class fresh from the functional spec alone
  (slower, and would re-introduce risk of missing a trap already solved).
- Supersedes: none

## D-009 — Test-first policy: pure-logic (default accepted)
- Date / phase: 2026-08-07 / Phase 2, step 4e
- Decision: `Test-first policy: pure-logic`. `clean_date()` and the date-range
  args-building logic (pure functions of their inputs) get their unit test
  written and seen failing before the implementation; the Playwright e2e suite
  is written alongside the implementation as usual. Applied as Keel's default
  since the project proceeded autonomously per the user's standing autonomy
  setting — recorded as "default accepted," not a re-litigated choice.
- Why: this is the value with net-negative cost per Keel's own guidance, and
  fits a project whose only real "pure logic" is exactly the date-validation
  and range-building code — the highest-value place to write the test first.
- Alternatives rejected: `pure-logic + acceptance` (more upfront cost, not
  clearly warranted for a single-filter v1); `none` (would leave the one piece
  of real logic in this project untested-first, exactly where it matters most).
- Supersedes: none

## D-010 — No front-end build/minify pipeline for v1
- Date / phase: 2026-08-07 / Phase 2, step 4
- Decision: the plugin ships no standalone `.js`/`.css` file for v1 — its only
  client-side code is a short string passed to `wp_add_inline_script()`. Keel's
  source-first/minified-pair contract is therefore not triggered; it applies
  automatically the moment a Later-roadmap filter needs real client-side logic
  large enough to warrant its own file.
- Why: matches the actual shape of the code (mirrors
  `docs/filtros-exportador-woocommerce.md` §6's `enqueue()` method).
- Alternatives rejected: none — this is a factual statement about the code's
  shape, not a discretionary choice.
- Supersedes: none

## D-011 — Rubric accepted: hooks & extensibility shape
- Date / phase: 2026-08-07 / Phase 2, step 6a
- Decision: accepted Keel's recommended rubric pass for a plugin. Rubric
  written to `docs/rubrics/hooks-and-extensibility.md` (5 criteria on
  additive/non-destructive filter behavior, prefix discipline, hook
  documentation timing, closed value sets, and not boxing in the Later
  roadmap) and scored against the Phase 2 spec — all criteria passed, no spec
  changes required.
- Why: applied the recommended default ("recommended: yes for a plugin ... the
  way other people hook into it can't be changed without breaking their sites")
  since the session is proceeding autonomously per the user's standing
  autonomy setting.
- Alternatives rejected: declining the rubric pass (would have skipped a cheap,
  relevant check for a hook-based plugin explicitly designed to grow).
- Supersedes: none

## D-012 — `clean_date()` made public for direct unit testing
- Date / phase: 2026-08-07 / Phase 5, Sprint 1
- Decision: `EFWC_Date_Filter::clean_date()` is `public static`, not `private`
  as it was written in the D-008 reference. Every other method/property keeps
  the reference's shape.
- Why: pure-logic unit tests (D-009) need to call it directly; a private
  method would force testing it only indirectly through `query_args()`,
  losing the precise per-input assertions in `tests/unit/test-clean-date.php`
  (including the SQL-injection-shaped-input case from the threat model).
- Alternatives rejected: keeping it private and testing only through
  `query_args()` (weaker test granularity); a `@internal`-tagged public method
  is the standard, low-risk way to make a pure helper testable.
- Supersedes: none

## D-013 — Test-first policy gap acknowledged for Sprint 1's ported code
- Date / phase: 2026-08-07 / Phase 5, Sprint 1 close
- Decision: recorded honestly (per SKILL.md "declared is not delivered") that
  Sprint 1's unit tests were NOT observed failing before the implementation,
  contrary to the letter of D-009 (`Test-first policy: pure-logic`) — the
  already-verified reference class (D-008) was ported and its tests authored
  in the same pass. `docs/05-test-points.md` documents this explicitly rather
  than marking the row as compliant. The rule is reaffirmed for every
  Later-roadmap filter that is NOT a straight port: its unit test must be
  written and observed failing before its implementation, no exception.
- Why: this project's process integrity depends on never claiming a check
  passed that did not actually run as specified.
- Alternatives rejected: silently marking the row as "Red first: observed"
  (would misrepresent what actually happened).
- Supersedes: none

## D-014 — `scripts/keel-continue`'s real macOS launch was not fired this session
- Date / phase: 2026-08-07 / Phase 5, Sprint 1 close
- Decision: `scripts/keel-continue` was built and verified end-to-end on its
  refuse-to-fire path (stale/dirty hand-off → prints the prompt, opens
  nothing) but its actual `osascript`-driven Terminal launch (the `start`
  action) was never triggered live. It remains untested in the one way that
  matters most — actually opening a window.
- Why: firing it for real, right now, would open an unsupervised Claude Code
  session in a new Terminal window mid-session, with nobody positioned to
  notice if it launched wrong (per SKILL.md's own residual-risk note on
  `start`). That is exactly the kind of action this project's session
  chooses to do deliberately, not as a side effect of building the launcher.
- Alternatives rejected: firing it anyway "to prove it works" — would have
  produced exactly the failure mode `references/project-state.md` warns
  about (a second live session with no one watching for cross-talk).
- Supersedes: none. Per SKILL.md "An unexercised mechanism is not a blocked
  one" — this is a fact about the mechanism's history, not a veto; the first
  real sprint close that actually chains is its own evidence.

## D-015 — End-user `guide/` declined for v1
- Date / phase: 2026-08-07 / Phase 6, Documentation
- Decision: the full HTML `guide/` artifact (`references/guide-theme.md`'s
  canonical documentation theme, vendored + brand layer + dev portal) is
  **declined for v1**. `readme.txt` (wordpress.org's own user-facing surface)
  plus `docs/usage/` (installation, configuration, getting-started, examples
  — all task-first, plain-language) already give complete coverage for a
  single-feature plugin, and this session has no way to authentically vendor
  the canonical theme's external release assets without fabricating them.
- Why: `docs/usage/` and `readme.txt` already satisfy the guide's own
  coverage bar (every v1 feature and every setting has its section) for a
  product this size; building a parallel HTML guide on an unvendored/
  improvised theme would violate "never invent" worse than declining. The
  decision is explicitly reversible — a future session can vendor the real
  theme and build `guide/` properly the moment its source is available.
- Alternatives rejected: building `guide/` on improvised, non-canonical
  HTML/CSS (the one path the reference explicitly forbids: "never on
  improvised per-project HTML/CSS").
- Supersedes: none
