# Classes — Export Filters for WooCommerce

## `EFWC_Date_Filter`
`includes/class-efwc-date-filter.php`

Static-only utility class (no instantiation) that adds the date filter to
WooCommerce's native product exporter.

### Constants
| Constant | Value | Purpose |
|---|---|---|
| `FIELD_TYPE` | `efwc_date_field` | The `name` of the date-field `<select>` |
| `FIELD_FROM` | `efwc_date_from` | The `name` of the "from" date `<input>` |
| `FIELD_TO` | `efwc_date_to` | The `name` of the "to" date `<input>` |

### Public methods

#### `init()`
Registers every hook this class needs (`woocommerce_product_export_row`,
`admin_enqueue_scripts` at priority 20, `woocommerce_product_export_product_query_args`).
Called once, from `export-filters-for-woocommerce.php`'s `efwc_init()`, only
when WooCommerce is active.

```php
add_action( 'plugins_loaded', function () {
	if ( class_exists( 'WooCommerce' ) ) {
		EFWC_Date_Filter::init();
	}
} );
```

#### `render_rows()`
Echoes the two admin-screen `<tr>` rows. No parameters, no return value —
called via `do_action( 'woocommerce_product_export_row' )`.

#### `enqueue( string $hook )`
Adds the inline show/hide script, only on the `product_page_product_exporter`
screen. `$hook` is the current admin screen hook suffix, passed by
`admin_enqueue_scripts`.

#### `query_args( array $args ): array`
The core logic. Given WooCommerce's export query args, returns them
unmodified unless a valid date filter was submitted, in which case it adds
`date_created` or `date_modified` in WooCommerce's native operator syntax.

```php
// Example: called by WooCommerce core via apply_filters(), not directly —
// shown here for illustration of the contract.
$filtered_args = EFWC_Date_Filter::query_args( array( 'limit' => 50, 'page' => 1 ) );
// $filtered_args might now be:
// array( 'limit' => 50, 'page' => 1, 'date_created' => '2026-03-01...2026-03-31' )
```

#### `clean_date( string $value ): string`
Validates a raw date string against `^\d{4}-\d{2}-\d{2}$` and `checkdate()`.
Returns the value unchanged if valid, or an empty string otherwise. Public
(not private, per D-012) so it can be unit-tested directly.

```php
EFWC_Date_Filter::clean_date( '2026-03-15' ); // '2026-03-15'
EFWC_Date_Filter::clean_date( '2026-02-30' ); // '' (30 February doesn't exist)
EFWC_Date_Filter::clean_date( '01.02.2026' ); // '' (dots not accepted, matches WC's own parser)
```

See `tests/unit/test-clean-date.php` and `tests/unit/test-query-args.php` for
the full, real, passing test suite these examples are drawn from.
