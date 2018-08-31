( function( $ ) {
	$( document ).ready( function() {
		function gglplsn_badge_type() {
			var badge_type = $( 'input[name="gglplsn_badge_type"]:checked' ).val();
			if ( 'community' == badge_type ) {
				$( '.gglplsn-badge-id-th' ).html( js_string.community_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.community_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.community_tagline_info );
			} else if ( 'page' == badge_type ) {
				$( '.gglplsn-badge-id-th' ).html( js_string.page_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.page_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.page_tagline_info );
			} else if ( 'person' == badge_type ) {
				$( '.gglplsn-badge-id-th' ).html( js_string.person_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.person_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.person_tagline_info );
			}
			if ( $( '.gglplsn_badge_type' ).is( ':checked' ) && $( '.gglplsn_enabled_icon' ).is( ':checked' ) ) {
				$( '#gglplsn-show-owners' ).show();
			} else {
				$( '#gglplsn-show-owners' ).hide();
			}
		}
		function gglplsn_badge_layout() {
			var badge_width = $( 'input[name="gglplsn_badge_width"]' );
			if ( 'portrait' == $( 'input[name="gglplsn_badge_layout"]:checked' ).val() ) {
				badge_width.attr( 'min', '180' );
				if ( 180 > badge_width.val() ) {
					badge_width.val( '180' );
				}
			} else {
				badge_width.attr( 'min', '273' );
				if ( 273 > badge_width.val() ) {
					badge_width.val( '273' );
				}
			}
		}
		gglplsn_badge_type();
		$( 'input[name="gglplsn_badge_type"]' ).change( function() {
			gglplsn_badge_type();
		} );
		/* min width for badge */
		gglplsn_badge_layout();
		$( 'input[name="gglplsn_badge_layout"]' ).change( function() {
			gglplsn_badge_layout();
		} );

		/* min width value on change */
		$( 'input[name$="_width"]' ).each( function() {
			$( this ).change( function() {
				var val = parseInt( $( this ).val() );
				var min = parseInt( $( this ).attr( 'min' ) );
				var max = parseInt( $( this ).attr( 'max' ) );
				if ( val < min ) {
					$( this ).val( min );
				} else if ( val > max ) {
					$( this ).val( max );
				}
			} );
		} );

		/* View hangout invite id */
		$( '.gglplsn-invite-tr-noscript' ).hide();
		$( '.gglplsn-view-invited' ).show();
		var disabled_type = 0;
		$( '#gglplsn_hangout_invite_type' ).change( function() {
			if ( 0 == disabled_type ) {
				disabled_type = 1;
				$( '.gglplsn-hangout-invite-id th, .gglplsn-hangout-invite-id td, .gglplsn-hangout-invite-id td > input, .gglplsn-hangout-invite-id .gglplsn-id-prompt' ).show();
			} else {
				$( '#gglplsn_hangout_invite_id' ).val( '' );
			}
			if ( 'EMAIL' == $( this ).val() ) {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.email_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.email_info );
			} else if ( 'PHONE' == $( this ).val() ) {
				$( '.gglplsn-hangout-invite-id th' ).html( js_string.phone_th );
				$( '.gglplsn-hangout-invite-id .bws_info' ).html( js_string.phone_info );
			} else if ( 'PROFILE' == $( this ).val() ) {
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
			var added = false;
			var phone = false;
			var any = false;
			var validate_email = true;
			var vis_val = $( '#gglplsn_hangout_invite_id' ).val();
			var vis_type = $( '#gglplsn_hangout_invite_type' ).val();
			if ( 'EMAIL' == vis_type ) {
				var ajax_success = false;
				$.ajax({
					type: "POST",
					dataType: "json",
					url: ajaxurl,
					async: false,
					data: {
						action: 'gglplsn_validate_email',
						gglplsn_email_for_validate: vis_val,
						gglplsn_nonce: js_string.gglplsn_ajax_nonce
					},
					success: function( data ) {
						if ( -1 != data ) {
							ajax_success = true;
						}

						if ( false == data.gglplsn_email_validate ) {
							validate_email = false;
						}
					}
				} );
			}
			$( '.gglplsn-view-invited input[name^="gglplsn_hangout_invite_id_hidden"' ).each( function() {
				if ( $( this ).val() == vis_val ) {
					if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.already_added + '</span>' ).slideDown(300);
					added = true;
				}
			} );
			$( '.gglplsn-view-invited input[name^="gglplsn_hangout_invite_type_hidden"' ).each( function() {
				if ( 'PHONE' == $( this ).val() ) {
					phone = true;
				} else {
					any = true;
				}

				if ( 'PHONE' == vis_type && true == phone && $( this ).val() == vis_type ) {
					if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.one_number + '</span>' ).slideDown( 300 );
				}
			} );
			if ( '' == vis_val ) {
				if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.empty_id + '</span>' ).slideDown( 300 );
			} else if ( true == added || true == phone ) {
				added = false;
				if ( true == phone && 'PHONE' != vis_type ) {
					if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.number_added + '</span>' ).slideDown( 300 );
					phone = false;
				}
			} else if ( false == phone && 'PHONE' == vis_type && true == any ) {
				if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.any_added + '</span>' ).slideDown( 300 );
				any = false;
			} else if ( false == validate_email || false == ajax_success ) {
				if ( 'block' == $( '#gglplsn_invite_id_error' ).css( 'display' ) ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.invalid_email + '</span>' ).slideDown( 300 );
			} else {
				$( '#gglplsn_invite_id_error' ).slideUp( 300 ).html( '' );
				$( '.gglplsn-view-invited' ).append( '<div><input name="gglplsn_hangout_invite_type_hidden[]" value="' + vis_type + '" type="hidden"><input name="gglplsn_hangout_invite_id_hidden[]" value="' + vis_val + '" type="hidden"><span><a class="gglplsn-delbutton"></a>&nbsp' + vis_val + '</span></div>' );
			}
		} );
		$( '.gglplsn-view-invited' ).on( 'click', '.gglplsn-delbutton', function() {
			$( this ).parent().parent().remove();
		} );

		/* Check the isset id for follow and badge */
		$( '.bws_form #bws-submit-button' ).click( function() {
			var submit_form, focus_form = false;
			if ( $( 'input[name="gglplsn_follow_js"]' ).is( ':visible' ) || $( 'input[name="gglplsn_badge_js"]' ).is( ':visible' ) ){
				if ( $( 'input[name="gglplsn_follow_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_follow_id"]' ).val() ) {
					$( 'input[name="gglplsn_follow_id"]' ).attr( "required", "required" );
					$( 'input[name="gglplsn_follow_id"]' ).focus();
					focus_form = true;
				} else {
					$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );
				}
				if ( ! focus_form ) {
					if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_badge_id"]' ).val() ) {
						$( 'input[name="gglplsn_badge_id"]' ).attr( "required", "required" );
						$( 'input[name="gglplsn_badge_id"]' ).focus();
					} else {
						$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
						submit_form = true;
					}
				}
			} else {
				$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );
				$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
				submit_form = true;
			}
			if ( submit_form ) {
				$( this ).submit();
			}
		} );
		$( 'input[name="bws_restore_default"]' ).click( function(){
			$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );
			$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
		} );
	} );
} ) ( jQuery );
