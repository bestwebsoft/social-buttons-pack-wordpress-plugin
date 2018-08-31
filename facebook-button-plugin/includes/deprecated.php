<?php
/**
* Includes deprecated functions
*/

/**
* @deprecated since 2.56
 * @todo remove after 01.07.2018
*/
if ( ! function_exists( 'fcbkbttn_update_options' ) ) {
	function fcbkbttn_update_options() {
		global $fcbkbttn_options;

		if ( isset( $fcbkbttn_options['layout_option'] ) ) {
			if( 'icon_link' == $fcbkbttn_options['layout_option'] || 'link' == $fcbkbttn_options['layout_option'] || 'icon' == $fcbkbttn_options['layout_option'] ) {
				$fcbkbttn_options['layout_like_option']  = 'standard';
				$fcbkbttn_options['layout_share_option'] = $fcbkbttn_options['layout_option'];
			} else if ( 'standard' == $fcbkbttn_options['layout_option'] ){
				$fcbkbttn_options['layout_like_option']  = $fcbkbttn_options['layout_option'];
				$fcbkbttn_options['layout_share_option'] = 'button_count';
			} else {
				$fcbkbttn_options['layout_like_option'] = $fcbkbttn_options['layout_share_option'] = $fcbkbttn_options['layout_option'];
			}
			unset( $fcbkbttn_options['layout_option'] );
		}
	}
}