<?php
/*
Plugin Name: Aardvark Plugin
Plugin URI: 
Description: A required plugin for the Aardvark theme you purchased from ThemeForest. It includes a number of features that you can still use if you switch to another theme.
Version: 1.7
Author: GhostPool
Author URI: http://themeforest.net/user/GhostPool/portfolio?ref=GhostPool
License: You should have purchased a license from ThemeForest.net
Text Domain: aardvark-plugin
*/

// Ensure latest version of plugin installed
function ghostpool_aardvark_plugin_update() {}
	
if ( ! class_exists( 'GhostPool_Aardvark' ) ) {

	class GhostPool_Aardvark {

		public function __construct() {

			add_action( 'plugins_loaded', array( &$this, 'ghostpool_plugin_load_textdomain' ) );	
			add_action( 'init', array( &$this, 'ghostpool_load_shortcodes' ) );
			add_action( 'vc_before_init', array( &$this, 'ghostpool_vc_functions' ), 9 );				
						
			// Add shortcode support to Text widget
			add_filter( 'widget_text', 'do_shortcode' );

			// Add excerpt support to pages
			if ( ! function_exists( 'ghostpool_add_excerpts_to_pages' ) ) {
				function ghostpool_add_excerpts_to_pages() {
					 add_post_type_support( 'page', 'excerpt' );
				}
			}
			add_action( 'init', 'ghostpool_add_excerpts_to_pages' );

			// Display pages in post category and tag queries
			if ( ! function_exists( 'ghostpool_page_support_query' ) ) {
				function ghostpool_page_support_query( $query ) {
					if ( ( is_category() OR is_tag() ) && ! is_admin() && $query->is_main_query() ) { 
						$query->set( 'post_type', 'any' );	
					}	
				}
			}
			add_action( 'pre_get_posts', 'ghostpool_page_support_query' );
	
			// Load voting functions
			require_once( sprintf( "%s/inc/voting-functions.php", dirname( __FILE__ ) ) );	
			
			// Load share icons
			require_once( sprintf( "%s/inc/share-icons.php", dirname( __FILE__ ) ) );
			
			// Add user profile fields
			if ( ! function_exists( 'ghostpool_custom_profile_methods' ) ) {
				function ghostpool_custom_profile_methods( $profile_fields ) {
					$profile_fields['twitter'] = esc_html__( 'Twitter URL', 'aardvark-plugin' );
					$profile_fields['facebook'] = esc_html__( 'Facebook URL', 'aardvark-plugin' );
					$profile_fields['googleplus'] = esc_html__( 'Google+ URL', 'aardvark-plugin' );
					$profile_fields['pinterest'] = esc_html__( 'Pinterest URL', 'aardvark-plugin' );
					$profile_fields['youtube'] = esc_html__( 'YouTube URL', 'aardvark-plugin' );
					$profile_fields['vimeo'] = esc_html__( 'Vimeo URL', 'aardvark-plugin' );
					$profile_fields['flickr'] = esc_html__( 'Flickr URL', 'aardvark-plugin' );
					$profile_fields['linkedin'] = esc_html__( 'LinkedIn URL', 'aardvark-plugin' );
					$profile_fields['instagram'] = esc_html__( 'Instagram URL', 'aardvark-plugin' );
					return $profile_fields;
				}
			}
			add_filter( 'user_contactmethods', 'ghostpool_custom_profile_methods' );
						
			// Load functions if plugin is activated without theme
			if ( ! function_exists( 'ghostpool_load_shortcode_functions' ) ) {
				function ghostpool_load_shortcode_functions() {
								
					if ( ! function_exists( 'ghostpool_option' ) ) {
						function ghostpool_option() {}
					}	
					if ( ! function_exists( 'ghostpool_excerpt' ) ) {
						function ghostpool_excerpt() {}
					}	
					if ( ! function_exists( 'ghostpool_pagination_arrows' ) ) {
						function ghostpool_pagination_arrows() {}
					}
					if ( ! function_exists( 'ghostpool_pagination' ) ) {
						function ghostpool_pagination() {}
					}
					if ( ! function_exists( 'ghostpool_cats' ) ) {
						function ghostpool_cats() {}
					}
					if ( ! function_exists( 'ghostpool_tax' ) ) {
						function ghostpool_tax() {}
					}				
					if ( ! function_exists( 'ghostpool_orderby' ) ) {
						function ghostpool_orderby() {}
					}
					if ( ! function_exists( 'ghostpool_paged' ) ) {
						function ghostpool_paged() {}
					}
					if ( ! function_exists( 'ghostpool_filter' ) ) {
						function ghostpool_filter() {}
					}
					
				}
			}			
			add_action( 'init', 'ghostpool_load_shortcode_functions' );	
																											
		} 
		
		public static function ghostpool_activate() {} 		
		
		public static function ghostpool_deactivate() {}

		public function ghostpool_plugin_load_textdomain() {
			load_plugin_textdomain( 'aardvark-plugin', false, trailingslashit( WP_LANG_DIR ) . 'plugins/' );
			load_plugin_textdomain( 'aardvark-plugin', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}
		
		public function ghostpool_vc_functions() {
			if ( function_exists( 'vc_set_as_theme' ) ) {
				vc_set_shortcodes_templates_dir( dirname( __FILE__ ) . '/shortcodes' );
			}
		}

		public function ghostpool_load_shortcodes() {
			if ( function_exists( 'vc_set_as_theme' ) ) {
						
				// Custom divider field
				if ( ! class_exists( 'ghostpool_wpb_divider_field' ) ) {
					function ghostpool_wpb_divider_field() {
						return '<div class="gp-divider"></div>';
					}
				}	
				vc_add_shortcode_param( 'gp_divider', 'ghostpool_wpb_divider_field' );
			
				// Load shortcodes
				require_once( sprintf( "%s/shortcodes/bp-activity.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/bp-groups.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/bp-members.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/bp-profile-search.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/bp-whos-online.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/carousel-posts.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/carousel-images.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/events.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/events-calendar.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/featured-box.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/login-register-form.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/particles.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/pmp-register-form.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/posts.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/post-submission-form.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/pricing-column.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/sensei-courses.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/showcase.php", dirname( __FILE__ ) ) );
				require_once( sprintf( "%s/shortcodes/statistics.php", dirname( __FILE__ ) ) );			
			}
		}		
					
	}
	
}

// WP mail function
if ( ! function_exists( 'ghostpool_wp_mail' ) ) {
	function ghostpool_wp_mail( $to = '', $subject = '', $message = '', $headers = '' ) {
		return wp_mail( $to, $subject, $message, $headers );				
	}
}

// User registration emails
$theme_variable = get_option( 'ghostpool_aardvark' );
if ( ! function_exists( 'wp_new_user_notification' ) && ! function_exists( 'bp_is_active' ) && $theme_variable['login_register_popup_redirect'] == 'enabled' ) {
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = 'both' ) {

		if ( $deprecated !== null ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}
	
		global $wpdb;
		$user = get_userdata( $user_id );
		
		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		// Switch language context…
		do_action( 'wpml_switch_language_for_email', $user_email );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		
		// Admin email
		$message  = sprintf( esc_html__( 'New user registration on your blog %s:', 'aardvark-plugin' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Username: %s', 'aardvark-plugin' ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Email: %s', 'aardvark-plugin' ), $user_email ) . "\r\n";
		$message = apply_filters( 'ghostpool_registration_notice_message', $message, $blogname, $user_login, $user_email );
		@wp_mail( get_option( 'admin_email' ), sprintf( apply_filters( 'ghostpool_registration_notice_subject', esc_html__( '[%s] New User Registration', 'aardvark-plugin' ), $blogname ), $blogname ), $message );

		if ( 'admin' === $notify || empty( $notify ) ) {
			return;
		}
		
		// User email
		$message  = esc_html__( 'Hi there,', 'aardvark-plugin' ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Welcome to %s.', 'aardvark-plugin' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Username: %s', 'aardvark-plugin' ), $user_login ) . "\r\n";
		$message .= esc_html__( 'Password: [use the password you entered when signing up]', 'aardvark-plugin' ) . "\r\n\r\n";
		$message .= esc_html__( 'Please login at', 'aardvark' ) . ' ' . home_url( '/#login' ) . "\r\n\r\n";	
		$message = apply_filters( 'ghostpool_registered_user_message', $message, $blogname, $user_login, $user_email );
		wp_mail( $user_email, sprintf( apply_filters( 'ghostpool_registered_user_subject', esc_html__( '[%s] Your username and password', 'aardvark-plugin' ), $blogname ), $blogname ), $message );

		// Switch language back
		do_action( 'wpml_restore_language_from_email' );

	}
	
}

// Active/deactivate plugin
if ( class_exists( 'GhostPool_Aardvark' ) ) {

	register_activation_hook( __FILE__, array( 'GhostPool_Aardvark', 'ghostpool_activate' ) );
	register_deactivation_hook( __FILE__, array( 'GhostPool_Aardvark', 'ghostpool_deactivate' ) );

	$ghostpool_plugin = new GhostPool_Aardvark();

}

?>