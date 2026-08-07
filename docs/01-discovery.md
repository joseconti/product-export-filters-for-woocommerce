# Discovery — Export Filters for WooCommerce

## Problem & outcome
WooCommerce's native product CSV exporter (`Products > Export`) has no filtering
options beyond category, product type, and explicit product IDs. Store owners who
need to export "products modified last week" or "products created in March" have
no way to do it from the admin — they either export everything and filter in a
spreadsheet, or ask a developer for a one-off PHP snippet (see the cited support
thread below). The outcome this project must deliver: let a store owner filter the
native product export by date (created or modified, with a from/to range,
including a single day) directly from the existing `Products > Export` screen,
without installing a separate export tool.

## Competitive landscape & opportunity
- Scan status: done
- Source: see `docs/00-competitive-landscape.md` for per-competitor inventory,
  unified feature list, and external-demand list.
- **Niche verdict:** no competitor extends WooCommerce's native exporter via
  hooks — every plugin found ships its own parallel exporter (WP All Export,
  Store Exporter, WPFactory Export WooCommerce, WebToffee, Smart Manager,
  Product Import Export for WooCommerce, Advanced Order Export). This project's
  positioning — additive, hook-based, doesn't replace the screen users already
  know — has zero direct competition; the trade-off is a narrower addressable
  market, since anyone who needs serious export flexibility already reaches for
  one of those heavier tools.
- Table-stakes features **within the native-exporter-extension niche**: none —
  the niche has no existing entrants to set a baseline.
- Table-stakes features **in the adjacent full-exporter category** (for context,
  not automatically adopted): date filter, category/tag filter, stock-status
  filter, product-type filter, price-range filter, custom-field export.
- Differentiator candidates (grounded in external-demand list):
  - Adding a created-date column/filter to the *native* exporter — the exact,
    cited, unmet request.
  - Staying free and single-purpose against reviewed "pay to filter" and
    "overpriced, constant upsell" fatigue with the market leader.
  - A "never sold" filter (`total_sales = 0`) — zero competitors found offering
    this; it is the user's own idea, not externally cited, but it costs nothing
    to add later (native `wc_get_products()` query var) and no one else has it.
- AI / MCP / agentic layer proposals: none proposed. A CSV date-filter admin
  screen has no natural fit for an AI/MCP layer — forced filler, dropped.

## Project type
- Primary: WordPress plugin / WooCommerce extension. Secondary: none.
- Security profile loaded: `references/security/wordpress.md`

## Feature list (v1, agreed with the user)
| Feature | What it does | Users | Priority | Why in v1 | Constraint |
|---|---|---|---|---|---|
| Date filter (created / modified) | Selector: All dates / Date created / Date last modified | Store owners exporting product catalogs | Must | Differentiator — direct cited demand; user's original request | Must not clobber `meta_query` (WooCommerce trap 4.1); native exporter behavior only, no separate export screen |
| Date range (from/to, same-day allowed) | Two date inputs shown when a date field is chosen; both inclusive; same date in both = single day | Same | Must | Core of the date filter; user's explicit requirement | Regex in `parse_date_for_wp_query()` only accepts `YYYY-MM-DD`; inverted from/to auto-sorted instead of returning zero rows |

## Competitive confrontation (step 3a)
- Decision mode chosen by the user: assistant's recommendation applied wholesale
  (option 1 of 3 offered)

| # | Functionality | Who has it | Demand evidence | In v1? | Est. cost (AI h + dev h, rough) | Recommendation | DECISION |
|---|---|---|---|---|---|---|---|
| 1 | Date filter (created/modified, range, same-day) | WP All Export, Store Exporter, WebToffee, Smart Manager (partial) | Direct cited unmet request (native exporter) | Yes | 3–5 AI h + 1–2 dev h (code already drafted and verified against WC 11.0.0 source) | Include — this is v1 | **v1** |
| 2 | Tag filter | WP All Export, Import/Export Suite, Store Exporter, Smart Manager, WPFactory, WebToffee | None found specifically; ubiquitous across the adjacent category | No | 1–2 AI h + 0.5 dev h (native `tag` query var) | Include soon — cheapest parity gap, and the user's own notes flag it as "the most conspicuous absence: there's category but no tag" | **Later — v1.1** |
| 3 | Stock status filter | WP All Export, Import/Export Suite, Store Exporter, Smart Manager, WebToffee | None found specifically | No | 1–2 AI h + 0.5 dev h (native `stock_status` query var) | Include soon — cheap, common | **Later — v1.1** |
| 4 | Price range filter | WP All Export, Import/Export Suite, Smart Manager, WPFactory, WebToffee | None found | No | 2–3 AI h + 1 dev h (native `price`/`regular_price`/`sale_price`, same operator pattern as dates) | Later — needs its own UI (min/max), no cited demand | **Later** |
| 5 | Product status filter (draft/scheduled/etc. selectable) | Import/Export Suite, Store Exporter, Smart Manager, WebToffee | None found | No | 2–3 AI h + 1 dev h (native exporter currently hardcodes the status array) | Later — real but not urgent | **Later** |
| 6 | Featured-product filter | Store Exporter, WebToffee | None found | No | 1 AI h + 0.5 dev h (native `featured` query var) | Later — cheap, low priority | **Later** |
| 7 | SKU filter (LIKE) / no-SKU audit | WP All Export, Smart Manager, WPFactory, WebToffee | None found externally; user's own idea | No | 1–2 AI h + 0.5 dev h | Later | **Later** |
| 8 | "Never sold" filter (`total_sales = 0`) | None found in any competitor | None found externally; user's own idea | No | 1 AI h + 0.5 dev h | Later — genuine differentiator once added, since literally no competitor offers it | **Later** |
| 9 | Delimiter choice (`,` vs `;`) | Not a filter — export mechanic; several competitors handle it as a format option | None found; user's own note that ES/PT Excel users expect `;` | No | 1 AI h + 0.5 dev h (single `woocommerce_product_export_delimiter` filter) | Later — small effort, real ES/PT market value | **Later** |
| 10 | Batch size control | Not a filter; export mechanic | None found | No | 1 AI h + 0.5 dev h | Later — useful on slow hosting with large catalogs | **Later** |
| 11 | Custom fields / ACF export | WP All Export, Store Exporter, Smart Manager, WPFactory | None found | No | n/a | Not applicable — the native exporter already exports post meta; this project extends filtering, not field export | **Never** (out of architectural scope) |
| 12 | Multiple export formats (Excel/XML) | WP All Export, Store Exporter, Advanced Order Export, WebToffee, Visser Labs | None found | No | n/a | Contradicts the core positioning (extend the native CSV exporter, don't replace it) | **Never** (architecture decision, see D-001) |
| 13 | Scheduled / remote delivery (FTP/SFTP/email) | Store Exporter Deluxe, WPFactory, WebToffee, Advanced Order Export Pro | None found | No | 15–25 AI h + several dev h (Action Scheduler, credentials UI, security surface) | Premium-territory, per the user's own notes | **Never for v1** — candidate for a future premium tier |
| 14 | Brand / attribute (`pa_*`) filters | Not offered by any competitor as a standalone filter (tax_query based) | None found | No | 5–8 AI h + 1–2 dev h (`tax_query`, needs the query_args migration from D-00X below first) | Real differentiator (native taxonomy since WC 9.4, no exporter offers it) but not urgent | **Later** |
| 15 | Saved filter profiles ("columns + filters" reusable) | Not offered as such by any competitor found | None found | No | 10–15 AI h + 2–3 dev h | The user's own note: "what turns a utility into a product" — real, but too big for v1 | **Later — v1.x roadmap, evaluate for premium** |

- Scope impact: v1 stays exactly what the user originally scoped (dates only);
  everything else the confrontation surfaced is deliberately deferred, not
  dropped — visible in the Later list below.
- Honest assessment revisited after the confrontation? No — the confrontation
  did not change v1's scope, so the assessment below stands as originally
  reasoned.

## Scope
- **v1:** date filter (created / modified, from/to range, same-day supported)
  on the native product exporter.
- **Later (v1.1+ roadmap, cheapest first):** tag filter, stock-status filter,
  delimiter choice (`,`/`;`), never-sold filter, no-SKU filter, price-range
  filter, product-status filter, featured filter, batch-size control, brand/
  attribute filters, saved filter profiles.
- **Never (for this project's architecture):** replacing the native exporter
  with a separate screen/engine, multi-format export (Excel/XML/JSON),
  duplicating meta/custom-field export the native exporter already does.
  Scheduled/remote delivery is not ruled out forever but is explicitly
  premium-tier territory, not v1 or the free-tier roadmap.

## Honest assessment
The idea is sound but narrow, and that narrowness is the honest read, not a
weakness to talk around. Every heavier competitor already does date filtering
(and far more) as part of a bigger tool; this project's actual differentiator
is not "we filter by date" but "you don't have to install a 500KB exporter with
five upsell prompts to get one filter WooCommerce should have shipped." That
positioning is real — it is backed by a cited support thread of a user who had
to be handed raw PHP, and by reviews citing fatigue with paywalled filters and
constant upsells on the market leader — but it caps the addressable audience:
serious exporters will keep reaching for WP All Export regardless of what this
plugin does. The plugin's win condition is organic long-tail discovery
("woocommerce export by date", "filter product export free") plus a genuinely
free, always-growing toolset that a heavier competitor's free tier won't match
feature-for-feature (per the cited "free tier gates category filter" and
"useless without paying" reviews).

The v1 scope itself is appropriately tight: it ships exactly the cited gap,
nothing more, and the roadmap (tags, stock status, delimiter, never-sold) is
cheap enough — all native `wc_get_products()` query vars — that v1.1 can follow
quickly without architectural rework, as long as the `date_created`/
`date_modified` → manual `date_query` migration (docs/filtros-exportador-woocommerce.md
§4.1, §8.4) happens at the second filter as the user's own notes already flag.

- Verdict: **proceed**.
- User decision: proceed (verdict was not negative, no override needed).

## WP-CLI export path — checked, confirmed out of scope
WooCommerce also exposes product data through WP-CLI (`wp wc product list
--format=csv`, etc.) — auto-generated from the REST API by
`WC_CLI_REST_Command`/`WC_CLI_Runner`, verified in the WC 11.0.0 source
(`includes/cli/class-wc-cli-runner.php`). Two things confirmed, one by
reading the source and one live in the wp-env playground:

- **It never goes through this plugin's hook.** The CLI path queries via the
  REST API's `WC_REST_Products_Controller` → `WC_REST_CRUD_Controller::prepare_objects_query()`,
  a completely separate code path from `WC_Product_CSV_Exporter` /
  `woocommerce_product_export_product_query_args`. This plugin has zero
  effect there, in either direction.
- **It doesn't need to.** The REST/CLI path already has its own native date
  filtering — `--after`/`--before` (maps to `date_query` on `post_date`) and
  `--modified_after`/`--modified_before` (maps to `post_modified`), built the
  *safe* way (a manually-constructed `date_query` array, never the
  `date_created`/`date_modified` args that clobber `meta_query` — see §4.1).
  Verified live: `wp wc product list --format=count` → 240 products;
  `wp wc product list --after=2026-03-01T00:00:00 --before=2026-03-27T23:59:59 --format=count`
  → 40, correctly narrowed.

**Conclusion:** the date-filtering gap this plugin fixes is specific to the
admin **`Products > Export` screen**. The CLI/REST path never had it. This
confirms D-001's scope boundary (extend the native admin exporter only) was
correctly drawn — there is no matching CLI gap to also close.

## Constraints & non-negotiables
- Extends WooCommerce's native product exporter via hooks (`woocommerce_product_export_row`,
  `woocommerce_product_export_product_query_args`, and related filters listed in
  `docs/filtros-exportador-woocommerce.md` §3). Never replaces it, never
  duplicates its batching, column, or download logic.
- Must not break the existing behavior of `Products > Export` for users who
  don't touch the new filter (all-dates default, zero-impact when unused).
- Known WooCommerce-core traps to respect (verified against WC 11.0.0 source,
  `docs/filtros-exportador-woocommerce.md` §4): setting `date_created`/
  `date_modified` directly wipes the auto-generated `meta_query` (§4.1) —
  single-filter v1 can use it directly, but the architecture decision to
  migrate to manual `date_query` is due the moment a second filter (e.g. tag,
  §"Later") is added, not before. Variations bypass `query_args` entirely when
  `category`/`include` are set (§4.2) — documented as WooCommerce's own
  behavior, not a defect of this plugin. `orderby` must never be touched
  (§4.4) — batching depends on the fixed `ID ASC` order.
- Compliance/data concerns: none beyond ordinary WordPress data-handling — the
  plugin filters an existing admin-only, capability-gated (`export`) export
  flow; it introduces no new storage of personal data.
- Installed base / upgrade reality: fresh v1, no existing installed base.
- External dependencies with fixed versions: WooCommerce (the native product
  exporter's hooks used here have existed since WC 3.5.0; developed and
  verified against WC 11.0.0). WordPress minimum fixed at 6.4 and WooCommerce
  minimum at 8.0 in `docs/03-technical-plan.md` §Support matrix. If
  WooCommerce is inactive, the plugin fails safe — verified live (AC-13):
  an admin notice, never fatal.
- License: **GPL-3.0-or-later** (required for wordpress.org distribution).

## Installed base / upgrade
- Fresh v1. No migration/backward-compat/uninstall complexity beyond standard
  clean deactivation (no custom tables, no persisted options beyond WordPress
  standard plugin housekeeping if any is added later).

## External dependencies (fixed versions)
| Dependency | Exact version | Source | Behavior if absent/incompatible |
|---|---|---|---|
| WooCommerce | Verified against 11.0.0; hooks used are stable since 3.5.0 | wordpress.org / woocommerce.com | Plugin must detect WooCommerce's absence/inactivity and show an admin notice; must never fatal-error the site |

## Internationalization & output language
- Multi-language: yes.
- Base/output language of the built product: English (fixed rule for
  WordPress/WooCommerce projects).
- Target output locales: en_US (base) + es_ES for v1; more locales can be
  added later via the standard `.pot`/`.po`/`.mo` flow without code changes.
- Mechanism: WordPress text domain + `.pot` generated from the English source
  strings (`export-filters-for-woocommerce` text domain).
- Docs language: English (token economy) — confirmed with the user; the
  conversation itself continues in Spanish.

## Accessibility (non-negotiable — stated up front)
- Target platform(s): Web / WordPress admin (WooCommerce `Products > Export`
  screen).
- Reference loaded: `references/accessibility.md`
- Reference loaded: `references/anti-patterns.md`
- Targeted level: WCAG 2.2 AA floor. The plugin adds native `<select>`,
  `<label>`, and `<input type="date">` elements to an existing WordPress admin
  table row (`<tr>`) — inherits WP admin's own accessible markup patterns;
  every added control gets an explicit `<label for>`, and the show/hide
  behavior of the date-range row is implemented so it never removes the
  fields from the accessibility tree in a way that traps keyboard/screen-reader
  users (verified in Phase 5 with the automated accessibility pass + a manual
  keyboard-only pass).

## Project website intent
- Will there be a project site? No — distribution is via wordpress.org
  (the plugin repository page itself), no separate marketing site for v1.

## Design needed?
- No. The plugin's entire UI is two `<tr>` rows added to WooCommerce's own
  admin export screen, using WordPress admin's native styling. No custom
  visual design is needed; Phases 3–4 are skipped for this project.

## Design system / brand identity
- Status: n/a — no UI needing its own design (uses native WP/WC admin styling
  exclusively).

## Environment & test drivers (step 5a preflight)
- This session can run commands where the repo lives: yes (local Bash on
  macOS, this machine).
- Environment restrictions found: none found.
- `claude` on PATH: yes — `/Users/joseconti/.local/bin/claude` →
  `~/.local/share/claude/versions/2.1.224` (native installer). Verified via
  `command -v claude`.
- Machines in play: same machine for user, repo host, and test runner (local
  macOS development machine; a real WordPress/WooCommerce playground is
  needed to actually exercise the exporter — see Phase 2 §4 for the wp-env
  recipe).
- Present on the test machine: `git`, `gh` (authenticated), `claude` CLI. A
  local WooCommerce checkout already exists under `~/.wp-env/...` from other
  projects, useful as a source reference but not this project's own
  playground (Phase 5 stands up its own wp-env instance).
- Missing or too old: PHP/WP-CLI/Docker (or wp-env's own Node-based runtime)
  presence not yet probed — deferred to the Phase 5 scaffold's `keel-doctor`,
  since Phase 2's technical plan fixes the exact WordPress/WooCommerce version
  matrix first.
- Impossible on this machine: none identified — this is a standard WordPress
  admin-screen plugin, no platform-exclusive surface (no iOS/Windows-native
  UI).
- Screen-stealing verdict per platform: web/WP-admin — headless, does not take
  over the user's screen.
- Licence or privilege consequences flagged to the user: none.

## Preliminary estimate (AI-time based)
- Estimate v1 (preliminary) recorded in `docs/estimate.md`.
- Token ledger created: `docs/token-ledger.md`.
- Client budget: no — solo project, no client to bill.
- Chaining: **start** — requested by the user. Gated on the single-lane lock
  and `scripts/keel-continue`/`scripts/keel-handoff-verify`, both created at
  the Phase 5 scaffold; until then this project behaves as `prefill` in
  practice (the hand-off file is always written and shown either way).
  Platform verified: macOS.

## Open questions for the user
- None outstanding — the repository's existing name
  (`product-export-filters-for-woocommerce`) differs from the chosen plugin
  slug (`export-filters-for-woocommerce`); the repo itself is NOT being
  renamed (that is a disruptive, separate action affecting existing links),
  the plugin's own code/slug/text-domain simply uses the new name. Flagged
  here for visibility, not left silent — see D-004 in `docs/decisions.md`.
