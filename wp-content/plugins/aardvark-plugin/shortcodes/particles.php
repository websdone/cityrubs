<?php if ( ! function_exists( 'ghostpool_particles' ) ) {

	function ghostpool_particles( $atts, $content = null ) {	

		extract( shortcode_atts( array(
			'number' => '80',
			'size' => '4',
			'color' => '#ffffff',
			'line_color' => '#ffffff',
			'classes' => '',
		), $atts ) );
		
		wp_enqueue_script( 'particles-js' );

		// Unique Name	
		STATIC $i = 0;
		$i++;
		$name = 'gp_particles_' . $i;
				
		// Classes
		$css_classes = array(
			'gp-particles',
			$classes,
		);
		$css_classes = trim( implode( ' ', array_filter( array_unique( $css_classes ) ) ) );
		$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes . vc_shortcode_custom_css_class( '', ' ' ), '', $atts );
		
		ob_start(); ?>
		
		<div id="<?php echo sanitize_html_class( $name ); ?>" class="<?php echo esc_attr( $css_classes ); ?>"></div>

		<?php echo <<<EOT
		<script>
		jQuery(window).load(function(){

			jQuery.when( jQuery('#$name').parentsUntil('.vc_row').css({position: 'static'}), jQuery('#$name').closest('.wpb_column').siblings().css( {"z-index" : "2", "position": "relative"} ), jQuery('#$name').siblings().css( {"z-index" : "2", "position": "relative"} ) ).then(function() {

				particlesJS("$name", {
				  "particles": {
					"number": {
					  "value": $number,
					  "density": {
						"enable": true,
						"value_area": 800
					  }
					},
					"color": {
					  "value": "$color"
					},
					"shape": {
					  "type": "circle",
					  "stroke": {
						"width": 0,
						"color": "#000000"
					  },
					  "polygon": {
						"nb_sides": 5
					  },
					  "image": {
						"src": "img/github.svg",
						"width": 100,
						"height": 100
					  }
					},
					"opacity": {
					  "value": 0.5,
					  "random": false,
					  "anim": {
						"enable": false,
						"speed": 1,
						"opacity_min": 0.1,
						"sync": false
					  }
					},
					"size": {
					  "value": $size,
					  "random": true,
					  "anim": {
						"enable": false,
						"speed": 40,
						"size_min": 0.5,
						"sync": false
					  }
					},
					"line_linked": {
					  "enable": true,
					  "distance": 150,
					  "color": "$line_color",
					  "opacity": 0.4,
					  "width": 1
					},
					"move": {
					  "enable": true,
					  "speed": 6,
					  "direction": "none",
					  "random": false,
					  "straight": false,
					  "out_mode": "out",
					  "bounce": false,
					  "attract": {
						"enable": false,
						"rotateX": 600,
						"rotateY": 1200
					  }
					}
				  },
				  "interactivity": {
					"detect_on": "canvas",
					"events": {
					  "onhover": {
						"enable": true,
						"mode": "grab"
					  },
					  "onclick": {
						"enable": true,
						"mode": "push"
					  },
					  "resize": true
					},
					"modes": {
					  "grab": {
						"distance": 140,
						"line_linked": {
						  "opacity": 1
						}
					  },
					  "bubble": {
						"distance": 400,
						"size": 40,
						"duration": 2,
						"opacity": 8,
						"speed": 3
					  },
					  "repulse": {
						"distance": 200,
						"duration": 0.4
					  },
					  "push": {
						"particles_nb": 4
					  },
					  "remove": {
						"particles_nb": 2
					  }
					}
				  },
				  "retina_detect": true
				});
			});
		});

		</script>

EOT;

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;

	}
}
add_shortcode( 'gp_particles', 'ghostpool_particles' ); ?>