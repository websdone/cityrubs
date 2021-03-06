<?php
/**
 * Plugin Name: BuddyPress Member Reviews
 * Plugin URI: https://wbcomdesigns.com/downloads/buddypress-user-profile-reviews/
 * Description: This plugin  allows only site members to add reviews to the buddypress members on the site. But the member can not review himself/herself. And if the visitor is not logged in, he can only see the listing of the reviews but can not review.  The review form allows the members to even rate the member's profile out of 5 points with multiple review criteria..
 * Version: 1.0.8
 * Author: Wbcom Designs
 * Author URI: https://wbcomdesigns.com
 * License: GPLv2+
 * Text Domain: bp-member-reviews
 * Domain Path: /languages
 *
 * @package BuddyPress_Member_Reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

	/**
	* Constants used in the plugin
	*/
	define( 'BUPR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'BUPR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	/* define options name for admin setting option */
	define( 'BUPR_GENERAL_OPTIONS', 'bupr_admin_general_options' );
	define( 'BUPR_CRITERIA_OPTIONS', 'bupr_admin_criteria_options' );
	define( 'BUPR_SHORTCODE_OPTIONS', 'bupr_admin_shortcode_options' );
	define( 'BUPR_DISPLAY_OPTIONS', 'bupr_admin_display_options' );

if ( ! function_exists( 'bupr_load_textdomain' ) ) {
	add_action( 'init', 'bupr_load_textdomain' );
	/**
	 * Load plugin textdomain.
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	function bupr_load_textdomain() {
		$domain = 'bp-member-reviews';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, 'languages/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
}



if ( ! function_exists( 'bupr_plugins_files' ) ) {

	add_action( 'plugins_loaded', 'bupr_plugins_files' );

	/**
	 * Include requir files
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	function bupr_plugins_files() {
		if ( ! in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'admin_notices', 'bupr_admin_notice' );
		} else {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bupr_admin_page_link' );
			/**
			* Include needed files on init
			*/
			$include_files = array(
				'includes/bupr-scripts.php',
				'admin/bupr-admin.php',
				'includes/bupr-filters.php',
				'includes/bupr-shortcodes.php',
				'includes/widgets/display-review.php',
				'includes/bupr-ajax.php',

			);

			foreach ( $include_files as $include_file ) {
				include $include_file;
			}
		}
	}
}

if ( ! function_exists( 'bupr_admin_notice' ) ) {
	/**
	 * Display admin notice
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	function bupr_admin_notice() {
		$bupr_plugin = 'BuddyPress Member Reviews';
		$bp_plugin   = 'BuddyPress';

		echo '<div class="error"><p>' . sprintf( __( '%1$s is ineffective now as it requires %2$s to function correctly.', 'bp-member-reviews' ), '<strong>' . esc_html( $bupr_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' ) . '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

add_action( 'bp_init', 'bupr_bp_notifications_for_review', 12 );

/**
 * BuddyPress member reviews notification.
 *
 * @author   Wbcom Designs
 * @since    1.0.0
 */
function bupr_bp_notifications_for_review() {
	include 'includes/bupr-notification.php';
	buddypress()->bupr_bp_review                                = new BUPR_Notifications();
			buddypress()->bupr_bp_review->notification_callback = 'bupr_format_notifications';
}



/**
 * Settings link for this plugin.
 *
 * @author   Wbcom Designs
 * @since    1.0.0
 * @param    array $links    The plugin setting links array.
 */
function bupr_admin_page_link( $links ) {
	$page_link = array(
		'<a href="' . admin_url( 'admin.php?page=bp-member-review-settings' ) . '">Settings</a>',
	);
	return array_merge( $links, $page_link );
}
