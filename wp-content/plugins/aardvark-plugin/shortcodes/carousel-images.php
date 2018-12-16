<?php
		
if ( ! function_exists( 'ghostpool_carousel_images' ) ) {

	function ghostpool_carousel_images( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',	
			'images' => '',
			'image_link' => 'lightbox',
			'items_in_view' => '0',
			'image_size' => 'gp_square_image',
			'slider_speed' => '0',
			'animation_speed' => '0.6',
			'buttons' => 'disabled',
			'arrows' => 'enabled',
			'classes' => '',
			'icon_color' => '',
			'icon' => '',
			'css' => '',
		), $atts ) );
	
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_carousel_images_wrapper_' . $i;
			
		// CSS Editor
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );

		// Get image dimensions
		global $_wp_additional_image_sizes;
		if ( in_array( $image_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$image_width  = get_option( "{$image_size}_size_w" );
			$image_height  = get_option( "{$image_size}_size_h" );
		} else {
			$image_width = $_wp_additional_image_sizes[$image_size]['width'];
			$image_height = $_wp_additional_image_sizes[$image_size]['height'];
		}							
										
		// Classes
		$css_classes = array(
			'gp-carousel-wrapper',
			'gp-slider',
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
		
		ob_start(); ?>

		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">
			
			<div class="gp-widget-title">
				<?php if ( $icon ) { ?><i class="gp-element-icon <?php echo esc_attr( $icon ); ?>"></i><?php } ?>
				<?php if ( $widget_title ) { ?><h3 class="widgettitle"><?php echo esc_attr( $widget_title ); ?></h3><?php } ?>
			</div>
			
			<?php if ( $images ) {
			
				// Get image IDs
				$image_ids = $image_ids = array_filter( explode( ',', $images ) );	

				if ( $image_ids ) { ?>
	
					 <ul class="slides">
						<?php foreach ( $image_ids as $image_id ) { ?>
			
							<li>
			
								<?php 
				
								$image = wp_get_attachment_image_src( $image_id, $image_size );
				
								if ( $image[0] ) {
								
									// Image link
									if ( $image_link == 'url' ) {
										echo '<a href="' . wp_get_attachment_url( $image_id ) . '">';
									} elseif ( $image_link == 'lightbox' ) {
										echo '<a href="' . wp_get_attachment_url( $image_id ) . '" data-lightbox="gallery">';
									}
							
									?>
								
										<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo absint( $image_width ); ?>" height="<?php echo absint( $image_height ); ?>" alt="<?php if ( get_post_meta( $image_id, '_wp_attachment_image_alt', true) ) { echo esc_attr( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" />
									
									<?php if ( $image_link != 'disabled' ) {
										echo '</a>';
									} ?>
						
								<?php } ?>
								
							</li>
							
						<?php } ?>
					</ul>

				<?php } ?>	
				
			<?php } ?>
			
		</div>	

		<script>
		jQuery( document ).ready( function( $ ) {
	
			'use strict';

			var $window = $(window),
				flexslider = { vars:{} };

			function getGridSize() {
				<?php if ( $items_in_view != 0 ) { ?>
					return ( $window.width() <= 567 ) ? 1 : ( $window.width() <= 1023 ) ? <?php if ( $items_in_view == 1 ) { ?>1<?php } else { ?>2<?php } ?> : <?php echo esc_js( $items_in_view ); ?>;
				<?php } ?>	
			}

			if ( $( 'body' ).hasClass( 'gp-theme' ) ) {

				$window.load(function() {
					$( '#<?php echo esc_js( $name ); ?>' ).flexslider({
						animation: 'slide',
						animationLoop: false,
						itemWidth: <?php echo esc_js( $image_width ); ?>,
						itemMargin: 30,
						slideshowSpeed: <?php if ( $slider_speed != '0' ) { echo esc_js( $slider_speed ) * 1000; } else { echo '9999999'; } ?>,
						animationSpeed: <?php echo absint( $animation_speed * 1000 ); ?>,
						directionNav: <?php if ( $arrows == 'enabled' ) { ?>true<?php } else { ?>false<?php } ?>,			
						controlNav: <?php if ( $buttons == 'enabled' ) { ?>true<?php } else { ?>false<?php } ?>,			
						pauseOnAction: true, 
						pauseOnHover: false,
						prevText: '',
						nextText: '',
						minItems: getGridSize(),
						maxItems: getGridSize(),
						start: function( slider ) {
							flexslider = slider;
						}
					});	
				});
		
				$window.resize( function() {
					var gridSize = getGridSize();
					flexslider.vars.minItems = gridSize;
					flexslider.vars.maxItems = gridSize;
				});	
				
			}
		
		});
		</script>
					 				
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}

}

add_shortcode( 'gp_carousel_images', 'ghostpool_carousel_images' );
	
?>