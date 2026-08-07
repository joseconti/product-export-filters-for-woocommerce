# Keel conformance — Export Filters for WooCommerce

> Derived from `MANIFEST.md` Table 1 (Keel v5.12.0), never from recollection.
> One row per applicable requirement. Written at Phase 1 step 0a; re-swept at
> every post-update reconciliation and at the Phase 7 gate.

| Requirement | State | Note |
|---|---|---|
| `docs/PROGRESS.md` | present | |
| `docs/decisions.md` | present | |
| `docs/lessons-learned.md` | present | |
| Off-machine durability | present | git remote `origin` verified |
| Clean working tree at block close | present | this session's work committed to `develop` before close |
| `CLAUDE.md` + `AGENTS.md` (portability lock) | present | v5.12.0 stamp |
| `GEMINI.md` / `.gemini/settings.json` mirror | n/a | user does not work with Gemini CLI (not asked/needed — not raised as a tool in use) |
| `.claude/skills/keel/` + `.agents/skills/keel/` | present | embedded, verified file-for-file against the installed copy |
| `docs/00-competitive-landscape.md` | present | |
| `docs/01-discovery.md` | present | |
| `docs/estimate.md` | present | Estimate v1 (preliminary) |
| `docs/token-ledger.md` | present | |
| `docs/02-functional-spec.md` | present | |
| `docs/03-technical-plan.md` | present | |
| `docs/threat-model.md` | present | |
| `docs/flows/` | present | `docs/flows/date-filter-export.md` |
| `docs/budget.md` | n/a | `Client budget: no` |
| `docs/spec-references/` | n/a | the Phase 2 reference artifact (D-008) is `docs/filtros-exportador-woocommerce.md`, already committed at its own path; the spec cites it directly rather than duplicating it under `docs/spec-references/` |
| `docs/rubrics/` | present | `docs/rubrics/hooks-and-extensibility.md` (D-011) |
| `docs/design/references/` | n/a | no UI design needed (D-006) |
| Assistant rules / subagents containers | missing | Phase 2 close, if accepted — not yet asked |
| `docs/design/DESIGN-BRIEF.md` | n/a | no UI design needed (D-006) |
| `docs/design/design-handoff/` | n/a | no UI design needed (D-006) |
| `docs/BUILD-SPEC.md` | n/a | no UI design needed (D-006) |
| `docs/design/design-requests/` | n/a | no UI design needed (D-006) |
| `.gitignore` + `.gitattributes` | present | both present with the required entries |
| `docs/sprints/` | present | `docs/sprints/sprint-1.md` (not yet closed) |
| `docs/05-test-points.md` | present | 13 real, driven test points logged with actual command output |
| `docs/api/INDEX.md` | present | empty by design — v1 exposes no public surface of its own |
| `docs/keel-conformance.md` | present | this file |
| `docs/playground.md` | present | verified live this session |
| `scripts/keel-verify` | present | passes; covers [E] paths, php -l, phpcs, version touchpoints, .gitignore hygiene, no committed .mo |
| `scripts/keel-doctor` | missing | next session — compiles from `docs/03-technical-plan.md` §Environment requirements |
| `scripts/` build/minify script | n/a | no front-end JS/CSS assets shipped by this plugin (one small inline admin script, no build pipeline, D-010) |
| `scripts/keel-handoff-verify` | missing | next session — needed for `Chaining: start` (D-007) to actually fire |
| Single-lane lock | missing | next session — required before `start` can actually fire (card: `Chaining: start`) |
| `scripts/keel-continue` | missing | next session |
| `.githooks/pre-commit` | missing | Phase 5 scaffold, if assistant-config accepted — not yet asked |
| Permission allow-lists (committed) | missing | Phase 5 scaffold, if assistant-config accepted — not yet asked |
| CI workflow | missing | Phase 5 scaffold, if assistant-config accepted and forge has CI — not yet asked |
| MCP registration | n/a | technical plan does not define dev MCP servers (Phase 2 will confirm) |
| `docs/architecture.md` | missing | Phase 6 |
| `docs/api/`, `docs/usage/`, `docs/reference/` | missing | Phase 6 |
| `docs/security.md` | missing | Phase 6 |
| `docs/accessibility.md` | missing | Phase 6 |
| `README.md` | missing | Phase 6 |
| `guide/` | missing | Phase 6 — not yet asked whether declined |
| `guide/_theme/` + `guide/brand/` | missing | Phase 6 |
| `docs/07-release.md` | missing | Phase 7 |
| `<site-docs>/` | n/a | no project website intent for v1 |
| `docs/.keel/slices/<n>.json` | n/a | no worktree fan-out planned for this small a scope |
| `docs/issues.md` | missing | created on first forge issue contact |
| `docs/old/` | n/a | no archiving needed yet |
| `docs/04-adoption-audit.md` | n/a | this is a new project, not an adoption |

Nothing is `declined` at this point — every `missing` row names the phase that
creates it, and every `n/a` row quotes its excluding condition.
