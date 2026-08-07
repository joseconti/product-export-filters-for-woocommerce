# Installation — Export Filters for WooCommerce

## Requirements
- WordPress 6.4 or later.
- WooCommerce 8.0 or later, active. Verified against WooCommerce 11.0.0.
- PHP 7.4 or later.

## Install
1. Download the plugin ZIP (from wordpress.org once released, or build it
   locally per `docs/07-release.md`'s packaging step).
2. In wp-admin, go to **Plugins > Add New > Upload Plugin**, choose the ZIP,
   click **Install Now**, then **Activate**.
3. If WooCommerce is not active, the plugin shows an admin notice and does
   nothing else — no fatal error. Activate WooCommerce first if needed.

## Verify it worked
Go to **Products > Export**. You should see a new "Filter by date" field
above the "Generate CSV" button, with **All dates** selected by default.

No configuration screen, no settings page — this plugin has none. See
`docs/usage/getting-started.md` for how to use the filter itself.
