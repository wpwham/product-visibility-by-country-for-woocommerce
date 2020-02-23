<?php
/**
 * Product Visibility by Country for WooCommerce - Settings
 *
 * @version 1.3.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_PVBC' ) ) :

class Alg_WC_Settings_PVBC extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.3.1
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_pvbc';
		$this->label = __( 'Product Visibility by Country', 'product-visibility-by-country-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );
		// Sections
		require_once( 'class-alg-wc-pvbc-settings-section.php' );
		require_once( 'class-alg-wc-pvbc-settings-general.php' );
		require_once( 'class-alg-wc-pvbc-settings-product-terms.php' );
		require_once( 'class-alg-wc-pvbc-settings-admin.php' );
		require_once( 'class-alg-wc-pvbc-settings-advanced.php' );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	function maybe_unsanitize_option( $value, $option, $raw_value ) {
		return ( ! empty( $option['alg_wc_pvbc_raw'] ) ? $raw_value : $value );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'product-visibility-by-country-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'product-visibility-by-country-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'product-visibility-by-country-for-woocommerce' ) . '</strong>',
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'product-visibility-by-country-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Settings_PVBC();
