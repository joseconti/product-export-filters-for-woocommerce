<?php
/**
 * Seeds synthetic WooCommerce products spread across dates and statuses, for
 * the wp-env playground. Run with:
 *   wp-env run cli wp eval-file wp-content/plugins/export-filters-for-woocommerce/tests/seed/seed-products.php
 *
 * Creates enough products to force a multi-batch export at WooCommerce's
 * default batch size of 50 (abstract-wc-csv-exporter.php $limit — AC-08),
 * across publish/draft/future statuses (AC-09),
 * with post_date and post_modified spread across several months so date
 * filtering has real, verifiable buckets to select.
 *
 * @package Export_Filters_For_WooCommerce
 */

if ( ! defined( 'WP_CLI' ) ) {
	echo "Run this via WP-CLI (wp eval-file), not directly.\n";
	exit( 1 );
}

$statuses = array( 'publish', 'publish', 'publish', 'draft', 'future' );
$months   = array( '2026-01', '2026-02', '2026-03', '2026-04', '2026-05', '2026-06' );

$created = 0;

foreach ( range( 1, 60 ) as $i ) {
	$status = $statuses[ $i % count( $statuses ) ];
	$month  = $months[ $i % count( $months ) ];
	$day    = str_pad( (string) ( 1 + ( $i % 27 ) ), 2, '0', STR_PAD_LEFT );
	$date   = "{$month}-{$day} 10:00:00";

	$product = new WC_Product_Simple();
	$product->set_name( "Seed product {$i}" );
	$product->set_regular_price( '9.99' );
	$product->set_status( $status );
	// A 'future' status product keeps the same seeded date as any other
	// status here — this seed only needs the STATUS to be 'future' so
	// AC-09 can assert it is included in a date-filtered export, not for
	// the date to be genuinely in the future relative to real time. WP's
	// own future-post cron (which would otherwise flip a past-dated
	// 'future' post to 'publish') does not run synchronously in this
	// playground, so the status is stable for the test's lifetime.
	$product->set_date_created( strtotime( $date ) );

	$id = $product->save();

	// Spread date_modified independently for a subset, so "Date last
	// modified" filtering has its own distinct buckets from "Date created".
	if ( 0 === $i % 3 ) {
		wp_update_post(
			array(
				'ID'            => $id,
				'post_modified' => "{$month}-{$day} 15:30:00",
			)
		);
	}

	++$created;
}

WP_CLI::success( "Seeded {$created} products across statuses and dates for the export-date-filter playground." );
