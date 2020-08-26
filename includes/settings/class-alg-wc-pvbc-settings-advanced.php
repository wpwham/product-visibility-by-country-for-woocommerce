<?php
/**
 * Product Visibility by Country for WooCommerce - Advanced Section Settings
 *
 * @version 1.3.1
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Settings_Advanced' ) ) :

class Alg_WC_PVBC_Settings_Advanced extends Alg_WC_PVBC_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'product-visibility-by-country-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.3.1
	 * @since   1.2.0
	 * @todo    [dev] remove all `alg_wc_pvbc_modify_` options
	 */
	public static function get_settings() {
		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'product-visibility-by-country-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_advanced_options',
			),
			array(
				'title'    => __( 'Debug mode', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will add log to %s.', 'product-visibility-by-country-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' .
						__( 'WooCommerce > Status > Logs', 'product-visibility-by-country-for-woocommerce' ) . '</a>' ) . ' ' .
					__( 'Do not enable unless there are any issues with the plugin.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_debug_mode',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Disable URL', 'product-visibility-by-country-for-woocommerce' ),
				'desc_tip' => __( 'You can disable plugin on some URLs of your site. One URL per line.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'Ignored if empty.', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'product-visibility-by-country-for-woocommerce' ),
					'<code>/?woocommerce_gpf=google&feed_currency=GBP&feed_country=GB</code>' ),
				'id'       => 'alg_wc_pvbc_disable_url',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;',
				'alg_wc_pvbc_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_advanced_options',
			),
			array(
				'title'    => __( '"Modify query" Optimization Options', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Affects "Modify query" option only.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'Do not change settings unless there are any issues with the plugin.', 'product-visibility-by-country-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pvbc_advanced_modify_query_options',
			),
			array(
				'title'    => __( 'Queries', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_modify_query_only_main_query',
				'default'  => 'yes',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'yes'        => __( 'Check main query only', 'product-visibility-by-country-for-woocommerce' ),
					'and_search' => __( 'Check main and search queries only', 'product-visibility-by-country-for-woocommerce' ),
					'no'         => __( 'Check all queries', 'product-visibility-by-country-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Check post type', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_modify_query_check_post_type',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use simple redirect', 'product-visibility-by-country-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-visibility-by-country-for-woocommerce' ),
				'id'       => 'alg_wc_pvbc_simple_redirect',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pvbc_advanced_modify_query_options',
			),
		);
		return $advanced_settings;
	}

}

endif;

return new Alg_WC_PVBC_Settings_Advanced();
