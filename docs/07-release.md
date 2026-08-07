# Release — Export Filters for WooCommerce v1.0.0 (candidate)

> Prepared on `develop`. Per SKILL.md "Git flow": Keel prepares the
> candidate and stops here — the merge to `main`, the tag, and the published
> release are the user's own act, on explicit instruction.

## .gitignore boundary (what never enters git)
`.DS_Store`, `.claude/settings.local.json`, `CLAUDE.local.md`,
`.keel-update-check`, `docs/continuation-prompt.md`, `node_modules/`,
`vendor/`, `test-results/`, `playwright-report/`, `languages/*.mo`,
`.phpunit.result.cache`, `*.log`, `.release-check/` (this phase's own
scratch dir for the packaging test, removed after use). Verified: `git
status --porcelain` clean, nothing sensitive tracked (confidential-data scan
re-run over the whole tracked tree — zero hits beyond the skill's own
documentation prose describing secret *patterns*, which is expected and
harmless).

## export-ignore boundary (in repo, not in package)
`/docs`, `/tests`, `/.claude`, `/.agents`, `/.github`, `.gitattributes`,
`.gitignore`, `.distignore`, `.wp-env.json`, `phpcs.xml.dist`,
`phpunit.xml.dist`, `playwright.config.js`, `composer.json`,
`composer.lock`, `package.json`, `package-lock.json`, `CLAUDE.md`,
`AGENTS.md`, `/scripts` (Keel meta-tooling — found leaking into the first
archive attempt, fixed this phase), `README.md` (the repo-facing doc;
`readme.txt` is what wordpress.org actually reads and it ships).

## Package contents (verified)
Built with `git archive HEAD | tar -x` (never `zip -r .` — that would ignore
the export-ignore boundary), plus the compiled `languages/*.mo` injected
afterward with `msgfmt` (the `.mo` is `[G]`/gitignored on purpose — `git
archive` only includes tracked files, so packaging MUST compile it fresh; a
ZIP built from `git archive` alone would silently ship with no working
Spanish translation). Verified contents:

```
export-filters-for-woocommerce.php
includes/class-efwc-date-filter.php
languages/export-filters-for-woocommerce-es_ES.mo   (compiled this step)
languages/export-filters-for-woocommerce-es_ES.po
languages/export-filters-for-woocommerce.pot
LICENSE
readme.txt
uninstall.php
```
8 files, ~22 KB zipped. No dev file, no secret, no test, no doc source.

## Changelog entry (oldest → newest)
`readme.txt` already carries the only entry needed for a first release:
```
= 1.0.0 =
* Initial release: date filter (created / last modified) with a from/to range.
```

## Pre-release verification results (full suite re-run on the candidate)
| Check | Command | Result |
|---|---|---|
| `keel-doctor --check` | `./scripts/keel-doctor` | VERDICT: OK — all blocking rows OK (Node 24.14.0, Docker 29.6.2 running, Playwright + Chromium installed); every optional/advisory row OK |
| Unit tests | `npx wp-env run tests-cli --env-cwd=wp-content/plugins/product-export-filters-for-woocommerce phpunit` | **17/17 pass**, 18 assertions, real WP core test suite |
| e2e tests | `npx playwright test` | **5/5 pass**, headless Chromium, trace+video recorded |
| Coding standard | `vendor/bin/phpcs --standard=phpcs.xml.dist` | Clean, 0 errors |
| i18n | `wp i18n make-pot` (re-run) | 11 msgids, identical to the committed `.pot` (only the timestamp differed — reverted, no real drift) |
| `keel-verify` | `./scripts/keel-verify` | VERDICT: PASS (all 29 checks) |
| Real-environment install of the exact distributable | `wp plugin install <candidate.zip> --activate`, then a driven Playwright smoke check | **Pass** — the field renders, the range row toggles, on a genuinely fresh install of the packaged ZIP (not the dev tree) |
| Uninstall lifecycle | Read-only inspection (see below) instead of a literal `wp plugin uninstall` run | **Pass, verified read-only** — see the note below |

**Note on the uninstall lifecycle check.** A literal `wp plugin uninstall`
run against the bind-mounted dev copy was attempted earlier this phase and
**deleted the actual repository files on disk** (wp-env mounts `.` directly
into the container — recorded as L-005, a critical lesson). Recovery was
complete (everything was already pushed to `origin/develop`) but the method
changed: uninstall was verified the safe way — read-only inspection
confirming the plugin creates zero options, transients, cron events, or
tables (`wp option list --search='*efwc*'`, `wp transient list
--search='*efwc*'`, `wp cron event list`, `wp db query "SHOW TABLES LIKE
'%efwc%'"` — all empty), which matches `uninstall.php`'s intentional no-op.
There is nothing for uninstall to leave behind because the plugin creates
no persistent state in the first place.

## Self-audit results (`references/anti-patterns.md`)
| # | Question | Answer | Evidence |
|---|---|---|---|
| 1 | Every declared tool runs in a test-point command, blocking? | Yes | `docs/03-technical-plan.md` §Tooling commands — all four (phpcs, phpunit, playwright, package) match `docs/05-test-points.md` real runs |
| 2 | Every command cited in CLAUDE.md/AGENTS.md/README/docs exists as a real script? | Yes | `scripts/keel-verify`, `keel-doctor`, `keel-handoff-verify`, `keel-continue` all present, executable, verified running this session |
| 3 | Every Version touchpoint carries the same value? | Yes | `./scripts/keel-verify` "version touchpoints agree (1.0.0)" |
| 4 | Every code-map path carries its marker, every [E] exists on disk? | Yes | `docs/03-technical-plan.md` code map — all rows [E]; `keel-verify`'s [E]-path check passes |
| 5 | Every documented extension point has a test asserting it fires with documented args? | n/a | v1 exposes no hooks of its own (`docs/reference/hooks-and-extension-points.md`) |
| 6 | Every generated artifact has a consumer? | Yes | `.pot` → the `.po` translators (and the `.mo` compiled from it); no orphan generator |
| 7 | Every `docs/05-test-points.md` row carries command + output? | Yes | Re-checked this file directly |
| 8 | Has the suppression count grown since the last sprint close? | No | One `phpcs:ignore` exists, added and documented in Sprint 1, unchanged since |
| 9 | Every deliberate omission recorded? | Yes | `docs/decisions.md` (D-001–D-015) and `docs/threat-model.md` "Not defended" |
| 10 | Every present-tense control actually `IN PLACE` with evidence? | Yes | `docs/threat-model.md` "Defended" table, all rows `TO BUILD`→verified this session, one `IN PLACE` (pagination inherent, no code needed) |
| 11 | Exactly one authoritative file per artifact, `.mo` matches a fresh build? | Yes | `.mo` recompiled from the committed `.po` this session with `msgfmt`, verified valid |
| 12 | Every doc reachable from an index, every internal link resolves? | Yes | `README.md` and `docs/api/README.md` link into every doc produced; spot-checked |
| 13 | Every AC has a driven test or a delegation tag? | Yes | `docs/05-test-points.md` — 13/13 AC rows, all `driven`, no untagged human-verdict row |
| 14 | Does `keel-doctor --check` pass, so the suite's green result is real? | Yes | See table above |
| 15 | Was every test seen to fail first, per the test-first policy? | **No, for the ported code** | D-013 records this honestly — the reference implementation's tests were authored alongside it, not observed failing first |
| 16 | Did every bug fix start from a failing reproduction test? | n/a | No bugs fixed in product code this cycle (L-001–L-005 were process/tooling bugs in tests/tooling, not the shipped plugin code) |
| 17 | Every applicable MANIFEST Table 1 row carries a state in keel-conformance.md? | Yes | `docs/keel-conformance.md`, regenerated this phase |
| 18 (WP) | `wp i18n make-pot` reports zero untranslated/wrongly-domained strings? | Yes | Re-run this phase, 11/11 msgids captured under the correct text domain |
| 19 (WP) | Does uninstall remove every option/table/meta/event the plugin creates? | Yes — vacuously | Zero of any of those exist to remove (verified read-only, see above) |
| 20 (WP) | Every entry point checks capability and nonce? | Yes | The one entry point (`query_args()`) checks the WooCommerce nonce; render/enqueue run only inside WooCommerce's own already-capability-gated screen (`docs/security.md`) |

## Threat-model verification
`docs/threat-model.md` reviewed against the shipped code: every "Defended"
row's control is genuinely in the code (nonce check, regex+`checkdate()`
validation, `esc_attr`/`esc_html__` output, additive-only `$args` writes,
native pagination preserved) — moved from `TO BUILD` to verified this
session; delivery states updated accordingly. "Not defended" table unchanged
and still accurate for v1 (no new omission introduced).

## Accessibility verification results
Automated: `@axe-core/playwright`, scoped to this plugin's own two `<tr>`
rows, 0 critical/serious violations, both states (range hidden/shown) — see
`docs/accessibility.md` and `docs/05-test-points.md`. Real assistive-technology
pass: not yet performed, recorded honestly as a deferred item (optional,
non-gating for this surface per `docs/03-technical-plan.md`).

## Issues closed by this release
`docs/issues.md` does not exist yet — no forge issues have been opened or
worked on this project (first release).

## Token reconciliation
Not measurable from this environment (no per-session token counter exposed
by this tool). `docs/token-ledger.md` records this honestly as "estimated"
per session rather than a fabricated figure; a precise reconciliation is
deferred until the environment exposes real usage data.

## Release artifacts
- Distributable: `export-filters-for-woocommerce-1.0.0.zip` — built,
  installed fresh in the real wp-env playground, and verified working
  (see "Pre-release verification results" above). Not persisted to disk
  after this phase (build reproducibly with `git archive HEAD | tar -x -C
  <dir> export-filters-for-woocommerce && msgfmt ... && zip -r`).
- **Version: proposed 1.0.0** (first public release — no prior tag exists).
  Every touchpoint (`export-filters-for-woocommerce.php` header,
  `EFWC_VERSION` constant, `readme.txt` Stable tag) already reads `1.0.0`
  and agrees (`keel-verify`).
- **Not tagged, not merged to `main`, not published.** Per SKILL.md "Git
  flow" and the version-change discipline: this is the user's explicit act.
  `develop` is ready; `main`/`master` does not exist yet on this repository
  (fresh repo, `develop` was created from the unborn `main` at Phase 1) —
  the user's first release is also the first time `main` gets a commit.

## What remains, entirely the user's call
1. Say the word to merge `develop` → `main` (or push `develop` directly to
   `main` for a first release — either way, it's the action that makes this
   public) and tag `v1.0.0`.
2. Create the wordpress.org SVN presence (if that's the intended
   distribution channel) and upload the built ZIP + `readme.txt` there.
3. Decide whether to also publish a GitHub Release with the ZIP attached
   (`gh release create v1.0.0 <zip> --notes-from-tag` once tagged) — if so,
   verify the attachment afterward (`gh release view v1.0.0`), never assume
   it from the release page text.
