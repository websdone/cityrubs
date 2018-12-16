<?php
/**
 * Class to add custom scripts and styles.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BUPRScriptsStyles' ) ) {
	/**
	 * Class to add custom scripts and styles.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	class BUPRScriptsStyles {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			// Add Scripts only on reviews tab.
			add_action( 'wp_enqueue_scripts', array( $this, 'bupr_custom_variables' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wpdocs_styles_method' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bupr_admin_custom_variables' ) );
		}

		/**
		 * Actions performed for enqueuing scripts and styles for site front
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_custom_variables() {
			wp_enqueue_style( 'bupr-reviews-css', BUPR_PLUGIN_URL . 'assets/css/bupr-reviews.css' );
			wp_enqueue_style( 'bupr-font-awesome', BUPR_PLUGIN_URL . 'admin/assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'bupr-front-css', BUPR_PLUGIN_URL . 'assets/css/bupr-front.css' );
			wp_enqueue_script( 'bupr-front-js', BUPR_PLUGIN_URL . 'assets/js/bupr-front.js', array( 'jquery' ) );
		}

		/**
		 * Actions performed for enqueuing styles for site front
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function wpdocs_styles_method() {
			/* get display tab setting from db */
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_color'] ) ) {
				$bupr_star_color = $bupr_display_settings['bupr_star_color'];
			}
			if ( empty( $bupr_star_color ) ) {
				$bupr_star_color = '#FFC400';
			}
			wp_enqueue_style( 'bupr-rating-css', BUPR_PLUGIN_URL . 'assets/css/bupr-front.css' );

			$custom_css = ".bupr-star-rate {
			        			color: {$bupr_star_color};
			        		}
				";
			wp_add_inline_style( 'bupr-rating-css', $custom_css );
		}

		/**
		 * Actions performed for enqueuing scripts and styles for admin page
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_custom_variables() {

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( 'review_page_bp-member-review-settings' === $screen->base || 'edit-review' === $screen->id ) {
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_style( 'bupr-font-awesome', BUPR_PLUGIN_URL . 'admin/assets/css/font-awesome.min.css' );
					if ( ! wp_style_is( 'wbcom-selectize-css', 'enqueued' ) ) {
						wp_enqueue_style( 'wbcom-selectize-css', BUPR_PLUGIN_URL . 'admin/assets/css/selectize.css' );
					}
					if ( ! wp_script_is( 'wbcom-selectize-js', 'enqueued' ) ) {
						wp_enqueue_script( 'wbcom-selectize-js', BUPR_PLUGIN_URL . 'admin/assets/js/selectize.min.js', array( 'jquery' ) );
					}
					wp_enqueue_script( 'bupr-js-admin', BUPR_PLUGIN_URL . 'admin/assets/js/bupr-admin.js', array( 'jquery' ) );

					wp_localize_script(
						'bupr-js-admin',
						'bupr_admin_ajax_object',
						array(
							'ajaxurl' => admin_url( 'admin-ajax.php' ),
						)
					);
					wp_enqueue_style( 'bupr-css-admin', BUPR_PLUGIN_URL . 'admin/assets/css/bupr-admin.css' );
					if ( ! wp_script_is( 'wp-color-picker', 'enqueued' ) ) {
						wp_enqueue_style( 'wp-color-picker' );
					}
					/* add wp color picker */
					wp_enqueue_script( 'bupr-color-picker', BUPR_PLUGIN_URL . 'admin/assets/js/bupr-color-picker.js', array( 'wp-color-picker' ), false, true );
				}
			}	
		}
	}
	new BUPRScriptsStyles();
}
