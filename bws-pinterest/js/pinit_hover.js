(function( $ ){
	$( document ).ready( function() {
		$( '#fancybox-outer' ).hover( function() {
			$( 'body' ).find( '#fancybox-img' ).attr( 'data-pin-no-hover', '1' );
		});
	});
})( jQuery );