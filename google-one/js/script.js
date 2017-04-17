( function( $ ) {
	$( document ).ready( function() {

		/* Show/Hide options */
		$( '.gglplsn-form tr, .gglplsn-form > .bws_form > div' ).each( function() {
			if( $( this ).hasClass( 'gglplsn-hide-option' ) ) {
				$( this ).hide();
			}
		} );

		$( 'input[name="gglplsn_plus_one_js"]' ).change( function() {
			if( true == $( this ).is( ':checked' ) && 'inline' == $( 'select[name="gglplsn_plus_one_annotation"]' ).val() ) {
				$( '.gglplsn-plus-one-annotation-type' ).show();
			} else {
				$( '.gglplsn-plus-one-annotation-type' ).hide();
			}

			$( '.gglplsn-plus-one-options' ).toggle();
		} );

		$( 'input[name="gglplsn_share_js"]' ).change( function() {
			if( true == $( this ).is( ':checked' ) && 'inline' == $( 'select[name="gglplsn_share_annotation"]' ).val() ) {
				$( '.gglplsn-share-annotation-type' ).show();
			} else {
				$( '.gglplsn-share-annotation-type' ).hide();
			}

			if( true == $( this ).is( ':checked' ) && 'vertical-bubble' != $( 'select[name="gglplsn_share_annotation"]' ).val() ) {
				$( '.gglplsn-share-size' ).show();
			} else {
				$( '.gglplsn-share-size' ).hide();
			}

			$( '.gglplsn-share-options' ).toggle();
		} );

		$( 'input[name="gglplsn_follow_js"]' ).change( function() {
			$( '.gglplsn-follow-options' ).toggle();
			if( $( 'input[name="gglplsn_follow_id"]' ).attr( 'required' ) ) {
				$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );
			} else {
				$( 'input[name="gglplsn_follow_id"]' ).attr( 'required', 'required' );
			}
		} );

		$( 'input[name="gglplsn_hangout_js"]' ).change( function() {
			$( '.gglplsn-hangout-options' ).toggle();
		} );

		$( 'input[name="gglplsn_badge_js"]' ).change( function() {
			$( '.gglplsn-badge-options' ).toggle();
			if( true == $( this ).is( ':checked' ) && 'community' == $( 'select[name="gglplsn_badge_type"]' ).val() ) {
				$( '.gglplsn-show-owners' ).show();
			} else {
				$( '.gglplsn-show-owners' ).hide();
			}

			if( $( 'input[name="gglplsn_badge_id"]' ).attr( 'required' ) ) {
				$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
			} else {
				$( 'input[name="gglplsn_badge_id"]' ).attr( 'required', 'required' );
			}
		} );

		/* min width value on change */
		$( 'input[name$="_width"]' ).each( function(){
			$( this ).change( function() {
				var val = parseInt( $( this ).val() );
				var min = parseInt( $( this ).attr( 'min' ) );
				var max = parseInt( $( this ).attr( 'max' ) );
				if( val < min ) {
					$( this ).val( min );
				} else if( val > max ) {
					$( this ).val( max );
				}
			} );
		} );

		/* min width for badge */
		$( 'select[name="gglplsn_badge_layout"]' ).change( function() {
			var badge_width = $( 'input[name="gglplsn_badge_width"]' );
			if( 'portait' == $( this ).val() ) {
				badge_width.attr( 'min', '180' );
				if( parseInt( badge_width.val() ) < 180 ) {
					badge_width.val( '180' );
				}
			} else {
				badge_width.attr( 'min', '273' );
				if( parseInt( badge_width.val() ) < 273 ) {
					badge_width.val( '273' );
				}
			}
		} );

		/* Show/Hide owners option for badge */
		$( 'select[name="gglplsn_badge_type"]' ).change( function() {
			if( 'community' == $( this ).val() ) {
				$( '.gglplsn-show-owners' ).show();
				$( '.gglplsn-badge-id-th' ).html( js_string.community_id_th );
				$( '.gglplsn-badge-id-info' ).html( '(' + js_string.community_id_info + ')' );
				$( '.gglplsn-badge-tagline-info' ).html( '(' + js_string.community_tagline_info + ')' );
			} else if( 'page' == $( this ).val() ) {
				$( '.gglplsn-show-owners' ).hide();
				$( '.gglplsn-badge-id-th' ).html( js_string.page_id_th );
				$( '.gglplsn-badge-id-info' ).html( '(' + js_string.page_id_info + ')' );
				$( '.gglplsn-badge-tagline-info' ).html( '(' + js_string.page_tagline_info + ')' );
			} else if( 'person' == $( this ).val() ) {
				$( '.gglplsn-show-owners' ).hide();
				$( '.gglplsn-badge-id-th' ).html( js_string.person_id_th );
				$( '.gglplsn-badge-id-info' ).html( '(' + js_string.person_id_info + ')' );
				$( '.gglplsn-badge-tagline-info' ).html( '(' + js_string.person_tagline_info + ')' );
			}
		} );

		/* Display Width for +1 and Share */
		$( 'select[name="gglplsn_plus_one_annotation"]' ).change( function() {
			if( 'inline' == $( this ).val() ) {
				$( '.gglplsn-plus-one-annotation-type' ).show();
			} else {
				$( '.gglplsn-plus-one-annotation-type' ).hide();
			}
		} );
		$( 'select[name="gglplsn_share_annotation"]' ).change( function() {
			if( 'inline' == $( this ).val() ) {
				$( '.gglplsn-share-annotation-type' ).show();
			} else {
				$( '.gglplsn-share-annotation-type' ).hide();
			}

			if( 'vertical-bubble' == $( this ).val() ) {
				$( '.gglplsn-share-size' ).hide();
			} else {
				$( '.gglplsn-share-size' ).show();
			}
		} );

		$( '.gglplsn-hangout-topic-text' ).focus( function() {
			$( '.gglplsn-hangout-topic-radio' ).attr( 'checked', 'checked' );
		} );

		/* View hangout invite id */
		$( '.gglplsn-invite-tr-noscript' ).hide();
		$( '.gglplsn-view-invited' ).show();
		var disabled_type = 0;
		$( '#gglplsn_hangout_invite_type' ).change( function() {
			if( 0 == disabled_type ) {
				disabled_type = 1;
				$( '.gglplsn-hangout-invite-id th, .gglplsn-hangout-invite-id td, .gglplsn-hangout-invite-id td > input, .gglplsn-hangout-invite-id .gglplsn-id-prompt' ).show();
			} else {
				$( '#gglplsn_hangout_invite_id' ).val( '' );
			}

			if( 'EMAIL' == $( this ).val() ) {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.email_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.email_info );
			} else if( 'PHONE' == $( this ).val() ) {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.phone_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.phone_info );
			} else if( 'PROFILE' == $( this ).val() ) {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.profile_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.profile_info );
			} else {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.circle_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.circle_info );
			}
		} );

		/* Adding invited for hangout */

		$( '#gglplsn_invite_id_error' ).hide();
		$( '#gglplsn_hangout_invite_add' ).click( function( e ) {
			e.preventDefault();
			var added 			= false;
			var phone 			= false;
			var any 			= false;
			var validate_email 	= true;
			var vis_val 		= $( '#gglplsn_hangout_invite_id' ).val();
			var vis_type 		= $( '#gglplsn_hangout_invite_type' ).val();
			if( 'EMAIL' == vis_type ) {
				var ajax_success = false;
				$.ajax( {
					type: "POST",
					dataType: "json",
					url: ajaxurl,
					async: false,
					data: {
						action: 					'gglplsn_validate_email',
						gglplsn_email_for_validate:	vis_val,
						gglplsn_nonce: 				js_string.gglplsn_ajax_nonce
					},
				success: function( data ) {
						if( -1 != data ) {
							ajax_success = true;
						}

						if( false == data.gglplsn_email_validate ) {
							validate_email = false;
						}
					},
				} );
			}
			$('.gglplsn-view-invited input[name^="gglplsn_hangout_invite_id_hidden"').each( function() {
				if( $( this ).val() == vis_val ) {
					if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.already_added + '</span>' ).slideDown( 300 );
					added = true;
				}
			} );
			$('.gglplsn-view-invited input[name^="gglplsn_hangout_invite_type_hidden"').each( function() {
				if( 'PHONE' == $( this ).val() ) {
					phone = true;
				} else {
					any = true;
				}

				if( 'PHONE' == vis_type && true == phone && $( this ).val() == vis_type ) {
					if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.one_number + '</span>' ).slideDown( 300 );
				}
			} );

			if( '' == vis_val ) {
				if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.empty_id + '</span>' ).slideDown( 300 );
			} else if( true == added || true == phone ) {
				added = false;
				if( true == phone && 'PHONE' != vis_type ) {
					if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.number_added + '</span>' ).slideDown( 300 );
					phone = false;
				}
			} else if( false == phone && 'PHONE' == vis_type && true == any ) {
				if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.any_added + '</span>' ).slideDown( 300 );
				any = false;
			} else if( false == validate_email || false == ajax_success ) {
				if( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.invalid_email + '</span>' ).slideDown( 300 );
			} else {
				$( '#gglplsn_invite_id_error' ).slideUp( 300 ).html( '' );
				$( '.gglplsn-view-invited' ).append( '<div><input name="gglplsn_hangout_invite_type_hidden[]" value="' + vis_type + '" type="hidden"><input name="gglplsn_hangout_invite_id_hidden[]" value="' + vis_val + '" type="hidden"><span><a class="delbutton"></a>&nbsp' + vis_val + '</span></div>' );
			}
		} );
		$( '.gglplsn-view-invited' ).on( 'click', '.delbutton', function() {
			$( this ).parent().parent().remove();
		} );

		/* Check the isset id for follow and badge */
		$( '#gglplsn_settings_form_block #bws-submit-button' ).click( function() {
			if ( $( 'input[name="gglplsn_follow_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_follow_id"]' ).val() ) {
				$( 'input[name="gglplsn_follow_id"]' ).focus();
			} else if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_badge_id"]' ).val() ) {
				$( 'input[name="gglplsn_badge_id"]' ).focus();
			}
		} );

		$( '.gglplsn-follow-focus' ).click( function() {
			$( 'input[name="gglplsn_follow_id"]' ).focus();
		} );
		$( '.gglplsn-badge-focus' ).click( function() {
			$( 'input[name="gglplsn_badge_id"]' ).focus();
		} );

		if( true != $( 'input[name="gglplsn_follow_js"]' ).prop('checked') && '' == $( 'input[name="gglplsn_follow_id"]' ).val() ) {
			$( 'input[name="gglplsn_follow_js"]' ).change( function() {
				$( '.gglplsn-follow-notice' ).removeClass( 'gglplsn-unvisible-notice' );
			} );
		}

		if( true != $( 'input[name="gglplsn_badge_js"]' ).prop('checked') && '' == $( 'input[name="gglplsn_badge_id"]' ).val() ) {
			$( 'input[name="gglplsn_badge_js"]' ).change( function() {
				$( '.gglplsn-badge-notice' ).removeClass( 'gglplsn-unvisible-notice' );
			} );
		}
	} );
} ) ( jQuery );