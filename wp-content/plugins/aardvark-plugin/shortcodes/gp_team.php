<?php 

global $columns, $column_spacing, $image_width, $image_height, $image_border, $ghostpool_counter;

extract( shortcode_atts( array( 
	'columns' => '3',
	'column_spacing' => '30',
	'image_width' => '250',
	'image_height' => '250',
	'image_border' => 'rgba(0,0,0,0.1)',
	'classes' => '',
	'css' => '',
 ), $atts ) );

ob_start(); 

// Classes
$css_classes = array(
	'gp-team-wrapper',
	$classes,
);
$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );

$ghostpool_counter = 0;
	
?>

<div class="<?php echo esc_attr( $css_classes ); ?>"<?php if ( $column_spacing > 0 ) { ?> style="margin-left: -<?php echo( floatval( $column_spacing ) ); ?>px;"<?php } ?>>
	<?php echo do_shortcode( $content ); ?>
</div>

<?php

$output_string = ob_get_contents();
ob_end_clean(); 
echo wp_kses_post( $output_string );

?>