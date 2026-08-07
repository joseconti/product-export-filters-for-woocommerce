# API Index — Export Filters for WooCommerce
> One line per public surface. Grep here FIRST; open the full doc only on a hit.

| Surface | Kind | Code file | Doc | Purpose (one line) |
|---------|------|-----------|-----|--------------------|

This plugin exposes no hooks or public functions of its own in v1 — it only
*consumes* WooCommerce's own exporter hooks
(`woocommerce_product_export_row`, `woocommerce_product_export_product_query_args`).
`EFWC_Date_Filter` and its methods are internal implementation, not a public
API surface, and are not indexed here. This file gains its first row the
moment a Later-roadmap filter exposes a hook of its own (per the change map
in `docs/03-technical-plan.md`).
