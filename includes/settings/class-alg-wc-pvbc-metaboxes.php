<?php
/**
 * Product Visibility by Country for WooCommerce - Metaboxes
 *
 * @version 1.3.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBC_Metaboxes' ) ) :

class Alg_WC_PVBC_Metaboxes {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_action( 'add_meta_boxes',    array( $this, 'add_pvbc_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_pvbc_meta_box' ), PHP_INT_MAX, 2 );
	}

	/**
	 * add_pvbc_metabox.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_pvbc_metabox() {
		add_meta_box(
			'alg-wc-product-visibility-by-country',
			__( 'Product Visibility by Country', 'product-visibility-by-country-for-woocommerce' ),
			array( $this, 'display_pvbc_metabox' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * display_pvbc_metabox.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function display_pvbc_metabox() {
		$current_post_id = get_the_ID();
		$html = '';
		$html .= '<table class="widefat striped">';
		foreach ( $this->get_meta_box_options() as $option ) {
			$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
			if ( $is_enabled ) {
				if ( 'title' === $option['type'] ) {
					$html .= '<tr>';
					$html .= '<th colspan="3" style="text-align:left;font-weight:bold;">' . $option['title'] . '</th>';
					$html .= '</tr>';
				} else {
					$custom_attributes = '';
					$the_post_id   = ( isset( $option['product_id'] ) ) ? $option['product_id'] : $current_post_id;
					$the_meta_name = ( isset( $option['meta_name'] ) )  ? $option['meta_name']  : '_' . $option['name'];
					if ( get_post_meta( $the_post_id, $the_meta_name ) ) {
						$option_value = get_post_meta( $the_post_id, $the_meta_name, true );
					} else {
						$option_value = ( isset( $option['default'] ) ) ? $option['default'] : '';
					}
					$css = ( isset( $option['css'] ) ) ? $option['css']  : '';
					$input_ending = '';
					if ( 'select' === $option['type'] ) {
						if ( isset( $option['multiple'] ) ) {
							$custom_attributes = ' multiple';
							$option_name       = $option['name'] . '[]';
						} else {
							$option_name       = $option['name'];
						}
						if ( isset( $option['custom_attributes'] ) ) {
							$custom_attributes .= ' ' . $option['custom_attributes'];
						}
						$options = '';
						foreach ( $option['options'] as $select_option_key => $select_option_value ) {
							$selected = '';
							if ( is_array( $option_value ) ) {
								foreach ( $option_value as $single_option_value ) {
									if ( '' != ( $selected = selected( $single_option_value, $select_option_key, false ) ) ) {
										break;
									}
								}
							} else {
								$selected = selected( $option_value, $select_option_key, false );
							}
							$options .= '<option value="' . $select_option_key . '" ' . $selected . '>' . $select_option_value . '</option>';
						}
					} elseif ( 'textarea' === $option['type'] ) {
						if ( '' === $css ) {
							$css = 'min-width:300px;';
						}
					} else {
						$input_ending = ' id="' . $option['name'] . '" name="' . $option['name'] . '" value="' . $option_value . '">';
						if ( isset( $option['custom_attributes'] ) ) {
							$input_ending = ' ' . $option['custom_attributes'] . $input_ending;
						}
						if ( isset( $option['placeholder'] ) ) {
							$input_ending = ' placeholder="' . $option['placeholder'] . '"' . $input_ending;
						}
					}
					switch ( $option['type'] ) {
						case 'price':
							$field_html = '<input style="' . $css . '" class="short wc_input_price" type="number" step="0.0001"' . $input_ending;
							break;
						case 'date':
							$field_html = '<input style="' . $css . '" class="input-text" display="date" type="text"' . $input_ending;
							break;
						case 'textarea':
							$field_html = '<textarea style="' . $css . '" id="' . $option['name'] . '" name="' . $option['name'] . '">' .
								$option_value . '</textarea>';
							break;
						case 'select':
							$field_html = '<select' . $custom_attributes . ' style="' . $css . '" id="' . $option['name'] . '" name="' .
								$option_name . '" class="' . ( isset( $option['class'] ) ? $option['class'] : '' ) . '">' . $options . '</select>' .
								( ! empty( $option['desc_tip'] ) ? $option['desc_tip'] : '' );
							break;
						default:
							$field_html = '<input style="' . $css . '" class="short" type="' . $option['type'] . '"' . $input_ending;
							break;
					}
					$html .= '<tr>';
					$maybe_tooltip = ( ! empty( $option['tooltip'] ) ? wc_help_tip( $option['tooltip'], true ) : '' );
					$html .= '<th style="text-align:left;width:25%;">' . $option['title'] . $maybe_tooltip . '</th>';
					if ( isset( $option['desc'] ) && '' != $option['desc'] ) {
						$html .= '<td style="font-style:italic;width:25%;">' . $option['desc'] . '</td>';
					}
					$html .= '<td>' . $field_html . '</td>';
					$html .= '</tr>';
				}
			}
		}
		$html .= '</table>';
		$html .= '<input type="hidden" name="alg_wc_pvbc_save_post" value="alg_wc_pvbc_save_post">';
		echo $html;
	}

	/**
	 * sanitize_input.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 * @todo    [dev] check if it's the same as `wc_clean()`
	 */
	function sanitize_input( $value ) {
		return ( is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value ) );
	}

	/**
	 * save_pvbc_meta_box.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function save_pvbc_meta_box( $post_id, $post ) {
		// Check that we are saving with current metabox displayed.
		if ( ! isset( $_POST[ 'alg_wc_pvbc_save_post' ] ) ) {
			return;
		}
		// Save options
		foreach ( $this->get_meta_box_options() as $option ) {
			if ( 'title' === $option['type'] ) {
				continue;
			}
			$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
			if ( $is_enabled ) {
				$option_value  = ( isset( $_POST[ $option['name'] ] ) ? $this->sanitize_input( $_POST[ $option['name'] ] ) : $option['default'] );
				$_post_id      = ( isset( $option['product_id'] )     ? $option['product_id']                              : $post_id );
				$_meta_name    = ( isset( $option['meta_name'] )      ? $option['meta_name']                               : '_' . $option['name'] );
				update_post_meta( $_post_id, $_meta_name, $option_value );
			}
		}
	}

	/**
	 * get_currently_selected.
	 *
	 * @version 1.3.1
	 * @since   1.2.0
	 */
	function get_currently_selected( $visible_or_invisible ) {
		if ( '' != ( $country_codes = get_post_meta( get_the_ID(), '_' . 'alg_wc_pvbc_' . $visible_or_invisible, true ) ) ) {
			return sprintf( '<em>' . __( 'Currently selected: %s.', 'product-visibility-by-country-for-woocommerce' ) . '</em>',
				implode( ', ', array_intersect_key( alg_wc_pvbc_get_countries(), array_flip( $country_codes ) ) ) );
		}
		return '';
	}

	/**
	 * get_meta_box_options.
	 *
	 * @version 1.3.1
	 * @since   1.0.0
	 * @todo    [dev] add "Enabled" option
	 * @todo    [dev] add "Select all" button
	 * @todo    [dev] (maybe) add flags
	 */
	function get_meta_box_options() {
		$is_chosen_select = ( 'chosen_select' === get_option( 'alg_wc_pvbc_select_box_type', 'chosen_select' ) );
		$options = array(
			array(
				'title'    => __( 'Visible in countries', 'product-visibility-by-country-for-woocommerce' ),
				'tooltip'  => __( 'If no countries selected - product will be visible in all countries.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'If you fill in this option, you don\'t need to fill in "Invisible in countries" option.', 'product-visibility-by-country-for-woocommerce' ) .
					( $is_chosen_select ? '' : '<br>' . __( 'Hold Control (Ctrl) key to select/deselect multiple countries. Hold Control + A to select all countries.', 'product-visibility-by-country-for-woocommerce' ) ),
				'name'     => 'alg_wc_pvbc_visible',
				'default'  => '',
				'type'     => 'select',
				'options'  => alg_wc_pvbc_get_countries(),
				'multiple' => true,
				'class'    => ( $is_chosen_select ? 'chosen_select' : '' ),
				'css'      => ( $is_chosen_select ? 'width:100%;'   : 'height:300px;width:100%;' ),
				'desc_tip' => ( $is_chosen_select ? ''              : $this->get_currently_selected( 'visible' ) ),
			),
			array(
				'title'    => __( 'Invisible in countries', 'product-visibility-by-country-for-woocommerce' ),
				'tooltip'  => __( 'If no countries selected - product will be visible in all countries.', 'product-visibility-by-country-for-woocommerce' ) . ' ' .
					__( 'If you fill in this option, you don\'t need to fill in "Visible in countries" option.', 'product-visibility-by-country-for-woocommerce' ) .
					( $is_chosen_select ? '' : '<br>' . __( 'Hold Control (Ctrl) key to select/deselect multiple countries. Hold Control + A to select all countries.', 'product-visibility-by-country-for-woocommerce' ) ),
				'name'     => 'alg_wc_pvbc_invisible',
				'default'  => '',
				'type'     => 'select',
				'options'  => alg_wc_pvbc_get_countries(),
				'multiple' => true,
				'class'    => ( $is_chosen_select ? 'chosen_select' : '' ),
				'css'      => ( $is_chosen_select ? 'width:100%;'   : 'height:300px;width:100%;' ),
				'desc_tip' => ( $is_chosen_select ? ''              : $this->get_currently_selected( 'invisible' ) ),
			),
		);
		return $options;
	}

}

endif;

return new Alg_WC_PVBC_Metaboxes();
