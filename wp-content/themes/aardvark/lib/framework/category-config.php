<?php

/**
 * Category fields
 *
 */
 
// Media field
function ghostpool_category_media_field( $option, $term_meta ) { 
 			
	// Load scripts
	wp_enqueue_media();
	
	?>
	<script>
	jQuery( document ).ready( function( $ ) {
		var mediaUploader;
		$( '#gp_upload_image_<?php echo esc_attr( $option["id"] ); ?>' ).click( function( e ) {
			e.preventDefault();
			if ( mediaUploader ) {
				mediaUploader.open();
				return;
			}
			mediaUploader = wp.media.frames.file_frame = wp.media({
				title: '<?php esc_html_e( "Choose Image", "aardvark" ); ?>',
				button: {
					text: '<?php esc_html_e( "Choose Image", "aardvark" ); ?>'
				}, 
				multiple: false 
			});
			mediaUploader.on( 'select', function() {
				var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();
				$( '#gp_term_meta_<?php echo esc_attr( $option["id"] ); ?>' ).val( attachment.url );
				$( '#gp-cat-image-preview-<?php echo esc_attr( $option["id"] ); ?>' ).show();
				$( '#gp-cat-image-preview-<?php echo esc_attr( $option["id"] ); ?> img' ).attr( 'src', attachment.sizes.thumbnail.url );
				$( '#gp-remove-image-<?php echo esc_attr( $option["id"] ); ?>' ).show();
			});
			mediaUploader.open();
		});
		$( '#gp-remove-image-<?php echo esc_attr( $option["id"] ); ?>' ).click( function( e ) {
			e.preventDefault();
			$( '#gp_term_meta_<?php echo esc_attr( $option["id"] ); ?>' ).val( '' );
			$( '#gp-cat-image-preview-<?php echo esc_attr( $option["id"] ); ?>' ).hide();
			$( this ).hide();
		});
	});
	</script>

	<?php if ( $term_meta[$option['id']] ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $term_meta[$option['id']] ) ); 
		$image_id = $attachment[0]; 
		$image_thumb = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$image_thumb = $image_thumb[0];
	} else {
		$image_thumb = '';
	} ?>

	<div id="gp-cat-image-preview-<?php echo esc_attr( $option['id'] ); ?>" class="gp-cat-image-preview"<?php if ( $term_meta[$option['id']] ) { ?> style="display: block;"<?php } ?>>
		<img src="<?php echo $image_thumb; ?>" alt="" />
	</div>

	<input type="button" id="gp_upload_image_<?php echo esc_attr( $option["id"] ); ?>" class="gp-upload-image-button button button-primary" value="<?php if ( $term_meta[$option['id']] ) { esc_attr_e( 'Change Image', 'aardvark' ); } else { esc_attr_e( 'Add Image', 'aardvark' ); } ?>" />
	<?php if ( $term_meta[$option['id']] ) { ?>
		<a class="gp-remove-image-button" id="gp-remove-image-<?php echo esc_attr( $option["id"] ); ?>" href="#"><?php esc_attr_e( 'Remove Image', 'aardvark' ); ?></a>
	<?php } ?>

	<input id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" type="hidden" name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]" value="<?php echo esc_url( $term_meta[$option['id']] ? $term_meta[$option['id']] : '' ); ?>" />
	<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
	
<?php }
								
/**
 * Category options
 *
 */
if ( ! function_exists( 'ghostpool_category_options' ) ) {
	function ghostpool_category_options() {

		global $ghostpool_cat_options;

		if ( ! is_array( $ghostpool_cat_options ) ) {
			$ghostpool_cat_options = array();
		}

		// Category Options
		$ghostpool_cat_options[] = array( 
			'id'      => 'exclude',
			'name'    => esc_html__( 'Exclude', 'aardvark' ),
			'desc'    => esc_html__( 'Exclude this item from showing up in post meta text.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag', 'product_cat', 'product_tag' ),
			'options' => array( 
				'enabled' => esc_html__( 'Enabled', 'aardvark' ),
				'disabled' => esc_html__( 'Disabled', 'aardvark' ),
			),
			'default' => 'disabled',
		);
				
		$ghostpool_cat_options[] = array( 
			'id'      => 'page_header',
			'name'    => esc_html__( 'Page Header', 'aardvark' ),
			'desc'    => esc_html__( 'The page header on the page.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ), 
				'gp-standard-page-header' => esc_html__( 'Standard', 'aardvark' ),
				'gp-fullwidth-page-header' => esc_html__( 'Full Width', 'aardvark' ), 
				'gp-full-page-page-header' => esc_html__( 'Full Page', 'aardvark' ),
				'gp-page-header-disabled' => esc_html__( 'Disabled', 'aardvark' ),
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'page_header',
			'name'    => esc_html__( 'Page Header', 'aardvark' ),
			'desc'    => esc_html__( 'The page header on the page.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'product_cat', 'product_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ), 
				'gp-standard-page-header' => esc_html__( 'Standard', 'aardvark' ),
				'gp-fullwidth-page-header' => esc_html__( 'Full Width', 'aardvark' ), 
				'gp-full-page-page-header' => esc_html__( 'Full Page', 'aardvark' ),
				'gp-page-header-disabled' => esc_html__( 'Disabled', 'aardvark' ),
			),
			'default' => 'default',
		);
		
		$ghostpool_cat_options[] = array( 
			'id'      => 'page_header_bg',
			'name'    => esc_html__( 'Page Header Background', 'aardvark' ),
			'desc'    => esc_html__( 'The background of the page header.', 'aardvark' ),
			'type'    => 'media',
			'tax'     => array( 'category', 'post_tag', 'product_cat', 'product_tag' ),
			'default' => '',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'layout',
			'name'    => esc_html__( 'Page Layout', 'aardvark' ),
			'desc'    => esc_html__( 'The page header on the page.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag', 'product_cat', 'product_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ), 
				'gp-left-sidebar' => esc_html__( 'Left Sidebar', 'aardvark' ), 
				'gp-right-sidebar' => esc_html__( 'Right Sidebar', 'aardvark' ), 
				'gp-both-sidebars' => esc_html__( 'Both Sidebars', 'aardvark' ), 
				'gp-no-sidebar' => esc_html__( 'No Sidebars', 'aardvark' ), 
				'gp-fullwidth' => esc_html__( 'Full Width', 'aardvark' ),
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'left_sidebar',
			'name'    => esc_html__( 'Left Sidebar', 'aardvark' ),
			'desc'    => esc_html__( 'The left sidebar to display.', 'aardvark' ),
			'type'    => 'sidebars',
			'tax'     => array( 'category', 'post_tag', 'product_cat', 'product_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ),
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'right_sidebar',
			'name'    => esc_html__( 'Right Sidebar', 'aardvark' ),
			'desc'    => esc_html__( 'The right sidebar to display.', 'aardvark' ),
			'type'    => 'sidebars',
			'tax'     => array( 'category', 'post_tag', 'product_cat', 'product_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ),
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'format',
			'name'    => esc_html__( 'Format', 'aardvark' ),
			'desc'    => esc_html__( 'The format to display the items in.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ), 
				'gp-posts-list' => esc_html__( 'List', 'aardvark' ),
				'gp-posts-large' => esc_html__( 'Large', 'aardvark' ),
				'gp-posts-columns-2' => esc_html__( '2 Columns', 'aardvark' ), 
				'gp-posts-columns-3' => esc_html__( '3 Columns', 'aardvark' ), 
				'gp-posts-columns-4' => esc_html__( '4 Columns', 'aardvark' ),
				'gp-posts-masonry' => esc_html__( 'Masonry', 'aardvark' ), 
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'style',
			'name'    => esc_html__( 'Style', 'aardvark' ),
			'desc'    => esc_html__( 'The style to display the items in.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ),
				'gp-style-classic' => esc_html__( 'Classic', 'aardvark' ),
				'gp-style-modern' => esc_html__( 'Modern', 'aardvark' ),
			),
			'default' => 'default',
		);

		$ghostpool_cat_options[] = array( 
			'id'      => 'alignment',
			'name'    => esc_html__( 'Alignment', 'aardvark' ),
			'desc'    => esc_html__( 'The alignment of the item content.', 'aardvark' ),
			'type'    => 'select',
			'tax'     => array( 'category', 'post_tag' ),
			'options' => array( 
				'default' => esc_html__( 'Default', 'aardvark' ),
				'gp-align-left' => esc_html__( 'Left Aligned', 'aardvark' ),
				'gp-align-center' => esc_html__( 'Center Aligned', 'aardvark' ),
			),
			'default' => 'default',
		);
				
	}
}
add_action( 'after_setup_theme', 'ghostpool_category_options', 11 );
 
// New category options 
if ( ! function_exists( 'ghostpool_add_tax_fields' ) ) {
	function ghostpool_add_tax_fields( $tag ) {
		
		global $ghostpool_cat_options;
		
		// Get current screen
		$screen = get_current_screen();

		// Get category option
		if ( isset( $tag->term_id ) ) {
			$term_id = $tag->term_id;
			$term_meta = get_option( "taxonomy_$term_id" );
		} else {
			$term_meta = null;
		}
		
		// Run category options through filter to add custom options
		$options = apply_filters( 'ghostpool_custom_category_options', $ghostpool_cat_options );
		
		foreach ( $options as $option ) {
		
			switch( $option['type'] ) {
			
				case 'select' :
				
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
		
						<div class="form-field">
							<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							<select id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]">
								<?php foreach ( $option['options'] as $key => $value ) { ?>
									<?php if ( $term_meta[$option['id']] != '' ) { ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $term_meta[$option['id']] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
									<?php } else { ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $option['default'] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
							<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
						</div>
			
					<?php }
					
				break;

				case 'sidebars' :
			
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
	
						<div class="form-field">
							<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							<select id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]">
								
								<?php foreach ( $option['options'] as $key => $value ) { ?>
									<?php if ( $term_meta[$option['id']] != '' ) { ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $term_meta[$option['id']] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
									<?php } else { ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $option['default'] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
									<?php } ?>
								<?php } ?>
								
								<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { ?>
									<option value="<?php echo sanitize_title( $sidebar['id'] ); ?>"<?php if ( isset( $term_meta[$option['id']] ) && $term_meta[$option['id']] == $sidebar['id'] ) { ?>selected="selected"<?php } ?>>
										<?php echo ucwords( $sidebar['name'] ); ?>
									</option>
								<?php } ?>
							</select>
							<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
						</div>
		
					<?php } 
					
				break;
				
				case 'text' :
			
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
	
						<div class="form-field">
							<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							<input name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]" id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" type="text" value="<?php echo esc_url( $term_meta[$option['id']] ? $term_meta[$option['id']] : '' ); ?>" />
							<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
						</div>
		
					<?php }
					
				break;

				case 'color' :
				
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}
										
					if ( $add_field == true ) { 					

						// Load scripts
						wp_enqueue_style( 'wp-color-picker' );
						wp_enqueue_script( 'wp-color-picker' );
					
						?>
		
						<div class="form-field">
							<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							<script>
								jQuery( document ).ready( function($){  
									$( '#gp_term_meta_<?php echo esc_attr( $option["id"] ); ?>' ).wpColorPicker();
								});
							</script>
							<input name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]" id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" type="text" value="<?php echo esc_attr( $term_meta[$option['id']] ? $term_meta[$option['id']] : '' ); ?>" />
							<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
						</div>
			
					<?php }
					
				break;
					
				case 'media' :
			
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
				
						<div class="form-field">
							<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							<?php ghostpool_category_media_field( $option, $term_meta ); ?>
						</div>

					<?php } 
				
				break;
									
			}
						
		}
		
	}
}
add_action( 'category_add_form_fields', 'ghostpool_add_tax_fields' );	
add_action( 'post_tag_add_form_fields', 'ghostpool_add_tax_fields' );	
add_action( 'product_cat_add_form_fields', 'ghostpool_add_tax_fields' );
add_action( 'product_tag_add_form_fields', 'ghostpool_add_tax_fields' );

// Edit category options
if ( ! function_exists( 'ghostpool_edit_tax_fields' ) ) {
	function ghostpool_edit_tax_fields( $tag ) {

		global $ghostpool_cat_options;

		// Get current screen
		$screen = get_current_screen();

		// Get category option
		if ( isset( $tag->term_id ) ) {
			$term_id = $tag->term_id;
			$term_meta = get_option( "taxonomy_$term_id" );
		} else {
			$term_meta = null;
		}
		
		// Run category options through filter to add custom options
		$options = apply_filters( 'ghostpool_custom_category_options', $ghostpool_cat_options );
		
		foreach ( $options as $option ) {
		
			switch( $option['type'] ) {
			
				case 'select' :
				
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
		
						<tr class="form-field">
							<th scope="row" valign="top">
								<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							</th>
							<td>	
								<select id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]">
									<?php foreach ( $option['options'] as $key => $value ) { ?>
										<?php if ( $term_meta[$option['id']] != '' ) { ?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $term_meta[$option['id']] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
										<?php } else { ?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $option['default'] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
										<?php } ?>
									<?php } ?>
								</select>
								<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
							</td>
						</tr>
			
					<?php }
					
				break;

				case 'sidebars' :
			
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
	
						<tr class="form-field">
							<th scope="row" valign="top">
								<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							</th>
							<td>	
								<select id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]">
								
									<?php foreach ( $option['options'] as $key => $value ) { ?>
										<?php if ( $term_meta[$option['id']] != '' ) { ?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $term_meta[$option['id']] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
										<?php } else { ?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $option['default'] == $key ) { echo ' selected="selected"'; } ?>><?php echo esc_attr( $value ); ?></option>
										<?php } ?>
									<?php } ?>
								
									<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { ?>
										<option value="<?php echo sanitize_title( $sidebar['id'] ); ?>"<?php if ( isset( $term_meta[$option['id']] ) && $term_meta[$option['id']] == $sidebar['id'] ) { ?>selected="selected"<?php } ?>>
											<?php echo ucwords( $sidebar['name'] ); ?>
										</option>
									<?php } ?>
								</select>
								<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
							</td>
						</tr>
		
					<?php } 
					
				break;
				
				case 'text' :
			
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
	
						<tr class="form-field">
							<th scope="row" valign="top">
								<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							</th>
							<td>
								<input name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]" id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" type="text" value="<?php echo esc_url( $term_meta[$option['id']] ? $term_meta[$option['id']] : '' ); ?>" />
								<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
							</td>
						</tr>
		
					<?php }
					
				break;

				case 'color' :
					
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}
					
					if ( $add_field == true ) { 					

						// Load scripts
						wp_enqueue_style( 'wp-color-picker' );
						wp_enqueue_script( 'wp-color-picker' );
					
						?>
	
						<tr class="form-field">
							<th scope="row" valign="top">
								<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							</th>
							<td>
								<script>
									jQuery( document ).ready( function($) {  
										$( '#gp_term_meta_<?php echo esc_attr( $option["id"] ); ?>' ).wpColorPicker();
									});
								</script>
								<input name="gp_term_meta[<?php echo esc_attr( $option['id'] ); ?>]" id="gp_term_meta_<?php echo esc_attr( $option['id'] ); ?>" type="text" value="<?php echo esc_attr( $term_meta[$option['id']] ? $term_meta[$option['id']] : '' ); ?>" />
								<p class="description"><?php echo esc_attr( $option['desc'] ); ?></p>
							</td>
						</tr>
		
					<?php }
					
				break;
				
				case 'media' :
				
					// Checking what category pages to show this option on
					$add_field = false;
					foreach ( $option['tax'] as $type ) {
						if ( $screen->taxonomy == $type ) {
							$add_field = true;
						}
					}

					if ( $add_field == true ) { ?>
					
						<tr class="form-field">
							<th scope="row" valign="top">
								<label for="category-<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_attr( $option['name'] ); ?></label>
							</th>
							<td>
								<?php ghostpool_category_media_field( $option, $term_meta ); ?>
							</td>
						</tr>

					<?php } 
					
				break;
																
			}
			
		}	
	
	}
}
add_action( 'edit_category_form_fields', 'ghostpool_edit_tax_fields' );	
add_action( 'post_tag_edit_form_fields', 'ghostpool_edit_tax_fields' );
add_action( 'product_cat_edit_form_fields', 'ghostpool_edit_tax_fields' );
add_action( 'product_tag_edit_form_fields', 'ghostpool_edit_tax_fields' );

// Save category options
if ( ! function_exists( 'ghostpool_save_tax_fields' ) ) {	
	function ghostpool_save_tax_fields( $term_id ) {
		if ( isset( $_POST['gp_term_meta'] ) ) {
			$term_id = $term_id;
			$term_meta = get_option( "taxonomy_$term_id" );
			$cat_keys = array_keys( $_POST['gp_term_meta'] );
				foreach ( $cat_keys as $key ) {
				if ( isset( $_POST['gp_term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['gp_term_meta'][$key];
				}
			}
			update_option( "taxonomy_$term_id", $term_meta );
		}
	}			
}
add_action( 'created_category', 'ghostpool_save_tax_fields' );		
add_action( 'edit_category', 'ghostpool_save_tax_fields' );
add_action( 'created_post_tag', 'ghostpool_save_tax_fields' ); 
add_action( 'edited_post_tag', 'ghostpool_save_tax_fields' );	
add_action( 'created_product_cat', 'ghostpool_save_tax_fields' );		
add_action( 'edited_product_cat', 'ghostpool_save_tax_fields' );
add_action( 'created_product_tag', 'ghostpool_save_tax_fields' );		
add_action( 'edited_product_tag', 'ghostpool_save_tax_fields' );