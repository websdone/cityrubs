<?php 

if ( ! function_exists( 'ghostpool_statistics' ) ) {

	function ghostpool_statistics( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'title' => '',
			'format' => 'gp-stats-columns',
			'posts' => 'enabled',
			'posts_title' => esc_html__( 'Posts', 'aardvark-plugin' ),
			'posts_icon' => 'fa fa-file-text-o',
			'posts_title_color' => '',
			'posts_icon_color' => '',
			'posts_number_color' => '',
			'comments' => 'enabled',
			'comments_title' => esc_html__( 'Comments', 'aardvark-plugin' ),
			'comments_icon' => 'fa fa-comment-o',
			'comments_title_color' => '',
			'comments_icon_color' => '',
			'comments_number_color' => '',
			'activity' => 'enabled',
			'activity_title' => esc_html__( 'Activity', 'aardvark-plugin' ),
			'activity_icon' => 'fa fa-commenting-o',
			'activity_title_color' => '',
			'activity_icon_color' => '',
			'activity_number_color' => '',
			'blogs' => 'enabled',
			'blogs_title' => esc_html__( 'Blogs', 'aardvark-plugin' ),
			'blogs_icon' => 'fa fa-sitemap',
			'blogs_title_color' => '',
			'blogs_icon_color' => '',
			'blogs_number_color' => '',
			'members' => 'enabled',
			'members_title' => esc_html__( 'Members', 'aardvark-plugin' ),
			'members_icon' => 'fa fa-user-o',
			'members_title_color' => '',
			'members_icon_color' => '',
			'members_number_color' => '',
			'online' => 'enabled',
			'online_title' => esc_html__( 'Online', 'aardvark-plugin' ),
			'online_icon' => 'fa fa-user-circle-o',
			'online_title_color' => '',
			'online_icon_color' => '',
			'online_number_color' => '',
			'groups' => 'enabled',
			'groups_title' => esc_html__( 'Groups', 'aardvark-plugin' ),
			'groups_icon' => 'fa fa-users',
			'groups_title_color' => '',
			'groups_icon_color' => '',
			'groups_number_color' => '',
			'forums' => 'enabled',
			'forums_title' => esc_html__( 'Forums', 'aardvark-plugin' ),
			'forums_icon' => 'fa fa-list',
			'forums_title_color' => '',
			'forums_icon_color' => '',
			'forums_number_color' => '',
			'topics' => 'enabled',
			'topics_title' => esc_html__( 'Topics', 'aardvark-plugin' ),
			'topics_icon' => 'fa fa-comments-o',
			'topics_title_color' => '',
			'topics_icon_color' => '',
			'topics_number_color' => '',
			'dummy_data' => 'disabled',
			'classes' => '',
			'css' => '',
		), $atts ) );
		
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_statistics_wrapper_' . $i;
			
		// Get activity count	
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) {
			if ( ! function_exists( 'ghostpool_bp_activity_updates' ) ) {
				function ghostpool_bp_activity_updates() {
					global $bp, $wpdb;
					if ( ! $count = wp_cache_get( 'gp_bp_activity_updates', 'bp' ) ) {
						$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE type = %s AND a.component = '{$bp->activity->id}'", 'activity_update' ) );
						if ( ! $count ) {
							$count == 0;
						}	
						if ( ! empty( $count ) ) {
							wp_cache_set( 'gp_bp_activity_updates', $count, 'bp' );
						}	
					}
					return $count;
				}
			}
			
			if ( ! function_exists( 'ghostpool_bp_activity_updates_delete_clear_cache' ) ) {
				function ghostpool_bp_activity_updates_delete_clear_cache( $args ) {
					if ( $args['type'] && $args['type'] == 'activity_update' )
						wp_cache_delete( 'gp_bp_activity_updates' );
				}
			}	
			add_action( 'bp_activity_delete', 'ghostpool_bp_activity_updates_delete_clear_cache' );
	
			if ( ! function_exists( 'ghostpool_bp_activity_updates_add_clear_cache' ) ) {
				function ghostpool_bp_activity_updates_add_clear_cache() {
					wp_cache_delete( 'gp_bp_activity_updates' );
				}
			}
			add_action( 'bp_activity_posted_update', 'ghostpool_bp_activity_updates_add_clear_cache' );
			
		}
		
		// Count number of users online
		if ( function_exists( 'bp_is_active' ) && ! function_exists( 'ghostpool_users_online' ) ) {
			function ghostpool_users_online() {
				$online = 0;
				if ( bp_has_members( 'user_id=0&type=online&per_page=99999&populate_extras=0' ) ) :
					while ( bp_members() ) : bp_the_member();
						$online++;
					endwhile;
				endif;
				return number_format( $online );
			}
		}
		
		// Classes
		$css_classes = array(
			'gp-statistics-wrapper',
			$format,
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
							
		ob_start(); ?>		

			<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">			

				<?php if ( $title ) { ?><h3 class="widgettitle"><?php echo esc_attr( $title ); ?></h3><?php } ?>

				<?php if ( $posts == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php $count_posts = wp_count_posts(); ?>
						<?php if ( $posts_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $posts_icon ); ?>"<?php if ( $posts_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $posts_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $posts_title_color != '' ) { ?> style="color: <?php echo esc_attr( $posts_title_color ); ?>"<?php } ?>><?php echo esc_attr( $posts_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $posts_number_color != '' ) { ?> style="color: <?php echo esc_attr( $posts_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 3511 ); } else { echo number_format( $count_posts->publish ); } ?></div>
						</div>	
					</div>	
				<?php } ?>

				<?php if ( $comments == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php $comments_count = wp_count_comments(); ?>
						<?php if ( $comments_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $comments_icon ); ?>"<?php if ( $comments_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $comments_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $comments_title_color != '' ) { ?> style="color: <?php echo esc_attr( $comments_title_color ); ?>"<?php } ?>><?php echo esc_attr( $comments_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $comments_number_color != '' ) { ?> style="color: <?php echo esc_attr( $comments_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 9889 ); } else { echo number_format( $comments_count->approved ); } ?></div>
						</div>		
					</div>	
				<?php } ?>

				<?php if ( $blogs == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $blogs_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $blogs_icon ); ?>"></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"><?php echo esc_attr( $blogs_title ); ?></div>
							<div class="gp-stats-count"><?php if ( $dummy_data == 'enabled' ) { echo number_format( 225 ); } elseif ( is_multisite() ) { echo number_format( get_blog_count() ); } ?></div>
						</div>		
					</div>	
				<?php } ?>

				<?php if ( $activity == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $activity_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $activity_icon ); ?>"<?php if ( $activity_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $activity_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $activity_title_color != '' ) { ?> style="color: <?php echo esc_attr( $activity_title_color ); ?>"<?php } ?>><?php echo esc_attr( $activity_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $activity_number_color != '' ) { ?> style="color: <?php echo esc_attr( $activity_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 4302 ); } elseif ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) { echo number_format( ghostpool_bp_activity_updates() ); } ?></div>
						</div>		
					</div>	
				<?php } ?>
															
				<?php if ( $members == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php $user_count = count_users(); ?>
						<?php if ( $members_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $members_icon ); ?>"<?php if ( $members_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $members_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $members_title_color != '' ) { ?> style="color: <?php echo esc_attr( $members_title_color ); ?>"<?php } ?>><?php echo esc_attr( $members_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $members_number_color != '' ) { ?> style="color: <?php echo esc_attr( $members_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 12744 ); } elseif ( function_exists( 'bp_is_active' ) ) { echo number_format( $user_count['total_users'] ); } ?></div>
						</div>	
					</div>	
				<?php } ?>

				<?php if ( $online == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $online_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $online_icon ); ?>"<?php if ( $online_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $online_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $online_title_color != '' ) { ?> style="color: <?php echo esc_attr( $online_title_color ); ?>"<?php } ?>><?php echo esc_attr( $online_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $online_number_color != '' ) { ?> style="color: <?php echo esc_attr( $online_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 125 ); } elseif ( function_exists( 'bp_is_active' ) ) { echo ghostpool_users_online(); } ?></div>
						</div>	
					</div>	
				<?php } ?>

				<?php if ( $groups == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $groups_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $groups_icon ); ?>"<?php if ( $groups_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $groups_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<div class="gp-stats-title"<?php if ( $groups_title_color != '' ) { ?> style="color: <?php echo esc_attr( $groups_title_color ); ?>"<?php } ?>><?php echo esc_attr( $groups_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $groups_number_color != '' ) { ?> style="color: <?php echo esc_attr( $groups_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 899 ); } elseif ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) { echo number_format( groups_get_total_group_count() ); } ?></div>
						</div>	
					</div>	
				<?php } ?>

				<?php if ( $forums == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $forums_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $forums_icon ); ?>"<?php if ( $forums_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $forums_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<?php $count_posts = wp_count_posts( 'forum' ); ?>
							<div class="gp-stats-title"<?php if ( $forums_title_color != '' ) { ?> style="color: <?php echo esc_attr( $forums_title_color ); ?>"<?php } ?>><?php echo esc_attr( $forums_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $forums_number_color != '' ) { ?> style="color: <?php echo esc_attr( $forums_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 88 ); } elseif ( function_exists( 'is_bbpress' ) ) { echo number_format( $count_posts->publish ); } ?></div>
						</div>		
					</div>	
				<?php } ?>

				<?php if ( $topics == 'enabled' ) { ?>
					<div class="gp-stats-col">
						<?php if ( $topics_icon ) { ?><i class="gp-stats-icon <?php echo esc_attr( $topics_icon ); ?>"<?php if ( $topics_icon_color != '' ) { ?> style="color: <?php echo esc_attr( $topics_icon_color ); ?>"<?php } ?>></i><?php } ?>
						<div class="gp-stats-details">
							<?php $count_posts = wp_count_posts( 'topic' ); ?>
							<div class="gp-stats-title"<?php if ( $topics_title_color != '' ) { ?> style="color: <?php echo esc_attr( $topics_title_color ); ?>"<?php } ?>><?php echo esc_attr( $topics_title ); ?></div>
							<div class="gp-stats-count"<?php if ( $topics_number_color != '' ) { ?> style="color: <?php echo esc_attr( $topics_number_color ); ?>"<?php } ?>><?php if ( $dummy_data == 'enabled' ) { echo number_format( 23543 ); } elseif ( function_exists( 'is_bbpress' ) ) { echo number_format( $count_posts->publish ); } ?></div>
						</div>	
					</div>	
				<?php } ?>
							
			</div>
									
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}

}
add_shortcode( 'gp_statistics', 'ghostpool_statistics' ); ?>