<?php
/*
Plugin Name: Product Visibility by Country for WooCommerce
Plugin URI: https://wpwham.com/products/product-visibility-by-country-for-woocommerce/
Description: Display WooCommerce products by customer's country.
Version: 1.3.3
Author: WP Wham
Author URI: https://wpwham.com/
Text Domain: product-visibility-by-country-for-woocommerce
Domain Path: /langs
Copyright: © 2020 WP Wham
WC tested up to: 3.9
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC' ) ) :

/**
 * Main Alg_WC_PVBC Class
 *
 * @class   Alg_WC_PVBC
 * @version 1.3.1
 * @since   1.0.0
 */
final class Alg_WC_PVBC {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.3.3';

	/**
	 * @var   Alg_WC_PVBC The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_PVBC Instance
	 *
	 * Ensures only one instance of Alg_WC_PVBC is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_PVBC - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_PVBC Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Check for active plugins
		if (
			! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ||
			( 'product-visibility-by-country-for-woocommerce.php' === basename( __FILE__ ) && $this->is_plugin_active( 'product-visibility-by-country-for-woocommerce-pro/product-visibility-by-country-for-woocommerce-pro.php' ) )
		) {
			return;
		}

		// Set up localisation
		load_plugin_textdomain( 'product-visibility-by-country-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Pro
		if ( 'product-visibility-by-country-for-woocommerce-pro.php' === basename( __FILE__ ) ) {
			require_once( 'includes/pro/class-alg-wc-pvbc-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * is_plugin_active.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function includes() {
		// Core
		$this->core = require_once( 'includes/class-alg-wc-pvbc-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.3.1
	 * @since   1.1.0
	 */
	function admin() {
		// Admin functions
		require_once( 'includes/alg-wc-pvbc-admin-functions.php' );
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Meta boxes
		require_once( 'includes/settings/class-alg-wc-pvbc-metaboxes.php' );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_pvbc_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pvbc' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'product-visibility-by-country-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a href="https://wpwham.com/products/product-visibility-by-country-for-woocommerce/">' .
				__( 'Unlock All', 'product-visibility-by-country-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Product Visibility by Country settings tab to WooCommerce settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'includes/settings/class-alg-wc-settings-pvbc.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		update_option( 'alg_wc_pvbc_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_wc_pvbc' ) ) {
	/**
	 * Returns the main instance of Alg_WC_PVBC to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_PVBC
	 * @todo    [dev] (maybe) `plugins_loaded`
	 */
	function alg_wc_pvbc() {
		return Alg_WC_PVBC::instance();
	}
}

alg_wc_pvbc();
