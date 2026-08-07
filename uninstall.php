<?php
/**
 * Uninstall handler.
 *
 * This plugin stores no options, no custom tables, and no transients — it
 * only hooks into WooCommerce's native exporter for the duration of a
 * request. There is nothing to clean up beyond WordPress's own removal of
 * the plugin files. This file exists to document that explicitly, per
 * WordPress plugin convention.
 *
 * @package Export_Filters_For_WooCommerce
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
