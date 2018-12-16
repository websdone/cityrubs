<?php if ( function_exists( 'em_init' ) ) {
	
	if ( ! function_exists( 'ghostpool_events' ) ) {
		function ghostpool_events( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'title' => esc_html__( 'Events', 'aardvark-plugin' ),	
				'scope' => 'future',
    			'order' => 'ASC',
    			'limit' => 5,
    			'category' => 0,
    			'format' => '<li>#_EVENTLINK<ul><li>#_EVENTDATES</li><li>#_LOCATIONTOWN</li></ul></li>',
    			'orderby' => 'event_start_date,event_start_time,event_name',
				'all_events' => 0,
				'all_events_text' => esc_html__( 'all events', 'aardvark-plugin' ),
				'no_events_text' => '<li>' . esc_html__( 'No events', 'aardvark-plugin' ) . '</li>',
				'classes' => '',	
				'css' => '',
			), $atts ) );			
						
			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_events_' . $i;
						
			// Classes
			$css_classes = array(
				'gp-events-element',
				'widget',
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
		
			the_widget( 'EM_Widget', $atts, $args );
		
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
		}
	}
	add_shortcode( 'gp_events', 'ghostpool_events' );

} ?>