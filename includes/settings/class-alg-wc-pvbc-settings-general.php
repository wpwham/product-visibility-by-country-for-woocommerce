<?php
/**
 * Product Visibility by Country for WooCommerce - General Section Settings
 *
 * @version 1.4.3
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Settings_General' ) ) :

class Alg_WC_PVBC_Settings_General extends Alg_WC_PVBC_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'product-visibility-by-country-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.4.3
	 * @since   1.0.0
	 */
	public static function get_settings() {
		$main_settings = array(
			array(
				'title'    => __( 'Product Visibility by Country Options', 'product-visibility-by-country-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_options',
				'desc'     => sprintf( __( 'Please note that if you have any <strong>caching</strong> plugins installed, you will need to set "Geolocate (with caching support)" option to "Default customer location" in %s.', 'product-visibility-by-country-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=general' ) . '">' .
						__( 'WooCommerce > Settings > General', 'product-visibility-by-country-for-woocommerce' ) . '</a>' ),
			),
			array(
				'title'    => __( 'Product Visibility by Country', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'product-visibility-by-country-for-woocommerce' ) . '</strong>',
				'desc_tip' =>
					__( 'Product Visibility by Country for WooCommerce', 'product-visibility-by-country-for-woocommerce' )
					. ' v' . WPWHAM_PRODUCT_VISIBILITY_BY_COUNTRY_VERSION . '.<br />'
					. '<a href="https://wpwham.com/documentation/product-visibility-by-country-for-woocommerce/?utm_source=documentation_link&utm_campaign=free&utm_medium=product_visibility_country" target="_blank" class="button">' .
					__( 'Documentation', 'product-visibility-by-country-for-woocommerce' ) . '</a>',
				'id'       => 'alg_wc_pvbc_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_options',
			),
		);
		$general_settings = array(
			array(
				'title'    => __( 'General Options', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'To set countries for each product, check "Product Visibility by Country" meta box on each product\'s edit page.', 'product-visibility-by-country-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_general_options',
			),
			array(
				'title'    => __( 'Hide catalog visibility', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will hide selected products in shop and search results. However product still will be accessible via direct link.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_visibility',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Make non-purchasable', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will make selected products non-purchasable (i.e. product can\'t be added to the cart).', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_purchasable',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Hide visibility in "WooCommerce Blocks"', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will hide selected products in blocks created with "WooCommerce Blocks" plugin.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_blocks',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Modify query', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will hide selected products completely (including direct link).', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_query',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc_tip' => __( 'Optionally set URL for hidden products to redirect to (i.e. different from 404 page).', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					sprintf( __( 'Enter full URL (i.e. with %s).', 'product-visibility-by-country-for-woocommerce' ), '<em>http(s)://</em>' ) . ' ' .
					__( '"Modify query" option must be enabled.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'Ignored if empty.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Redirect URL.', 'product-visibility-by-country-for-woocommerce' ) .
					apply_filters( 'alg_wc_pvbc_settings', ' ' .
						sprintf( 'You will need <a href="%s" target="_blank">Product Visibility by Country for WooCommerce Pro</a> plugin to customize redirect URL.',
							'https://wpwham.com/products/product-visibility-by-country-for-woocommerce/' ) ),
				'id'       => 'alg_wc_pvbc_query_redirect_url',
				'default'  => '',
				'type'     => 'text',
				'css'      => 'width:100%',
				'custom_attributes' => apply_filters( 'alg_wc_pvbc_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'desc_tip' => __( 'Enable this if you are still seeing hidden products in "Products" widgets.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Modify widget query', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_query_widget',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Hide price', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will hide prices for selected products.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Hide', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_hide_price',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'alg_wc_pvbc_hide_price_content',
				'desc'     => __( 'Content', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'Set this if you wish to replace price with some message. Can be empty.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'You can use HTML and/or shortcodes here.', 'product-visibility-by-country-for-woocommerce' ),
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;',
				'alg_wc_pvbc_raw' => true,
			),
			array(
				'title'    => __( 'Info on single product page', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will output message on single product page for selected products.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Add', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_info_on_single_product',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'alg_wc_pvbc_info_on_single_product_content',
				'desc'     => __( 'Content', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'You can use HTML and/or shortcodes here.', 'product-visibility-by-country-for-woocommerce' ),
				'default'  => '<p><strong>' . __( 'The product is not available in your country.', 'product-visibility-by-country-for-woocommerce' ) . '</strong></p>',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;',
				'alg_wc_pvbc_raw' => true,
			),
			array(
				'title'    => __( 'Info on archives', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'This will output message on archives for selected products.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Add', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_info_on_loop',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'alg_wc_pvbc_info_on_loop_content',
				'desc'     => __( 'Content', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'You can use HTML and/or shortcodes here.', 'product-visibility-by-country-for-woocommerce' ),
				'default'  => '<p><strong>' . __( 'The product is not available in your country.', 'product-visibility-by-country-for-woocommerce' ) . '</strong></p>',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;',
				'alg_wc_pvbc_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_general_options',
			),
		);
		return array_merge( $main_settings, $general_settings );
	}

}

endif;

return new Alg_WC_PVBC_Settings_General();
