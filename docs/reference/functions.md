# Functions — Export Filters for WooCommerce

Both functions below live in `export-filters-for-woocommerce.php` and are
internal bootstrap glue, not a public API — neither is meant to be called by
another plugin, and neither is indexed in `docs/api/INDEX.md`.

## `efwc_init(): void`
Hooked to `plugins_loaded`. If WooCommerce is active, requires
`includes/class-efwc-date-filter.php` and calls `EFWC_Date_Filter::init()`.
Otherwise registers `efwc_missing_woocommerce_notice()` on `admin_notices`
and does nothing else — no fatal error, no partial initialization.

## `efwc_missing_woocommerce_notice(): void`
Hooked to `admin_notices` only when WooCommerce is missing/inactive. Prints a
warning notice, gated on `current_user_can( 'activate_plugins' )` so it is
only shown to users who could actually act on it.
