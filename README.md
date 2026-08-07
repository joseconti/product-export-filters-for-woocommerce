# Export Filters for WooCommerce

Adds filtering options to WooCommerce's native product exporter, starting
with a date filter (created / last modified, with a from/to range). It
extends `Products > Export` via WooCommerce's own hooks — it does not
replace it, and it does not touch the product importer.

## Requirements
- WordPress 6.4+, WooCommerce 8.0+ (developed and verified against
  WooCommerce 11.0.0), PHP 7.4+.

## Install
See [`docs/usage/installation.md`](docs/usage/installation.md).

## Quickstart
Go to **Products > Export**, pick a date field, set a From/To range (same
date in both = a single day), click **Generate CSV**. Full walkthrough:
[`docs/usage/getting-started.md`](docs/usage/getting-started.md).

## Documentation
- [`docs/usage/`](docs/usage/) — installation, configuration, getting
  started, examples.
- [`docs/architecture.md`](docs/architecture.md) — how it's built.
- [`docs/reference/hooks-and-extension-points.md`](docs/reference/hooks-and-extension-points.md) —
  which WooCommerce hooks this plugin uses, and how to extend it safely.
- [`docs/security.md`](docs/security.md) / [`docs/accessibility.md`](docs/accessibility.md) —
  the applied security and accessibility posture.
- [`docs/01-discovery.md`](docs/01-discovery.md) — the competitive research
  and scope reasoning behind v1.
- [`docs/playground.md`](docs/playground.md) — how to run this project
  locally and verify it for real.

## Roadmap
v1 ships the date filter only, deliberately. Tag, stock-status, delimiter
choice, a "never sold" filter, price range, product status, featured, batch
size, brand/attribute filters, and saved filter profiles are recorded as the
"Later" roadmap in [`docs/01-discovery.md`](docs/01-discovery.md) — cheap to
add on top of the current architecture, none of them cut silently.

## License
GPL-3.0-or-later. See [`LICENSE`](LICENSE).
