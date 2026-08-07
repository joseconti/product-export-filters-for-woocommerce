=== Export Filters for WooCommerce ===
Contributors: joseconti
Tags: woocommerce, export, products, csv, date filter
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
WC requires at least: 8.0
WC tested up to: 11.0
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Adds filtering options to WooCommerce's native product exporter, starting with a date filter.

== Description ==

WooCommerce's built-in product exporter (**Products > Export**) has no way to filter which products get exported beyond category, product type, and explicit product IDs. This plugin adds that — starting with the most requested gap: filtering by date.

= What it adds =

* A "Filter by date" selector: All dates / Date created / Date last modified.
* A date range (From / To) — both inclusive, so the same date in both fields exports a single day.
* Works transparently with WooCommerce's existing batching, columns, category filter, and "export selected products" — nothing about the native exporter is replaced or duplicated.

= How it works =

The plugin extends WooCommerce's native exporter entirely through its own public hooks (`woocommerce_product_export_row`, `woocommerce_product_export_product_query_args`) — it does not ship a separate export screen, and it does not touch the product importer.

= More filters are coming =

Tag, stock status, delimiter choice, a "never sold" filter, and more are on the roadmap — see the plugin's development repository for the full list.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/` or install it through the WordPress admin.
2. Activate it. WooCommerce must already be active — the plugin does nothing (and shows an admin notice) if it is not.
3. Go to **Products > Export** and use the new "Filter by date" field.

== Frequently Asked Questions ==

= Does this replace the WooCommerce product exporter? =

No. It adds two fields to the existing screen. Everything else — column selection, category/type filters, batching, the CSV download — is untouched, native WooCommerce behavior.

= Does this touch the product importer? =

No, only the exporter.

= What happens if I leave both date fields empty? =

The export behaves exactly as if "All dates" were selected.

== Changelog ==

= 1.0.0 =
* Initial release: date filter (created / last modified) with a from/to range.
