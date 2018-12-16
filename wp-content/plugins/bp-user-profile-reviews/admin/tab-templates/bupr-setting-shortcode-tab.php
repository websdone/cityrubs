<?php
/**
 * BuddyPress Member Review shortcode tab function file.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bupr-adming-setting">
	<div class="bupr-tab-header">
		<h3>
			<?php esc_html_e( 'Reviews Shortcode ', 'bp-member-reviews' ); ?>
		</h3>
		<input type="hidden" class="bupr-tab-active" value="shortcode"/>
	</div>

	<div class="bupr-admin-settings-block">
		<div id="bupr-settings-tbl" class="bupr-table">
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<strong>
						<?php echo esc_attr( '[add_profile_review_form]' ); ?>
					</strong>
				</div>
				<div class="bupr-admin-col-6">
					<p class="description">
						<?php esc_html_e( ' This shortcode will be display BP member review form.', 'bp-member-reviews' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
