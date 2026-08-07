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
| `scripts/keel-doctor` | present | `--check`/`--plan`/`--fix`/`--json` all verified working; found and fixed two real corroboration gaps (composer.phar, ~/.local/bin/claude) live on this machine |
| `scripts/` build/minify script | n/a | no front-end JS/CSS assets shipped by this plugin (one small inline admin script, no build pipeline, D-010) |
| `scripts/keel-handoff-verify` | present | the 6 courier checks + single-lane lock take/release; verified live — correctly refused a stale/dirty hand-off |
| Single-lane lock | present | implemented inside `scripts/keel-handoff-verify` (lock file under `~/.keel/state/`, keyed by the real working-tree path) |
| `scripts/keel-continue` | present | tool detection, launch receipt, circuit breaker, script-file launch (never an interpolated string) all implemented; verified the refuse-to-fire path live (stale hand-off → printed prompt, no window opened); the actual macOS Terminal-launch path is implemented per spec but not fired in this session (firing it would open a real unsupervised chat window without the user watching it happen) |
| `.githooks/pre-commit` | present | installed, `core.hooksPath` set, VERIFIED live by staging a synthetic secret and confirming the commit was blocked (D-018) |
| Permission allow-lists (committed) | present | `.claude/settings.json`, built only from this project's verified commands (D-018) |
| CI workflow | present | `.github/workflows/ci.yml` — lint, phpcs, unit, e2e, gitleaks, keel-verify, the plan's exact commands (D-018) |
| Assistant rules (path-scoped) | present | `.claude/rules/` — code-style, security, docs-discipline (D-018) |
| Assistant subagents | present | `.claude/agents/` — code-reviewer, security-auditor, docs-verifier, playground-qa, a11y-auditor, test-driver (D-018); design-fidelity-auditor/launch-verifier/guide-qa not generated — n/a per D-006/D-015 |
| Model binding | present | reviewer=claude-sonnet-5, mechanical=claude-haiku-4-5-20251001, recorded in D-018 and the project card |
| MCP registration | n/a | technical plan does not define dev MCP servers |
| `docs/architecture.md` | present | as-built, Mermaid data flow, decisions consolidated |
| `docs/api/`, `docs/usage/`, `docs/reference/` | present | api/README.md + INDEX.md (empty by design); usage/ (4 files); reference/ (3 files) |
| `docs/security.md` | present | applied result, consolidated from the threat model |
| `docs/accessibility.md` | present | applied result + real verification evidence + honest gap (no AT pass yet) |
| `README.md` | present | |
| `guide/` | **declined** | D-015 — readme.txt + docs/usage/ give full task coverage for this size of plugin; canonical theme assets not authentically vendorable from this session; reversible |
| `guide/_theme/` + `guide/brand/` | n/a | condition: only if `guide/` exists (declined, D-015) |
| `docs/07-release.md` | present | full pre-release verification, self-audit, threat-model re-verification, real package-install test |
| `<site-docs>/` | n/a | no project website intent for v1 |
| `docs/.keel/slices/<n>.json` | n/a | no worktree fan-out planned for this small a scope |
| `docs/issues.md` | missing | created on first forge issue contact |
| `docs/old/` | n/a | no archiving needed yet |
| `docs/04-adoption-audit.md` | n/a | this is a new project, not an adoption |

One row is `declined` (`guide/`, D-015) — recorded with its decision entry.
Every other `missing` row (`docs/issues.md`) names the real condition that
creates it (first forge issue contact), and every `n/a` row quotes its
excluding condition. Nothing is silently unaccounted for.
