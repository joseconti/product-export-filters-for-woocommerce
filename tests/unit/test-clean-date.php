<?php
/**
 * Unit tests for EFWC_Date_Filter::clean_date().
 *
 * Covers AC-07 (invalid date silently dropped) and the SQL-injection-shaped
 * input row in docs/threat-model.md's "Defended" table.
 *
 * @package Export_Filters_For_WooCommerce
 */

class Test_EFWC_Clean_Date extends WP_UnitTestCase {

	public function test_accepts_a_valid_date() {
		$this->assertSame( '2026-03-15', EFWC_Date_Filter::clean_date( '2026-03-15' ) );
	}

	public function test_rejects_an_empty_string() {
		$this->assertSame( '', EFWC_Date_Filter::clean_date( '' ) );
	}

	public function test_rejects_a_calendar_impossible_date() {
		// AC-07: 30 February does not exist.
		$this->assertSame( '', EFWC_Date_Filter::clean_date( '2026-02-30' ) );
	}

	public function test_rejects_dotted_format() {
		// docs/spec-references/filtros-exportador-woocommerce.md §4.3: parse_date_for_wp_query()'s
		// regex does not accept dots.
		$this->assertSame( '', EFWC_Date_Filter::clean_date( '01.02.2026' ) );
	}

	public function test_rejects_malformed_garbage() {
		$this->assertSame( '', EFWC_Date_Filter::clean_date( 'not-a-date' ) );
	}

	public function test_rejects_sql_injection_shaped_input() {
		// docs/threat-model.md "Defended": SQL-injection-shaped strings never
		// reach the query — the regex gate rejects them before checkdate().
		$this->assertSame( '', EFWC_Date_Filter::clean_date( "2026-01-01' OR '1'='1" ) );
	}

	public function test_accepts_leap_day() {
		$this->assertSame( '2028-02-29', EFWC_Date_Filter::clean_date( '2028-02-29' ) );
	}

	public function test_rejects_non_leap_year_feb_29() {
		$this->assertSame( '', EFWC_Date_Filter::clean_date( '2026-02-29' ) );
	}
}
