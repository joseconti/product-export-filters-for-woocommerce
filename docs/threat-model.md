# Threat model — Export Filters for WooCommerce

## Assumptions
- The plugin's source is public by construction — it ships to wordpress.org
  under GPL-3.0-or-later, so obscurity is never a control; every check below
  assumes an attacker can read the exact source.
- Adversaries are: (a) an unauthenticated visitor probing the site's public
  surface, (b) a logged-in user with a role below `edit_products`/`export`
  (e.g. Shop Manager without export rights, Customer), and (c) another
  plugin/theme running in the same process that might call the exposed filter
  in an unexpected context. A compromised administrator is explicitly out of
  scope (see "Not defended" below — standard for this project class).
- The feature touches no personal data of its own: it filters WHICH existing
  products are exported, using dates WordPress already stores
  (`post_date`/`post_modified`); it introduces no new storage.
- The only new server-side code path is one `apply_filters` callback
  (`woocommerce_product_export_product_query_args`) and one admin-screen
  render callback (`woocommerce_product_export_row`) — both already
  capability-gated by WooCommerce's own `Products > Export` screen.

## Defended
| Threat | Control | Delivery state |
|---|---|---|
| An unauthenticated or under-privileged request reaches the date-filter logic and exfiltrates product data outside their permission | The plugin's render callback only ever fires as part of WooCommerce's own `Products > Export` screen, which WooCommerce itself gates behind `current_user_can( 'edit_products' ) && current_user_can( 'export' )` before the screen renders at all. The plugin performs no capability check of its own because it never runs a code path WooCommerce hasn't already gated. | `TO BUILD` — Sprint 1; verified with a permission-denied Playwright test asserting a lower-privileged user never sees the screen at all |
| CSRF / a forged request triggers a filtered export the admin never intended | The query-args filter verifies `$_POST['security']` against the `wc-product-export` nonce (`wp_verify_nonce()`) before trusting any of the plugin's own POST fields; on failure it returns `$args` unmodified rather than trusting attacker-controlled input | `TO BUILD` — Sprint 1; verified by AC-11 (a request with a missing/invalid nonce is a no-op) |
| The filter fires outside a genuine AJAX export context (another plugin, a cron job, a REST call reusing the same `apply_filters` hook name) and trusts stray `$_POST` data | Guarded by `wp_doing_ajax()` plus an explicit `empty( $_POST['form'] )` check before any parsing happens; fails safe to `$args` unchanged | `TO BUILD` — Sprint 1; same AC-11 test extended to a non-AJAX context |
| Malformed or malicious date input (SQL-injection attempt, oversized string, non-date garbage) reaches the database query | Every date value is validated with a strict `^\d{4}-\d{2}-\d{2}$` regex AND `checkdate()` before ever being concatenated into the WooCommerce-native operator string (`>=`, `<=`, `...`); WooCommerce's own `parse_date_for_wp_query()` then turns that into parameterized `WP_Query`/`$wpdb` arguments — the plugin never builds raw SQL itself | `TO BUILD` — Sprint 1; verified by AC-07 (invalid date silently dropped) and a dedicated unit test feeding SQL-injection-shaped strings through `clean_date()` |
| Reflected/stored XSS via the rendered `<select>`/`<input>` markup or their values | All output uses `esc_attr()`/`esc_html__()`; the date inputs are native `<input type="date">` with no free-text echo of user input anywhere in the rendered HTML | `TO BUILD` — Sprint 1; verified by a static-analysis pass (`phpcs --standard=WordPress`, which flags unescaped output) plus a manual review at the Phase 5 test point |
| Another plugin/theme calls `woocommerce_product_export_product_query_args` with attacker-shaped `$args` before this plugin's callback runs, and this plugin blindly trusts `$args[$field]` already being set | The plugin only ever *writes* `$args[$field]`, never reads a pre-existing value from it — no trust placed in the incoming `$args` array's date-related keys | `TO BUILD` — Sprint 1 |
| Denial of service via an expensive/unbounded query | The plugin adds a WHERE-clause condition on indexed columns (`post_date`/`post_modified`) to WooCommerce's own paginated query; it does not remove WooCommerce's own `limit`/`page` pagination, so batch size stays bounded exactly as native | `IN PLACE` — inherent to only ever adding to `$args`, never replacing `limit`/`page` |

## Not defended
| Not defended | Consequence | If you need it |
|---|---|---|
| A compromised or malicious site administrator | An administrator can already execute arbitrary PHP on the site; no plugin-level control survives that | Nothing at the plugin level — this is the site's hosting and account-security problem |
| Other plugins and the theme sharing the same process/database/global scope | A hostile or broken plugin/theme can alter this plugin's behavior at runtime (e.g. by hooking the same filter at a later priority and overwriting `$args`) | Defensive prefixing (`EFWC_`), no reliance on another plugin's sanitization, and this plugin never trusting pre-existing values in the shared `$args` array (see "Defended" table above) |
| Rate limiting / abuse throttling of the export action itself | A user with legitimate export rights can trigger export runs repeatedly; this plugin adds no throttling beyond what WooCommerce's native exporter already does (none) | Out of this plugin's scope entirely — the native exporter has never had this, and adding it would be scope creep contradicting D-001 (hook into the native exporter, never expand its own surface). A site needing this should look at a web-application firewall or a dedicated rate-limiting plugin |
| Data-at-rest encryption of the exported CSV file | The CSV WooCommerce writes to disk (native behavior, unchanged by this plugin) is stored and downloaded exactly as it always has been | Out of scope — unchanged from stock WooCommerce; if this matters to a site's risk profile, it is a hosting/storage-encryption decision, not this plugin's |
| Auditing/logging of who ran a filtered export, or with which date range | No log is written (per `docs/03-technical-plan.md` §Conventions — logging: none for v1) | If audit trails matter, WooCommerce's own admin activity, or a dedicated audit-log plugin, would need to cover this — reserved as a Later-roadmap idea if ever requested |
