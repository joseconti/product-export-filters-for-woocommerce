# API reference — Export Filters for WooCommerce

This plugin exposes **no public API surface of its own** in v1 — no REST
routes, no WP-CLI commands, no hooks it fires, no public functions meant to
be called externally. It only *consumes* three of WooCommerce's own exporter
hooks; see `docs/reference/hooks-and-extension-points.md` for exactly which
ones and how.

`docs/api/INDEX.md` stays empty by design until a Later-roadmap filter
exposes a hook of its own — see `docs/architecture.md` "Extensibility."

Internal (non-public) class/function reference for maintainers:
`docs/reference/classes.md`, `docs/reference/functions.md`.
