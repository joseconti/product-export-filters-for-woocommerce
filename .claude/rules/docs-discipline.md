---
paths:
  - "export-filters-for-woocommerce.php"
  - "includes/**/*.php"
---

# Build discipline — Export Filters for WooCommerce

- Before writing ANY new function/method/class: grep docs/api/INDEX.md first; reuse or generalize an existing fit — a near-duplicate is a defect.
- Every public surface is documented at the moment it changes, in the same slice: created — documented in docs/reference/ or docs/api/ AND its INDEX.md row added (currently empty by design — v1 exposes no hooks of its own), with a runnable example; modified — its existing doc, example, and INDEX row updated to the as-built signature; removed — doc and row deleted if never released, or marked deprecated/removed with its replacement if it was.
- Extension points: any new filter this plugin exposes of its own is prefixed `efwc_`, documented with its exact signature before it ships, and stays additive-only on `$args` (never overwrites a pre-existing key).
- Update docs/PROGRESS.md and docs/decisions.md at the moment of change, never later.
