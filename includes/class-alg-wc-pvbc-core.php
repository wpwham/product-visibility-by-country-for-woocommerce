<?php
/**
 * Product Visibility by Country for WooCommerce - Core Class
 *
 * @version 1.3.2
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Core' ) ) :

class Alg_WC_PVBC_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.3.1
	 * @since   1.0.0
	 * @todo    [dev] back-end: product terms: add fields to "add category/tag" pages also (i.e. not only to "edit" pages)
	 * @todo    [dev] Export/Import: maybe add option to enable/disable these filters
	 * @todo    [dev] Export/Import: maybe check for AJAX (i.e. not just frontend)
	 * @todo    [feature] hide description
	 * @todo    [feature] add quick and bulk edit options
	 * @todo    [feature] add option to hide menu items
	 * @todo    [feature] add option to set visibility per *variation* (#12048)
	 * @todo    [feature] add option to set visibility by state, i.e. Geo IP by state (not available in WC: need "GeoLite2 City") (#13108)
	 */
	function __construct() {
		$this->is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		$this->is_debug              = ( 'yes' === get_option( 'alg_wc_pvbc_debug_mode', 'no' ) );
		if ( 'yes' === get_option( 'alg_wc_pvbc_enabled', 'yes' ) ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				if ( ! $this->do_disable_url() ) {
					// Front-end: Products
					if ( 'yes' === get_option( 'alg_wc_pvbc_visibility', 'yes' ) ) {
						add_filter( 'woocommerce_product_is_visible',               array( $this, 'product_by_country_visibility' ), PHP_INT_MAX, 2 );
					}
					if ( 'yes' === get_option( 'alg_wc_pvbc_purchasable', 'no' ) ) {
						add_filter( 'woocommerce_is_purchasable',                   array( $this, 'product_by_country_purchasable' ), PHP_INT_MAX, 2 );
					}
					if ( 'yes' === get_option( 'alg_wc_pvbc_blocks', 'no' ) ) {
						add_filter( 'woocommerce_blocks_product_grid_item_html',    array( $this, 'product_by_country_blocks' ), PHP_INT_MAX, 3 );
					}
					if ( 'yes' === get_option( 'alg_wc_pvbc_query', 'no' ) ) {
						$this->do_modify_query_only_main_query            = ( 'yes'        === get_option( 'alg_wc_pvbc_modify_query_only_main_query', 'yes' ) );
						$this->do_modify_query_only_main_query_and_search = ( 'and_search' === get_option( 'alg_wc_pvbc_modify_query_only_main_query', 'yes' ) );
						$this->do_modify_query_check_post_type            = ( 'yes'        === get_option( 'alg_wc_pvbc_modify_query_check_post_type', 'yes' ) );
						add_action( 'pre_get_posts',                                array( $this, 'product_by_country_pre_get_posts' ) );
						if ( 'yes' === get_option( 'alg_wc_pvbc_query_widget', 'no' ) ) {
							add_filter( 'woocommerce_products_widget_query_args',   array( $this, 'product_by_country_widget_query' ), PHP_INT_MAX );
						}
						do_action( 'alg_wc_pvbc_core_frontend_modify_query_loaded', $this );
					} else {
						// Price
						if ( 'yes' === get_option( 'alg_wc_pvbc_hide_price', 'no' ) ) {
							add_filter( 'woocommerce_get_price_html',               array( $this, 'product_by_country_price_html' ), PHP_INT_MAX, 2 );
							add_filter( 'woocommerce_product_get_price',            array( $this, 'product_by_country_price' ),      PHP_INT_MAX, 2 );
						}
						// Info
						if ( 'yes' === get_option( 'alg_wc_pvbc_info_on_single_product', 'no' ) ) {
							add_action( 'woocommerce_single_product_summary',       array( $this, 'add_message_on_single_product' ), 31 );
						}
						if ( 'yes' === get_option( 'alg_wc_pvbc_info_on_loop', 'no' ) ) {
							add_action( 'woocommerce_after_shop_loop_item',         array( $this, 'add_message_on_loop' ), 11 );
						}
						add_shortcode( 'alg_wc_pvbc_translate',                     array( $this, 'language_shortcode' ) );
					}
					// Export/Import
					add_filter( 'woocommerce_product_export_meta_value',            array( $this, 'product_export_meta_value' ),    PHP_INT_MAX, 4 );
					add_filter( 'woocommerce_product_importer_parsed_data',         array( $this, 'product_importer_parsed_data' ), PHP_INT_MAX, 2 );
					// Frontend loaded
					do_action( 'alg_wc_pvbc_core_frontend_loaded', $this );
				}
			} else {
				// Back-end: Admin products list
				if ( 'yes' === get_option( 'alg_wc_pvbc_add_column_visible_countries', 'no' ) ) {
					add_filter( 'manage_edit-product_columns',                  array( $this, 'add_product_columns' ),   PHP_INT_MAX );
					add_action( 'manage_product_posts_custom_column',           array( $this, 'render_product_column' ), PHP_INT_MAX );
				}
				// Backend loaded
				do_action( 'alg_wc_pvbc_core_backend_loaded', $this );
			}
		}
		do_action( 'alg_wc_pvbc_core_loaded', $this );
	}

	/**
	 * do_disable_url.
	 *
	 * @version 1.3.1
	 * @since   1.3.1
	 */
	function do_disable_url() {
		if ( '' != ( $url = get_option( 'alg_wc_pvbc_disable_url', '' ) ) ) {
			$url = array_map( 'trim', explode( PHP_EOL, $url ) );
			return ( in_array( $_SERVER['REQUEST_URI'], $url ) );
		}
		return false;
	}

	/**
	 * add_to_log.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function add_to_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'alg-wc-product-visibility-by-country' ) );
		}
	}

	/**
	 * is_pvbc_meta_key.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function is_pvbc_meta_key( $key ) {
		return ( '_' . 'alg_wc_pvbc_visible' === $key || '_' . 'alg_wc_pvbc_invisible' === $key );
	}

	/**
	 * product_importer_parsed_data.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function product_importer_parsed_data( $parsed_data, $raw_data ) {
		if ( isset( $parsed_data['meta_data'] ) && is_array( $parsed_data['meta_data'] ) ) {
			foreach ( $parsed_data['meta_data'] as $i => $meta_data ) {
				if ( $this->is_pvbc_meta_key( $meta_data['key'] ) && ! empty( $meta_data['value'] ) ) {
					$parsed_data['meta_data'][ $i ]['value'] = maybe_unserialize( $meta_data['value'] );
				}
			}
		}
		return $parsed_data;
	}

	/**
	 * product_export_meta_value.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function product_export_meta_value( $meta_value, $meta, $product, $row ) {
		if ( $this->is_pvbc_meta_key( $meta->key ) && is_array( $meta_value ) ) {
			$meta_value = serialize( $meta_value );
		}
		return $meta_value;
	}

	/**
	 * product_by_country_blocks.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function product_by_country_blocks( $item_html, $data, $product ) {
		return ( ! $this->is_product_visible( $this->get_product_id_or_variation_parent_id( $product ), $this->get_country_by_ip() ) ? '' : $item_html );
	}

	/**
	 * product_by_country_price_html.
	 *
	 * @version 1.1.5
	 * @since   1.1.5
	 */
	function product_by_country_price_html( $price_html, $product ) {
		return ( ! $this->is_product_visible( $this->get_product_id_or_variation_parent_id( $product ), $this->get_country_by_ip() ) ?
			( '' != ( $content = get_option( 'alg_wc_pvbc_hide_price_content', '' ) ) ? do_shortcode( $content ) : '' ) : $price_html );
	}

	/**
	 * product_by_country_price.
	 *
	 * @version 1.1.5
	 * @since   1.1.5
	 */
	function product_by_country_price( $price, $product ) {
		return ( ! $this->is_product_visible( $this->get_product_id_or_variation_parent_id( $product ), $this->get_country_by_ip() ) ?
			'' : $price );
	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_pvbc_translate lang="DE" lang_text="Das Produkt ist in Ihrem Land nicht verfügbar." not_lang_text="The product is not available in your country."]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_pvbc_translate lang="DE"]Das Produkt ist in Ihrem Land nicht verfügbar.[/alg_wc_pvbc_translate][alg_wc_pvbc_translate not_lang="DE"]The product is not available in your country.[/alg_wc_pvbc_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * output_message.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 * @todo    [feature] terms?
	 * @todo    [feature] position & priority
	 * @todo    [feature] replaced values (and/or shortcodes?), e.g. `%customer_country%`, `%product_title%`
	 */
	function output_message( $view ) {
		if ( ! $this->is_product_visible( get_the_ID(), $this->get_country_by_ip() ) ) {
			echo do_shortcode( get_option( 'alg_wc_pvbc_info_on_' . $view . '_content',
				'<p><strong>' . __( 'The product is not available in your country.', 'product-visibility-by-country-for-woocommerce' ) . '</strong></p>' ) );
		}
	}

	/**
	 * add_message_on_loop.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	function add_message_on_loop() {
		$this->output_message( 'loop' );
	}

	/**
	 * add_message_on_single_product.
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	function add_message_on_single_product() {
		$this->output_message( 'single_product' );
	}

	/**
	 * add_product_columns.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_product_columns( $columns ) {
		$columns[ 'alg_wc_pvbc_countries' ] = __( 'Countries', 'product-visibility-by-country-for-woocommerce' );
		return $columns;
	}

	/**
	 * render_product_column.
	 *
	 * @version 1.1.1
	 * @since   1.0.0
	 * @todo    [dev] (maybe) add flags
	 */
	function render_product_column( $column ) {
		if ( 'alg_wc_pvbc_countries' === $column ) {
			$html = '';
			if ( $countries = get_post_meta( get_the_ID(), '_' . 'alg_wc_pvbc_visible', true ) ) {
				if ( is_array( $countries ) && ! empty( $countries ) ) {
					$html .= '<span style="color:green;">' . implode( ', ', $countries ) . '</span>';
				}
			}
			if ( $countries = get_post_meta( get_the_ID(), '_' . 'alg_wc_pvbc_invisible', true ) ) {
				if ( is_array( $countries ) && ! empty( $countries ) ) {
					if ( ! empty ( $html ) ) {
						$html .= '<br>';
					}
					$html .= '<span style="color:red;">' . implode( ', ', $countries ) . '</span>';
				}
			}
			echo $html;
		}
	}

	/**
	 * maybe_add_eu_countries.
	 *
	 * @version 1.1.2
	 * @since   1.1.2
	 */
	function maybe_add_eu_countries( $countries ) {
		return ( ! empty( $countries ) && is_array( $countries ) && in_array( 'EU', $countries ) ?
			array_merge( $countries, array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV',
				'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' ) ) :
			$countries
		);
	}

	/**
	 * geolocate_via_api.
	 *
	 * @version 1.3.2
	 * @since   1.3.2
	 * @todo    [dev] this is experimental, i.e. not used anywhere
	 * @todo    [dev] (maybe) remove `sanitize_text_field()` and `strtoupper()`
	 */
	function geolocate_via_api( $field = 'city' ) {
		$ip_address = WC_Geolocation::get_external_ip_address();
		$result     = false;
		$response   = wp_safe_remote_get( sprintf( 'http://ip-api.com/json/%s', $ip_address ), array( 'timeout' => 2 ) );
		if ( ! is_wp_error( $response ) && $response['body'] ) {
			$data   = json_decode( $response['body'] );
			$result = isset( $data->{$field} ) ? $data->{$field} : '';
			$result = sanitize_text_field( strtoupper( $result ) );
		}
		return $result;
	}

	/**
	 * is_product_visible.
	 *
	 * @version 1.3.0
	 * @since   1.1.0
	 * @todo    [dev] (maybe) better log message for `alg_wc_pvbc_is_product_visible` filter
	 */
	function is_product_visible( $product_id, $country ) {
		if ( isset( $this->saved_product_visibility[ $product_id ] ) ) {
			return $this->saved_product_visibility[ $product_id ];
		}

		if ( ! apply_filters( 'alg_wc_pvbc_is_product_visible', true, $product_id, $country ) ) {
			if ( $this->is_debug ) {
				$message = sprintf( __( 'Product #%s is hidden for %s.', 'product-visibility-by-country-for-woocommerce' ), $product_id, $country ) . ' ' .
					__( 'Reason: Filter.', 'product-visibility-by-country-for-woocommerce' );
				$this->add_to_log( $message );
			}
			$this->saved_product_visibility[ $product_id ] = false;
			return $this->saved_product_visibility[ $product_id ];
		}

		$visible_countries = $this->maybe_add_eu_countries( get_post_meta( $product_id, '_' . 'alg_wc_pvbc_visible', true ) );
		if ( ! empty( $visible_countries ) && is_array( $visible_countries ) && ! in_array( $country, $visible_countries ) ) {
			if ( $this->is_debug ) {
				$message = sprintf( __( 'Product #%s is hidden for %s.', 'product-visibility-by-country-for-woocommerce' ), $product_id, $country ) . ' ' .
					sprintf( __( 'Reason: Visible in countries: %s.', 'product-visibility-by-country-for-woocommerce' ), implode( ', ', $visible_countries ) );
				$this->add_to_log( $message );
			}
			$this->saved_product_visibility[ $product_id ] = false;
			return $this->saved_product_visibility[ $product_id ];
		}

		$invisible_countries = $this->maybe_add_eu_countries( get_post_meta( $product_id, '_' . 'alg_wc_pvbc_invisible', true ) );
		if ( ! empty( $invisible_countries ) && is_array( $invisible_countries ) && in_array( $country, $invisible_countries ) ) {
			if ( $this->is_debug ) {
				$message = sprintf( __( 'Product #%s is hidden for %s.', 'product-visibility-by-country-for-woocommerce' ), $product_id, $country ) . ' ' .
					sprintf( __( 'Reason: Invisible in countries: %s.', 'product-visibility-by-country-for-woocommerce' ), implode( ', ', $invisible_countries ) );
				$this->add_to_log( $message );
			}
			$this->saved_product_visibility[ $product_id ] = false;
			return $this->saved_product_visibility[ $product_id ];
		}

		if ( $this->is_debug ) {
			$message = sprintf( __( 'Product #%s is shown for %s.', 'product-visibility-by-country-for-woocommerce' ), $product_id, $country ) . ' ' .
				sprintf( __( 'Visible in countries: %s. Invisible in countries: %s.', 'product-visibility-by-country-for-woocommerce' ),
					( ! empty( $visible_countries )   ? implode( ', ', $visible_countries )   : __( 'None', 'product-visibility-by-country-for-woocommerce' ) ),
					( ! empty( $invisible_countries ) ? implode( ', ', $invisible_countries ) : __( 'None', 'product-visibility-by-country-for-woocommerce' ) )
				);
			$this->add_to_log( $message );
		}
		$this->saved_product_visibility[ $product_id ] = true;
		return $this->saved_product_visibility[ $product_id ];
	}

	/**
	 * product_by_country_widget_query.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 * @todo    [dev] check `$args = $query_args;`
	 */
	function product_by_country_widget_query( $query_args ) {
		remove_action( 'pre_get_posts', array( $this, 'product_by_country_pre_get_posts' ) );
		$country                = $this->get_country_by_ip();
		$post__not_in           = ( isset( $query_args['post__not_in'] ) ? $query_args['post__not_in'] : array() );
		$args                   = $query_args;
		$args['fields']         = 'ids';
		$args['posts_per_page'] = -1;
		// Run query
		$loop = new WP_Query( $args );
		foreach ( $loop->posts as $product_id ) {
			if ( ! $this->is_product_visible( $product_id, $country ) ) {
				$post__not_in[] = $product_id;
			}
		}
		// Set `post__not_in`
		$query_args['post__not_in'] = $post__not_in;
		add_action( 'pre_get_posts', array( $this, 'product_by_country_pre_get_posts' ) );
		return $query_args;
	}

	/**
	 * product_by_country_pre_get_posts.
	 *
	 * @version 1.2.1
	 * @since   1.0.0
	 * @see     https://developer.wordpress.org/reference/files/wp-includes/query.php/
	 * @todo    [dev] optimization: `woocommerce_product_query`
	 * @todo    [dev] (important) optimization: when querying single product? (maybe use `$query->is_singular( 'product' )` or maybe simply use `template_redirect` to 404 / custom URL)
	 * @todo    [dev] optimization: save query results (hash?) (also maybe same in `product_by_country_widget_query`)
	 * @todo    [dev] optimization: maybe save `post__not_in` for all products (i.e. just once) (also maybe same in `product_by_country_widget_query`)
	 * @todo    [dev] optimization: maybe use args from the current `$query`
	 * @todo    [dev] (maybe) check `is_admin` and `! ajax`
	 * @todo    [dev] (maybe) optimization: `meta_query`: tried, but adding it seems to make it only slower, i.e: `'meta_query' => array( 'relation' => 'OR', array( 'key' => '_' . 'alg_wc_pvbc_visible', 'value' => '', 'compare' => '!=' ), array( 'key' => '_' . 'alg_wc_pvbc_invisible', 'value' => '', 'compare' => '!=' ) )`
	 */
	function product_by_country_pre_get_posts( $query ) {
		if (
			$query->is_admin() ||
			( $this->do_modify_query_only_main_query            && ! $query->is_main_query() ) ||
			( $this->do_modify_query_only_main_query_and_search && ! $query->is_main_query() && ! $query->is_search() ) ||
			( $this->do_modify_query_check_post_type            && ! in_array( $query->get( 'post_type' ), array( '', 'product' ) ) )
		) {
			return;
		}
		remove_action( 'pre_get_posts', array( $this, 'product_by_country_pre_get_posts' ) );
		$country        = $this->get_country_by_ip();
		$post__not_in   = $query->get( 'post__not_in' );
		$args           = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post__not_in'   => $post__not_in,
		);
		// Run query
		$loop = new WP_Query( $args );
		foreach ( $loop->posts as $product_id ) {
			if ( ! $this->is_product_visible( $product_id, $country ) ) {
				$post__not_in[] = $product_id;
			}
		}
		// Set `post__not_in`
		$query->set( 'post__not_in', $post__not_in );
		add_action( 'pre_get_posts', array( $this, 'product_by_country_pre_get_posts' ) );
	}

	/**
	 * product_by_country_purchasable.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function product_by_country_purchasable( $purchasable, $_product ) {
		return ( ! $this->is_product_visible( $this->get_product_id_or_variation_parent_id( $_product ), $this->get_country_by_ip() ) ? false : $purchasable );
	}

	/**
	 * product_by_country_visibility.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function product_by_country_visibility( $visible, $product_id ) {
		return ( ! $this->is_product_visible( $product_id, $this->get_country_by_ip() ) ? false : $visible );
	}

	/**
	 * get_country_by_ip.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_country_by_ip() {
		if ( isset( $this->saved_country_by_ip ) ) {
			return $this->saved_country_by_ip;
		}
		// Get the country by IP
		$location = ( class_exists( 'WC_Geolocation' ) ? WC_Geolocation::geolocate_ip() : array( 'country' => '' ) );
		// Base fallback
		if ( empty( $location['country'] ) ) {
			$location = wc_format_country_state_string( apply_filters( 'woocommerce_customer_default_location', get_option( 'woocommerce_default_country' ) ) );
		}
		$this->saved_country_by_ip = ( isset( $location['country'] ) ? $location['country'] : '' );
		return $this->saved_country_by_ip;
	}

	/**
	 * get_product_id_or_variation_parent_id.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_product_id_or_variation_parent_id( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		if ( $this->is_wc_version_below_3 ) {
			return $_product->id;
		} else {
			return ( $_product->is_type( 'variation' ) ) ? $_product->get_parent_id() : $_product->get_id();
		}
	}

}

endif;

return new Alg_WC_PVBC_Core();
