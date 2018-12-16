<?php if ( ! function_exists( 'ghostpool_featured_box' ) ) {

	function ghostpool_featured_box( $atts, $content = null ) {	
	
		global $gp_counter, $format, $title, $title_length, $excerpt_length, $meta_cats, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes; $gp_counter = 1;
		
		extract( shortcode_atts( array(
			'cats' => '',
			'page_ids' => '',
			'post_types' => 'post',
			'format' => 'gp-featured-box-2-2-2-2',
			'layout' => 'gp-wide',
			'spacing' => '0',
			'orderby' => 'newest',
			'offset' => '',
			'title' => 'enabled',
			'title_length' => '0',
			'excerpt_length' => '0',
			'meta_author' => '',
			'meta_date' => '',
			'meta_views' => '',
			'meta_likes' => '',
			'meta_comment_count' => '',
			'meta_cats' => '',
			'meta_tags' => '',
			'classes' => '',
			'css' => '',
		), $atts ) );
		
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_featured_box_wrapper_' . $i;

		// Add CSS styling to header	
		if ( function_exists( 'ghostpool_featured_box_css' ) ) {
			ghostpool_featured_box_css( $name, $spacing, $layout );
		}
		
		// Number of items	
		if ( $format == 'gp-featured-box-2-2-2-2' ) {
			$per_page = 8;
		} elseif ( $format == 'gp-featured-box-2-1-2' OR $format == 'gp-featured-box-1-2-2' ) {
			$per_page = 5;	
		} elseif ( $format == 'gp-featured-box-1-1' ) {
			$per_page = 2;
		} else {
			$per_page = 1;
		}
																		
		$args = array(
			'post_status'         => 'publish',
			'post_type'     	  => $post_types ? explode( ',', $post_types ) : '',
			'post__in'      	  => $page_ids ? explode( ',', $page_ids ) : '',
			ghostpool_cats( $cats, 'param' ) => ghostpool_cats( $cats, 'variables' ),
			'orderby' 		      => ghostpool_orderby( $orderby, 'orderby' ),
			'order' 		      => ghostpool_orderby( $orderby, 'order' ),	
			'meta_key' 		      => ghostpool_orderby( $orderby, 'meta_key' ),	
			'meta_query' 		  => ghostpool_orderby( $orderby, 'meta_query' ),
			'posts_per_page'      => $per_page,
			'offset' 		      => $offset,	
			'paged'          	  => 1,
			'ignore_sticky_posts' => 1,
		);
		
		$args = apply_filters( 'ghostpool_featured_box_element_query', $args, $post_types, $page_ids, $cats, $orderby, $per_page, $offset );
		
		$gp_query = new WP_Query( $args );  
		
		// Classes
		$css_classes = array(
			'gp-featured-box-wrapper',
			$format,
			$layout,
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		
		ob_start(); ?>		
		
		<?php if ( $gp_query->have_posts() ) : ?>

			<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">	
	
				<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>
												
					<?php if ( $format == 'gp-featured-box-2-2-2-2' ) { 
						get_template_part( 'lib/sections/featured-box/featured-box-2-2-2-2' );
					} elseif ( $format == 'gp-featured-box-2-1-2' ) { 
						get_template_part( 'lib/sections/featured-box/featured-box-2-1-2' );
					} elseif ( $format == 'gp-featured-box-1-2-2' ) { 
						get_template_part( 'lib/sections/featured-box/featured-box-1-2-2' );
					} elseif ( $format == 'gp-featured-box-1-1' ) { 
						get_template_part( 'lib/sections/featured-box/featured-box-1-1' );
					} else {
						get_template_part( 'lib/sections/featured-box/featured-box-1' );
					} ?>
			
				<?php $gp_counter++; endwhile; ?>
			
			</div>

		<?php else : ?>

			<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'aardvark-plugin' ); ?></strong>

		<?php endif; wp_reset_postdata(); ?>

		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}

}
add_shortcode( 'gp_featured_box', 'ghostpool_featured_box' ); ?>