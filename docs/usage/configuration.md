# Configuration — Export Filters for WooCommerce

There is no settings screen and no `wp_options` entry — the plugin has
nothing to configure. The one thing you interact with lives directly on
**Products > Export**:

| Field | Where | Valid values | Default | Effect |
|---|---|---|---|---|
| Filter by date | `Products > Export`, above "Generate CSV" | All dates / Date created / Date last modified | All dates | Chooses which WooCommerce date column the range below filters against |
| Date range — From | Same screen, shown only when a date field is chosen | Any valid calendar date, or empty | empty | Lower bound of the export (inclusive). Empty = open-ended on this side |
| Date range — To | Same screen | Any valid calendar date, or empty | empty | Upper bound of the export (inclusive). Empty = open-ended on this side. Same value as From = a single day |

There is nothing else to configure: no capability to grant (it reuses the
`Products > Export` screen's own `edit_products` + `export` requirement, per
`docs/02-functional-spec.md` "Permissions matrix"), no cron, no external
credential.
