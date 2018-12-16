function ghostpool_vote( ID, type ) {

	var $ = jQuery.noConflict();

	// For the LocalStorage 
	var itemName = "gpvoting" + ID;
	var container = '#gp-voting-' + ID;

	// Check if the LocalStorage value exist. If do nothing.
	if ( ! localStorage.getItem( itemName ) ) {

		// Set HTML5 LocalStorage so the user can not vote again unless he clears it.							   
		localStorage.setItem( itemName, true );

		// Set the localStorage type as well
		var typeItemName = "gpvoting" + ID + "-" + type;
		localStorage.setItem( typeItemName, true );

		// Data for the Ajax Request
		var data = {
			action: 'ghostpool_add_vote',
			postid: ID,
			type: type,
			nonce: ghostpool_voting.nonce
		};

		$.post( ghostpool_voting.ajaxurl, data, function( response ) {			

			var object = $( container );

			$( container ).html( '' );

			$( container ).append( response );

			// Remove the class and ID so we don't have 2 DIVs with the same ID
			$( object ).removeClass( 'gp-voting-container' );
			$( object ).attr( 'id', '' );

			// Add the class to the clicked element
			var new_container = '#gp-voting-' + ID;

			// Check the type
			if ( type == 1 ) { 
				voting_class = ".gp-voting-up"; 
			} else { 
				voting_class = ".gp-voting-down";
			}

			$( new_container +  voting_class ).addClass( 'gp-voted' );

		});
	
	} else {

		// Display message if we detect LocalStorage
		$( '#gp-voting-' + ID + ' .gp-already-voted' ).fadeIn().css( 'display', 'block' );
	
	}
}

jQuery( document ).ready( function( $ ) {

	// Get all voting containers
	$( '.gp-voting-container' ).each( function( index ) {

		// Get data attribute
		 var content_id = $( this ).data( 'content-id' );

		 var itemName = "gpvoting" + content_id;

		// Check if this content has localstorage
		if ( localStorage.getItem( itemName ) ) {

			// Check if it's Up or Down vote
			if ( localStorage.getItem( "gpvoting" + content_id + "-1" ) ) {
				$( this ).find( '.gp-voting-up' ).addClass( 'gp-voted' );
			}
			if ( localStorage.getItem( "gpvoting" + content_id + "-0" ) ) {
				$( this ).find( '.gp-voting-down' ).addClass( 'gp-voted' );
			}
		}
	
	});
	
});	