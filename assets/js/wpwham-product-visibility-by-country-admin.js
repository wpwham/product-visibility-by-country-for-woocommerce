/**
 * Product Visibility by Country for WooCommerce - admin scripts
 *
 * @version 1.x.x
 * @since   1.x.x
 * @author  WP Wham
 */

(function( $ ){
	
	$( document ).ready( function(){
		
		function logicExplainer( $visibleEl, $invisibleEl, $messageEl, message ) {
			if ( $visibleEl && $visibleEl.val() && $visibleEl.val().length && $invisibleEl && $invisibleEl.val() && $invisibleEl.val().length ) {
				$messageEl.html( message ).show();
			} else if ( $visibleEl && $visibleEl.val() && $visibleEl.val().length ) {
				$invisibleEl.prop( 'disabled', true );
				$messageEl.hide();
			} else if ( $invisibleEl && $invisibleEl.val() && $invisibleEl.val().length ) {
				$visibleEl.prop( 'disabled', true );
				$messageEl.hide();
			} else {
				$visibleEl.prop( 'disabled', false );
				$invisibleEl.prop( 'disabled', false );
				$messageEl.hide();
			}
		}
		
		function logicExplainerMetabox() {
			logicExplainer(
				$( '#alg_wc_pvbc_visible' ),
				$( '#alg_wc_pvbc_invisible' ),
				$( '#wpwham-product-visibility-by-country-meta-box-messages' ),
				'<span style="color: red;">'
					+ wpwham_product_visibility_by_country_admin.i18n.logical_error
					+ '</span>'
					+ '<br /><br />'
					+ wpwham_product_visibility_by_country_admin.i18n.see_documentation
			);
		}
		
		// attach handlers for product page metabox
		var $metabox = $( '#alg-wc-product-visibility-by-country' );
		if ( $metabox.length ) {
			$( '#alg_wc_pvbc_visible, #alg_wc_pvbc_invisible' ).on( 'change', function(){
				logicExplainerMetabox();
			});
			logicExplainerMetabox();
		
			$( '#alg_wc_pvbc_visible' ).parent().on( 'click', function(){
				if ( $( '#alg_wc_pvbc_visible' ).prop( 'disabled' ) ) {
					$( '#wpwham-product-visibility-by-country-meta-box-messages' )
						.html( wpwham_product_visibility_by_country_admin.i18n.why_is_visible_disabled
							+ '<br /><br />'
							+ wpwham_product_visibility_by_country_admin.i18n.see_documentation ).toggle();
				}
			});
			
			$( '#alg_wc_pvbc_invisible' ).parent().on( 'click', function(){
				if ( $( '#alg_wc_pvbc_invisible' ).prop( 'disabled' ) ) {
					$( '#wpwham-product-visibility-by-country-meta-box-messages' )
						.html( wpwham_product_visibility_by_country_admin.i18n.why_is_invisible_disabled
							+ '<br /><br />'
							+ wpwham_product_visibility_by_country_admin.i18n.see_documentation ).toggle();
				}
			});
		}
		
	});
	
})( jQuery );
