<?php if ( ! function_exists( 'ghostpool_sensei_courses' ) ) {

	function ghostpool_sensei_courses( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'format' => 'gp-posts-masonry',
			'style' => 'gp-style-classic',
			'alignment' => 'gp-align-left',
			'teacher' => '',
			'cats' => '',
			'exclude' => '',
			'include' => '',
			'number' => '8',
			'orderby' => 'date',
			'order' => 'desc',
			'purchase_button' => '',
			'meta_author' => '',
			'meta_lessons' => '',
			'meta_cats' => '',
			'meta_progress' => '',
			'meta_previews' => '',
			'classes' => '',
			'css' => '',
			'title_color' => '',
			'post_title_color' => '',
			'post_title_hover_color' => '',
			'post_link_color' => '',
			'post_link_hover_color' => '',
			'post_text_color' => '',
			'meta_text_color' => '',
			'masonry_bg_color' => '',
			'masonry_border_color' => '',
		), $atts ) );
		
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_sensei_courses_wrapper_' . $i;
		
		// Options
		$GLOBALS['ghostpool_courses_format'] = $format;
		$GLOBALS['ghostpool_courses_style'] = $style;
		$GLOBALS['ghostpool_courses_alignment'] = $alignment;
		$GLOBALS['ghostpool_course_purchase_button'] = $purchase_button;

		// Add CSS styling to header
		if ( function_exists( 'ghostpool_posts_css' ) ) {
			ghostpool_posts_css( $name, $title_color, $post_title_color, $post_title_hover_color, $post_link_color, $post_link_hover_color, $post_text_color, $meta_text_color, $masonry_bg_color, $masonry_border_color );
		}
				
		// Hide meta data classes
		if ( $meta_author != '1' ) {
			$meta_author_class = ' gp-hide-meta-author'; 
		} else {
			$meta_author_class = '';
		}
		
		if ( $meta_lessons != '1' ) {
			$meta_lessons_class = ' gp-hide-meta-lessons'; 
		} else {
			$meta_lessons_class = '';
		}
		
		if ( $meta_cats != '1' ) {
			$meta_cats_class = ' gp-hide-meta-cats'; 
		} else {
			$meta_cats_class = '';
		}
		
		if ( $meta_progress != '1' ) {
			$meta_progress_class = ' gp-hide-meta-progress'; 
		} else {
			$meta_progress_class = '';
		}
				
		if ( $meta_previews != '1' ) {
			$meta_previews_class = ' gp-hide-meta-previews'; 
		} else {
			$meta_previews_class = '';
		}

		// Classes
		$css_classes = array(
			$meta_author_class,
			$meta_lessons_class,
			$meta_cats_class,
			$meta_progress_class,
			$meta_previews_class,
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
				
		ob_start(); ?>	
		
		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">

			<?php echo do_shortcode( '[sensei_courses 
			teacher="' . $teacher . '"
			category="' . $cats . '"
			exclude="' . $exclude . '"
			ids="' . $include . '"
			number="' . $number . '"
			orderby="' . $orderby . '"
			order="' . $order . '"]' ); ?>
			
		</div>	
									
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		$GLOBALS['ghostpool_courses_format'] = null;
		$GLOBALS['ghostpool_courses_style'] = null;
		$GLOBALS['ghostpool_courses_alignment'] = null;

	}

}
add_shortcode( 'gp_sensei_courses', 'ghostpool_sensei_courses' ); ?>