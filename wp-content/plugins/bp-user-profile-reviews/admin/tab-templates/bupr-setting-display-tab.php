<?php
/**
 * BuddyPress Member Review display tab function file.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$bupr_spinner_src = includes_url() . '/images/spinner.gif';
	/* get display tab setting from db */
	$bupr_star_color       = '#FFC400';
	$bupr_review_title     = 'Reviews';
	$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_review_title'] ) ) {
	$bupr_review_title = $bupr_display_settings['bupr_review_title'];
}
if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_color'] ) ) {
	$bupr_star_color = $bupr_display_settings['bupr_star_color'];
}

?>
<div class="bupr-adming-setting ">
	<p class="description">
		<b><?php esc_html_e( 'Note', 'bp-member-reviews' ); ?>:</b>
		<?php esc_html_e( ' Rating will be displayed in Review form, if and only if you added criteria for review.', 'bp-member-reviews' ); ?>
	</p>
	<div class="bupr-tab-header">
		<h3>
			<?php esc_html_e( 'Labels', 'bp-member-reviews' ); ?>
		</h3>
		<input type="hidden" class="bupr-tab-active" value="display"/>
	</div>

	<div class="bupr-admin-settings-block">
		<div id="bupr-settings-tbl" class="bupr-table">
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-4 bupr-label">
					<?php esc_html_e( 'Reviews', 'bp-member-reviews' ); ?>
				</div>
				<div class="bupr-admin-col-8">
					<input type="text" name="bupr_member_tab_title" id="bupr_member_tab_title" value="<?php echo ! empty( $bupr_review_title ) ? esc_attr( $bupr_review_title ) : 'Review'; ?>">
					<p class="description">
						<?php esc_html_e( 'Change Labels from BuddyPress tab and review form.', 'bp-member-reviews' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="bupr-tab-header">
		<h3>
			<?php esc_html_e( 'Colors ', 'bp-member-reviews' ); ?>
		</h3>
	</div>

	<div class="bupr-admin-settings-block">
		<div id="bupr-settings-tbl" class="bupr-table">
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-4 bupr-label">
					<?php esc_html_e( 'Rating color', 'bp-member-reviews' ); ?>
				</div>
				<div class="bupr-admin-col-8">
					<input type="text" id="bupr_display_color" class="bupr-admin-color-picker" value="<?php echo ! empty( $bupr_star_color ) ? esc_attr( $bupr_star_color ) : '#FFC400'; ?>">
					<p class="description">
						<?php esc_html_e( 'This option lets you to change star rating color.', 'bp-member-reviews' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="bupr-admin-row">
		<div class="bupr-admin-col-6">
			<input type="button" class="button button-primary" id="bupr-save-display-settings" value="<?php esc_html_e( 'Save Settings', 'bp-member-reviews' ); ?>">
			<img src="<?php echo esc_attr( $bupr_spinner_src ); ?>" class="bupr-admin-settings-spinner" />
		</div>
	</div>

</div>
