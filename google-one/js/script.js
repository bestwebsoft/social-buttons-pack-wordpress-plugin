( function( $ ) {
	$( document ).ready( function() {
		function gglplsn_plus_one_js() {
			if ( $( 'input[name="gglplsn_plus_one_js"]' ).is( ':checked' ) && 'inline' == $( 'input[name="gglplsn_plus_one_annotation"]:checked' ).val() ) {
				$( '.gglplsn-plus-one-annotation-type' ).show();
			} else {
				$( '.gglplsn-plus-one-annotation-type' ).hide();
			}

			if ( $( 'input[name="gglplsn_plus_one_js"]' ).is( ':checked' ) ) {
				$( '.gglplsn_plus_one_enabled' ).show();
			} else {
				$( '.gglplsn_plus_one_enabled' ).hide();
			}
		}
		function gglplsn_share_js() {
			if ( true == $( 'input[name="gglplsn_share_js"]' ).is( ':checked' ) && 'inline' == $( 'input[name="gglplsn_share_annotation"]:checked' ).val() ) {
				$( '.gglplsn-share-annotation-type' ).show();
			} else {
				$( '.gglplsn-share-annotation-type' ).hide();
			}

			if ( $( 'input[name="gglplsn_share_js"]' ).is( ':checked' ) && 'vertical-bubble' != $( 'input[name="gglplsn_share_annotation"]:checked' ).val() ) {
				$( '.gglplsn-share-size' ).show();
			} else {
				$( '.gglplsn-share-size' ).hide();
			}

			if (  $( 'input[name="gglplsn_share_js"]' ).is( ':checked' ) ) {
				$( '.gglplsn_share_enabled' ).show();
			} else {
				$( '.gglplsn_share_enabled' ).hide();
			}
		}
		function gglplsn_follow_js() {
			if (  $( 'input[name="gglplsn_follow_js"]' ).is( ':checked' ) ) {
				$( '.gglplsn_follow_enabled' ).show();
				$( 'input[name="gglplsn_follow_id"]' ).attr( 'required', 'required' );
				if ( '' == $( 'input[name="gglplsn_follow_id"]' ).val() ) {
					$( '.gglplsn-follow-notice' ).removeClass( 'gglplsn-unvisible-notice' );
				}
			} else {
				$( '.gglplsn_follow_enabled' ).hide();
				$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );				
			}
		}
		function gglplsn_hangout_js() {
			if ( $( 'input[name="gglplsn_hangout_js"]' ).is( ':checked' ) ) {
				$( '.gglplsn_hangout_enabled' ).show();
			} else {
				$( '.gglplsn_hangout_enabled' ).hide();
			}
		}
		function gglplsn_badge_js() {
			if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) ) {
				$( '.gglplsn_badge_enabled' ).show();
				if ( '' == $( 'input[name="gglplsn_badge_id"]' ).val() ) {
					$( '.gglplsn-badge-notice' ).removeClass( 'gglplsn-unvisible-notice' );
				}
			} else {
				$( '.gglplsn_badge_enabled' ).hide();
			}

			if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) ) {
				$( 'input[name="gglplsn_badge_id"]' ).attr( 'required', 'required' );
			} else {
				$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
			}

			if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) && 'community' == $( 'input[name="gglplsn_badge_type"]:checked' ).val() ) {
				$( '.gglplsn-show-owners' ).show();
			} else {
				$( '.gglplsn-show-owners' ).hide();
			}
		}
		function gglplsn_badge_type() {
			var badge_type = $( 'input[name="gglplsn_badge_type"]:checked' ).val();
			if ( 'community' == badge_type ) {
				$( '.gglplsn-show-owners' ).show();
				$( '.gglplsn-badge-id-th' ).html( js_string.community_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.community_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.community_tagline_info );
			} else if ( 'page' == badge_type ) {
				$( '.gglplsn-show-owners' ).hide();
				$( '.gglplsn-badge-id-th' ).html( js_string.page_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.page_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.page_tagline_info );
			} else if ( 'person' == badge_type ) {
				$( '.gglplsn-show-owners' ).hide();
				$( '.gglplsn-badge-id-th' ).html( js_string.person_id_th );
				$( '.gglplsn-badge-id-info' ).html( js_string.person_id_info );
				$( '.gglplsn-badge-tagline-info' ).html( js_string.person_tagline_info );
			}
		}
		function gglplsn_plus_one_annotation() {
			if ( 'inline' == $( 'input[name="gglplsn_plus_one_annotation"]:checked' ).val() ) {
				$( '.gglplsn-plus-one-annotation-type' ).show();
			} else {
				$( '.gglplsn-plus-one-annotation-type' ).hide();
			}
		}
		function gglplsn_share_annotation() {
			if ( 'inline' == $( 'input[name="gglplsn_share_annotation"]:checked' ).val() ) {
				$( '.gglplsn-share-annotation-type' ).show();
			} else {
				$( '.gglplsn-share-annotation-type' ).hide();
			}

			if ( 'vertical-bubble' == $( 'input[name="gglplsn_share_annotation"]:checked' ).val() ) {
				$( '.gglplsn-share-size' ).hide();
			} else {
				$( '.gglplsn-share-size' ).show();
			}
		}
		function gglplsn_badge_layout() {
			var badge_width = $( 'input[name="gglplsn_badge_width"]' );
			if ( 'portait' == $( 'input[name="gglplsn_badge_layout"]:checked' ).val() ) {
				badge_width.attr( 'min', '180' );
				if ( badge_width.val() < 180 ) {
					badge_width.val( '180' );
				}
			} else {
				badge_width.attr( 'min', '273' );
				if ( badge_width.val() < 273 ) {
					badge_width.val( '273' );
				}
			}
		}

		gglplsn_plus_one_js();		
		$( 'input[name="gglplsn_plus_one_js"]' ).change( function() {
			gglplsn_plus_one_js();
		} );
		gglplsn_share_js();
		$( 'input[name="gglplsn_share_js"]' ).change( function() {
			gglplsn_share_js();
		} );
		gglplsn_follow_js();
		$( 'input[name="gglplsn_follow_js"]' ).change( function() {
			gglplsn_follow_js();
		} );
		gglplsn_hangout_js();
		$( 'input[name="gglplsn_hangout_js"]' ).change( function() {
			gglplsn_hangout_js();
		} );
		gglplsn_badge_js();
		$( 'input[name="gglplsn_badge_js"]' ).change( function() {
			gglplsn_badge_js();
		} );
		gglplsn_badge_type();
		$( 'input[name="gglplsn_badge_type"]' ).change( function() {
			gglplsn_badge_type();
		} );
		gglplsn_plus_one_annotation();
		$( 'input[name="gglplsn_plus_one_annotation"]' ).change( function() {
			gglplsn_plus_one_annotation();
		} );
		gglplsn_share_annotation();
		$( 'input[name="gglplsn_share_annotation"]' ).change( function() {
			gglplsn_share_annotation();
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
			var added 			= false;
			var phone 			= false;
			var any 			= false;
			var validate_email 	= true;
			var vis_val 		= $( '#gglplsn_hangout_invite_id' ).val();
			var vis_type 		= $( '#gglplsn_hangout_invite_type' ).val();
			if ( 'EMAIL' == vis_type ) {
				var ajax_success = false;
				$.ajax( {
				type: "POST",
				dataType: "json",
				url: ajaxurl,
				async: false,
				data: {
					action: 						'gglplsn_validate_email',
					gglplsn_email_for_validate: 	vis_val,
					gglplsn_nonce: 				js_string.gglplsn_ajax_nonce
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
					if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.already_added + '</span>' ).slideDown( 300 );
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
					if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.one_number + '</span>' ).slideDown( 300 );
				}
			} );
			if ( '' == vis_val ) {
				if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.empty_id + '</span>' ).slideDown( 300 );
			} else if ( true == added || true == phone ) {
				added = false;
				if ( true == phone && 'PHONE' != vis_type ) {
					if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
						$( '#gglplsn_invite_id_error' ).hide();
					}
					$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.number_added + '</span>' ).slideDown( 300 );
					phone = false;
				}
			} else if ( false == phone && 'PHONE' == vis_type && true == any ) {
				if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
					$( '#gglplsn_invite_id_error' ).hide();
				}
				$( '#gglplsn_invite_id_error' ).html( '<span>' + js_string.any_added + '</span>' ).slideDown( 300 );
				any = false;
			} else if ( false == validate_email || false == ajax_success ) {
				if ( $( '#gglplsn_invite_id_error' ).css( 'display' ) == 'block' ) {
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
		$( '#gglplsn_settings_form #bws-submit-button' ).click( function() {
			var submit_form, focus_form = false;
			if ( $( 'input[name="gglplsn_follow_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_follow_id"]' ).val() ) {
				if ( $( 'input[name="gglplsn_follow_js"]' ).is( ':visible' ) ) {
					$( 'input[name="gglplsn_follow_id"]' ).focus();
					focus_form = true;
				} else {
					$( 'input[name="gglplsn_follow_js"]' ).prop( "checked", false );
					$( 'input[name="gglplsn_follow_id"]' ).removeAttr( 'required' );
					submit_form = true;
				}
			}
			if ( ! focus_form ) {				
				if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':checked' ) && '' == $( 'input[name="gglplsn_badge_id"]' ).val()  ) {
					if ( $( 'input[name="gglplsn_badge_js"]' ).is( ':visible' ) ) {
						$( 'input[name="gglplsn_badge_id"]' ).focus();
					} else {
						$( 'input[name="gglplsn_badge_js"]' ).prop( "checked", false );
						$( 'input[name="gglplsn_badge_id"]' ).removeAttr( 'required' );
						submit_form = true;
					}
				}
			}

			if ( submit_form ) {
				$( this ).submit();
			}
		} );
	} );
} ) ( jQuery );