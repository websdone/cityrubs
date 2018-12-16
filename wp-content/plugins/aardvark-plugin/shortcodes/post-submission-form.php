<?php if ( ! function_exists( 'ghostpool_post_submission_form' ) ) {
	function ghostpool_post_submission_form( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'email_address' => '',
			'subject' => '',
			'post_title' => '1',
			'featured_image' => '1',
			'name' => '1',
			'email' => '1',
			'cats' => '1',
			'parent_cat' => '',
			'formats' => '1',
			'post_content' => '1',
			'tags' => '1',
			'visitors_can_post' => 'enabled',
			'submit_status' => 'pending',
			'email_notification' => 'enabled',
			'toc_url' => '',			
			'gdpr' => '',		
			'gdpr_text' => '',
			'classes' => '',
			'css' => '',
		), $atts ) );
		
		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_post_submission_form_wrapper_' . $i;
					
		// Classes
		$css_classes = array(
			'gp-post-form',
			'gp-post-submission-form',
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
								
		ob_start(); ?>
		
		<?php if ( ( $visitors_can_post == 'enabled' && ! is_user_logged_in() ) OR is_user_logged_in() ) { ?>

			<div id="<?php echo sanitize_html_class( $name ); ?>">
			
				<form class="<?php echo esc_attr( $css_classes ); ?>" name="ghostpool_post_submission_form" method="post" enctype="multipart/form-data">	

					<div class="gp-success <?php echo esc_attr( $css_classes ); ?>">
						<strong><?php esc_html_e( 'Thanks for submitting your post!', 'aardvark-plugin' ); ?></strong>
						<p><?php if ( $submit_status == 'approved' ) {
							echo esc_html__( 'Your post was succesfully added. You can view your post', 'aardvark-plugin' ) . ' <span class="gp-view-post-link"><a href="' . get_permalink( $_GET['post_id'] ) . '">' . esc_html__( 'here', 'aardvark-plugin' ) . '</a>.</span>';
						} else {
							if ( get_option( 'permalink_structure' ) ) {
								$permalink_structure = '?';
							} else { 
								$permalink_structure = '&';
							}
							esc_html_e( 'Your post is currently being reviewed. ', 'aardvark-plugin' );
							if ( is_user_logged_in() ) {
								echo esc_html__( 'You can check the status of your post', 'aardvark-plugin' ) . ' <span class="gp-view-post-link"><a href="' . get_permalink( ghostpool_option( 'post_submission_page' ) ) . '">' . esc_html__( 'here', 'aardvark-plugin' ) . '</a>.</span>';
							}	
						} ?></p>
					</div>

					<?php if ( $post_title == '1' ) { ?>				
						<p class="gp-post-form-title gp-field-container">
							<input type="text" name="ghostpool_post_title" class="gp-field" value="<?php if ( isset( $_POST['ghostpool_post_title'] ) ) { echo esc_attr( $_POST['ghostpool_post_title'] ); } ?>" placeholder="<?php esc_html_e( 'Enter title', 'aardvark-plugin' ); ?>" required>
							<span class="gp-message"><?php esc_html_e( 'A post title is required.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>
			
					<?php if ( $featured_image == '1' ) { ?>
						<p class="gp-post-form-image gp-field-container">
							<a id="gp-upload-image" href="#" class="gp-upload-image button gp-field" data-upload-image="<?php esc_html_e( 'Add image', 'aardvark-plugin' ); ?>" data-image-added="<?php esc_html_e( 'Image added', 'aardvark-plugin' ); ?>" data-image-not-added="<?php esc_html_e( 'Can you only upload 1 image', 'aardvark-plugin' ); ?>" /><?php esc_html_e( 'Add image', 'aardvark-plugin' ); ?></a>							
							<span class="gp-uploads"></span>
						</p>
					<?php } ?>
			
					<?php if ( $name == '1' ) { ?>	
						<?php if ( ! is_user_logged_in() ) { ?>
							<p class="gp-post-form-name gp-field-container">
								<input type="text" name="ghostpool_post_username" class="gp-field" value="<?php if ( isset( $_POST['ghostpool_post_username'] ) ) { echo esc_attr( $_POST['ghostpool_post_username'] ); } ?>" placeholder="<?php esc_html_e( 'Enter name', 'aardvark-plugin' ); ?>" required>
								<span class="gp-message"><?php esc_html_e( 'A name is required.', 'aardvark-plugin' ); ?></span>
							</p>
						<?php } else { ?>
							<input type="hidden" name="ghostpool_username" value="">
						<?php } ?>
					<?php } ?>
			
					<?php if ( $email == '1' ) { ?>
						<?php if ( ! is_user_logged_in() ) { ?>
							<p class="gp-post-form-email gp-field-container">
								<input type="text" name="ghostpool_post_email" class="gp-field" value="<?php if ( isset( $_POST['ghostpool_post_email'] ) ) { echo esc_attr( $_POST['ghostpool_post_email'] ); } ?>" placeholder="<?php esc_html_e( 'Enter email', 'aardvark-plugin' ); ?>" required>
								<span class="gp-message"><?php esc_html_e( 'An email is required.', 'aardvark-plugin' ); ?></span>
							</p>
						<?php } else { ?>
							<input type="hidden" name="ghostpool_email" value="">
						<?php } ?>
					<?php } ?>
								
					<?php if ( $cats == '1' ) { ?>
						<p class="gp-post-form-categories gp-field-container">
							<?php 
		
							if ( ! empty( $parent_cat ) && preg_match( '/^[1-9, ][0-9, ]*$/', $parent_cat ) ) {		
								$cat_id = (int) $parent_cat;
							} elseif ( ! empty( $parent_cat ) ) {
								$cat = get_term_by( 'slug', $parent_cat );
								$cat_id = $cat->term_id;
							} else {
								$cat_id = '';
							}

							$term = term_exists( (int) $cat_id, 'category' );
							if ( $term === 0 OR $term === null ) {
								$cat_id = '';
							}

							$args = array(
								'taxonomy'   => 'category',
								'parent' 	 => (int) $cat_id,
								'hide_empty' => false,
							);	
						
							$terms = get_terms( $args );

							if ( ! empty( $terms ) ) { ?>
								<select name="ghostpool_post_categories" class="gp-field" required>	
									<option value=""><?php esc_html_e( 'Select a category', 'aardvark-plugin' ); ?></option>							
									<?php foreach ( $terms as $term ) { 
										if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) { ?>
											<option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_attr( $term->name ); ?></option>
										<?php }
									} ?>
								</select>
							<?php } ?>
							<span class="gp-message"><?php esc_html_e( 'A category is required.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>

					<?php if ( $formats == '1' ) { ?>
						<p class="gp-post-form-formats gp-field-container">
							<select name="ghostpool_post_formats" class="gp-field" required>	
								<option value="0"><?php esc_html_e( 'Standard', 'aardvark-plugin' ); ?></option>							
								<?php $post_formats = get_theme_support( 'post-formats' ); ?>
								<?php foreach ( $post_formats[0] as $post_format ) { ?>
									<option value="<?php echo esc_attr( $post_format ); ?>"><?php echo esc_attr( ucfirst( $post_format ) ); ?></option>
								<?php } ?>
							</select>	
							<span class="gp-message"><?php esc_html_e( 'A post format required.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>	
			
					<?php if ( $post_content == '1' ) { ?>				
						<p class="gp-post-form-content gp-field-container">
							<textarea name="ghostpool_post_content" class="gp-field" placeholder="<?php esc_html_e( 'Enter text', 'aardvark-plugin' ); ?>" required><?php if ( isset( $_POST['ghostpool_post_content'] ) ) { echo stripslashes( html_entity_decode( $_POST['ghostpool_post_content'] ) ); } ?></textarea>
							<span class="gp-message"><?php esc_html_e( 'Content is required.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>
			
					<?php if ( $tags == '1' ) { ?>
						<p class="gp-post-form-tags gp-field-container">
							<input type="text" name="ghostpool_post_tags" class="gp-field" value="<?php if ( isset( $_POST['ghostpool_post_tags'] ) ) { echo esc_attr( $_POST['ghostpool_post_tags'] ); } ?>" placeholder="<?php esc_html_e( 'Separate tags with commas', 'aardvark-plugin' ); ?>" required>
							<span class="gp-message"><?php esc_html_e( 'Post tags are required.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>
			
					<?php if ( $toc_url ) { ?>
						<p class="gp-post-form-toc gp-field-container">
							<input type="checkbox" name="ghostpool_post_toc" value="1"<?php if ( isset( $_POST['ghostpool_post_toc'] ) ) { ?> checked<?php } ?> required><?php esc_html_e( 'I agree with the', 'aardvark-plugin' ); ?> <a href="<?php echo esc_url( $toc_url ); ?>" target="_blank"><?php esc_html_e( 'terms and conditions', 'aardvark-plugin' ); ?></a>
							<span class="gp-message"><?php esc_html_e( 'Please accept the terms and conditions.', 'aardvark-plugin' ); ?></span>
						</p>
					<?php } ?>
	
					<p class="gp-post-form-submit">
						<input type="submit" value="<?php esc_attr_e( 'Submit Post', 'aardvark-plugin' ); ?>" tabindex="40" class="gp-submit" name="submit" />
					</p>

					<?php if ( $gdpr == 'enabled' ) { ?>
						<p class="gp-gdpr"><input name="gdpr" class="gdpr" type="checkbox" value="1" required /> <label><?php echo wp_kses_post( $gdpr_text ); ?></label></p>
					<?php } ?>
									
					<?php if ( $name ) { ?>
						<input type="hidden" value="<?php echo sanitize_html_class( $name ); ?>" class="gp-post-submission-form-id">
					<?php } ?>
					
					<?php if ( $email_address ) { ?>
						<input type="hidden" value="<?php echo esc_html( antispambot( $email_address ) ); ?>" class="gp-post-submission-email-address">
					<?php } ?>	
					
					<?php if ( $subject ) { ?>
						<input type="hidden" value="<?php echo esc_attr( $subject ); ?>" class="gp-post-submission-subject">
					<?php } ?>
					
					<?php if ( $submit_status ) { ?>
						<input type="hidden" value="<?php echo esc_attr( $submit_status ); ?>" class="gp-post-form-status">
					<?php } ?>	
					
					<?php if ( $email_notification ) { ?>
						<input type="hidden" value="<?php echo esc_attr( $email_notification ); ?>" class="gp-post-form-notification">
					<?php } ?>	
									
				</form>
			
			</div>
			
		<?php } else { ?>

			<div class="gp-post-submission-login">
				<strong class="gp-active"><?php echo sprintf( wp_kses( __( 'To login to submit a post please click <a href="%s">here</a>.', 'aardvark-plugin' ), array( 'a' => array( 'href' => array() ) ) ), ghostpool_login_link() ); ?></strong>
			</div>

		<?php } ?>
		
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}
}
add_shortcode( 'gp_post_submission_form', 'ghostpool_post_submission_form' ); ?>