# Hooks & extension points — Export Filters for WooCommerce

## Hooks this plugin exposes
None in v1. This plugin only *consumes* WooCommerce's own exporter hooks (see
below); it introduces no `apply_filters()`/`do_action()` of its own. The
moment a Later-roadmap filter needs one (e.g. to let a third party contribute
its own date-adjacent filter), it will be documented here with its exact
signature, the slice that introduced it, and a runnable example, per the
Phase 5 density rule.

## WooCommerce hooks this plugin consumes
| Hook | Type | Fired by | What this plugin does with it |
|---|---|---|---|
| `woocommerce_product_export_row` | action | WooCommerce core, `html-admin-page-product-export.php`, inside `<tbody>` | `EFWC_Date_Filter::render_rows()` echoes two `<tr>` rows: the date-field selector and the (initially hidden) date-range inputs |
| `admin_enqueue_scripts` | action | WordPress core, on every admin screen | `EFWC_Date_Filter::enqueue()`, registered at **priority 20** (WooCommerce registers its own `wc-product-export` handle at priority 10, so this always runs after it exists), adds an inline script via `wp_add_inline_script()` that shows/hides the range row on the selector's `change` event |
| `woocommerce_product_export_product_query_args` | filter | WooCommerce core, `class-wc-product-csv-exporter.php`, building the `wc_get_products()` args | `EFWC_Date_Filter::query_args( $args )` — see below |

### `woocommerce_product_export_product_query_args` — what this plugin adds
- **Only writes** `$args['date_created']` or `$args['date_modified']` — never
  reads or overwrites a pre-existing key (see `docs/threat-model.md`,
  "Defended").
- Guarded by `wp_doing_ajax()` and a valid `wc-product-export` nonce; returns
  `$args` unmodified on anything else (a non-AJAX context, a missing/invalid
  nonce, no date field selected).
- Value format matches WooCommerce's own `parse_date_for_wp_query()`
  convention exactly: `YYYY-MM-DD...YYYY-MM-DD` (range), `>=YYYY-MM-DD`
  (open-ended from), or `<=YYYY-MM-DD` (open-ended to).

## Extending this plugin from another plugin/theme
Because `EFWC_Date_Filter` writes to the same `woocommerce_product_export_product_query_args`
filter, another plugin can safely add its own condition to the same `$args`
array from a *later* priority — as long as it also only ever writes keys it
owns, following the same additive discipline this plugin follows itself
(`docs/rubrics/hooks-and-extensibility.md`, criterion 1).

```php
// Runs after this plugin (default priority 10) if registered at priority 20.
add_filter( 'woocommerce_product_export_product_query_args', function ( $args ) {
	// Add your own condition here — never overwrite $args['date_created']
	// or $args['date_modified'] if this plugin may have set them.
	return $args;
}, 20 );
```
