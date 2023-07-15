<?php
/*
Plugin Name: Product Visibility by Country for WooCommerce
Plugin URI: https://wpwham.com/products/product-visibility-by-country-for-woocommerce/
Description: Display WooCommerce products by customer's country.
Version: 1.4.8
Author: WP Wham
Author URI: https://wpwham.com/
Text Domain: product-visibility-by-country-for-woocommerce
Domain Path: /langs
WC requires at least: 3.0
WC tested up to: 6.5
Copyright: © 2018-2022 WP Wham. All rights reserved.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPWHAM_PRODUCT_VISIBILITY_BY_COUNTRY_VERSION', '1.4.8' );
define( 'WPWHAM_PRODUCT_VISIBILITY_BY_COUNTRY_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/' );

if ( ! class_exists( 'Alg_WC_PVBC' ) ) :

/**
 * Main Alg_WC_PVBC Class
 *
 * @class   Alg_WC_PVBC
 * @version 1.4.8
 * @since   1.0.0
 */
final class Alg_WC_PVBC {
	
	public $core = null;
	
	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.4.8';

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
	 * @version 1.4.3
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
		add_action( 'woocommerce_system_status_report', array( $this, 'add_settings_to_status_report' ) );
		// Version update
		if ( get_option( 'alg_wc_pvbc_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.4.8
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pvbc' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'product-visibility-by-country-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a href="https://wpwham.com/products/product-visibility-by-country-for-woocommerce/?utm_source=plugins_page&utm_campaign=free&utm_medium=product_visibility_country">' .
				__( 'Unlock All', 'product-visibility-by-country-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add settings to WC status report
	 *
	 * @version 1.4.3
	 * @since   1.4.3
	 * @author  WP Wham
	 */
	public static function add_settings_to_status_report() {
		#region add_settings_to_status_report
		$protected_settings = array( 'wpwham_product_visibility_country_license' );
		$settings_general   = Alg_WC_PVBC_Settings_General::get_settings();
		$settings_admin     = Alg_WC_PVBC_Settings_Admin::get_settings();
		$settings_product   = Alg_WC_PVBC_Settings_Product_Terms::get_settings();
		$settings_advanced  = Alg_WC_PVBC_Settings_Advanced::get_settings();
		$settings = array_merge(
			$settings_general, $settings_admin, $settings_product, $settings_advanced
		);
		?>
		<table class="wc_status_table widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Product Visibility by Country Settings"><h2><?php esc_html_e( 'Product Visibility by Country Settings', 'product-visibility-by-country-for-woocommerce' ); ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $settings as $setting ): ?>
				<?php 
				if ( in_array( $setting['type'], array( 'title', 'sectionend' ) ) ) { 
					continue;
				}
				if ( isset( $setting['title'] ) ) {
					$title = $setting['title'];
				} elseif ( isset( $setting['desc'] ) ) {
					$title = $setting['desc'];
				} else {
					$title = $setting['id'];
				}
				$value = get_option( $setting['id'] ); 
				if ( in_array( $setting['id'], $protected_settings ) ) {
					$value = $value > '' ? '(set)' : 'not set';
				}
				?>
				<tr>
					<td data-export-label="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( $title, 'product-visibility-by-country-for-woocommerce' ); ?>:</td>
					<td class="help">&nbsp;</td>
					<td><?php echo is_array( $value ) ? print_r( $value, true ) : $value; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		#endregion add_settings_to_status_report
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
