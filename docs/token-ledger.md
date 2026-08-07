# Token Ledger — Export Filters for WooCommerce

> Actual token usage. One row per working session, appended at session end.
> Method is always stated: measured (environment counter, API usage, provider dashboard)
> or estimated (volume-based). An honest estimate beats an empty cell.

## Sessions
| Date | Phase/sprint | Model(s) | Input tokens | Output tokens | Method | Notes |
|------|--------------|----------|--------------|---------------|--------|-------|
| 2026-08-07 | Phase 1 — Discovery | claude-sonnet-5 | not exposed by this environment | not exposed by this environment | estimated (session length) | Competitive scan, discovery doc, state files, portability lock; one subagent research call included |
| 2026-08-07 | Phase 2 (spec/plan/threat-model) + Phase 5 scaffold, Sprint 1, Phase 6, Phase 7 candidate prep | claude-sonnet-5 | not exposed by this environment | not exposed by this environment | estimated (session length — one long continuous session) | Real wp-env playground stood up multiple times; full automated suite run repeatedly; package built and installed live; L-005 incident and recovery |
| 2026-08-07 | Assistant config package (rules, agents incl. security-auditor, permissions, pre-commit gate, CI) + WP-CLI export-path research | claude-sonnet-5 | not exposed by this environment | not exposed by this environment | estimated (session length) | Live security audit run inline; pre-commit gate verified with a synthetic secret |

Running total: not precisely measured across any session — this environment
(Claude Code, this subscription) does not expose a per-session token counter.
Every row is honestly marked "estimated," never fabricated as a number.

## Final reconciliation (at release — Phase 7)
- Total tokens by model: not measurable from this environment — no per-session
  or cumulative counter is exposed here. Recorded honestly as unavailable
  rather than invented.
- Cost at verified prices: n/a — subscription mode (Claude Code), ≈0 marginal
  cost per session; no per-token billing applies to this project.
- Estimate vs actual: cannot be computed in AI-hours-vs-tokens terms without a
  counter; qualitatively, actual session time ran longer than the Phase 1/2
  preliminary-to-firm estimate (7–12h then 7–9.5h AI-time), mainly because of
  the real playground verification loop (multiple wp-env stand-ups, the L-005
  incident and recovery, live install testing of the actual distributable)
  and the assistant-config package added after the original estimate's scope.
- Lesson for future estimates: real-environment verification loops (stand up
  → seed → test → tear down, repeated across phases) and incident recovery
  are real time that a pre-Phase-5 estimate cannot fully anticipate — future
  estimates for WordPress-plugin projects should pad the "Phase 5 test
  points" line specifically for this, not just the implementation line.
