# Examples — Export Filters for WooCommerce

Real scenarios, matching the acceptance criteria in
`docs/02-functional-spec.md` and verified live in `docs/05-test-points.md`.

## Export everything created on a single day
Filter by date: **Date created**. From: `2026-03-15`. To: `2026-03-15`.
→ Only products created on March 15, 2026 export. (AC-03)

## Export everything modified since a date, open-ended
Filter by date: **Date last modified**. From: `2026-06-01`. To: *(empty)*.
→ Every product modified on or after June 1, 2026 exports, with no upper
bound. (AC-04)

## Export everything up to a date
Filter by date: **Date created**. From: *(empty)*. To: `2026-01-31`.
→ Every product created on or before January 31, 2026 exports. (AC-05)

## Dates entered backwards
Filter by date: **Date created**. From: `2026-03-31`. To: `2026-03-01`.
→ The plugin swaps them automatically — you still get the March 1–31 range,
never a broken/empty export. (AC-06)

## Combined with WooCommerce's own category filter
Select a category in WooCommerce's native "Categories" field AND a date
range here. → Both filters apply together. One WooCommerce-native caveat to
know: if the export also resolves product **variations** through category
selection, those variations bypass every export filter (date included) —
this is WooCommerce's own documented behavior
(`docs/spec-references/filtros-exportador-woocommerce.md` §4.2), not something this plugin
can change. (AC-10)

## Large catalogs (multiple export batches)
Nothing to do differently — the date filter is carried in the export form
itself, which WooCommerce resends unchanged on every AJAX batch, so it stays
active from the first row to the last regardless of catalog size. Verified
with a 60-product seeded catalog forcing more than one batch. (AC-08)
