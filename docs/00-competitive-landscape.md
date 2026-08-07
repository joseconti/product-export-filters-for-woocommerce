# Competitive landscape — Export Filters for WooCommerce

> Research method: web search + fetch of official plugin pages (wordpress.org, vendor
> sites), support forums and reviews, run 2026-08-07. No invented features; anything
> unclear is marked "not determined."

## Niche verdict

**No plugin was found that extends WooCommerce's NATIVE product exporter (the
`Products > Export` screen, `WC_Product_CSV_Exporter`) via hooks to add filters,
without replacing it.** Every competitor investigated ships its own separate/parallel
export engine (own screen, own UI, own CSV pipeline).

Direct evidence of the gap: a WooCommerce.org support thread —
["Add published date column to woocommerce product export?"](https://wordpress.org/support/topic/add-published-date-column-to-woocommerce-product-export/)
— is a user asking exactly how to modify the *native* exporter to add a
created-date column. No plugin did this for them; a volunteer handed them raw PHP
using `woocommerce_product_export_product_default_columns` /
`woocommerce_product_export_product_column_*`. Search queries targeting this exact
concept surfaced only full replacement exporters (Store Exporter, WPFactory Export
WooCommerce, Product CSV Import Suite, WebToffee), never a lightweight hook-based
add-on to the native screen.

Per protocol, the fallback baseline below is the adjacent category: **general-purpose
WooCommerce product/store export plugins that include filtering as one of many
features.**

## Per-competitor inventory

| Name | URL | License/pricing | Status | Filter/export feature list | Source |
|---|---|---|---|---|---|
| **WP All Export — Product Export Add-On for WooCommerce** (Soflyy) | wordpress.org/plugins/product-export-for-woocommerce/ | Freemium. Free: CSV/simple XML, basic fields, bulk edit. Premium (add-on to paid WP All Export core): Excel, complex XML/Google Merchant, advanced filtering, scheduling, Zapier | Active — updated ~2026-07-30, 10,000+ installs, tested to WP 7.0.3 | Name, description, price, SKU, stock status, product type, category, tags, attributes/variations, custom fields (incl. 3rd-party plugin fields), media, **date fields with relative conditions** ("last week", "last month"), nested/combination filter logic. Formats: CSV, Excel, Google Sheets, XML | [listing](https://wordpress.org/plugins/product-export-for-woocommerce/), [filter docs](https://www.wpallimport.com/documentation/filter-exported-wordpress-data/) |
| **Product Import Export for WooCommerce** (Import Export Suite) | wordpress.org/plugins/product-import-export-for-woo/ | Free core + paid premium (variable products, advanced) | Active — updated ~2026-06-22, 80,000+ installs, tested to WP 7.0.3 | Category, tag, stock status, product type, individually selected products, product status. Custom batch size, CSV delimiter choice, column ordering. **No explicit date-created/modified filter documented.** | [listing](https://wordpress.org/plugins/product-import-export-for-woo/) |
| **Store Exporter** (WebDorado) | wordpress.org/plugins/woocommerce-exporter/ | Freemium. Free: basic CSV. Pro (Deluxe): more formats/filters/scheduling, ~$39.50/yr | Active — updated ~2026-07-23, 7,000+ installs, tested to WP 7.0.3 | Category/tag, status, type (incl. variations), stock status/quantity, featured status, **date modified**. Also exports orders (date, status, customer, country, coupon, gateway, shipping). Formats: CSV, TSV, XLS, XLSX, XML, RSS, WP Media; email/FTP/SFTP delivery (Pro). WP-CLI, WPML, multisite | [listing](https://wordpress.org/plugins/woocommerce-exporter/) |
| **Smart Manager** (StoreApps) | wordpress.org/plugins/smart-manager-for-wp-e-commerce/ | Freemium. Lite free (limited). Pro $199/yr (full CSV export, scheduling) | Active — updated ~2026-08-07 (very frequent), 10,000+ installs, tested to WP 7.0.3 | Spreadsheet-style filters with operators (<, >, =, contains, "any of", starts/ends with), AI-powered search (Pro), saved searches (Pro). Fields: date, stock status, product type, SKU, price/sale price, categories, tags, attributes, tax class, post status, coupon expiry. Lite export limited to stock-related columns | [listing](https://wordpress.org/plugins/smart-manager-for-wp-e-commerce/) |
| **Advanced Order Export For WooCommerce** (AlgolPlus) | wordpress.org/plugins/woo-order-export-lite/ | Freemium, open-source free core | Active — updated ~2026-06-08, 100,000+ installs, tested to WP 7.0.3 | Order-centric, not product-catalog: order date range, status, payment, billing/shipping, order meta; product fields only within order line items. Formats: Excel, CSV, XML, JSON, PDF, HTML | [listing](https://wordpress.org/plugins/woo-order-export-lite/) |
| **Export WooCommerce** (WPFactory) | wpfactory.com/item/export-woocommerce/ | Paid: $49.99/yr (1 site), $149 lifetime, $499 lifetime All-Access. Free version also exists | Active — v2.3.4 at fetch, updated ~4 weeks prior | Products, orders, order items, customers. 100+ fields. Filters: SKU, name, price, stock level, category, tags, product type, custom fields (ACF, postmeta, 3rd-party), variation attributes, gallery URLs. Markets itself as hookable, but as a **separate pipeline**, not a native-exporter extension | [product page](https://wpfactory.com/item/export-woocommerce/) |
| **WebToffee Product Import Export Plugin for WooCommerce** | webtoffee.com/product/product-import-export-woocommerce/ | Paid only: $69/yr (1 site) to $249/yr (25 sites) | Active (commercial, date not determined) | Title, type, category, tags, SKU, price, quantity, featured status, stock status, product status, **created date**, description. Formats: CSV, XML, Excel, TSV, Sheets. FTP/SFTP scheduled automation | [product page](https://www.webtoffee.com/product/product-import-export-woocommerce/) |
| **Visser Labs — Store Exporter / Store Exporter Deluxe** | visser.com.au/plugins/woocommerce-export/ | Freemium; Deluxe paid tier | Not determined (vendor site, no installs/last-update figure captured) | CSV/TSV/XLS/XLSX/XML of orders, products, customers, subscriptions; email/FTP/SFTP/cloud delivery. Specific filter list not determined (page not deep-fetched) | [vendor page](https://visser.com.au/plugins/woocommerce-export/) |

Note: a "Product CSV Export for WooCommerce" plugin by "8manage" could not be
verified to exist under that name — likely a mix-up with Visser Labs. Recorded as
not determined rather than guessed.

## Unified feature list (who has it)

| Feature | WP All Export | Product Import/Export Suite | Store Exporter | Smart Manager | Adv. Order Export | WPFactory | WebToffee | Visser Labs |
|---|---|---|---|---|---|---|---|---|
| Date created/modified filter | Yes (relative dates) | No | Yes (modified) | Yes | Order-only | Not confirmed | Yes (created) | Not determined |
| Category filter | Paid tier | Yes | Yes | Yes | Order-only | Yes | Yes | Not determined |
| Tag filter | Yes | Yes | Yes | Yes | Order-only | Yes | Yes | Not determined |
| Stock status filter | Yes | Yes | Yes | Yes | Order-only | Not explicit | Yes | Not determined |
| SKU filter | Yes | Not explicit | Not explicit | Yes | Order-only | Yes | Yes | Not determined |
| Price range filter | Yes | Yes | Not explicit | Yes | Not explicit | Yes | Yes | Not determined |
| Product type filter | Yes | Yes | Yes | Yes | Order-only | Not explicit | Yes | Not determined |
| Featured-product filter | Not explicit | Not explicit | Yes | Not explicit | Not explicit | Not explicit | Yes | Not determined |
| Product status (draft/published) filter | Not explicit | Yes | Yes | Yes | Not explicit | Not explicit | Yes | Not determined |
| Custom fields / ACF export | Yes | Not explicit | Yes | Yes (Pro) | Yes (order meta) | Yes | Not explicit | Not determined |
| Multiple export formats | Yes | CSV only (core) | Yes | Pro: CSV | Yes | CSV/XML | Yes | Yes |
| Scheduled/automated export | Pro | Not explicit | Pro | Pro | Pro | Not explicit | Yes | Deluxe |
| Remote delivery (FTP/SFTP/email) | Not explicit | Not explicit | Pro | Not explicit | Pro | Not explicit | Yes | Deluxe |
| Extends the *native* WC exporter | No | No | No | No | No | No | No | No |

## External-demand list (cited)

1. **"Add a created-date column to the native WooCommerce product exporter"** — direct
   unmet request; a volunteer had to hand out raw PHP using
   `woocommerce_product_export_product_default_columns` /
   `_column_*`. This is exactly the gap this project fills.
   Source: [wordpress.org support thread](https://wordpress.org/support/topic/add-published-date-column-to-woocommerce-product-export/).
2. **"Export only products from a specific category" gated behind a paid tier even
   on the market leader (WP All Export)** — the plugin author confirmed the free
   version does not support category filters.
   Source: [wordpress.org support thread](https://wordpress.org/support/topic/possible-to-export-only-products-from-specific-category/).
3. **Free-tier gating frustration** — 1-star reviews on "Product Import Export for
   WooCommerce" citing the free tier as "useless without paying" and not exporting
   variations. Signals demand for a genuinely free, single-purpose filter tool.
   Source: [wordpress.org reviews](https://wordpress.org/plugins/product-import-export-for-woo/#reviews).
4. **Pricing/upsell fatigue with WP All Export** — "Very useful but way overpriced.
   Constant 'buy this to do that'" (2-star review).
   Source: [wordpress.org reviews](https://wordpress.org/plugins/product-export-for-woocommerce/#reviews).

No wordpress.org evidence was found specifically for "never-sold" or "no-SKU"
export demand — not determined whether that demand exists at meaningful volume;
those ideas come from the user's own technical notes (`docs/filtros-exportador-woocommerce.md`
§7), not from external citation.

## Uncertainty flags

- Exact last-updated dates for Visser Labs and WebToffee are not determined
  (vendor pages, no wordpress.org metadata).
- "8manage" publisher attribution not verified.
- No forum/review evidence found for out-of-stock-only or brand/vendor export
  demand specifically.
