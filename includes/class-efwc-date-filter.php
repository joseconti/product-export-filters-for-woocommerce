<?php
/**
 * Date filter for WooCommerce's native product exporter.
 *
 * Ported and renamed from the verified reference implementation in
 * docs/filtros-exportador-woocommerce.md §6 (D-008). Extends the exporter via
 * its own hooks — never replaces it, never duplicates its batching, column,
 * or download logic.
 *
 * @package Export_Filters_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class EFWC_Date_Filter.
 */
class EFWC_Date_Filter {

	const FIELD_TYPE = 'efwc_date_field';
	const FIELD_FROM = 'efwc_date_from';
	const FIELD_TO   = 'efwc_date_to';

	/**
	 * Registers every hook this class needs.
	 */
	public static function init() {
		add_action( 'woocommerce_product_export_row', array( __CLASS__, 'render_rows' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ), 20 );
		add_filter( 'woocommerce_product_export_product_query_args', array( __CLASS__, 'query_args' ) );
	}

	/**
	 * Renders the two extra rows on Products > Export. The hook fires inside
	 * <tbody>, so every echo here must be a <tr>. Every input carries a
	 * name= attribute: WooCommerce's own JS serializes the whole form and
	 * resends it unchanged on every AJAX batch, so the filter value persists
	 * across the entire export with no transient or session needed.
	 */
	public static function render_rows() {
		?>
		<tr class="efwc-export-date-field-row">
			<th scope="row">
				<label for="efwc-export-date-field"><?php esc_html_e( 'Filter by date', 'export-filters-for-woocommerce' ); ?></label>
			</th>
			<td>
				<select id="efwc-export-date-field" name="<?php echo esc_attr( self::FIELD_TYPE ); ?>" style="width:100%;max-width:400px;">
					<option value=""><?php esc_html_e( 'All dates', 'export-filters-for-woocommerce' ); ?></option>
					<option value="date_created"><?php esc_html_e( 'Date created', 'export-filters-for-woocommerce' ); ?></option>
					<option value="date_modified"><?php esc_html_e( 'Date last modified', 'export-filters-for-woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="efwc-export-date-range" style="display:none;">
			<th scope="row">
				<label for="efwc-export-date-from"><?php esc_html_e( 'Date range', 'export-filters-for-woocommerce' ); ?></label>
			</th>
			<td>
				<input type="date" id="efwc-export-date-from" name="<?php echo esc_attr( self::FIELD_FROM ); ?>" />
				<span aria-hidden="true">&mdash;</span>
				<input type="date" id="efwc-export-date-to" name="<?php echo esc_attr( self::FIELD_TO ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Both ends are included. Use the same date in both fields to export a single day. Leaving one empty keeps that side of the range open.', 'export-filters-for-woocommerce' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Shows/hides the date-range row only. Priority 20: WooCommerce
	 * registers 'wc-product-export' in admin_enqueue_scripts at priority 10,
	 * so this always runs after the handle exists.
	 *
	 * @param string $hook The current admin screen hook suffix.
	 */
	public static function enqueue( $hook ) {
		if ( 'product_page_product_exporter' !== $hook ) {
			return;
		}

		wp_add_inline_script(
			'wc-product-export',
			"jQuery( function ( $ ) {
				var \$field = $( '#efwc-export-date-field' ),
					\$range = $( '.efwc-export-date-range' );

				function efwcToggle() {
					\$range.toggle( '' !== \$field.val() );
				}

				\$field.on( 'change', efwcToggle );
				efwcToggle();
			} );"
		);
	}

	/**
	 * Translates the selection into a query var for wc_get_products().
	 *
	 * @param array $args The export query args being built.
	 * @return array
	 */
	public static function query_args( $args ) {

		if ( ! wp_doing_ajax() || empty( $_POST['form'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $args;
		}

		// check_ajax_referer() already ran in do_ajax_product_export(), but
		// this filter can fire from other contexts. Nothing is assumed here.
		if ( empty( $_POST['security'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'wc-product-export' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $args;
		}

		// The nonce is already verified above; every individual value pulled
		// out of $form below is sanitized at its own point of use
		// (sanitize_key() on the field selector, sanitize_text_field() plus
		// strict date validation in clean_date()) — phpcs cannot see that
		// downstream sanitization from this line alone.
		parse_str( wp_unslash( $_POST['form'] ), $form ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$field = isset( $form[ self::FIELD_TYPE ] ) ? sanitize_key( $form[ self::FIELD_TYPE ] ) : '';

		if ( ! in_array( $field, array( 'date_created', 'date_modified' ), true ) ) {
			return $args;
		}

		$from = self::clean_date( isset( $form[ self::FIELD_FROM ] ) ? $form[ self::FIELD_FROM ] : '' );
		$to   = self::clean_date( isset( $form[ self::FIELD_TO ] ) ? $form[ self::FIELD_TO ] : '' );

		if ( ! $from && ! $to ) {
			return $args;
		}

		// Swap reversed input instead of returning zero rows.
		if ( $from && $to && $from > $to ) {
			list( $from, $to ) = array( $to, $from );
		}

		if ( $from && $to ) {
			$args[ $field ] = $from . '...' . $to;
		} elseif ( $from ) {
			$args[ $field ] = '>=' . $from;
		} else {
			$args[ $field ] = '<=' . $to;
		}

		return $args;
	}

	/**
	 * Validates YYYY-MM-DD and that the date genuinely exists.
	 * parse_date_for_wp_query()'s regex does not accept dots, so any other
	 * format would silently fail downstream — this rejects it explicitly
	 * instead, treating it as empty.
	 *
	 * @param string $value Raw date string from the request.
	 * @return string The validated date, or '' if invalid.
	 */
	public static function clean_date( $value ) {
		$value = sanitize_text_field( $value );

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) ) {
			return '';
		}

		return checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ? $value : '';
	}
}
