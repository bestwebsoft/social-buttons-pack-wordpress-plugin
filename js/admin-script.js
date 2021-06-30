( function( $ ) {
	$( document ).ready( function() {
		$( '.nstgrm_settings_form' ).on( 'click', '#nstgrm_image_custom .add_media', function open_media_window() {
			var currentParent = $( this ).parents( 'td' );
			if ( this.window === undefined ) {
				this.window = wp.media({
					title: sclbttns_var.wp_media_title,
					library: { type: 'image' },
					multiple: false,
					button: { text: sclbttns_var.wp_media_button }
				});

				var self = this; /* Needed to retrieve our variable in the anonymous function below */
				this.window.on( 'select', function() {
					var all = self.window.state().get( 'selection' ).toJSON();
					currentParent.find( '.nstgrm-image' ).html( '<img src="' + all[0].url + '" /><span class="nstgrm-delete-image"><span class="dashicons dashicons-no-alt"></span></span>' );
					currentParent.find( '.nstgrm-image-id' ).val( all[0].id );
				});
			}

			this.window.open();
			return false;
		});

		$( document ).on( 'click', '.nstgrm-delete-image', function(){
			$( this ).parent().next().val( '' );
			$( this ).parent().html( '' );
		});
	} );
} )( jQuery );