# Accessibility — Export Filters for WooCommerce (applied result)

Consolidated from `docs/01-discovery.md` §Accessibility, `docs/02-functional-spec.md`
"Per-screen accessibility requirements," and the real automated pass recorded
in `docs/05-test-points.md`.

## Standard targeted
WCAG 2.2 AA as the floor. This plugin adds a small, fixed amount of markup
(two `<tr>` rows: one `<select>`, two `<input type="date">`, their `<label>`s)
to a single existing WordPress admin screen — the target is full conformance
on that markup, not a partial best-effort.

## What was actually implemented
- **Semantic structure & labels.** Every added control has an explicit
  `<label for>` bound to its `id` (`efwc-export-date-field`,
  `efwc-export-date-from`, `efwc-export-date-to`) — no placeholder-as-label,
  no unlabeled control.
- **Keyboard operability.** The `<select>` and both `<input type="date">`
  fields are native form controls, fully keyboard-operable by construction;
  the show/hide of the range row is driven by the select's own native
  `change` event, not a custom widget that could trap focus.
- **No color-only signal.** Nothing in this feature communicates state
  through color alone — there is no error/success state beyond the native
  form's own (WooCommerce's).
- **Native date widget.** `<input type="date">` uses the browser's own
  accessible date picker rather than a custom JS calendar widget.
- **Show/hide never discards a value.** The range row is toggled with CSS
  `display`, not removed from the DOM — a value typed, then hidden, then
  shown again is preserved (verified by AC-12's Playwright test).

## Verification performed
| Check | Tool | Scope | Result |
|---|---|---|---|
| Automated accessibility scan | `@axe-core/playwright` | The two `<tr>` rows this plugin renders, in both states (range hidden / shown) — deliberately scoped away from WooCommerce's own native Select2 widgets on the same screen, which are core's responsibility, not this plugin's (see `docs/lessons-learned.md` L-003) | **Pass** — 0 critical/serious violations, verified live against a real wp-env playground |
| Keyboard-only interaction | Playwright, driving `selectOption()` on the native `<select>` (fires the same `change` event a keyboard-driven selection does) | Range-row toggle | **Pass** |
| Focus/DOM-removal check | Playwright, asserting the range row uses `display` toggling, not removal | Value persistence across hide/show | **Pass** (implicit in the AC-02/AC-12 test passing) |

## Real assistive-technology pass
**Not yet performed.** This is recorded honestly as a gap, not silently
skipped: per `docs/03-technical-plan.md` §Testing, a real screen-reader pass
is optional and not gating for this project (the surface is two native WP
admin form controls, already covered by WordPress core's own accessibility
work on that screen), but it has not been offered to the user yet. Offering
it is deferred to before the wordpress.org submission — tracked in
`docs/PROGRESS.md` "Deferred items."

## Known gaps
None found on the markup this plugin controls. The one pre-existing gap
found during verification (WooCommerce core's own Select2 widgets emitting
an `aria-expanded` attribute their ARIA role doesn't allow) is explicitly
**not** this plugin's defect — it exists on the same screen regardless of
whether this plugin is installed, and fixing it is out of this project's
scope (`docs/threat-model.md` "Not defended" — "Other plugins and the theme").
