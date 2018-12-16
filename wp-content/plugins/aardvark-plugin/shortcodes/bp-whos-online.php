<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'members' ) ) {
	
	if ( ! function_exists( 'ghostpool_bp_whos_online' ) ) {
		function ghostpool_bp_whos_online( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'title' => '',
				'format' => 'gp-large-avatars',
				'max_members' => 20,		
				'classes' => '',	
				'css' => '',
				'avatar_border_color' => '',
			), $atts ) );			
						
			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_bp_whos_online_' . $i;

			// Add CSS styling to header
			if ( function_exists( 'ghostpool_buddypress_css' ) ) {
				ghostpool_buddypress_css( $name, '', '', $avatar_border_color );
			}
						
			// Classes
			$css_classes = array(
				'gp-bp-element',
				$format,
				$classes,
			);
			$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
			$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );

			$args = array(
				'before_widget' => '<div id="' . sanitize_html_class( $name ) . '" class="' . esc_attr( $css_classes ) . '">',
				'end_widget' => '</div>',
				'before_title'  => '<h3 class="widgettitle">',
				'after_title'   => '</h3>',
			);
				
			ob_start();
		
			the_widget( 'BP_Core_Whos_Online_Widget', $atts, $args );
		
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
		}
	}
	add_shortcode( 'gp_bp_whos_online', 'ghostpool_bp_whos_online' );

} ?>