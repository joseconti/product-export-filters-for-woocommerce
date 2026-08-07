<?php
/**
 * Unit tests for EFWC_Date_Filter::query_args().
 *
 * Covers AC-03–06 (range building, single day, open ends, swap-on-inversion)
 * and AC-11 (fails open without a valid nonce / outside an AJAX context).
 *
 * @package Export_Filters_For_WooCommerce
 */

class Test_EFWC_Query_Args extends WP_UnitTestCase {

	private $original_post;

	public function set_up() {
		parent::set_up();
		$this->original_post = $_POST;
	}

	public function tear_down() {
		$_POST = $this->original_post;
		parent::tear_down();
	}

	private function post_form( $field, $from = '', $to = '' ) {
		$form = array(
			EFWC_Date_Filter::FIELD_TYPE => $field,
			EFWC_Date_Filter::FIELD_FROM => $from,
			EFWC_Date_Filter::FIELD_TO   => $to,
		);
		$_POST['form']     = http_build_query( $form );
		$_POST['security'] = wp_create_nonce( 'wc-product-export' );
	}

	public function test_all_dates_leaves_args_unchanged_ac01() {
		$this->post_form( '' );
		$args = EFWC_Date_Filter::query_args( array( 'limit' => 10 ) );
		$this->assertArrayNotHasKey( 'date_created', $args );
		$this->assertArrayNotHasKey( 'date_modified', $args );
	}

	public function test_same_from_and_to_builds_single_day_range_ac03() {
		$this->post_form( 'date_created', '2026-03-15', '2026-03-15' );
		$args = EFWC_Date_Filter::query_args( array() );
		$this->assertSame( '2026-03-15...2026-03-15', $args['date_created'] );
	}

	public function test_only_from_builds_open_ended_range_ac04() {
		$this->post_form( 'date_modified', '2026-03-01', '' );
		$args = EFWC_Date_Filter::query_args( array() );
		$this->assertSame( '>=2026-03-01', $args['date_modified'] );
	}

	public function test_only_to_builds_open_ended_range_ac05() {
		$this->post_form( 'date_modified', '', '2026-03-31' );
		$args = EFWC_Date_Filter::query_args( array() );
		$this->assertSame( '<=2026-03-31', $args['date_modified'] );
	}

	public function test_reversed_from_to_is_swapped_ac06() {
		$this->post_form( 'date_created', '2026-03-31', '2026-03-01' );
		$args = EFWC_Date_Filter::query_args( array() );
		$this->assertSame( '2026-03-01...2026-03-31', $args['date_created'] );
	}

	public function test_invalid_date_dropped_not_fatal_ac07() {
		$this->post_form( 'date_created', '2026-02-30', '2026-03-31' );
		$args = EFWC_Date_Filter::query_args( array() );
		// 'from' was invalid and dropped -> treated as "only To".
		$this->assertSame( '<=2026-03-31', $args['date_created'] );
	}

	public function test_missing_nonce_fails_open_ac11() {
		$_POST['form'] = http_build_query(
			array( EFWC_Date_Filter::FIELD_TYPE => 'date_created' )
		);
		unset( $_POST['security'] );
		$args = EFWC_Date_Filter::query_args( array( 'limit' => 10 ) );
		$this->assertSame( array( 'limit' => 10 ), $args );
	}

	public function test_invalid_nonce_fails_open_ac11() {
		$_POST['form']     = http_build_query(
			array( EFWC_Date_Filter::FIELD_TYPE => 'date_created' )
		);
		$_POST['security'] = 'not-a-real-nonce';
		$args               = EFWC_Date_Filter::query_args( array( 'limit' => 10 ) );
		$this->assertSame( array( 'limit' => 10 ), $args );
	}

	public function test_unrecognized_field_value_is_ignored() {
		// A tampered/unexpected value in the closed set is treated as "all dates".
		$this->post_form( 'something_else' );
		$args = EFWC_Date_Filter::query_args( array( 'limit' => 10 ) );
		$this->assertSame( array( 'limit' => 10 ), $args );
	}
}
