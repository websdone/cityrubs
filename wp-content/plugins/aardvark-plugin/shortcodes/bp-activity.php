<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) {

	if ( ! function_exists( 'ghostpool_bp_activity' ) ) {
		function ghostpool_bp_activity( $atts, $content = null ) {
			
			global $exclude_types;
	
			extract( shortcode_atts( array(
				'title' => '',
				'post_form' => 'enabled',
				'scope' => '',
				'display_comments' => 'threaded',
				'allow_comments' => 'gp-comments-enabled',		
				'exclude_types' => '',
				'include' => '',
				'order' => 'DESC',
				'per_page' => '5',
				'max' => '',
				'show_hidden' => '',
				'search_terms' => '',
				'user_id' => '',	
				'object' => '',
				'action' => '',
				'primary_id' => '',
				'secondary_id' => '',
				'classes' => '',
				'css' => '',	
			), $atts ) );
		
			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_bp_activity_' . $i;
				
			// Classes
			$css_classes = array(
				'activity',
				$allow_comments,
				$classes,
			);
			$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
			$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	
			// Exclude activity types
			if ( ! empty( $exclude_types ) ) {
				if ( ! function_exists( 'ghostpool_exclude_activity_types' ) ) {
					function ghostpool_exclude_activity_types( $a, $activities ) {
			
						global $exclude_types;
 
						if ( ! bp_is_blog_page() )
							return $activities;

						$exclude_types = preg_replace( '/\s+/', '', $exclude_types );
						$types = explode( ',', $exclude_types );

						foreach ( $activities->activities as $key => $activity ) {

							foreach( $types as $type ) {
					
								if ( $activity->type == $type ) {
									unset( $activities->activities[$key] );
									$activities->activity_count = $activities->activity_count - 1;
									$activities->total_activity_count = $activities->total_activity_count - 1;
									$activities->pag_num = $activities->pag_num - 1;
								}
							}
	
						}
			
						$activities_new = array_values( $activities->activities );
						$activities->activities = $activities_new;
						return $activities;
					}
				}
				add_action( 'bp_has_activities','ghostpool_exclude_activity_types', 10, 2 );

			}
				
			// Activity query	
			$query_string = "scope=$scope&display_comments=$display_comments&include=$include&sort=$order&per_page=$per_page&max=$max&show_hidden=$show_hidden&search_terms=$search_terms&user_id=$user_id&object=$object&action=$action&primary_id=$primary_id&secondary_id=$secondary_id&count_total=count_query&page_arg=actsc";

			// Add to option for use in ajax function
			if ( ! update_option( 'ghostpool_activity_query', $query_string ) ) {
				add_option( 'ghostpool_activity_query', $query_string );
			} else { 
				update_option( 'ghostpool_activity_query', $query_string );
			}
			
			ob_start(); ?>
			
			<div id="buddypress">
		
				<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">

					<?php if ( $title ) { ?><h3 class="widgettitle"><?php echo esc_attr( $title ); ?></h3><?php } ?>
							
					<?php if ( is_user_logged_in() && $post_form == 'enabled' ) { bp_get_template_part( 'activity/post-form' ); } ?>
		
					<?php
		
					do_action( 'bp_before_activity_loop' ); ?>
		
					<?php
				
					if ( bp_has_activities( $query_string ) ) : ?>
		
						<?php if ( empty( $_POST['page'] ) ) : ?>
		
							<ul id="activity-stream" class="gp-section-loop activity-list item-list">
		
						<?php endif; ?>
		
						<?php while ( bp_activities() ) : bp_the_activity(); ?>

							<?php bp_get_template_part( 'activity/entry' ); ?>

						<?php endwhile; ?>
				
						<?php if ( bp_activity_has_more_items() ) : ?>

							<?php if ( function_exists( 'bp_activity_load_more_link' ) ) { ?>
						
								<li class="load-more">
									<a href="<?php bp_activity_load_more_link(); ?>"><?php esc_html_e( 'Load More', 'aardvark-plugin' ); ?></a>
								</li>
						
							<?php } ?>
						
						<?php endif; ?>
				
						<?php if ( empty( $_POST['page'] ) ) : ?>
						
							</ul>
					
						<?php endif; ?>
			
					<?php else : ?>
		
						<div id="message" class="info">
							<p><?php esc_html_e( 'Sorry, there was no activity found. Please try a different filter.', 'aardvark-plugin' ); ?></p>
						</div>
		
					<?php endif; ?>
		
					<?php do_action( 'bp_after_activity_loop' ); ?>
		
					<?php if ( empty( $_POST['page'] ) ) : ?>

						<form name="activity-loop-form" id="activity-loop-form" method="post">

							<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

						</form>

					<?php endif; ?>

				</div>
		
			</div>
			
			<?php 

			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;

		}
	}
	add_shortcode( 'gp_activity', 'ghostpool_bp_activity' );

	// Pass activity query string to ajax
	if ( ! function_exists( 'ghostpool_bp_activity_loop' ) ) {
		function ghostpool_bp_activity_loop( $ajax_string ) {
			if ( ! empty( $ajax_string ) ) {
				$ajax_string .= '&';
			}
			if ( bp_is_blog_page() ) {
				if ( WP_DEBUG OR false === ( $transient = get_transient( 'transient_query_string' ) ) ) {
    				$transient = $ajax_string . get_option( 'ghostpool_activity_query' );
   					set_transient( 'transient_query_string', $transient, 6 * HOUR_IN_SECONDS );		
   				} else {
					get_transient( 'transient_query_string' );
   				}
   				//print_r($transient);
   				return $transient;
			} else {
				$ajax_string .= 'per_page=20';	
				return $ajax_string;	
			}
		}
	}
	add_filter( 'bp_ajax_querystring', 'ghostpool_bp_activity_loop', 20, 2 );

} ?>