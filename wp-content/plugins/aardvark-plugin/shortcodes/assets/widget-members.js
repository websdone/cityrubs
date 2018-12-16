jQuery(document).ready( function() {
	member_widget_click_handler();

	// WP 4.5 - Customizer selective refresh support.
	if ( 'undefined' !== typeof wp && wp.customize && wp.customize.selectiveRefresh ) {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
			member_widget_click_handler();
		} );
	}
});

function member_widget_click_handler() {
	jQuery('.gp-bp-element #members-list-options a').on('click',
		function() {
			var link = this;
			
			var element = jQuery(link).parent().parent();
			
			jQuery(link).addClass('loading');

			jQuery( element ).find( '#members-list-options a' ).removeClass('selected');
			jQuery(this).addClass('selected');

			jQuery.post( ajaxurl, {
				action: 'bp_element_members',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': jQuery( element ).find('input#_wpnonce-members' ).val(),
				'max-members': jQuery( element ).find('input.gp-members-element-max' ).val(),
				'filter': jQuery(this).attr('id'),
				'format': jQuery( element ).find( 'input.gp-members-element-format' ).val(),
				'cover_images': jQuery( element ).find( 'input.gp-members-element-cover-images').val()
			},
			function(response)
			{
				jQuery(link).removeClass('loading');
				member_widget_response(response,element);
			});

			return false;
		}
	);
}

function member_widget_response(response,element) {
	response = response.substr(0, response.length-1);
	response = response.split('[[SPLIT]]');

	var element = jQuery( element ).find( '.gp-bp-members' );

	if ( response[0] !== '-1' ) {
				
		element.fadeOut(200,
			function() {
				element.html(response[1]);
				element.fadeIn(200);			
			}
		);

	} else {
		element.fadeOut(200,
			function() {
				var message = '<p>' + response[1] + '</p>';
				element.html(message);
				element.fadeIn(200);
			}
		);
	}
}