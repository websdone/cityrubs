<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
	
	if ( ! function_exists( 'ghostpool_bp_groups' ) ) {
		function ghostpool_bp_groups( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'title' => '',
				'format' => 'gp-posts-masonry',
				'max_groups' => 8,
				'filters' => 'disabled',
				'group_default' => 'newest',
				'cover_images' => 'enabled',	
				'classes' => '',
				'css' => '',	
				'link_color' => '',
				'masonry_bg_color' => '',
				'masonry_border_color' => '',
				'title_color' => '',
				'text_color' => '',
			), $atts ) );
			
			global $groups_template;
						
			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_buddypress_groups_' . $i;		
			
			// Add CSS styling to header
			ghostpool_buddypress_css( $name, $link_color, $masonry_bg_color, $masonry_border_color, $title_color, $text_color );
	
			if ( $filters == 'enabled' ) {
				wp_enqueue_script( 'ghostpool-bp-element-groups-js', plugins_url( '/shortcodes/assets/widget-groups.js', dirname( __FILE__ ) ), array( 'jquery' ), bp_get_version() );
			}
			
			if ( $group_default == 'my-groups' ) {
				$user_id = bp_loggedin_user_id();
			} else {
				$user_id = 0;
			}
			
			$max_groups = ! empty( $max_groups ) ? (int) $max_groups : 8;

			$groups_args = array(
				'user_id'  => $user_id,
				'type'     => $group_default == 'my-groups' ? 'alphabetical' : $group_default,
				'per_page' => $max_groups,
				'max'      => $max_groups,
			);
			
			// Back up the global.
			$old_groups_template = $groups_template;
		
			// Classes
			$css_classes = array(
				'gp-bp-wrapper',
				'gp-bp-groups',
				$format,
				$format == 'gp-posts-masonry' ? 'gp-columns-4' : '',
				$format == 'gp-posts-list' ? 'widget buddypress' : '',
				$format == 'gp-posts-list' ? '' : 'gp-align-center',
				$format == 'gp-posts-list' ? '' : 'gp-style-classic',
				$classes,
			);
			$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
			$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );					

			ob_start(); ?>
			
			<div id="<?php echo sanitize_html_class( $name ); ?>" class="gp-bp-element <?php if ( $format == 'gp-posts-list' ) { echo 'gp-bp-posts-list'; } ?>">

				<?php if ( $title ) { ?><h3 class="widgettitle"><?php echo esc_attr( $title ); ?></h3><?php } ?>
				
				<?php if ( bp_has_groups( $groups_args ) ) : ?>
				
					<?php if ( $filters == 'enabled' ) { ?>
						<div class="item-options" id="groups-list-options">
							<a href="<?php bp_groups_directory_permalink(); ?>" id="newest-groups"<?php if ( $group_default == 'newest' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( "Newest", 'aardvark-plugin') ?></a>
							<a href="<?php bp_groups_directory_permalink(); ?>" id="recently-active-groups"<?php if ( $group_default == 'active' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( "Active", 'aardvark-plugin') ?></a>
							<a href="<?php bp_groups_directory_permalink(); ?>" id="popular-groups" <?php if ( $group_default == 'popular' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( "Popular", 'aardvark-plugin') ?></a>
							<a href="<?php bp_groups_directory_permalink(); ?>" id="alphabetical-groups" <?php if ( $group_default == 'alphabetical' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( "Alphabetical", 'aardvark-plugin') ?></a>
							<a href="<?php bp_groups_directory_permalink(); ?>" id="my-groups" <?php if ( $group_default == 'my-groups' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( "My Groups", 'aardvark-plugin') ?></a>
						</div>
					<?php } ?>
			
					<ul class="<?php echo esc_attr( $css_classes ); ?>" aria-live="polite" aria-relevant="all" aria-atomic="true">
					
						<?php if ( $format == 'gp-posts-masonry' ) { ?><li class="gp-gutter-size"></li><?php } ?>
			
						<?php while ( bp_groups() ) : bp_the_group(); ?>
						
							<li <?php bp_group_class( array( 'gp-post-item' ) ); ?>>
							
								<?php if ( $format == 'gp-posts-list' ) { ?>
					
									<div class="item-avatar">
										<a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar_thumb() ?></a>
									</div>
									<div class="item">
										<div class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></div>
										<div class="item-meta">										
											<?php if ( 'newest' == $group_default ) { ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'created %s', 'aardvark-plugin' ), bp_get_group_date_created() ); ?></span>
											<?php } elseif ( 'popular' == $group_default ) { ?>
												<span class="activity"><?php bp_group_member_count(); ?></span>
											<?php } else { ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'active %s', 'aardvark-plugin' ), bp_get_group_last_active() ); ?></span>
											<?php } ?>
										</div>
									</div>
								
								<?php } elseif ( $format != 'gp-posts-masonry' ) { ?>
						
									<a href="<?php bp_group_permalink() ?>" class="gp-bp-avatar">
										<span class="gp-bp-hover-effect"></span>
										<?php bp_group_avatar_thumb(); ?>
									</a>
											
								<?php } else { ?>
							
										<?php if ( $cover_images == 'enabled' ) {
											$cover_image_url = bp_attachments_get_attachment( 'url', array( 'object_dir' => 'groups', 'item_id' => bp_get_group_id() ) );
											if ( bp_group_use_cover_image_header() == '1' && $cover_image_url != '' ) { ?>
												<a href="<?php bp_group_permalink() ?>" class="gp-post-thumbnail" style="background-image: url(<?php echo esc_url( $cover_image_url ); ?>);">
													<span class="gp-bp-col-cover-overlay"><?php echo preg_replace( '/\D/', '', bp_get_group_member_count() ); ?></span>
													<?php if ( ! bp_disable_group_avatar_uploads() ) { ?>												
														<span class="gp-bp-col-avatar">
															<span class="gp-bp-hover-effect"></span>
															<?php bp_group_avatar_thumb(); ?>
														</span>
													<?php } ?>
												</a>
											<?php } 
										} ?>

									<div class="gp-loop-content<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?> gp-no-cover-image<?php } ?>">
			
										<?php if ( ( $cover_images == 'disabled' OR $cover_image_url == '' ) && ! bp_disable_group_avatar_uploads() ) { ?>
											<span class="gp-bp-col-cover-overlay"><?php echo preg_replace( '/\D/', '', bp_get_group_member_count() ); ?></span>							
											<div class="gp-bp-col-avatar">
												<a href="<?php bp_group_permalink() ?>">													
													<span class="gp-bp-hover-effect"></span>
													<?php bp_group_avatar_thumb(); ?>
												</a>
											</div>
										<?php } ?>

										<div class="gp-loop-title"><?php bp_group_link(); ?></div>
								
										<div class="gp-loop-meta">
											<?php if ( 'newest' == $group_default ) { ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'created %s', 'aardvark-plugin' ), bp_get_group_date_created() ); ?></span>
											<?php } elseif ( 'popular' == $group_default ) { ?>
												<span class="activity"><?php bp_group_member_count(); ?></span>
											<?php } else { ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'active %s', 'aardvark-plugin' ), bp_get_group_last_active() ); ?></span>
											<?php } ?>
										</div>
								
										<div class="gp-loop-text">
											<?php bp_group_description_excerpt(); ?>
										</div>
								
									</div>
									
								<?php } ?>	
								
							</li>
							
						<?php endwhile; ?>
						
					</ul>	

					<?php wp_nonce_field( 'ghostpool_bp_element_groups', '_wpnonce-groups' ); ?>
					<input type="hidden" name="max_groups" class="gp-groups-element-max" value="<?php echo esc_attr( $max_groups ); ?>" />
					<input type="hidden" name="gp-groups-element-format" class="gp-groups-element-format" value="<?php echo esc_attr( $format ); ?>" />
					<input type="hidden" name="gp-groups-element-cover-images" class="gp-groups-element-cover-images" value="<?php echo esc_attr( $cover_images ); ?>" />
					
				<?php else: ?>

					<?php esc_html_e(  'There are no groups to display.', 'aardvark-plugin' ) ?>

				<?php endif; ?>
				
			</div>
				
			<?php		
			
			// Restore the global.
			$groups_template = $old_groups_template;
			
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
		
		}
	}
	add_shortcode( 'gp_bp_groups', 'ghostpool_bp_groups' );
	
	/**
	 * Ajax group display
	 *
	 */
	function ghostpool_ajax_bp_element_groups() {
	
		check_ajax_referer( 'ghostpool_bp_element_groups' );
		
		switch ( $_POST['filter'] ) {
			case 'newest-groups':
				$type = 'newest';
			break;
			case 'recently-active-groups':
				$type = 'active';
			break;
			case 'popular-groups':
				$type = 'popular';
			break;
			case 'alphabetical-groups':
				$type = 'alphabetical';
			break;	
			case 'my-groups':
				$type = 'my-groups';
				$user_id = bp_loggedin_user_id();
			break;
		}
		
		$per_page = isset( $_POST['max_groups'] ) ? intval( $_POST['max_groups'] ) : 8;
		$format = isset( $_POST['format'] ) ? $_POST['format'] : 'gp-posts-masonry';
		$cover_images = isset( $_POST['cover_images'] ) ? $_POST['cover_images'] : 'enabled';
		
		$groups_args = array(
			'user_id'  => $user_id,
			'type'     => $type == 'my-groups' ? 'alphabetical' : $type,
			'per_page' => $per_page,
			'max'      => $per_page,
		);
		
		if ( bp_has_groups( $groups_args ) ) : ?>
	
			<?php echo "0[[SPLIT]]"; ?>
			
			<?php if ( $format == 'gp-posts-masonry' ) { ?><li class="gp-gutter-size"></li><?php } ?>
				
			<?php while ( bp_groups() ) : bp_the_group(); ?>

				<li <?php bp_group_class( array( 'gp-post-item' ) ); ?>>
				
					<?php if ( $format == 'gp-posts-list' ) { ?>
					
						<div class="item-avatar">
							<a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar_thumb() ?></a>
						</div>
						<div class="item">
							<div class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></div>
							<div class="item-meta">
								<?php if ( 'newest-groups' === $_POST['filter'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'created %s', 'aardvark-plugin' ), bp_get_group_date_created() ); ?></span>
								<?php elseif ( 'popular-groups' === $_POST['filter'] ) : ?>
									<span class="activity"><?php bp_group_member_count(); ?></span>
								<?php else : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'active %s', 'aardvark-plugin' ), bp_get_group_last_active() ); ?></span>
								<?php endif; ?>
							</div>
						</div>
								
					<?php } elseif ( $format != 'gp-posts-masonry' ) { ?>
			
						<a href="<?php bp_group_permalink() ?>" class="gp-bp-avatar">
							<span class="gp-bp-hover-effect"></span>
							<?php bp_group_avatar_thumb(); ?>
						</a>
							
					<?php } else { ?>

						<?php if ( $cover_images == 'enabled' ) {
								$cover_image_url = bp_attachments_get_attachment( 'url', array( 'object_dir' => 'groups', 'item_id' => bp_get_group_id() ) );
								if ( bp_group_use_cover_image_header() == '1' && $cover_image_url != '' ) { ?>
								<a href="<?php bp_group_permalink() ?>" class="gp-post-thumbnail" style="background-image: url(<?php echo esc_url( $cover_image_url ); ?>);">
									<span class="gp-bp-col-cover-overlay"><?php echo preg_replace( '/\D/', '', bp_get_group_member_count() ); ?></span>
									<?php if ( ! bp_disable_group_avatar_uploads() ) { ?>												
										<span class="gp-bp-col-avatar">
											<span class="gp-bp-hover-effect"></span>
											<?php bp_group_avatar_thumb(); ?>
										</span>
									<?php } ?>
								</a>
							<?php } 
						} ?>

						<div class="gp-loop-content<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?> gp-no-cover-image<?php } ?>">
					
							<?php if ( ( $cover_images == 'disabled' OR $cover_image_url == '' ) && ! bp_disable_group_avatar_uploads() ) { ?>					
								<span class="gp-bp-col-cover-overlay"><?php echo preg_replace( '/\D/', '', bp_get_group_member_count() ); ?></span>												
								<div class="gp-bp-col-avatar">
									<a href="<?php bp_group_permalink() ?>">
										<span class="gp-bp-hover-effect"></span>
										<?php bp_group_avatar_thumb(); ?>
									</a>
								</div>
							<?php } ?>
									
							<div class="gp-loop-title"><?php bp_group_link(); ?></div>
				
							<div class="gp-loop-meta">
								<?php if ( 'newest-groups' === $_POST['filter'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'created %s', 'aardvark-plugin' ), bp_get_group_date_created() ); ?></span>
								<?php elseif ( 'popular-groups' === $_POST['filter'] ) : ?>
									<span class="activity"><?php bp_group_member_count(); ?></span>
								<?php else : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'active %s', 'aardvark-plugin' ), bp_get_group_last_active() ); ?></span>
								<?php endif; ?>
							</div>
				
							<div class="gp-loop-text">
								<?php bp_group_description_excerpt(); ?>
							</div>
				
						</div>

					<?php } ?>
								
				</li>
							
			<?php endwhile; ?>

		<?php else: ?>

			<?php echo "-1[[SPLIT]]"; ?>
			<?php esc_html_e( 'There were no groups found, please try another filter.', 'aardvark-plugin' ); ?>

		<?php endif;
		
		die();
	}
	add_action( 'wp_ajax_bp_element_groups', 'ghostpool_ajax_bp_element_groups', 1 );
	add_action( 'wp_ajax_nopriv_bp_element_groups', 'ghostpool_ajax_bp_element_groups', 1 );
	
} ?>