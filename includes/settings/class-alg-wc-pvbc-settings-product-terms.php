<?php
/**
 * Product Visibility by Country for WooCommerce - Product Terms Section Settings
 *
 * @version 1.4.8
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Settings_Product_Terms' ) ) :

class Alg_WC_PVBC_Settings_Product_Terms extends Alg_WC_PVBC_Settings_Section {
	
	public $id   = '';
	public $desc = '';
	
	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'product_terms';
		$this->desc = __( 'Product Terms', 'product-visibility-by-country-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.4.8
	 * @since   1.2.0
	 */
	public static function get_settings() {
		$product_terms_settings = array(
			array(
				'title'    => __( 'Product Terms Options', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => sprintf( __( 'When enabled, this will add new options to category and tag edit pages in "%s" and "%s".', 'product-visibility-by-country-for-woocommerce' ),
					sprintf( '<a href="%s">%s > %s</a> > %s',
						admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ),
						__( 'Products', 'product-visibility-by-country-for-woocommerce' ),
						__( 'Categories', 'product-visibility-by-country-for-woocommerce' ),
						__( 'Category', 'product-visibility-by-country-for-woocommerce' )
					),
					sprintf( '<a href="%s">%s > %s</a> > %s',
						admin_url( 'edit-tags.php?taxonomy=product_tag&post_type=product' ),
						__( 'Products', 'product-visibility-by-country-for-woocommerce' ),
						__( 'Tags', 'product-visibility-by-country-for-woocommerce' ),
						__( 'Tag', 'product-visibility-by-country-for-woocommerce' )
					) ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_product_terms_options',
			),
			array(
				'title'    => __( 'Hide product terms', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you want to be able to hide product <strong>categories</strong> and <strong>tags</strong>.', 'product-visibility-by-country-for-woocommerce' ) .
					apply_filters( 'alg_wc_pvbc_settings', ' ' .
						sprintf( 'You will need <a href="%s" target="_blank">Product Visibility by Country for WooCommerce Pro</a> plugin to hide product terms.',
							'https://wpwham.com/products/product-visibility-by-country-for-woocommerce/?utm_source=settings_product_terms_hide&utm_campaign=free&utm_medium=product_visibility_country' ) ),
				'desc'     => '<strong>' . __( 'Enable', 'product-visibility-by-country-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_pvbc_product_terms',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_pvbc_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Hide products', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you want to additionally hide products in hidden terms.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( '"Hide product terms" option must be enabled.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_product_terms_products',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_product_terms_options',
			),
		);
		return $product_terms_settings;
	}

}

endif;

return new Alg_WC_PVBC_Settings_Product_Terms();
