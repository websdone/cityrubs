<?php 

if ( ! function_exists( 'ghostpool_showcase' ) ) {

	function ghostpool_showcase( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',
			'cats' => '', 
			'page_ids' => '',
			'post_types' => 'post',
			'ranking' => 'gp-no-ranking',
			'format' => 'gp-posts-horizontal',	
			'style' => 'gp-style-classic',
			'alignment' => 'gp-align-left',
			'orderby' => 'newest',
			'filter_cats' => '',
			'filter_date' => '',
			'filter_title' => '',					
			'filter_comment_count' => '',
			'filter_views' => '',
			'filter_likes' => '',
			'filter_cat_id' => '',
			'per_page' => '5',
			'offset' => '',
			'large_excerpt_length' => '80',
			'small_excerpt_length' => '0',
			'large_meta_author' => '',
			'large_meta_date' => '',
			'large_meta_comment_count' => '',
			'large_meta_views' => '',
			'large_meta_likes' => '',
			'large_meta_cats' => '',
			'large_meta_tags' => '',	
			'small_meta_author' => '',
			'small_meta_date' => '',
			'small_meta_comment_count' => '',
			'small_meta_views' => '',
			'small_meta_likes' => '',
			'small_meta_cats' => '',
			'small_meta_tags' => '',
			'large_read_more_link' => 'disabled',
			'small_read_more_link' => 'disabled',
			'page_arrows' => 'disabled',
			'pagination' => 'disabled',
			'see_all' => 'disabled',
			'see_all_link' => '',
			'see_all_text' => esc_html__( 'See All Items', 'aardvark-plugin' ),
			'classes' => '',
			'css' => '',
			'icon_color' => '',
			'icon' => '',
			'title_color' => '',
			'post_title_color' => '',
			'post_title_hover_color' => '',
			'post_link_color' => '',
			'post_link_hover_color' => '',
			'post_text_color' => '',
			'meta_text_color' => '',
			'loop_border_color' => '',
			'ranking_bg_color' => '',
			'ranking_text_color' => '',
		), $atts ) );
		
		// Load filter variables
		$type['cats'] = $filter_cats;
		$type['date'] = $filter_date;
		$type['title'] = $filter_title;
		$type['comment_count'] = $filter_comment_count;
		$type['views'] = $filter_views;
		$type['likes'] = $filter_likes;

		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_showcase_wrapper_' . $i;

		// Add CSS styling to header
		if ( function_exists( 'ghostpool_posts_css' ) ) {
			ghostpool_posts_css( $name, $title_color, $icon_color, $post_title_color, $post_title_hover_color, $post_link_color, $post_link_hover_color, $post_text_color, $meta_text_color, '', $loop_border_color, $ranking_bg_color, $ranking_text_color );
		}
				
		// Post counter
		$counter = 1;
		
		// Ranking counter;
		$ranking_counter = 1;
															
		$args = array(
			'post_status'         => 'publish',
			'post_type'      	  => $post_types ? explode( ',', $post_types ) : '',
			'post__in'            => $page_ids ? explode( ',', $page_ids ) : '',
			ghostpool_cats( $cats, 'param' ) => ghostpool_cats( $cats, 'variables' ),
			'orderby' 		      => ghostpool_orderby( $orderby, 'orderby' ),
			'order' 		      => ghostpool_orderby( $orderby, 'order' ),	
			'meta_key' 		      => ghostpool_orderby( $orderby, 'meta_key' ),
			'meta_query' 		  => ghostpool_orderby( $orderby, 'meta_query' ),
			'posts_per_page'      => $per_page,
			'offset' 		      => $offset,
			'paged'          	  => $pagination != 'disabled' ? ghostpool_paged() : 1,
			'ignore_sticky_posts' => 1,
			'post__not_in' 		  => apply_filters( 'ghostpool_post_not_in', array( get_the_ID() ) ),
		);
		
		$args = apply_filters( 'ghostpool_showcase_element_query', $args, $post_types, $page_ids, $cats, $orderby, $per_page, $offset, $pagination );
		
		$gp_query = new WP_Query( $args );
		
		// Classes
		$css_classes = array(
			'gp-showcase-wrapper',
			'gp-ajax-element',
			$format,
			$style,
			$alignment,
			$ranking,
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
		
		ob_start(); ?>	

		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>" data-type="showcase"<?php if ( function_exists( 'ghostpool_filter_variables' ) ) { echo ghostpool_filter_variables( get_the_ID(), $cats, $post_types, $format, $style, $orderby, $per_page, $offset, '', '', '', '', '', '', '', '', '', '', '', $pagination, $page_arrows, $ranking, $large_excerpt_length, $small_excerpt_length, $large_meta_author, $small_meta_author, $large_meta_date, $small_meta_date, $large_meta_comment_count, $small_meta_comment_count, $large_meta_views, $small_meta_views, $large_meta_likes, $small_meta_likes, $large_meta_cats, $small_meta_cats, $large_meta_tags, $small_meta_tags, $large_read_more_link, $small_read_more_link ); } ?>>

			<div class="gp-widget-title">
					
				<?php if ( $icon ) { ?><i class="gp-element-icon <?php echo esc_attr( $icon ); ?>"></i><?php } ?>
					
				<?php if ( $widget_title ) { ?><h3 class="widgettitle"><?php echo esc_attr( $widget_title ); ?></h3><?php } ?>				
																											
				<?php ghostpool_filter( $type, $filter_cat_id, $orderby, $pagination, false ); ?>
			
			</div>
			
			<?php if ( $gp_query->have_posts() ) : ?>
				
				<div class="gp-section-loop <?php echo sanitize_html_class( ghostpool_option( 'ajax' ) ); ?>">				
						
					<?php if ( $page_arrows == 'enabled' ) { ?>
						<div class="gp-pagination gp-standard-pagination gp-pagination-arrows">
							<?php echo ghostpool_pagination_arrows( $gp_query->max_num_pages ); ?>
						</div>
					<?php } ?>
				
					<div class="gp-section-loop-inner">
		
						<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); 
					
							if ( $counter % $per_page == 1 ) {
								$excerpt_length = $large_excerpt_length;
								$meta_author = $large_meta_author;
								$meta_date = $large_meta_date;
								$meta_comment_count = $large_meta_comment_count;
								$meta_views = $large_meta_views;
								$meta_likes = $large_meta_likes;
								$meta_cats = $large_meta_cats;
								$meta_tags = $large_meta_tags;
								$read_more_link = $large_read_more_link;
							} else {
								$excerpt_length = $small_excerpt_length;
								$meta_author = $small_meta_author;
								$meta_date = $small_meta_date;
								$meta_comment_count = $small_meta_comment_count;
								$meta_views = $small_meta_views;
								$meta_likes = $small_meta_likes;
								$meta_cats = $small_meta_cats;
								$meta_tags = $small_meta_tags;
								$read_more_link = $small_read_more_link;	
							}
								
							if ( function_exists( 'ghostpool_post_loop_showcase' ) ) { ghostpool_post_loop_showcase( $style, $excerpt_length, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes, $meta_cats, $meta_tags, $read_more_link, $counter, $ranking, $ranking_counter, $per_page, $gp_query->post_count ); }
								
						$counter++; $ranking_counter++; endwhile; ?>
					
					</div>

					<?php if ( $pagination == 'page-numbers' ) { ?>
						<?php echo ghostpool_pagination( $gp_query->max_num_pages, $pagination ); ?>
					<?php } ?>
							
				</div>

			<?php else : ?>

				<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'aardvark-plugin' ); ?></strong>

			<?php endif; wp_reset_postdata(); ?>
							
		</div>
					
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}

}

add_shortcode( 'gp_showcase', 'ghostpool_showcase' ); ?>