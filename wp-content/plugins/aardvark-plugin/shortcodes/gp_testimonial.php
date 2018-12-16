<?php

extract( shortcode_atts( array( 
	'image'    => '',
	'headline' => '',
	'name'     => '',
 ), $atts ) );

ob_start(); ?>

	<li class="gp-testimonial-slide">
	
		<div class="gp-testimonial-box">
		
			<?php if ( $image ) { 
				$image_url = wp_get_attachment_image_src( $image, array( 100, 100 ) ); ?>
				<div class="gp-testimonial-image">
					<img src="<?php echo esc_url( $image_url[0] ); ?>" width="100" height="100" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image" />	
					<span class="gp-pointer"></span>			
				</div>						
			<?php } ?>
							
			<div class="gp-testimonial-quote">
				<?php if ( $headline ) { ?><div class="gp-testimonial-headline"><?php echo esc_attr( $headline ); ?></div><?php } ?>
				<?php if ( $content ) { ?><div class="gp-testimonial-text"><?php echo do_shortcode( wpb_js_remove_wpautop( $content, true ) ); ?></div><?php } ?>
				<?php if ( $name ) { ?><div class="gp-testimonial-name"><?php echo esc_attr( $name ); ?></div><?php } ?>
			</div>
		
		</div>

	</li>

<?php

$output_string = ob_get_contents();
ob_end_clean(); 	
echo wp_kses_post( $output_string );

?>