/*
 * Script for custom pin it button on image hover.
 */
(function( $ ) {
	/*
	* Function for adding custom Pin It button on image hover.
	*/
	$( document ).ready(function() {
		$( document ).on( 'mouseenter', 'img', function( e ) {
			var imageWidth = $( this ).width();
			var imageHeight = $( this ).height();
			var imageNoPin = $( this ).attr( 'data-pin-nopin' );
			if ( 100 < imageWidth && 100 < imageHeight && 'undefined' == typeof imageNoPin ) {
				var currentUrl = encodeURIComponent( window.location.href );
				var imagePosition = $( this ).offset();
				var imageSrc = encodeURIComponent( $( this ).attr( 'src' ) );
				var customButtonImage = $( '#bws-custom-hover-js' ).attr( 'data-custom-button-image' );
				$( 'body' ).append( '<a id="pntrst-custom-hover" href="https://www.pinterest.com/pin/create/button/?url=' + currentUrl +'&media=' + imageSrc +'" data-pin-do="buttonPin" data-pin-custom="true" target="_blank"><img width="40" src="' + customButtonImage +'"></a>' );
				$( '#pntrst-custom-hover' ).css( {
					'position': 'absolute',
					'top': imagePosition.top + 10,
					'left': imagePosition.left + 10,
					'z-index': '999999'
				} );
			}
		} );		
		$( document ).on( 'mouseleave', 'img', function( e ) {
			if ( ! $( e.relatedTarget ).is( '#pntrst-custom-hover > img' ) ) {
				$( '#pntrst-custom-hover' ).remove();
			}
		} );

	});
})( jQuery );
