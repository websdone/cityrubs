<?php if ( function_exists( 'bp_is_active' ) && defined( 'BPS_FORM' ) ) {

	if ( ! function_exists( 'ghostpool_bp_profile_search' ) ) {
		function ghostpool_bp_profile_search( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'title' => '',
				'form_id' => '',
				'display' => 'all',
				'format' => 'gp-large',
				//'template_name' => '',
				'classes' => '',
				'css' => '',
				'text_color' => '',
				'box_bg_color' => '',
				'box_border_color' => '',
			), $atts ) );

			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_buddypress_profile_search_' . $i;		

			// Add CSS styling to header
			if ( function_exists( 'ghostpool_bp_profile_search_css' ) ) {
				ghostpool_bp_profile_search_css( $name, $text_color, $box_bg_color, $box_border_color );
			}
				
			// Default template
			if ( $template_name == '' ) {
				$template_name = 'members/bps-form-sample-2';
			}
			
			// If no form ID is entered find first form
			if ( $form_id == '' OR $form_id == '0' ) {
				$args  = array(
					'post_type'   => 'bps_form',
					'title'       => 'Member Search',
					'posts_per_page' => '1',
				);
				$query = new WP_Query( $args );
				$posts = $query->posts;
				if ( ! empty( $posts ) && is_array( $posts ) ) {
					foreach ( $posts as $post ) {
						$form_id = $post->ID;
						break;
					}
				}
				
			}	
			
			// Classes
			$css_classes = array(
				'gp-bps-wrapper',
				$format,
				$classes,
			);
			$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
			$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );	
			
			ob_start(); ?>
			
			<?php if ( is_user_logged_in() OR ( ! is_user_logged_in() && $display == 'all' ) ) { ?>
			
				<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">
				
					<?php if ( $title ) { ?><div class="gp-bps-title"><?php echo esc_attr( $title ); ?></div><?php } ?>
			
					<?php echo do_shortcode( '[bps_form id="' . $form_id . '"]' ); ?>
			
				</div>
			
			<?php } elseif ( ! is_user_logged_in() && $display == 'login-form' ) { ?>
			
				<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?> gp-login-display">
				
					<?php get_template_part( 'lib/sections/login/login-form' ); ?>
				
				</div>
				
			<?php } ?>
						
			<?php
			
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;

		}
	}
	add_shortcode( 'gp_bp_profile_search', 'ghostpool_bp_profile_search' );

} ?>