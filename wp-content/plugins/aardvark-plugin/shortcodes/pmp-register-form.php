<?php if ( defined( 'PMPRO_VERSION' ) ) {

	if ( ! function_exists( 'ghostpool_pmp_register_form' ) ) {
		function ghostpool_pmp_register_form( $atts, $content = null ) {
	
			extract( shortcode_atts( array(
				'fields' => 'username,email,confirm-email,password',
				'button_text' => esc_html__( 'Sign Up', 'aardvark' ),
				'redirect_url' => '',
				'classes' => '',
				'css' => '',
			), $atts ) );

			// Unique Name	
			STATIC $i = 0;
			$i++;
			$name = 'gp_pmp_register_form_' . $i;		
			
			$fields = explode( ',', $fields );
			
			// Classes
			$css_classes = array(
				'gp-pmp_register-form-wrapper',
				$classes,
			);
			$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
			$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );	
			
			ob_start(); ?>
			
			<?php if ( ! is_user_logged_in() ) { ?>
			
				<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">

					<form name="pmpro_form" class="gp-pmp-register-form"<?php if ( $redirect_url ) { ?> action="<?php echo esc_url( $redirect_url ); ?>"<?php } ?> method="post">

						<?php if ( in_array( 'username', $fields ) ) { ?>
							<div class="gp-form-field gp-username">
								<input type="text" name="username" size="30" placeholder="<?php esc_html_e( 'Username', 'aardvark' ); ?>" required />
							</div>
						<?php } ?>
						
						<?php if ( in_array( 'email', $fields ) ) { ?>
							<div class="gp-form-field gp-email">
								<input type="email" name="bemail" size="30" placeholder="<?php esc_html_e( 'Email', 'aardvark' ); ?>" required />
							</div>
						<?php } ?>

						<?php if ( in_array( 'confirm-email', $fields ) ) { ?>
							<div class="gp-form-field gp-email">
								<input type="email" name="bconfirmemail" size="30" placeholder="<?php esc_attr_e( 'Confirm Email', 'aardvark' ); ?>" required />
							</div>
						<?php } ?>

						<?php if ( in_array( 'password', $fields ) ) { ?>
							<div class="gp-form-field gp-password">
								<input type="password" name="password" size="30" placeholder="<?php esc_attr_e( 'Password', 'aardvark' ); ?>" required />
							</div>
						<?php } ?>
																		
						<?php if ( in_array( 'confirm-password', $fields ) ) { ?>
							<div class="gp-form-field gp-password">
								<input type="password" name="password2" size="30" placeholder="<?php esc_attr_e( 'Confirm Password', 'aardvark' ); ?>" required />
							</div>
						<?php } ?>
						
						<?php if ( $button_text ) { ?>
							<div class="gp-submit">
								<input type="submit" name="wp-submit" value="<?php echo esc_attr( $button_text ); ?>" />
							</div>	
						<?php } ?>
						
						<?php wp_nonce_field( 'ghostpool_pmp_register_form_action', 'ghostpool_pmp_register_form_nonce' ); ?>

					</form>
					
				</div>
	
			<?php } ?>
						
			<?php
			
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;

		}
	}
	add_shortcode( 'gp_pmp_register_form', 'ghostpool_pmp_register_form' );

} ?>