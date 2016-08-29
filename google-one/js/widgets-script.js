( function( $ ) {
	$( document ).ready( function() {
		$( document ).on( 'change', '.gglplsn-badge-type', function() {
			if( 'community' == $( this ).val() ) {
				$( this ).parents( '.widget-content' ).find( '.gglplsn-show-owners' ).show().find( 'input' ).attr( "disabled", false );
			} else {
				$( this ).parents( '.widget-content' ).find( '.gglplsn-show-owners' ).hide().find( 'input' ).attr( "disabled", true );
			}
		} );
	} );
} ) ( jQuery );