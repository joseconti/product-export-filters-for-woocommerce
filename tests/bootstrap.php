<?php
/**
 * PHPUnit bootstrap for Export Filters for WooCommerce.
 *
 * Uses WordPress core's own test suite (available inside the wp-env
 * `tests-cli` container, per docs/03-technical-plan.md §Testing) plus
 * WooCommerce loaded as a plugin under test.
 *
 * @package Export_Filters_For_WooCommerce
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );
}

require_once $_tests_dir . '/includes/functions.php';

// EFWC_Date_Filter::query_args() only ever runs during a real WooCommerce
// export AJAX request (guarded by wp_doing_ajax()); the test suite simulates
// that context for the whole run, matching how the code is actually invoked
// in production.
if ( ! defined( 'DOING_AJAX' ) ) {
	define( 'DOING_AJAX', true );
}

/**
 * Loads WooCommerce, then this plugin, as if activated normally.
 */
function _efwc_manually_load_plugin() {
	// The installed folder name varies by install method (e.g.
	// "woocommerce" from a Composer/SVN checkout, "woocommerce.latest-stable"
	// from wp-env's zip-URL install) — matched by glob rather than hardcoded.
	$wc_candidates = glob( WP_CONTENT_DIR . '/plugins/woocommerce*/woocommerce.php' );
	if ( ! empty( $wc_candidates ) ) {
		require $wc_candidates[0];
	}
	require dirname( __DIR__ ) . '/export-filters-for-woocommerce.php';
}
tests_add_filter( 'muplugins_loaded', '_efwc_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
