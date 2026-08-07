# Security — Export Filters for WooCommerce (applied result)

Consolidated from `docs/threat-model.md` and `references/security/wordpress.md`.
This describes what was actually built and verified, not the generic profile.

## In place
| Protection | Where | Evidence |
|---|---|---|
| Capability gating | Inherited — the plugin's render/filter callbacks only ever run inside WooCommerce's own `Products > Export` screen, already gated on `current_user_can( 'edit_products' ) && current_user_can( 'export' )` | Native WooCommerce behavior; a permission-denied user never reaches the screen |
| CSRF protection | `EFWC_Date_Filter::query_args()` verifies `$_POST['security']` against the `wc-product-export` nonce via `wp_verify_nonce()` before trusting any plugin field | AC-11 unit tests (`tests/unit/test-query-args.php`), passing |
| Request-context guard | `wp_doing_ajax()` + `empty( $_POST['form'] )` check before any parsing | Same AC-11 tests |
| Input validation | Every date is checked against `^\d{4}-\d{2}-\d{2}$` AND `checkdate()` before use — rejects SQL-injection-shaped strings, malformed input, and calendar-impossible dates (30 Feb) | `tests/unit/test-clean-date.php`, including a dedicated SQL-injection-shaped-input test, all passing |
| Output escaping | `esc_attr()` / `esc_html__()` on every echoed value; native `<input type="date">` with no free-text echo | `phpcs --standard=WordPress` clean (0 errors) |
| No raw SQL | The plugin never builds a query string itself — it only sets a WooCommerce-native operator string, which WooCommerce's own `parse_date_for_wp_query()` turns into parameterized `WP_Query` arguments | Code review; the plugin has no `$wpdb` usage at all |
| Fail-safe on missing dependency | `efwc_init()` checks `class_exists( 'WooCommerce' )` before doing anything; shows an admin notice, never fatals | Verified live: WooCommerce deactivated in the wp-env playground, `/wp-admin/plugins.php` loaded with no fatal error and the notice text present (`docs/05-test-points.md`, AC-13) |
| No trust in shared `$args` | The plugin only ever *writes* `$args[$field]`, never reads a pre-existing value from it, so another plugin/theme sharing the same filter cannot inject a value this plugin would blindly trust | `docs/threat-model.md` "Defended" table |

## Not defended (deliberate, per the WordPress security profile)
See `docs/threat-model.md` "Not defended" for the full table with
consequences and what to add if needed: a compromised administrator, other
plugins/theme sharing the same process, rate limiting, data-at-rest
encryption of the CSV, and audit logging. None of these are specific
weaknesses of this plugin — they are out of scope by the same reasoning
`references/security/wordpress.md` states for any WordPress plugin of this
class.

## Keeping it intact
- Any new filter added to the roadmap must follow the same additive-only
  discipline on `$args` (never read/overwrite a pre-existing key) —
  `docs/rubrics/hooks-and-extensibility.md` criterion 1.
- Any new user input follows the same pattern: validate with a closed,
  explicit rule (regex + `checkdate()`-style semantic check, or
  `sanitize_key()` against a fixed value list) before it ever reaches a
  WooCommerce query argument.
- `phpcs --standard=WordPress` runs at every test point (`docs/03-technical-plan.md`
  §Testing) and catches unescaped output before it ships.
