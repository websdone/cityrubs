<?php if ( ! function_exists( 'ghostpool_posts' ) ) {

	function ghostpool_posts( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',			
			'cats' => '',
			'page_ids' => '',
			'post_types' => 'post',
			'ranking' => 'gp-no-ranking',	
			'format' => 'gp-posts-list',
			'style' => 'gp-style-classic',
			'alignment' => 'gp-align-left',
			'orderby' => 'newest',
			'per_page' => '9',
			'offset' => '',
			'image_size' => 'default',
			'content_display' => 'excerpt',
			'excerpt_length' => '160',
			'meta_author' => '',
			'meta_date' => '',
			'meta_views' => '',
			'meta_likes' => '',
			'meta_comment_count' => '',
			'meta_cats' => '',
			'meta_tags' => '',
			'filter_cats' => '',
			'filter_date' => '',
			'filter_title' => '',					
			'filter_comment_count' => '',
			'filter_views' => '',
			'filter_likes' => '',
			'filter_cat_id' => '',
			'read_more_link' => 'disabled',
			'page_arrows' => 'disabled',
			'pagination' => 'page-numbers',
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
			'masonry_bg_color' => '',
			'masonry_border_color' => '',
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
		$name = 'gp_posts_wrapper_' . $i;
		
		// Add CSS styling to header
		if ( function_exists( 'ghostpool_posts_css' ) ) {
			ghostpool_posts_css( $name, $title_color, $icon_color, $post_title_color, $post_title_hover_color, $post_link_color, $post_link_hover_color, $post_text_color, $meta_text_color, $masonry_bg_color, $masonry_border_color, $ranking_bg_color, $ranking_text_color );
		}
						
		// Post counter
		$counter = 1;
		
		// Ranking counter;
		$ranking_counter = 1;
																	
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
			'paged'          	  => $pagination != 'disabled' ? ghostpool_paged() : 1,
			'ignore_sticky_posts' => 1,
			'post__not_in' 		  => apply_filters( 'ghostpool_post_not_in', array( get_the_ID() ) ),
		);
		
		$args = apply_filters( 'ghostpool_posts_element_query', $args, $post_types, $page_ids, $cats, $orderby, $per_page, $offset, $pagination );
		
		$gp_query = new WP_Query( $args );
		
		// Classes
		$css_classes = array(
			'gp-posts-wrapper',
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

		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>" data-type="posts"<?php if ( function_exists( 'ghostpool_filter_variables' ) ) { echo ghostpool_filter_variables( get_the_ID(), $cats, $post_types, $format, $style, $orderby, $per_page, $offset, $image_size, $content_display, $excerpt_length, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes, $meta_cats, $meta_tags, $read_more_link, $pagination ); } ?>>
			
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

						<?php if ( $format == 'gp-posts-masonry' ) { ?><div class="gp-gutter-size"></div><?php } ?>
					
						<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>
						
							<?php if ( function_exists( 'ghostpool_post_loop' ) ) { ghostpool_post_loop( $format, $style, $image_size, $content_display, $excerpt_length, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes, $meta_cats, $meta_tags, $read_more_link, $counter, $ranking, $ranking_counter ); } ?>
							
						<?php $counter++; $ranking_counter++; endwhile; ?>
						
					</div>

					<?php echo ghostpool_pagination( $gp_query->max_num_pages, $pagination ); ?>
					
				</div>	
																		
				<?php if ( $see_all == 'enabled' ) { ?>
					<a href="<?php echo esc_url( $see_all_link ); ?>" class="gp-see-all-link button"><?php echo esc_attr( $see_all_text ); ?></a>
				<?php } ?>

			<?php else : ?>

				<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'aardvark-plugin' ); ?></strong>

			<?php endif; wp_reset_postdata(); ?>
							
		</div>

		<?php if ( $pagination == 'load-more' ) { ?>
			<script>
			jQuery( document ).ready( function( $ ) {	
				'use strict';
				if ( $( 'body' ).hasClass( 'gp-theme' ) ) {
					$( '#<?php echo esc_js( $name ); ?> .gp-section-loop-inner' ).infinitescroll({
						debug: false,
						loading: {
							finishedMsg: '',
							img: '<?php echo get_template_directory_uri(); ?>/lib/framework/images/blank.gif',
							msgText: '',
							speed: 'fast',
						},
						nextSelector: '#<?php echo esc_js( $name ); ?> ul.page-numbers a',
						navSelector: '#<?php echo esc_js( $name ); ?> .gp-pagination',
						itemSelector: '#<?php echo esc_js( $name ); ?> .gp-post-item',
						maxPage: <?php echo esc_js( $gp_query->max_num_pages ); ?>,
						extraScrollPx: 0,
						bufferPx: 0
					}, function( json, opts ) {
						if ( opts.state.currPage == <?php echo esc_js( $gp_query->max_num_pages ); ?> ) {
							$( '#<?php echo sanitize_html_class( $name ); ?> .gp-load-more' ).remove();
						}
					});
					$( '#<?php echo esc_js( $name ); ?> .gp-section-loop-inner' ).infinitescroll( 'unbind' );
					$( '#<?php echo esc_js( $name ); ?> .gp-load-more-button' ).click( function() {
						 $( '#<?php echo esc_js( $name ); ?> .gp-section-loop-inner' ).infinitescroll( 'retrieve' );
						 return false;
					});	
				}
			});
			</script>
		<?php } ?>
									
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}

}
add_shortcode( 'gp_posts', 'ghostpool_posts' ); ?>