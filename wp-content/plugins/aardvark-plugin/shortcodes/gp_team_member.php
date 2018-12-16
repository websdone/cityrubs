<?php

extract( shortcode_atts( array( 
	'image' => '',
	'name' => '',
	'position' => '',
	'link' => '',
	'link_target' => '',
 ), $atts ) );

ob_start(); 

global $columns, $column_spacing, $image_width, $image_height, $image_border, $ghostpool_counter;

$ghostpool_counter = $ghostpool_counter + 1;
if ( $ghostpool_counter % $columns == 1 ) {
	$left_column = ' gp-left-column';
} else {
	$left_column = '';
}

$column_width = 100 / $columns;	
	
?>

<div class="gp-team-member<?php echo esc_attr( $left_column ); ?>" style="width: <?php echo floatval( $column_width ); ?>%;<?php if ( $column_spacing > 0 ) { ?> padding-left: <?php echo floatval( $column_spacing ); ?>px;"<?php } ?>>
	
	<?php if ( $image ) {
		
		$image_url = wp_get_attachment_image_src( $image, array( $image_width, $image_height ) ); ?>
		
		<?php if ( $link != '' ) { ?><a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php } ?>
			<img src="<?php echo esc_url( $image_url[0] ); ?>" width="<?php echo absint( $image_width ); ?>" height="<?php echo absint( $image_height ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-team-image"  style="border-color: <?php echo esc_attr( $image_border ); ?>;" />	
		<?php if ( $link != '' ) { ?></a><?php } ?>
										
	<?php } ?>
						
	<?php if ( $name ) { ?>
		<div class="gp-team-name"><?php echo esc_attr( $name ); ?></div>
	<?php } ?>
	
	<?php if ( $position ) { ?>
		<div class="gp-team-position"><?php echo esc_attr( $position ); ?></div>
	<?php } ?>
	
	<?php if ( $content ) { ?>
		<div class="gp-team-description"><?php echo do_shortcode( wpb_js_remove_wpautop( $content, true ) ); ?></div>
	<?php } ?>

</div>

<?php

$output_string = ob_get_contents();
ob_end_clean(); 	
echo wp_kses_post( $output_string );

?>