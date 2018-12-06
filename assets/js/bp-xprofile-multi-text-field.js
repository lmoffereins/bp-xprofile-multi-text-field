/**
 * BP XProfile Multi Text Field Scripts
 *
 * @package BP XProfile Multi Text Field
 * @subpackage Main
 */
( function( $ ) {

	// When clicking an input action button
	$( '.bp-user' ).on( 'click', '.field_type_multi_text button', function() {
		var $wrapper = $( this ).parent(),
		    maxItemsAllowed = $wrapper.parent().data( 'max-items-allowed' ) || 0,
		    itemCount = maxItemsAllowed ? $wrapper.siblings().length : -1;

		// Add field, only when items are allowed
		if ( this.classList.contains( 'add-field' ) && itemCount < maxItemsAllowed ) {
			$wrapper.siblings( '.multi-text-input:last-child' ).first().clone().removeAttr( 'style' ).insertAfter( $wrapper );

		// Remove field
		} else if ( this.classList.contains( 'remove-field' ) ) {

			// Keep the last item, clear it
			if ( $wrapper.is( ':first-child:nth-last-child(2)' ) ) {
				$wrapper.find( 'input' )[0].value = '';

			// Remove any other item
			} else {
				$wrapper.remove();
			}
		}
	});

})( jQuery );
