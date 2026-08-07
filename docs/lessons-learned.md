# Lessons Learned — Export Filters for WooCommerce

> Append-only; never trim.

## L-001 — wp-env's zip-installed WooCommerce folder isn't named `woocommerce`
- Symptom: PHPUnit bootstrap requiring `wp-content/plugins/woocommerce/woocommerce.php`
  by a hardcoded path fatals with "file not found" inside the `tests-cli`
  container.
- Cause: `.wp-env.json` installing WooCommerce from its `latest-stable.zip` URL
  makes wp-env register/mount it under the folder name
  `woocommerce.latest-stable`, not `woocommerce` — the folder name depends on
  the install method (a Composer/SVN checkout would use `woocommerce`).
- Fix: `tests/bootstrap.php` locates it with
  `glob( WP_CONTENT_DIR . '/plugins/woocommerce*/woocommerce.php' )` instead
  of a hardcoded path.
- Where: Phase 5 scaffold, `tests/bootstrap.php`.
- What failed first: the hardcoded `wp-content/plugins/woocommerce/woocommerce.php`
  path — silently resulted in `class_exists( 'WooCommerce' )` being false and
  `EFWC_Date_Filter` never loading, surfaced as "Class 'EFWC_Date_Filter' not
  found" across every unit test rather than a clear "WooCommerce not found"
  message.
- Check added: none possible generically — this is inherent to how wp-env
  names install-method-dependent folders; the glob-based fix is itself the
  durable check.
- Rule for next time: never hardcode a plugin's install folder name in a test
  bootstrap when it can be installed multiple ways (zip URL vs. Composer vs.
  SVN) — glob for the entry file instead.

## L-002 — `checkdate()`/WP-Cron interaction is irrelevant to seeding a stable "future" status in a playground
- Symptom: an e2e assertion expecting all 60 seeded products' `date_created`
  to fall inside a fixed range returned 58, not 60.
- Cause: the seed script deliberately shifted `future`-status products'
  `date_created` by +1 month to "make them genuinely future" relative to real
  time — but that shift pushed exactly 2 of them (the ones seeded for the
  last month in the range) outside the test's asserted date window. The shift
  was unnecessary: WP's `future`→`publish` cron transition does not fire
  synchronously inside `wp eval-file`, so a past-dated `future`-status post
  stays `future` for the lifetime of the playground regardless.
- Fix: removed the +1-month shift in `tests/seed/seed-products.php` — every
  status uses the same seeded date, and `future` stays `future` in the DB
  without needing an artificially-future date.
- Where: Phase 5 scaffold, `tests/seed/seed-products.php`.
- What failed first: the shifted-date version of the seed script, caught by
  the real Playwright e2e run (`AC-08` test), not by inspection.
- Check added: none — the seed script itself is now the check (simpler code,
  no longer has the failure mode).
- Rule for next time: don't over-engineer synthetic seed data to mimic a
  real-world constraint (a genuinely future post date) the test doesn't
  actually depend on — it only adds a second variable that can silently shift
  the numbers an assertion relies on.

## L-003 — axe-core flags WooCommerce core's own Select2 widgets, not this plugin's markup
- Symptom: the accessibility e2e test failed with a "critical" `aria-allowed-attr`
  violation (`aria-expanded` not allowed on that ARIA role) when scanning the
  whole `.woocommerce-exporter-wrapper`.
- Cause: the violation's `target` selectors all pointed at
  `.select2-selection--multiple` elements — WooCommerce's own native Columns/
  Types/Category multi-selects, rendered by WooCommerce core's own Select2
  integration, which this plugin never touches.
- Fix: scoped the axe `include()` to only the two `<tr>` rows this plugin
  actually renders (`tr.efwc-export-date-field-row`, `tr.efwc-export-date-range`)
  instead of the whole export form. Confirmed by first running unscoped and
  reading the violation's `target` field before concluding it wasn't this
  plugin's defect.
- Where: Phase 5 scaffold, `tests/e2e/export-date-filter.spec.js`.
- What failed first: the unscoped `.include( '.woocommerce-exporter-wrapper' )`
  version — would have blocked every future test run on a pre-existing
  WooCommerce-core issue this plugin has no way to fix.
- Check added: the scoping itself; documented inline in the test with the
  reasoning so a future session doesn't "fix" it by widening the scope again.
- Rule for next time: an accessibility scan on a screen this plugin only adds
  a small amount of markup to is scoped to that markup, not the whole screen
  — a wider scope makes the test responsible for someone else's code and
  produces false "this plugin is broken" alarms.

## L-004 — a restricted script `PATH` misses tools this session's own shell can see
- Symptom: `scripts/keel-doctor`'s first draft reported both Composer and the
  `claude` CLI as `MISSING`, on the very machine actively running this Claude
  Code session (so `claude` obviously exists and runs).
- Cause: exactly the trap documented in Keel's own
  `references/keel-maintenance.md` ("env.PATH") — a script's `PATH` can lack
  `~/.local/bin` (where the native `claude` installer places its binary) even
  though the user's interactive login shell has it; separately, Composer was
  installed as `composer.phar` behind a shell alias (`alias composer='php
  /usr/local/bin/composer.phar'`), which a non-interactive script never sees
  since aliases aren't expanded outside an interactive shell.
- Fix: `scripts/keel-doctor` corroborates a negative `command -v` result
  against the known install locations (`~/.local/bin/claude`,
  `/usr/local/bin/composer.phar`) before reporting `MISSING`, per
  `references/test-automation.md` "Detection rules that are not obvious".
- Where: Phase 5 scaffold, `scripts/keel-doctor`.
- What failed first: the naive `command -v claude` / `command -v composer`
  checks — passed code review by inspection but failed the moment the script
  actually ran on this real machine.
- Check added: the corroboration itself, now permanent in the script.
- Rule for next time: never trust a single `command -v` result for a tool
  that has a documented alternate install location or is commonly aliased —
  check the known alternates before writing a row as `MISSING`, on this
  project and any other Keel project's doctor script.

## L-005 — `wp plugin uninstall` deleted the real repo files, not just the container's copy (CRITICAL)
- Symptom: while verifying the Phase 7 release-gate's install→uninstall→reinstall
  lifecycle, running `npx wp-env run cli wp plugin uninstall
  product-export-filters-for-woocommerce` inside the wp-env `cli` container
  emptied the entire working directory on the host machine — `git status`
  afterward reported "not a git repository" because `.git` itself was gone.
- Cause: `.wp-env.json` mounts the plugin as `"."` — a **bind mount** from
  this repository's real path into the container's
  `wp-content/plugins/product-export-filters-for-woocommerce/`, not a copy.
  `wp plugin uninstall` deletes the plugin's directory as part of its normal,
  documented behavior — inside the container that IS the host repository, so
  the deletion happened on disk, for real, immediately.
- Fix: recovered in full — nothing was lost because every change through
  that point was already committed and pushed to `origin/develop` (a remote
  git server is unaffected by a local filesystem deletion). Re-cloned into a
  temp path, verified the clone matched origin exactly, `rsync`'d it back
  into place, reinstalled the gitignored `node_modules`/`vendor` dirs.
- Where: Phase 7, pre-release real-environment lifecycle verification.
- What failed first: treating `wp plugin uninstall` as a container-scoped,
  reversible operation because it *looks* like ordinary WP-CLI plugin
  management — the bind-mount fact was known (it's in `.wp-env.json`,
  written by this same project) but not connected to the risk of a
  file-deleting command before running it.
- Check added: none possible mechanically inside wp-env itself; the rule
  below is the check, and it is now written down so it is never
  re-discovered by running the command again.
- Rule for next time (UNBREAKABLE for any Keel WordPress-plugin project
  using wp-env with a bind-mounted plugin, i.e. `"."` in `.wp-env.json`'s
  `plugins`/`themes` list): **never run a WP-CLI command whose documented
  behavior deletes files** (`wp plugin uninstall`, `wp plugin delete`, `wp
  theme delete`, and equivalents) **against a bind-mounted path** — it
  deletes the real files on the host, not a container-local copy. To verify
  an uninstall lifecycle safely instead: (a) analytically confirm what the
  plugin would clean up by inspecting `uninstall.php` and searching for any
  options/transients/tables/scheduled events the plugin actually creates
  (`wp option list --search=<prefix>*`, etc. — read-only, safe), or (b) if a
  literal `wp plugin uninstall` run is genuinely needed, do it against a
  **copy** installed into a disposable environment that is NOT bind-mounted
  from the working repository (e.g. install the built distributable ZIP into
  a throwaway wp-env instance with no source mount, or a temporary
  `WP_CONTENT_DIR` copy) — never against the path the assistant is actively
  editing. This applies to every current and future Keel WordPress project
  on this machine, not only this one.
