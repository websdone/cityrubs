<?php get_header();

// Page options
$header = ghostpool_option( 'search_page_header' );
$format = ghostpool_option( 'search_format' );
$style = ghostpool_option( 'search_style' );
$alignment = ghostpool_option( 'search_alignment' );
$post_types = is_array( ghostpool_option( 'search_post_types' ) ) ? implode( ',', ghostpool_option( 'search_post_types' ) ) : ghostpool_option( 'search_post_types' );
$orderby = ghostpool_option( 'search_orderby' );
$per_page = ghostpool_option( 'search_per_page' );
$offset = ghostpool_option( 'search_offset' );
$image_size = ghostpool_option( 'search_image_size' );
$content_display = ghostpool_option( 'search_content_display' );
$excerpt_length = ghostpool_option( 'search_excerpt_length' );
$meta_author = ghostpool_option( 'search_meta', 'author' );
$meta_date = ghostpool_option( 'search_meta', 'date' );
$meta_comment_count = ghostpool_option( 'search_meta', 'comment_count' );
$meta_views = ghostpool_option( 'search_meta', 'views' );
$meta_likes = ghostpool_option( 'search_meta', 'likes' );
$meta_cats = ghostpool_option( 'search_meta', 'cats' );
$meta_tags = ghostpool_option( 'search_meta', 'tags' );
$read_more_link = ghostpool_option( 'search_read_more_link' );
$pagination = ghostpool_option( 'search_pagination' );

// Classes
$css_classes = array(
	'gp-posts-wrapper',
	'gp-archive-wrapper',
	$format,
	$style,
	$alignment,
);
$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );

?>
	
<?php ghostpool_page_header( 
	$post_id = '', 
	$type = $header,
	$bg = ghostpool_option( 'search_page_header_bg' ),
	$height = ghostpool_option( 'search_page_header_height', 'height' )
); ?>

<?php ghostpool_page_title( '', $header ); ?>
	
<div id="gp-content-wrapper" class="gp-container">

	<?php do_action( 'ghostpool_begin_content_wrapper' ); ?>
		
	<div id="gp-inner-container">

		<div id="gp-content">

			<?php if ( isset( $_GET['s'] ) && ( $_GET['s'] != '' ) ) { ?>

				<div class="<?php echo esc_attr( $css_classes ); ?>" data-type="search"<?php if ( function_exists( 'ghostpool_filter_variables' ) ) { echo ghostpool_filter_variables( '', '', $post_types, $format, $style, $orderby, $per_page, $offset, $image_size, $content_display, $excerpt_length, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes, $meta_cats, $meta_tags, $read_more_link, $pagination ); } ?>>
					
					<div id="gp-new-search">
						<div class="gp-divider-title"><?php esc_html_e( 'New Search', 'aardvark' ); ?></div>	
						<?php get_search_form(); ?>
					</div>
					
					<?php ghostpool_filter( ghostpool_option( 'search_filters' ), '', $orderby, $pagination ); ?>
			
					<div class="gp-section-loop <?php echo sanitize_html_class( ghostpool_option( 'ajax' ) ); ?>">

						<?php if ( have_posts() ) : ?>
						
							<?php global $wp_query; echo ghostpool_search_results_total( $wp_query->found_posts ); ?>
						
							<div class="gp-section-loop-inner">							
								<?php if ( $format == 'gp-posts-masonry' ) { ?><div class="gp-gutter-size"></div><?php } ?>							
								<?php while ( have_posts() ) : the_post(); ?>
									<?php if ( function_exists( 'ghostpool_post_loop' ) ) { ghostpool_post_loop( $format, $style, $image_size, $content_display, $excerpt_length, $meta_author, $meta_date, $meta_comment_count, $meta_views, $meta_likes, $meta_cats, $meta_tags, $read_more_link ); } ?>
								<?php endwhile; ?>
							</div>

							<?php echo ghostpool_pagination( $wp_query->max_num_pages, $pagination ); ?>

						<?php else : ?>

							<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'aardvark' ); ?></strong>

						<?php endif; ?>
					
					</div>
	
				</div>

			<?php } else { ?>
		
				<p><?php esc_html_e( 'You left the search box empty, please enter a valid term.', 'aardvark' ); ?></p>

			<?php } ?>				

		</div>

		<?php get_sidebar( 'left' ); ?>
	
		<?php get_sidebar( 'right' ); ?>
	
	</div>

	<?php do_action( 'ghostpool_end_content_wrapper' ); ?>
			
	<div class="gp-clear"></div>

</div>

<?php get_footer(); ?>