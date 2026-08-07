# Playground — Export Filters for WooCommerce

> Access details + try-it instructions. Local, throwaway credentials only —
> never production secrets. Last verified: 2026-08-07 (real run, see
> `docs/05-test-points.md`).

## What it is
A local `wp-env` instance (Docker) with WordPress + this plugin + WooCommerce
installed and active, plus a synthetic seed catalog of 60 products spread
across dates and statuses.

## Start / stop / reset
```
npm install                 # once, installs wp-env + Playwright + axe
npx wp-env start             # boots WordPress at http://localhost:8888
npx wp-env stop               # stops the containers (keeps data)
npx wp-env clean all          # wipes the database back to empty
npx wp-env destroy            # full teardown (containers + volumes)
```

## Seed data
```
npx wp-env run cli wp eval-file wp-content/plugins/product-export-filters-for-woocommerce/tests/seed/seed-products.php
```
Creates 60 synthetic products spread across six months (2026-01 to 2026-06)
and five statuses (mostly `publish`, some `draft`, some `future`) — enough to
force a multi-batch export at WooCommerce's default 50-row batch size.

## Try it yourself
1. `npx wp-env start`, then seed the catalog (command above).
2. Open http://localhost:8888/wp-admin/ — user `admin`, password `password`
   (wp-env's own default local credentials, not a production secret).
3. Go to **Products > Export**.
4. Try: leave "Filter by date" on "All dates" and generate a CSV — every
   seeded product exports. Then pick "Date created", set From/To to
   `2026-03-01`/`2026-03-27`, and generate again — only March-dated products
   export. Use the same date in both fields to try a single-day export.

## Automated verification
```
npx wp-env run tests-cli --env-cwd=wp-content/plugins/product-export-filters-for-woocommerce phpunit   # unit tests
npx playwright test                                                                                     # e2e (headless, trace+video on)
vendor/bin/phpcs --standard=phpcs.xml.dist                                                              # coding standard
```
All three are green as of the date above — see `docs/05-test-points.md` for
the exact commands run and their real output.
