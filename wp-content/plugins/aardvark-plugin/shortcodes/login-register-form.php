<?php if ( ! function_exists( 'ghostpool_login_register_form' ) ) {
	function ghostpool_login_register_form( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'display' => 'login-form,register-form,lost-password-form',
			'default_display' => 'gp-login-display',
			'classes' => '',	
			'css' => '',
		), $atts ) );			
					
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_login_register_form_' . $i;
					
		// Classes
		$css_classes = array(
			'gp-login-register-form-element',
			$default_display,
			$classes,
		);
		
		$display = explode( ',', $display );
		
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
			
		ob_start();
				
		$element_nonce = true;
	
		if ( ! is_user_logged_in() ) { ?>
		
			<div id="gp-login-element">		
				<?php include( locate_template( 'lib/sections/login/login-form.php' ) ); ?>
			</div>
			
		<?php }
		
		$element_nonce = false;
		
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
add_shortcode( 'gp_login_register_form', 'ghostpool_login_register_form' ); ?>