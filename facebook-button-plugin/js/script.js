(function($) {
	$(document).ready( function() {

		$( 'input[name="fcbkbttn_my_page"]' ).change( function() {
			if ( $( this ).is( ":checked" ) ) {
				$( '.fcbkbttn_my_page' ).show();
				if ( $( 'select[name="fcbkbttn_display_option"]' ).val() == 'custom' ) {
					$( '#fcbkbttn_display_option_custom' ).show();
				} else {
					$( '#fcbkbttn_display_option_custom' ).hide();
				}
			} else {
				$( '.fcbkbttn_my_page' ).hide();
			}
		});

		$( 'input[name="fcbkbttn_like"]' ).change( function() {
			if ( $( this ).is( ":checked" ) ) {
				$( '.fcbkbttn_like' ).show();
			} else {
				$( '.fcbkbttn_like' ).hide();
			}
		});

		$( 'input[name="fcbkbttn_share"]' ).change( function() {
			if ( $( this ).is( ":checked" ) ) {
				$( '.fcbkbttn_share' ).show();
			} else {
				if ( ! $( 'input[name="fcbkbttn_like"]' ).is( ":checked" ) ) {
					$( '.fcbkbttn_share' ).hide();
				}
			}
		});		

		$( 'select[name="fcbkbttn_display_option"]' ).change( function() {
			if ( $( this ).val() == 'custom' ) {
				$( '#fcbkbttn_display_option_custom' ).show();
			} else {
				$( '#fcbkbttn_display_option_custom' ).hide();
			}
		});

		$( 'input[name="fcbkbttn_like"]' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.fcbkbttn_share_layout' ).hide();
				$( '.fcbkbttn_like_layout' ).show().attr( 'selected', 'selected' );
				$( '.fcbkbttn_like' ).show();
			} else {
				$( '.fcbkbttn_share_layout' ).show();
				$( 'select[name="fcbkbttn_layout_option"]' ).find( 'option[value="button_count"]' ).attr( 'selected', 'selected' );
				$( '.fcbkbttn_like, .fcbkbttn_like_layout' ).hide();			
				if ( $( 'input[name="fcbkbttn_share"]' ).is( ":checked" ) ) {
					$( '.fcbkbttn_share' ).show();
				}
			}
		});

		$( 'select[name="fcbkbttn_layout_option"]' ).change( function() {
			if ( $( this ).val() == 'standard' ) {
				$( '.fcbkbttn_like_standard_layout' ).show();
			} else {
				$( '.fcbkbttn_like_standard_layout' ).hide();
			}
		});
		
	});
})(jQuery);