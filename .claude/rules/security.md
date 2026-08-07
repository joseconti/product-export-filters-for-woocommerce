---
paths:
  - "export-filters-for-woocommerce.php"
  - "includes/**/*.php"
---

# Security — Export Filters for WooCommerce

Distilled from `references/security/wordpress.md`, applied to this project (`docs/security.md` is the full applied record).

- Sanitize every input at the point it enters PHP (`sanitize_key`, `sanitize_text_field`), never trust `$_POST`/`$_GET` directly.
- Escape every output at print time (`esc_html__`, `esc_attr`), never build raw HTML from a variable.
- Any new POST-consuming code path verifies the WooCommerce export nonce (`wc-product-export`) before trusting its fields — never add a code path that skips this.
- Never build raw SQL — pass everything through `wc_get_products()` / `WP_Query` args, never `$wpdb->query()` with concatenated input.
- Every new filter this plugin adds to `$args` only ever WRITES its own key(s) — never reads or overwrites a pre-existing key another callback may have set (the additive-only rule, `docs/rubrics/hooks-and-extensibility.md`).
- `defined( 'ABSPATH' ) || exit;` at the top of every PHP file.
- No secrets, credentials, or real personal/customer data in code, comments, or logs — this plugin has none today; never add one without a decisions.md entry and a threat-model update.

Full profile: the Keel security reference for this project type governs; this file is the reminder, not the standard.
