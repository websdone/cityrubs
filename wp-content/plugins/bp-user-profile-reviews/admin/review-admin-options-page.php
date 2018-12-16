<?php
/**
 * File use for fetching corresponding tab function file.
 *
 * @package BuddyPress_Member_Reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( isset( $_GET['tab'] ) ) {
	$bupr_setting_tab = sanitize_text_field( wp_unslash($_GET['tab']) );
} else {
	$bupr_setting_tab = 'general';
}
bupr_include_admin_setting_tabs( $bupr_setting_tab );
/**
 * Include review setting template.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @param    string $bupr_setting_tab The string is used for getting current tab.
 */
function bupr_include_admin_setting_tabs( $bupr_setting_tab = 'general' ) {
	switch ( $bupr_setting_tab ) {
		case 'general':
			include 'tab-templates/bupr-setting-general-tab.php';
			break;

		case 'criteria':
			include 'tab-templates/bupr-setting-criteria-tab.php';
			break;
		case 'shortcode':
			include 'tab-templates/bupr-setting-shortcode-tab.php';
			break;

		case 'support':
			include 'tab-templates/bupr-setting-support-tab.php';
			break;

		case 'display':
			include 'tab-templates/bupr-setting-display-tab.php';
			break;

		default:
			include 'tab-templates/bupr-setting-general-tab.php';
			break;
	}
}
