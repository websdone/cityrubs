<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'members' ) ) {

	if ( ! function_exists( 'ghostpool_bp_members' ) ) {
		function ghostpool_bp_members( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'title' => '',
				'format' => 'gp-bp-round-avatars',
				'max_members' => 22,
				'filters' => 'disabled',
				'member_default' => 'newest',	
				'cover_images' => 'enabled',	
				'classes' => '',
				'css' => '',
				'link_color' => '',
				'masonry_bg_color' => '',
				'masonry_border_color' => '',
				'title_color' => '',
				'text_color' => '',
			), $atts ) );
			
			global $members_template;
						
			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_buddypress_members_' . $i;			
			
			// Add CSS styling to header
			if ( function_exists( 'ghostpool_buddypress_css' ) ) {
				ghostpool_buddypress_css( $name, $link_color, $masonry_bg_color, $masonry_border_color, $title_color, $text_color );
			}
			
			// Cover image URL
			$cover_image_url = '';
	
			if ( $filters == 'enabled' ) {
				wp_enqueue_script( 'ghostpool-bp-element-members-js', plugins_url( '/shortcodes/assets/widget-members.js', dirname( __FILE__ ) ), array( 'jquery' ), bp_get_version() );
			}
			
			$max_members = ! empty( $max_members ) ? (int) $max_members : 8;

			$members_args = array(
				'user_id'  => 0,
				'type'     => $member_default,
				'per_page' => $max_members,
				'max'      => $max_members,
			);
			
			// Back up the global.
			$old_members_template = $members_template;

			// Classes
			$css_classes = array(
				'gp-bp-wrapper',
				'gp-bp-members',
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
							
				<?php if ( bp_has_members( $members_args ) ) : ?>
				
					<?php if ( $filters == 'enabled' ) { ?>
						<div class="item-options" id="members-list-options">
							<a href="<?php bp_members_directory_permalink(); ?>" id="newest-members" <?php if ( 'newest' === $member_default ) : ?>class="selected"<?php endif; ?>><?php esc_html_e( 'Newest', 'aardvark-plugin' ); ?></a>
							<a href="<?php bp_members_directory_permalink(); ?>" id="recently-active-members" <?php if ( 'active' === $member_default ) : ?>class="selected"<?php endif; ?>><?php esc_html_e( 'Active', 'aardvark-plugin' ); ?></a>
							<?php if ( bp_is_active( 'friends' ) ) : ?>
								<a href="<?php bp_members_directory_permalink(); ?>" id="popular-members" <?php if ( 'popular' === $member_default ) : ?>class="selected"<?php endif; ?>><?php esc_html_e( 'Popular', 'aardvark-plugin' ); ?></a>
							<?php endif; ?>
						</div>
					<?php } ?>
			
					<ul class="<?php echo esc_attr( $css_classes ); ?>" aria-live="polite" aria-relevant="all" aria-atomic="true">

						<?php if ( $format == 'gp-posts-masonry' ) { ?><li class="gp-gutter-size"></li><?php } ?>
			
						<?php while ( bp_members() ) : bp_the_member(); ?>
						
							<li <?php bp_member_class( array( 'gp-post-item' ) ); ?>>
							
								<?php if ( $format == 'gp-posts-list' ) { ?>
					
									<div class="item-avatar">
										<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar(); ?></a>
									</div>
									<div class="item">
										<div class="item-title"><a href="<?php bp_member_permalink() ?>" title="<?php bp_member_name() ?>"><?php bp_member_name() ?></a></div>
										<div class="item-meta">										
											<?php if ( 'newest' == $member_default ) : ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php bp_member_registered(); ?></span>
											<?php elseif ( 'active' == $member_default ) : ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
											<?php else : ?>
												<span class="activity"><?php bp_member_total_friend_count(); ?></span>
											<?php endif; ?>
										</div>
									</div>
								
								<?php } elseif ( $format != 'gp-posts-masonry' ) { ?>
						
									<a href="<?php bp_member_permalink(); ?>" class="gp-bp-avatar">
										<span class="gp-bp-hover-effect"></span>
										<?php bp_member_avatar(); ?>
										<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
									</a>
											
								<?php } else { ?>
							
									<?php if ( $cover_images == 'enabled' ) {
										$cover_image_url = bp_attachments_get_attachment( 'url', array( 'object_dir' => 'members', 'item_id' => bp_get_member_user_id() ) );
										if ( bp_displayed_user_use_cover_image_header() == '1' && $cover_image_url != '' ) { ?>
											<a href="<?php bp_member_permalink(); ?>" class="gp-post-thumbnail" style="background-image: url(<?php echo esc_url( $cover_image_url ); ?>);">									
												<span class="gp-bp-col-avatar">
													<span class="gp-bp-hover-effect"></span>
													<?php bp_member_avatar(); ?>
													<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
												</span>
											</a>
										<?php } 
									} ?>

									<div class="gp-loop-content<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?> gp-no-cover-image<?php } ?>">		
				
										<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?>												
											<div class="gp-bp-col-avatar">
												<a href="<?php bp_member_permalink(); ?>">
													<span class="gp-bp-hover-effect"></span>
													<?php bp_member_avatar(); ?>
													<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
												</a>
											</div>
										<?php } ?>
		
										<div class="gp-loop-title">
											<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
										</div>

										<div class="gp-loop-meta">
											<?php if ( 'newest' == $member_default ) : ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php bp_member_registered(); ?></span>
											<?php elseif ( 'active' == $member_default ) : ?>
												<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
											<?php else : ?>
												<span class="activity"><?php bp_member_total_friend_count(); ?></span>
											<?php endif; ?>
										</div>
				
										<?php if ( bp_get_member_latest_update() ) : ?>
											<div class="gp-loop-text">
												<?php bp_member_latest_update(); ?>
											</div>
										<?php endif; ?>
								
									</div>
								
								<?php } ?>
								
							</li>
							
						<?php endwhile; ?>
						
					</ul>	

					<?php wp_nonce_field( 'ghostpool_bp_element_members', '_wpnonce-members', false ); ?>
					<input type="hidden" name="gp-members-element-max" class="gp-members-element-max" value="<?php echo esc_attr( $max_members ); ?>" />
					<input type="hidden" name="gp-members-element-format" class="gp-members-element-format" value="<?php echo esc_attr( $format ); ?>" />
					<input type="hidden" name="gp-members-element-cover-images" class="gp-members-element-cover-images" value="<?php echo esc_attr( $cover_images ); ?>" />

				<?php else: ?>

					<?php esc_html_e( 'There are no members to display.', 'aardvark-plugin' ) ?>

				<?php endif; ?>
				
			</div>
				
			<?php		
			
			// Restore the global.
			$members_template = $old_members_template;
			
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
		
		}
	}
	add_shortcode( 'gp_bp_members', 'ghostpool_bp_members' );
	
	/**
	 * Ajax member display
	 *
	 */
	function ghostpool_ajax_bp_element_members() {
	
		check_ajax_referer( 'ghostpool_bp_element_members' );
		
		// Setup some variables to check.
		$filter = ! empty( $_POST['filter'] ) ? $_POST['filter'] : 'newest-members';
		$max_members = ! empty( $_POST['max-members'] ) ? absint( $_POST['max-members'] ) : 22;
		$format = isset( $_POST['format'] ) ? $_POST['format'] : 'gp-bp-round-avatars';
		$cover_images = isset( $_POST['cover_images'] ) ? $_POST['cover_images'] : 'enabled';
	
		switch ( $filter ) {
			case 'newest-members':
				$type = 'newest';
			break;
			case 'popular-members' :
				if ( bp_is_active( 'friends' ) ) {
					$type = 'popular';
				} else {
					$type = 'active';
				}
			break;
			case 'recently-active-members' :
			default :
				$type = 'newest';
			break;
		}
		
		$members_args = array(
			'user_id'  => 0,
			'type'     => $type,
			'per_page' => $max_members,
			'max'      => $max_members,
			'populate_extras' => true,
			'search_terms'    => false,
		);
		
		if ( bp_has_members( $members_args ) ) : ?>
	
			<?php echo "0[[SPLIT]]"; ?>
			
			<?php if ( $format == 'gp-posts-masonry' ) { ?><li class="gp-gutter-size"></li><?php } ?>
		
			<?php while ( bp_members() ) : bp_the_member(); ?>

				<li <?php bp_member_class( array( 'gp-post-item' ) ); ?>>
		
					<?php if ( $format == 'gp-posts-list' ) { ?>
					
						<div class="item-avatar">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar(); ?></a>
						</div>
						<div class="item">
							<div class="item-title"><a href="<?php bp_member_permalink() ?>" title="<?php bp_member_name() ?>"><?php bp_member_name() ?></a></div>
							<div class="item-meta">										
								<?php if ( 'active' === $type ) : ?>
									><span class="activity"><?php bp_member_last_active(); ?></span>
								<?php elseif ( 'newest' === $type ) : ?>
									<span class="activity"><?php bp_member_registered(); ?></span>
								<?php elseif ( bp_is_active( 'friends' ) ) : ?>
									<span class="activity"><?php bp_member_total_friend_count(); ?></span>
								<?php endif; ?>
							</div>
						</div>
					
					<?php } elseif ( $format != 'gp-posts-masonry' ) { ?>
			
						<a href="<?php bp_member_permalink() ?>" class="gp-bp-avatar">
							<span class="gp-bp-hover-effect"></span>
							<?php bp_member_avatar(); ?>
							<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
						</a>
								
					<?php } else { ?>
			
						<?php if ( $cover_images == 'enabled' ) {
							$cover_image_url = bp_attachments_get_attachment( 'url', array( 'object_dir' => 'members', 'item_id' => bp_get_member_user_id() ) );
							if ( bp_displayed_user_use_cover_image_header() == '1' && $cover_image_url != '' ) { ?>
								<a href="<?php bp_member_permalink(); ?>" class="gp-post-thumbnail" style="background-image: url(<?php echo esc_url( $cover_image_url ); ?>);">											
									<span class="gp-bp-col-avatar">
										<span class="gp-bp-hover-effect"></span>
										<?php bp_member_avatar(); ?>
										<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
									</span>
								</a>
							<?php } 
						} ?>
													
						<div class="gp-loop-content<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?> gp-no-cover-image<?php } ?>">
				
							<?php if ( $cover_images == 'disabled' OR $cover_image_url == '' ) { ?>												
								<div class="gp-bp-col-avatar">
									<a href="<?php bp_member_permalink() ?>">
										<span class="gp-bp-hover-effect"></span>
										<?php bp_member_avatar(); ?>
										<?php if ( function_exists( 'ghostpool_is_user_online' ) ) { ghostpool_is_user_online( bp_get_member_user_id(), bp_get_member_last_active() ); } ?>
									</a>
								</div>
							<?php } ?>
		
							<div class="gp-loop-title">
								<a href="bp_member_permalink"><?php bp_member_name(); ?></a>
							</div>

							<div class="gp-loop-meta">
								<?php if ( 'active' === $type ) : ?>
									><span class="activity"><?php bp_member_last_active(); ?></span>
								<?php elseif ( 'newest' === $type ) : ?>
									<span class="activity"><?php bp_member_registered(); ?></span>
								<?php elseif ( bp_is_active( 'friends' ) ) : ?>
									<span class="activity"><?php bp_member_total_friend_count(); ?></span>
								<?php endif; ?>
							</div>
				
							<?php if ( bp_get_member_latest_update() ) : ?>
								<div class="gp-loop-text">
									<?php bp_member_latest_update(); ?>
								</div>
							<?php endif; ?>
				
						</div>
						
					<?php } ?>	
				
				</li>
							
			<?php endwhile; ?>

		<?php else: ?>

			<?php echo "-1[[SPLIT]]"; ?>
			<?php esc_html_e( 'There were no members found, please try another filter.', 'aardvark-plugin' ) ?>

		<?php endif;
		
		die();
	}
	add_action( 'wp_ajax_bp_element_members', 'ghostpool_ajax_bp_element_members', 1 );
	add_action( 'wp_ajax_nopriv_bp_element_members', 'ghostpool_ajax_bp_element_members', 1 );
	
} ?>