jQuery( document ).ready(
	function(){

		jQuery( '.add_review_msg' ).hide();
		jQuery( document ).on(
			'click', '#bupr-add-review-btn a', function(e){
				var show_content = jQuery( this ).attr( 'show' );
				localStorage.setItem( "bupr_show_form", show_content );
			}
		);
		/*----------------------------------------
		* Add Placeholder in search box
		*-----------------------------------------*/
		jQuery( '.dataTables_filter input[type="search"]' ).attr( 'placeholder','Enter Keywords....' );

		/*----------------------------------------
		* Select star on mouse enter
		*-----------------------------------------*/
		var reviews_pluginurl = jQuery( '#reviews_pluginurl' ).val();

		jQuery( '.member_stars' ).mouseenter(
			function(){
				jQuery( this ).parent().children().eq( 0 ).val( 'not_clicked' );
				var id        = jQuery( this ).attr( 'data-attr' );
				var parent_id = jQuery( this ).parent().attr( 'id' );
				for ( i = 1; i <= id; i++ ) {
					jQuery( '#' + parent_id ).children( '.' + i ).addClass( 'fa-star' ).removeClass( 'fa-star-o' );;
				}
			}
		);

		/*----------------------------------------
		* Remove Color on stars
		*-----------------------------------------*/
		jQuery( '.member_stars' ).mouseleave(
			function(){
				var clicked_id = jQuery( this ).parent().children().eq( 1 ).val();
				var id         = jQuery( this ).attr( 'data-attr' );
				var parent_id  = jQuery( this ).parent().attr( 'id' );
				if ( jQuery( this ).parent().children().eq( 0 ).val() !== 'clicked' ) {
					var j = parseInt( clicked_id ) + 1;
					for ( i = j; i <= 5; i++ ) {
						jQuery( '#' + parent_id ).children( '.' + i ).addClass( 'fa-star-o' ).removeClass( 'fa-star' );
					}
				}
			}
		);

		/*----------------------------------------
		* Color the stars on click
		*-----------------------------------------*/
		jQuery( '.member_stars' ).on(
			'click',function(){
				var attr       = jQuery( this ).attr( 'data-attr' );
				var clicked_id = attr;
				var parent_id  = jQuery( this ).parent().attr( 'id' );
				jQuery( this ).parent().children().eq( 1 ).val( attr );
				jQuery( this ).parent().children().eq( 0 ).val( 'clicked' );
				for ( i = 1; i <= attr; i++ ) {
					jQuery( '#' + parent_id ).children( '.' + i ).addClass( 'fa-star' ).removeClass( 'fa-star-o' );
				}

				var k = parseInt( attr ) + 1;
				for ( j = k; j <= 5; j++ ) {
					jQuery( '#' + parent_id ).children( '.' + j ).addClass( 'fa-star-o' ).removeClass( 'fa-star' );
				}
			}
		);

		jQuery( '#bupr_member_review_id' ).on(
			'change',function(){
				jQuery( this ).siblings( '.bupr-error-fields' ).hide();
			}
		);

		jQuery( '#review_desc' ).on(
			'keydown',function(){
				jQuery( this ).siblings( '.bupr-error-fields' ).hide();
			}
		);

		jQuery( '.member_stars' ).on(
			'click',function() {
				jQuery( this ).parent().next( '.bupr-error-fields' ).hide();
			}
		);

		/*----------------------------------------
		* Add new review in member profiles
		*-----------------------------------------*/
		jQuery( document ).on(
			'click', '#bupr_save_review', function(event){
				event.preventDefault();
				var rating_exist       = [];
				var bupr_member_id     = jQuery( '#bupr_member_review_id' ).val();
				var bupr_current_user  = jQuery( '#bupr_current_user_id' ).val();
				var bupr_review_title  = 'Review ' + jQuery.now();
				var bupr_review_desc   = jQuery( '#review_desc' ).val();
				var bupr_review_count  = jQuery( '#member_rating_field_counter' ).val();
				var bupr_review_rating = {};
				//var bupr_review_rating_text = {};
				var empty_rate         = 0;

				/* Send anonymous review. */
				var bupr_anonymous_review = '';
				if ( jQuery( '#bupr_anonymous_review' ).is( ':checked' ) ) {
					bupr_anonymous_review = 'yes';
				} else {
					bupr_anonymous_review = 'no';
				}

				jQuery( '.bupr-star-member-rating' ).each(
					function(index) {
						bupr_review_rating[index] = jQuery( this ).val();
					}
				);

				// jQuery( '.member_rating_text' ).each(
				// 	function(index) {
				// 		bupr_review_rating_text[jQuery( this ).data('id')] = jQuery( this ).val();
				// 	}
				// );
				jQuery( '.bupr-star-member-rating' ).each(
					function() {
						var rate_val = jQuery( this ).val();
						if ( rate_val > 0 ) {
							empty_rate = empty_rate + 1;
						} else {
							jQuery( this ).parent().next( '.bupr-error-fields' ).show();
						}
						rating_exist.push( rate_val );
					}
				);

				if ( bupr_review_count > 0 ) {
					if ( bupr_member_id == '' ) {
						jQuery( '#bupr_member_review_id' ).siblings( '.bupr-error-fields' ).show();
					} else {
						if (jQuery.inArray( '0', rating_exist ) == -1 ) {
							jQuery( '.bupr-save-reivew-spinner' ).show();
							jQuery.post(
								ajaxurl,
								{
									'action'                : 'allow_bupr_member_review_update',
									'bupr_member_id'        : bupr_member_id,
									'bupr_current_user'     : bupr_current_user,
									'bupr_review_title'     : bupr_review_title,
									'bupr_review_desc'      : bupr_review_desc,
									'bupr_review_rating'    : bupr_review_rating,
									'bupr_field_counter'    : bupr_review_count,
									'bupr_anonymous_review' : bupr_anonymous_review
								},
								function(response) {
									jQuery( '.bupr-save-reivew-spinner' ).hide();
									sessionStorage.reloadAfterPageLoad = true;
									var date                           = new Date();
									date.setTime( date.getTime() + (20 * 1000) );
									jQuery.cookie( 'response', response, { expires: date } );
									window.location.reload();
								}
							);
						}
					}
				} else {
					if ( bupr_member_id == '' || bupr_review_desc == ''  ) {
						if ( bupr_member_id == '' ) {
							jQuery( '#bupr_member_review_id' ).siblings( '.bupr-error-fields' ).show();
							event.preventDefault();
						}

						if ( bupr_review_desc == '' ) {
							jQuery( '#review_desc' ).siblings( '.bupr-error-fields' ).show();
							event.preventDefault();
						}
					} else {
						jQuery( '.bupr-save-reivew-spinner' ).show();
						jQuery.post(
							ajaxurl,
							{
								'action'                : 'allow_bupr_member_review_update',
								'bupr_member_id'        : bupr_member_id,
								'bupr_current_user'     : bupr_current_user,
								'bupr_review_title'     : bupr_review_title,
								'bupr_review_desc'      : bupr_review_desc,
								'bupr_review_rating'    : bupr_review_rating,
								'bupr_field_counter'    : bupr_review_count,
								'bupr_anonymous_review' : bupr_anonymous_review
							},
							function(response) {
								jQuery( '.bupr-save-reivew-spinner' ).hide();
								sessionStorage.reloadAfterPageLoad = true;
								var date                           = new Date();
								date.setTime( date.getTime() + (20 * 1000) );
								jQuery.cookie( 'response', response, { expires: date } );
								window.location.reload();
							}
						);
					}
				}
				// }
			}
		);

	}
);

/*----------------------------------------
* Display message after review submit
*-----------------------------------------*/
jQuery(
	function () {
		if ( jQuery.cookie( 'response' ) && jQuery.cookie( 'response' ) !== '') {
			jQuery( '.add_review_msg p' ).html( jQuery.cookie( 'response' ) );
			jQuery( '.add_review_msg' ).show();
			jQuery.cookie( 'response' , "" , -1 );
			jQuery( '#review_subject' ).val( '' );
			jQuery( '#review_desc' ).val( '' );
		}
	}
);
