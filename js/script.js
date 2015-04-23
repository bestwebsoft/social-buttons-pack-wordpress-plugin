(function($) {
	$(document).ready( function() {
		$( '.bws_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '.bws_settings_form_notice' ).css( 'display', 'block' );
			};
		});
		$( '.bws_settings_form select' ).bind( "change", function() {
			$( '.updated.fade' ).css( 'display', 'none' );
			$( '.bws_settings_form_notice' ).css( 'display', 'block' );
		});
	});
})(jQuery);