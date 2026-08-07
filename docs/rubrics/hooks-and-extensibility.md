# Rubric — Hooks & extensibility shape

> Recorded once at Phase 2 §6a for this plugin, per the user's "recommended: yes
> for a plugin" default. Scores the spec/plan's shape, not its completeness —
> the mechanical checklist already covers completeness.

## What good looks like
1. **Every filter this plugin registers stays additive to WooCommerce's own
   query args** — it only ever writes keys it owns, never overwrites or reads
   pre-existing keys another callback may have set (this is the actual shape
   that keeps the plugin safely stackable with other exporter add-ons, present
   or future).
2. **The `EFWC_` prefix is used with zero exceptions**, including on any hook
   this plugin might expose of its own in the Later roadmap (e.g. a future
   `efwc_before_date_filter_applied` action) — a public surface that skips the
   prefix is a defect the moment it ships, because renaming it later is a
   breaking change for every site depending on it.
3. **A filter this plugin ever exposes of its own documents its exact
   signature (args, types, when it fires) before it ships**, not after —
   consistent with Keel's "document every public surface at the moment it
   changes."
4. **The date-field selector's value set stays a closed, explicit list**
   (`''`, `date_created`, `date_modified`) rather than accepting an arbitrary
   string that gets passed straight to `$args[$field]` — an open string would
   let a crafted request set an unrelated `wc_get_products()` query var by
   accident (a shape problem, not a validation gap: even a perfectly
   "sanitized" arbitrary string is the wrong shape here).
5. **Nothing in v1 forces a Later-roadmap filter into a shape that will need
   breaking to add** — e.g. the args-building logic being a private method
   only this class calls, not a small reusable helper other filters could
   call, would force duplicate logic once the tag/stock-status filters
   (Later roadmap) land.

## Score against this spec (Phase 2 §6a pass)
| Criterion | Verdict | Note |
|---|---|---|
| 1 — additive, never overwrites/reads pre-existing `$args` keys | Pass | `docs/threat-model.md` "Defended" table row explicitly states this; `docs/02-functional-spec.md` Feature 2 processing step 5 only ever assigns `$args[$field]` |
| 2 — `EFWC_` prefix with zero exceptions | Pass | `docs/03-technical-plan.md` §Conventions fixes it project-wide; v1 exposes no hooks of its own yet, so nothing to check beyond the class/text-domain naming, which is compliant |
| 3 — future own-hooks documented at the moment they ship | n/a for v1 | v1 exposes no new hooks of its own (only consumes WooCommerce's); the rule is recorded in the change map for when one is added |
| 4 — closed, explicit value set for the field selector | Pass | `docs/02-functional-spec.md` Feature 1 fixes the three-value closed set; `in_array( ..., true )` gate noted explicitly |
| 5 — v1 doesn't box in the Later roadmap | Pass with a note | `docs/03-technical-plan.md` code map flags the `date_query` migration as due "the moment a second filter lands," not deferred silently; the args-building logic is small enough (a handful of lines) that extracting it into a shared helper at that point is a minor refactor, not a rewrite — acceptable for v1's scope |

No findings required a spec change; the rubric pass is recorded as evidence,
not as a gate that failed.
