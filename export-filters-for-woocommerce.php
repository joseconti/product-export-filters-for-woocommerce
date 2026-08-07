<?php
/**
 * Plugin Name:       Export Filters for WooCommerce
 * Plugin URI:        https://plugins.joseconti.com/export-filters-for-woocommerce/
 * Description:       Adds filtering options to WooCommerce's native product exporter, starting with a date filter (created / last modified, with a from/to range).
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * WC requires at least: 8.0
 * WC tested up to:   11.0
 * Author:            José Conti
 * Author URI:        https://plugins.joseconti.com
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       export-filters-for-woocommerce
 * Domain Path:       /languages
 *
 * @package Export_Filters_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

define( 'EFWC_VERSION', '1.0.0' );
define( 'EFWC_PLUGIN_FILE', __FILE__ );
define( 'EFWC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Bootstraps the plugin once every other plugin has loaded, so the
 * WooCommerce-active check below is reliable regardless of load order.
 */
function efwc_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'efwc_missing_woocommerce_notice' );
		return;
	}

	require_once EFWC_PLUGIN_DIR . 'includes/class-efwc-date-filter.php';

	EFWC_Date_Filter::init();
}
add_action( 'plugins_loaded', 'efwc_init' );

/**
 * Admin notice shown when WooCommerce is missing or inactive. The plugin
 * never fatals in this case — it simply does nothing beyond this notice.
 */
function efwc_missing_woocommerce_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' .
		esc_html__( 'Export Filters for WooCommerce requires WooCommerce to be active. The plugin is currently doing nothing.', 'export-filters-for-woocommerce' ) .
		'</p></div>';
}
