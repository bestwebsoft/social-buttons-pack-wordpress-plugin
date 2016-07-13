( function( $ ) {
	$( document ).ready( function() {
		$( '.gglplsn-badge-type' ).live( "change", function() {
			if( 'community' == $( this ).val() ) {
				$( this ).parent().parent().find( '.gglplsn-show-owners input' ).attr("disabled", false);
				$( this ).parent().parent().find( '.gglplsn-show-owners' ).show();
			} else {
				$( this ).parent().parent().find( '.gglplsn-show-owners input' ).attr("disabled", true);
				$( this ).parent().parent().find( '.gglplsn-show-owners' ).hide();
			}
		} );
	} );
} ) ( jQuery );