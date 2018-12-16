<?php if ( ! function_exists( 'ghostpool_pricing_column' ) ) {

	function ghostpool_pricing_column( $atts, $content = null ) { 

		extract( shortcode_atts( array(
			'title' => '', 
			'price' => '',
			'trial_price' => '',
			'currency_symbol'  => '',
			'payment_interval' => '',
			'level_id' => '',
			'highlight' => 'gp-normal-column',
			'highlight_text' => '',
			'button_link' => '',
			'button_text' => '',
			'style' => 'gp-style-1',
			'title_bg_color'  => '#232323',
			'highlight_title_bg_color' => '#fd643b',
			'title_text_color' => '#fff',
			'highlight_title_text_color' => '#fff',
			'price_bg_color' => '#fff',
			'price_circle_color' => '#f8f8f8',
			'price_text_color' => '#232323',
			'content_bg_color' => '#ffffff',
			'content_bg_color_alt' => '#f8f8f8',
			'content_text_color' => '#232323',
			'footer_bg_color' => '#fff',
			'button_bg_color' => '#39c8df',
			'button_bg_hover_color' => '#00a0e3',
			'button_text_color' => '#fff',
			'divider_color' => '#e6e6e6',
			'classes' => '',
			//'css' => '',
		), $atts ) );
		
		global $current_user;

		// Unique Name	
		STATIC $column_number = 0;
		$column_number++;
		$name = 'gp_pricing_column_' . $column_number;

		if ( defined( 'PMPRO_VERSION' ) ) {
		
			// Hide pricing column if signups disabled
			if ( $level_id && ! $current_user->membership_level->allow_signups ) {
				return;
			}	
		
			// Get membership levels
			$levels = pmpro_getAllLevels( false, true );
			
			$title = isset( $levels[$level_id]->name ) ? $levels[$level_id]->name : $title;
			
			$content = isset( $levels[$level_id]->description ) ? $levels[$level_id]->description  : $content;
			
			$price = isset( $levels[$level_id]->billing_amount ) ? floatval( $levels[$level_id]->billing_amount ) : $price;
			
			$trial_price = isset( $levels[$level_id]->trial_amount ) ? floatval( $levels[$level_id]->trial_amount ) : $trial_price;
			
			// Get cycle values
			if ( isset( $levels[$level_id]->cycle_number ) && isset( $levels[$level_id]->cycle_period ) ) {
				$cycle = esc_html__( 'Per ', 'aardvark-plugin' );
				if ( $levels[$level_id]->cycle_number != 1 ) {
					$cycle .= $levels[$level_id]->cycle_number . ' ' . $levels[$level_id]->cycle_period . 's';
				} else {
					$cycle .= $levels[$level_id]->cycle_period;
				}	
			}
			$payment_interval = isset( $cycle ) ? $cycle : $payment_interval;
		
			// Get current membership level
			if ( isset( $current_user->membership_level->ID ) && $level_id ) {
				$current_level = ( $current_user->membership_level->ID == $level_id );
			} else {
				$current_level = false;
			}
		
		} else {

			$level_id = '';

		}
		
		// Add CSS styling to header
		if ( function_exists( 'ghostpool_pricing_table_css' ) ) {
			ghostpool_pricing_table_css( $name, $title_bg_color, $highlight_title_bg_color, $title_text_color, $highlight_title_text_color, $price_bg_color, $price_circle_color, $price_text_color, $content_bg_color, $content_bg_color_alt, $content_text_color, $footer_bg_color, $button_bg_color, $button_bg_hover_color, $button_text_color, $divider_color );
		}
		
		// Classes
		$css_classes = array(
			'gp-pricing-column',
			$highlight,
			$style,
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
				
		ob_start(); ?>

		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>">

			<?php if ( $title ) { ?>
				<div class="gp-pricing-column-header">
					<?php if ( $highlight_text ) { ?>
						<h5 class="gp-pricing-column-highlight-text"><?php echo esc_attr( $highlight_text ); ?></h5>
					<?php } ?>
					<h5 class="gp-pricing-column-title">
						<?php echo esc_attr( $title ); ?>
					</h5>
				</div>
			<?php } ?>
	
			<div class="gp-pricing-column-costs">
				<div class="gp-pricing-column-circle">
					<?php if ( $currency_symbol && $price ) { ?>
						<h5 class="gp-pricing-column-symbol<?php if ( $trial_price ) { ?> gp-has-trial-price<?php } ?>"><?php echo esc_attr( $currency_symbol ); ?></h5>
					<?php } ?>
					<?php if ( $price ) { ?>
						<h5 class="gp-pricing-column-price<?php if ( $trial_price ) { ?> gp-has-trial-price<?php } ?>"><?php echo esc_attr( $price ); ?></h5>
					<?php } ?>
					<?php if ( $currency_symbol && $trial_price ) { ?>
						<h5 class="gp-pricing-column-symbol"><?php echo esc_attr( $currency_symbol ); ?></h5>
					<?php } ?>
					<?php if ( $trial_price ) { ?>
						<h5 class="gp-pricing-column-trial-price"><?php echo esc_attr( $trial_price ); ?></h5>
					<?php } ?>
					<?php if ( $payment_interval ) { ?>
						<h6 class="gp-pricing-column-interval"><?php echo esc_attr( $payment_interval ); ?></h6>
					<?php } ?>
				</div>	
			</div>
	
			<?php if ( $style != 'gp-style-2' ) { ?><div class="gp-pricing-column-divider"></div><?php } ?>
	
			<?php if ( $content ) { ?>
				<div class="gp-pricing-column-content">
					<?php echo do_shortcode( wpb_js_remove_wpautop( $content, true ) ); ?>
				</div>
			<?php } ?>
	
			<?php if ( $style != 'gp-style-2' ) { ?><div class="gp-pricing-column-divider"></div><?php } ?>
			
			<div class="gp-pricing-column-footer">	
			
				<?php if ( $button_link ) { ?>
				
					<a href="<?php echo esc_url( $button_link ); ?>" class="gp-pricing-column-button"><?php echo esc_attr( $button_text ); ?></a>
					
				<?php } elseif ( $level_id && ! $current_level ) { ?>
				
					<a class="gp-pricing-column-button" href="<?php echo pmpro_url( 'checkout', '?level=' . $level_id, 'https' ); ?>"><?php esc_html_e( 'Select', 'aardvark-plugin' ); ?></a>	
		
				<?php } elseif ( $level_id && $current_level ) {
				
					//if it's a one-time-payment level, offer a link to renew				
					if ( pmpro_isLevelExpiringSoon( $current_level ) && $current_user->membership_level->allow_signups ) { ?>
						<a class="gp-pricing-column-button" href="<?php echo pmpro_url( 'checkout', '?level=' . $level_id, 'https' ); ?>"><?php esc_html_e( 'Renew', 'aardvark-plugin' );?></a>
					<?php } else { ?>
						<a class="disabled gp-pricing-column-button" href="<?php echo pmpro_url( 'account' ); ?>"><?php esc_html_e( 'Your Level', 'aardvark-plugin' ); ?></a>
					<?php } ?>

				<?php } ?>

			</div>
	
		</div>

		<?php

		$output_string = ob_get_contents();
		ob_end_clean(); 
		return $output_string;

	}

}
add_shortcode( 'gp_pricing_column', 'ghostpool_pricing_column' ); ?>