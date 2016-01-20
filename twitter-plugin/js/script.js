( function( $ ) {
	$( document ).ready( function( $ ) {
		/* show/hide elements of form */ 
		$( '#twttr_lang_default' ).click( function() {
			if ( $( '#twttr_lang_default' ).is( ':checked' ) ) {
				$( '#twttr_lang_choose' ).hide();
			} else {
				$( '#twttr_lang_choose' ).show();
			}
		} );

		$( '#twttr_twitter_display' ).click( function() {
			if ( $( '#twttr_twitter_display' ).is( ':checked' ) ) {
				$( '.twttr_twitter_option' ).show();
			} else {
				$( '.twttr_twitter_option' ).hide();
			}
		} );

		$( '#twttr_followme_display' ).click( function() {
			if ( $( '#twttr_followme_display' ).is( ':checked' ) ) {
				$( '.twttr_followme_option' ).show();
			} else {
				$( '.twttr_followme_option' ).hide();
			}
		} );

		$( '#twttr_hashtag_display' ).click( function() {
			if ( $( '#twttr_hashtag_display' ).is( ':checked' ) ) {
				$( '.twttr_hashtag_option' ).show();
			} else {
				$( '.twttr_hashtag_option' ).hide();
			}
		} );

		$( '#twttr_mention_display' ).click( function() {
			if ( $( '#twttr_mention_display' ).is( ':checked' ) ) {
				$( '.twttr_mention_option' ).show();
			} else {
				$( '.twttr_mention_option' ).hide();
			}
		} );

		$( '#twttr_display_option' ).change( function() {
			var option = $( '#twttr_display_option option:selected' ).val();
			if ( option == 'custom' ) {
				$( '.twttr_display_option_custom' ).show();
				$( '.twttr_display_option_standart' ).hide();
			} else {
				$( '.twttr_display_option_custom' ).hide();
				$( '.twttr_display_option_standart' ).show();
			}
		} );

		$( '#twttr_text_twitter' ).click( function() {
			$( '#twttr_text_option_twitter' ).attr( 'checked', true );
		} );

		$( '#twttr_text_hashtag' ).click( function() {
			$( '#twttr_text_option_hashtag' ).attr( 'checked', true );
		} );

		$( '#twttr_text_mention' ).click( function() {
			$( '#twttr_text_option_mention' ).attr( 'checked', true );
		} );
	} );
} ) ( jQuery );
