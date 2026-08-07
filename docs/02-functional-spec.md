# Functional Spec — Export Filters for WooCommerce

## Functional requirements

### Feature 1 — Date field selector ("Filter by date")
- **Inputs:** a `<select>` with three options: empty/"All dates" (default),
  `date_created`, `date_modified`. Rendered on `woocommerce_product_export_row`.
- **Processing:** purely a UI trigger client-side (shows/hides the range row via
  inline JS); server-side, its value gates whether the query-args filter changes
  anything at all.
- **Outputs:** when "All dates" — no change to the native export. When a date
  field is chosen — the export's `wc_get_products()` query gains a
  `date_created` or `date_modified` argument (see Feature 2).
- **Preconditions:** WooCommerce active; user has `edit_products` + `export`
  capabilities (native screen's own gate — this plugin adds no new capability
  check, it reuses the screen's existing one).
- **Postconditions:** the exported CSV contains only rows matching the selected
  date scope (or all rows, if "All dates").
- **Error conditions:** none specific to this control — an unrecognized/tampered
  value in `jc_date_field` (outside the three valid options) is treated as "All
  dates" (fails safe, `in_array( ..., true )` check).

### Feature 2 — Date range (From / To)
- **Inputs:** two `<input type="date">` fields, `jc_date_from` and
  `jc_date_to`, shown only when a date field is selected. Both optional
  independently. Same value in both = single day.
- **Processing:** server-side, on `woocommerce_product_export_product_query_args`:
  1. Verify the request is a genuine WooCommerce export AJAX call with a valid
     `wc-product-export` nonce — else no-op (return `$args` unchanged).
  2. Parse `$_POST['form']`; read and validate each date against
     `^\d{4}-\d{2}-\d{2}$` AND `checkdate()` (rejects e.g. 2026-02-30).
  3. If both valid and From > To, swap them (never zero-result the export for
     reversed input).
  4. Build the operator string per WooCommerce's own `parse_date_for_wp_query()`
     convention: `from...to` (both), `>=from` (From only), `<=to` (To only).
  5. Set `$args[$field]` to that string. This is written **only** for a single
     v1 filter — the moment a second filter is added (Later roadmap), this must
     migrate to a manually-built `date_query` array to avoid WooCommerce's
     `meta_query`-clobbering behavior (`docs/spec-references/filtros-exportador-woocommerce.md`
     §4.1) — tracked as a note in the code map, not built now (YAGNI until the
     second filter actually lands).
- **Outputs:** the CSV export is limited to products whose `post_date` (created)
  or `post_modified` (modified) — site-local time, day precision — falls within
  the resolved range, inclusive on both ends.
- **Preconditions:** Feature 1's selector is not "All dates".
- **Postconditions:** every row in the CSV satisfies the date condition; the
  `total_rows` / percentage-complete counter WooCommerce shows during the AJAX
  batch loop still reflects the *filtered* count, because the filter runs before
  the count query, not after (this is why the implementation must never filter
  via `woocommerce_product_export_skip_product_row`, per
  `docs/spec-references/filtros-exportador-woocommerce.md` §3 "Aviso").
- **Error conditions:**
  - Invalid date string (`checkdate()` fails, or doesn't match the regex) →
    silently dropped, treated as empty for that side of the range.
  - Both sides empty despite a field being selected → equivalent to "All dates".
  - Nonce missing/invalid, or the filter fires outside a genuine AJAX export
    context → `$args` returned unchanged (fail-open to native behavior).

## Reference artifacts
- `docs/spec-references/filtros-exportador-woocommerce.md` §6 — kind: **code to port**. The
  `JC_Product_Export_Date_Filter` class is a working, WC-11.0.0-verified
  reference implementation of both features above. What must be matched
  exactly: the hook set used (`woocommerce_product_export_row`,
  `admin_enqueue_scripts` priority 20, `woocommerce_product_export_product_query_args`),
  the nonce/AJAX-context guard, the date validation via `checkdate()`, the
  From/To swap-on-inversion behavior, and the operator-string construction. What
  must deliberately change: the `JC_` prefix and text domain `jc` are renamed to
  this project's convention (`docs/03-technical-plan.md` §Conventions), and the
  class is split across the plugin's real file structure instead of living as a
  single inline snippet. Out of scope: nothing else in that document's §6 is
  ported as-is — §7 ("Ideas de filtros a añadir") is roadmap notes, not a
  reference artifact, and is not ported.
  - Source/author/license: written by the project owner (José Conti) for this
    project; no third-party code, so no license-compatibility question arises.
    Recorded as D-008 in `docs/decisions.md`.

## Data model
None. This feature is stateless: it reads two POST-carried values already
present in the export form's own submission (no new database table, no new
`wp_options` entry, no persisted settings). Nothing to migrate, nothing to clean
up on uninstall beyond WordPress's own plugin deactivation.

## Integrations
None. The plugin only adds hooks consumed by WooCommerce core's own exporter,
running entirely within the same request; there is no external API, no
third-party service, no outbound network call.

## Permissions matrix
| Role / capability | Can see the date filter fields | Can trigger a filtered export |
|---|---|---|
| User with `edit_products` + `export` (native screen's own gate) | Yes | Yes |
| Any other role | No — never reaches the screen (WooCommerce's own capability check on `Products > Export`) | No |

The plugin introduces **no new capability** and performs **no capability check
of its own** — it reuses the screen's existing gate. Its own server-side guard
(nonce + AJAX-context check) is a request-authenticity control, not a
permissions control; see `docs/threat-model.md`.

## Flows index
- `docs/flows/date-filter-export.md`

## Technical plan
See `docs/03-technical-plan.md`.

## Design split
- **Needs design:** none — two `<tr>` rows inside WooCommerce's own admin
  export table, using native WP admin form styling (`<select>`,
  `<input type="date">`, `<label>`, `<p class="description">`) with zero custom
  CSS. Per D-006, Phases 3–4 are skipped entirely.
- **No design:** the entire feature (backend hook logic + the two native-styled
  rows).
- **External manual setup:** none — no OAuth, no hosting panel, no DNS, no
  payment gateway.
- **Foreseen external assets:** none — no icons/images beyond what the
  wordpress.org plugin directory itself requires at release time (banner/icon
  assets for the `.org` listing, handled in Phase 7 packaging, not a design
  deliverable of this spec).
- **Per-screen accessibility requirements:** the one screen touched
  (`Products > Export`) — every added control has an explicit `<label for>`
  bound to its `id`; the date-range row's show/hide never removes the fields
  from the DOM in a way that discards their value or traps focus; the `<select>`
  is operable by keyboard exactly like WooCommerce's own controls on the same
  screen; the `<input type="date">` fields use the browser's native accessible
  date widget (no custom date-picker JS); color is never the only signal (there
  is none needed here — no error/success states beyond the native form's own).
- **Rich references held by the user:** none.
- **Target devices/viewports and exact breakpoints:** none beyond what the WP
  admin itself already handles responsively — this plugin adds table rows to an
  existing responsive WP admin table, no new breakpoint logic.

## Acceptance criteria

| ID | Criterion |
|---|---|
| AC-01 | With "All dates" selected (default), the exported CSV is byte-for-byte identical in row count and content to an export run with WooCommerce unmodified (Feature 1, Branch A). |
| AC-02 | Selecting "Date created" or "Date last modified" reveals the From/To row; leaving it on "All dates" keeps it hidden — verified via keyboard-only interaction (Tab + Enter/Space to change the select), not only mouse click. |
| AC-03 | Same date in From and To exports exactly the products created/modified on that single site-local day (day precision, compared against `post_date`/`post_modified`, not the `_gmt` columns). |
| AC-04 | Only From filled exports every matching product from that date onward, open-ended. |
| AC-05 | Only To filled exports every matching product up to and including that date, open-ended on the left. |
| AC-06 | From filled later than To: the export still returns the correct range (dates auto-swapped), never zero rows. |
| AC-07 | An invalid date (e.g. 30 February, or a malformed value bypassing the native date picker) does not error or fatal the export; that side of the range is treated as empty. |
| AC-08 | The filter persists correctly across every AJAX batch of a multi-batch export (large catalog forcing more than one batch) — verified against a seeded catalog large enough to force ≥2 batches. |
| AC-09 | Products with `future` (scheduled) or `draft` status that fall inside the date range are included, matching the native exporter's own status set. |
| AC-10 | Combining the date filter with WooCommerce's native category selector behaves per the documented variations caveat (`docs/spec-references/filtros-exportador-woocommerce.md` §4.2) — the date filter applies correctly to top-level products; the known variations-bypass-the-filter behavior is WooCommerce's own and is asserted as such (not silently "unverified"), not treated as this plugin's defect. |
| AC-11 | If the export AJAX request lacks a valid `wc-product-export` nonce, or `$_POST['form']` is absent, the query args are returned unmodified (fails open to native, unfiltered behavior) — verified by directly invoking the filter outside its normal request context. |
| AC-12 | Every control added to the screen has an accessible name (`<label for>`), is reachable and operable via keyboard alone, and the show/hide of the range row does not trap focus or silently discard an already-entered value when re-hidden and re-shown. |
| AC-13 | With WooCommerce inactive (or too old to expose the hook the plugin needs), the plugin does nothing harmful — no fatal error, an admin notice is shown instead. |

## Estimate & budget
See `docs/estimate.md` (Estimate v2, firm — appended at the close of this
phase). `Client budget: no` — no `docs/budget.md`.

## Open questions for the user
None.
