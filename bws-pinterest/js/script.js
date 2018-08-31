/*
 * Scripts for BWS Pinterest plugin.
 */
(function( $ ) {
	/*
	 * Function for enable/disable fields in admin plugin options page.
	 */
	function dynamicButtonOptions() {
		/* Check state of radio buttons for custom image and enable/disable file input. */
		if ( $( "#pntrst_image_custom" ).length > 0 ) {
			if ( $( "#pntrst_image_custom" ).is( ':checked' ) ) {
				$( "#pntrst-custom-image" ).prop( "disabled", false );
			} else if ( $( "#pntrst_image_default" ).is( ':checked' ) ) {
				$( "#pntrst-custom-image" ).prop( "disabled", true );
			}

			$( "#pntrst_image_custom" ).click(function() {
				$( "#pntrst-custom-image" ).attr( "disabled", false );
			});
			$( "#pntrst_image_default" ).click(function() {
				$( "#pntrst-custom-image" ).attr( "disabled", true );
			});
		}
		/* Check state of radio buttons for image type and show/hide options. */
		if ( $( "input[name='pntrst_image']" ).length > 0 ) {
			if ( '0' == $( "input[name='pntrst_image']:checked" ).val() ) {
				$( ".pntrst-image-default, .pntrst-image-shape, .pntrst-image-size, .pntrst-image-color, .pntrst-pin-counts" ).hide();
				$( ".pntrst-custom-button" ).show();
			} else {
				$( ".pntrst-image-default, .pntrst-image-shape, .pntrst-image-size" ).show();
				if ( '1' == $( "input[name='pntrst_image_shape']:checked" ).val() ) {
					$( ".pntrst-image-color, .pntrst-pin-counts" ).show();
				}
				$( ".pntrst-custom-button" ).hide();
			}

			$( "input[name='pntrst_image']" ).change(function() {
				if ( '0' == $( "input[name='pntrst_image']:checked" ).val() ) {
					$( ".pntrst-image-default, .pntrst-image-shape, .pntrst-image-size, .pntrst-image-color, .pntrst-pin-counts" ).hide();
					$( ".pntrst-custom-button" ).show();
				} else {
					$( ".pntrst-image-default, .pntrst-image-shape, .pntrst-image-size" ).show();
					if ( '1' == $( "input[name='pntrst_image_shape']:checked" ).val() ) {
						$( ".pntrst-image-color, .pntrst-pin-counts" ).show();
					}
					$( ".pntrst-custom-button" ).hide();
				}
			});
		}
		/* Check state of radio buttons for button shape and show/hide options. */
		if ( $( "input[name='pntrst_image_shape']" ).length > 0 ) {
			if ( '0' == $( "input[name='pntrst_image_shape']:checked" ).val() ) {
				$( ".pntrst-image-color, .pntrst-pin-counts" ).hide();
			} else {
				if ( '1' == $( "input[name='pntrst_image']:checked" ).val() ) {
					$( ".pntrst-image-color, .pntrst-pin-counts" ).show();
				}
			}

			$( "input[name='pntrst_image_shape']" ).change(function() {
				if ( '0' == $( "input[name='pntrst_image_shape']:checked" ).val() ) {
					$( ".pntrst-image-color, .pntrst-pin-counts" ).hide();
				} else {
					$( ".pntrst-image-color, .pntrst-pin-counts" ).show();
				}
			});
		}
	}

	/*
	 * Function for showing/hiding and clearing options fields in widget form.
	 * Checks type of widget(Pin, Board or Profile) and display appropriate options.
	 */
	function dynamicWidgetOptions() {
		$( "div[id*='_pntrst-widget-']" ).each(function () {
			/* Show/hide options fields depending on selected widget type. Works when widget type select changes */
			var optionField1 = $( this ).find( ".pntrst-pin-widget-size" );
			var optionField2 = $( this ).find( ".pntrst-widget-size" );
			var optionField3 = $( this ).find( ".pntrst-widget-url" );
			$( this ).find( "select[id*='-pntrst_widget_type']" ).each(function() {
				$( this ).change(function() {
					if ( 'embedPin' == $( this ).val() ) {
						optionField1.show();
						optionField2.hide();
						optionField3.show();
					} else {
						optionField1.hide();
						optionField2.show();
						if ( 'embedUser' == $( this ).val() ) {
							optionField3.hide();
						} else {
							optionField3.show();
						}
					}
				});
			});
		});
	}

	$( document ).ready(function() {
		/* Show/Hide options in plugin settings*/
		function pntrst_save() {
			if ( $( 'input[name="pntrst_save"]' ).is( ':checked' ) ) {
				$( '.pntrst_save_enabled' ).show();
			} else {
				$( '.pntrst_save_enabled' ).hide();
			}
		}

		function pntrst_follow() {
			if ( $( 'input[name="pntrst_follow"]' ).is( ':checked' ) ) {
				$( '.pntrst_follow_enabled' ).show();
			} else {
				$( '.pntrst_follow_enabled' ).hide();
			}
		}

		pntrst_save();
		$( 'input[name="pntrst_save"]' ).change(function() {
			pntrst_save();
		} );

		pntrst_follow();
		$( 'input[name="pntrst_follow"]' ).change(function() {
			pntrst_follow();
		} );

		$( document ).ajaxComplete(function() {
			dynamicWidgetOptions();
		});

		$( window ).load(function() {
			dynamicButtonOptions();
			dynamicWidgetOptions();
		});
	});
})( jQuery );
