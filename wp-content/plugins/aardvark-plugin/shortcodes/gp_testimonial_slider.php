<?php 

extract( shortcode_atts( array( 
	'effect'  => 'slide',
	'buttons' => 'false',
	'arrows'  => 'true',
	'speed'   => '0',
	'classes' => '',
	'css' => '',
	'headline_font_size' => '',
	'headline_line_height' => '',
	'quote_font_size' => '',
	'quote_line_height' => '',
	'name_font_size' => '',
	'name_line_height' => '',
	'avatar_border_color' => '',
	'headline_color' => '',
	'quote_color' => '',
	'name_color' => '',
 ), $atts ) );
  
// Unique Name
STATIC $i = 0;
$i++;
$name = 'gp_testimonial_slider_' . $i;

// Add CSS styling to header
if ( function_exists( 'ghostpool_testimonial_slider_css' ) ) {
	ghostpool_testimonial_slider_css( $name, $headline_font_size, $headline_line_height, $quote_font_size, $quote_line_height, $name_font_size, $name_line_height, $avatar_border_color, $headline_color, $quote_color, $name_color );
}

// Add arrows class
if ( $arrows == 'true' ) { 
	$arrows_class = 'gp-has-arrows';
} else {
	$arrows_class = '';
}

// Classes
$css_classes = array(
	'gp-testimonial-slider',
	'gp-slider',
	$arrows_class,
	$classes,
);
$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
		
ob_start(); ?>

<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">
	<ul class="slides">
		<?php echo do_shortcode( $content ); ?>
	</ul>
</div>

<?php

$output_string = ob_get_contents();
ob_end_clean(); 
echo wp_kses_post( $output_string );

?> 

<script>
jQuery( document ).ready( function( $ ) {
	'use strict';
	if ( $( 'body' ).hasClass( 'gp-theme' ) ) {
		$( window ).load( function() {
			$( '#<?php echo esc_js( $name ); ?>.gp-slider' ).flexslider( { 
				animation: '<?php echo esc_js( $effect ); ?>',
				slideshowSpeed: <?php if ( $speed == 0 ) { echo '9999999'; } else { echo esc_js( $speed ) * 1000; } ?>,
				animationSpeed: 600,
				smoothHeight: false,   
				directionNav: <?php if ( $arrows == 'true' ) { ?>true<?php } else { ?>false<?php } ?>,			
				controlNav: <?php if ( $buttons == 'true' ) { ?>true<?php } else { ?>false<?php } ?>,				
				pauseOnAction: true, 
				pauseOnHover: false,
				prevText: '',
				nextText: '',
				touch: true
			});
		});
	}
});
</script>