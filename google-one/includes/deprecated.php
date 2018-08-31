<?php
/**
* Includes deprecated functions
*/

/**
 * Removing deprecated options
 * @deprecated since 1.3.6
 * @todo remove after 12.04.2018
 */
if ( ! function_exists( 'gglplsn_remove_deprecated' ) ) {
	function gglplsn_remove_deprecated() {
		global $gglplsn_options;

		if ( empty( $gglplsn_options ) )
			$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! empty( $gglplsn_options['plus_one_annotation'] ) ) {
			unset( $gglplsn_options['plus_one_annotation'] );
			unset( $gglplsn_options['plus_one_annotation_type'] );
			unset( $gglplsn_options['share_annotation_type'] );
			unset( $gglplsn_options['share_annotation'] );
			update_option( 'gglplsn_options', $gglplsn_options );
		}

		if ( ! is_multisite() )
			return false;

		$site_options = get_site_option( 'gglplsn_options' );
		if ( ! empty( $site_options['plus_one_annotation'] ) ) {
			unset( $site_options['plus_one_annotation'] );
			unset( $site_options['plus_one_annotation_type'] );
			unset( $site_options['share_annotation_type'] );
			unset( $site_options['share_annotation'] );
			update_site_option( 'gglplsn_options', $site_options );
		}
	}
}