---
paths:
  - "export-filters-for-woocommerce.php"
  - "includes/**/*.php"
  - "tests/**/*.php"
  - "tests/**/*.js"
---

# Code style — Export Filters for WooCommerce

Source of truth: docs/03-technical-plan.md §Conventions. On any conflict, the plan wins — fix this file.

- Prefix/namespace: every class uses `EFWC_`; every procedural function/option key uses `efwc_`.
- Naming: classes `EFWC_Snake_Case_Words`; methods `snake_case()`; files `class-efwc-*.php`.
- Error handling: no exceptions — every failure path (invalid nonce, invalid date, WooCommerce inactive) fails safe and silent to native WooCommerce behavior. Never introduce a new error strategy without a decisions.md entry.
- Logging: none by default (this feature writes/calls nothing worth logging). If a Later-roadmap filter needs one, it's `WC_Logger` + a settings checkbox, ON in dev, OFF by default at release.
- Base language of source strings: English, text domain `export-filters-for-woocommerce`.
- Comments: PHPDoc on every public method (purpose, params, return); comment the why on non-obvious decisions (e.g. the meta_query-clobbering trap), never the what. English by default.
