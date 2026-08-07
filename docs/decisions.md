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
