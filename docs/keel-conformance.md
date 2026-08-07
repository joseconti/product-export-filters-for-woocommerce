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
| `docs/02-functional-spec.md` | missing | Phase 2 |
| `docs/03-technical-plan.md` | missing | Phase 2 |
| `docs/threat-model.md` | missing | Phase 2 |
| `docs/flows/` | missing | Phase 2 (likely a single short flow given the tiny v1 scope) |
| `docs/budget.md` | n/a | `Client budget: no` |
| `docs/spec-references/` | n/a until Phase 2 decides | condition: only if the spec records reference artifacts |
| `docs/rubrics/` | n/a until Phase 2 decides | condition: only if a rubric domain is accepted at §6a |
| `docs/design/references/` | n/a | no UI design needed (D-006) |
| Assistant rules / subagents containers | missing | Phase 2 close, if accepted — not yet asked |
| `docs/design/DESIGN-BRIEF.md` | n/a | no UI design needed (D-006) |
| `docs/design/design-handoff/` | n/a | no UI design needed (D-006) |
| `docs/BUILD-SPEC.md` | n/a | no UI design needed (D-006) |
| `docs/design/design-requests/` | n/a | no UI design needed (D-006) |
| `.gitignore` + `.gitattributes` | present (partial) | `.gitignore` present with the required entries; `.gitattributes` missing — Phase 5 scaffold |
| `docs/sprints/` | missing | Phase 5 |
| `docs/05-test-points.md` | missing | Phase 5 |
| `docs/api/INDEX.md` | missing | Phase 5 first slice |
| `docs/keel-conformance.md` | present | this file |
| `docs/playground.md` | missing | Phase 5 scaffold |
| `scripts/keel-verify` | missing | Phase 5 scaffold |
| `scripts/keel-doctor` | missing | Phase 5 scaffold |
| `scripts/` build/minify script | n/a | no front-end JS/CSS assets shipped by this plugin (one small inline admin script, no build pipeline) |
| `scripts/keel-handoff-verify` | missing | Phase 5 scaffold |
| Single-lane lock | missing | Phase 5 scaffold — required before `start` can actually fire (card: `Chaining: start`) |
| `scripts/keel-continue` | missing | Phase 5 scaffold |
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
