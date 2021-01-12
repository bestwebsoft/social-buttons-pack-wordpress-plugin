( function( $ ) {
	$( document ).ready( function( $ ) {
		function twttr_tweet_display() {
			if ( $( 'input[name="twttr_tweet_display"]' ).is( ':checked' ) ) {
				$( '.twttr_tweet_enabled' ).show();
			} else {
				$( '.twttr_tweet_enabled' ).hide();
			}
		}
		function twttr_followme_display() {
			if ( $( 'input[name="twttr_followme_display"]' ).is( ':checked' ) ) {
				$( '.twttr_follow_enabled' ).show();
			} else {
				$( '.twttr_follow_enabled' ).hide();
			}
		}
		function twttr_hashtag_display() {
			if ( $( 'input[name="twttr_hashtag_display"]' ).is( ':checked' ) ) {
				$( '.twttr_hashtag_enabled' ).show();
			} else {
				$( '.twttr_hashtag_enabled' ).hide();
			}
		}
		function twttr_mention_display() {
			if ( $( 'input[name="twttr_mention_display"]' ).is( ':checked' ) ) {
				$( '.twttr_mention_enabled' ).show();
			} else {
				$( '.twttr_mention_enabled' ).hide();
			}
		}
		/* Follow Button Image */
		function twttr_display_option() {
			if ( $( 'input[name="twttr_display_option"]:checked' ).val() === 'custom' ) {
				$( '.twttr_display_option_custom' ).show();
				$( '.twttr_display_option_standart' ).hide();
			} else {
				$( '.twttr_display_option_custom' ).hide();
				$( '.twttr_display_option_standart' ).show();
			}
		}
		/* Language */
		function twttr_lang_default() {
			if ( $( '#twttr_lang_default' ).is( ':checked' ) ) {
				$( '#twttr_lang_choose' ).hide();
			} else {
				$( '#twttr_lang_choose' ).show();
			}
		}
		/* custom text */
		function twttr_custom_text( element ) {
			if ( element.is( ':checked' ) ) {
				if ( element.val() === 'custom' ) {
					element.parent().siblings( ".twttr_custom_input" ).show();
				} else {
					element.parent().siblings( ".twttr_custom_input" ).hide();
				}
			}
		}

		twttr_tweet_display();
		$( 'input[name="twttr_tweet_display"]' ).click( function() {
			twttr_tweet_display();
		} );		
		twttr_followme_display();
		$( 'input[name="twttr_followme_display"]' ).click( function() {
			twttr_followme_display();
		} );
		twttr_hashtag_display();
		$( 'input[name="twttr_hashtag_display"]' ).click( function() {
			twttr_hashtag_display();
		} );
		twttr_mention_display();
		$( 'input[name="twttr_mention_display"]' ).click( function() {
			twttr_mention_display();
		} );

		twttr_lang_default();
		$( '#twttr_lang_default' ).click( function() {
			twttr_lang_default();
		} );

		twttr_display_option();
		$( 'input[name="twttr_display_option"]' ).change( function() {
			twttr_display_option();
		} );

		$( 'input[name="twttr_text_option_hashtag"], input[name="twttr_text_option_mention"], input[name="twttr_text_option_twitter"]' ).each( function( index ) {
			twttr_custom_text( $( this ) );
			$( this ).change( function() {
				twttr_custom_text( $( this ) );
			} );
		} );
	} );
} )( jQuery );
