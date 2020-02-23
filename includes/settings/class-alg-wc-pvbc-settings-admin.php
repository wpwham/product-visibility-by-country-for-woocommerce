<?php
/**
 * Product Visibility by Country for WooCommerce - Admin Section Settings
 *
 * @version 1.2.0
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Settings_Admin' ) ) :

class Alg_WC_PVBC_Settings_Admin extends Alg_WC_PVBC_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'admin';
		$this->desc = __( 'Admin', 'product-visibility-by-country-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function get_settings() {
		$admin_settings = array(
			array(
				'title'    => __( 'Admin Options', 'product-visibility-by-country-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_admin_options',
			),
			array(
				'title'    => __( 'Admin products list column', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will add "Countries" column to the admin products list.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Add', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_add_column_visible_countries',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Select box type', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'Select box type on product\'s edit page and on term\'s edit page.', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_select_box_type',
				'default'  => 'chosen_select',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'standard'      => __( 'Standard', 'product-visibility-by-country-for-woocommerce' ),
					'chosen_select' => __( 'Chosen select', 'product-visibility-by-country-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_admin_options',
			),
		);
		return $admin_settings;
	}

}

endif;

return new Alg_WC_PVBC_Settings_Admin();
