<?php
/**
 * BuddyPress Member Review general tab function file.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bupr_spinner_src = includes_url() . '/images/spinner.gif';

/* admin setting on dashboard */
$bupr_admin_settings      = get_option( BUPR_GENERAL_OPTIONS, true );
$bupr_multi_reviews       = $bupr_auto_approve_reviews = $bupr_allow_email = $bupr_allow_notification = $bupr_member_dir_reviews = $bupr_member_dir_add_reviews = $bupr_enable_anonymous_reviews = '';
$bupr_exc_member          = array();
$profile_reviews_per_page = 3;

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_multi_reviews'] ) ) {
	$bupr_multi_reviews = $bupr_admin_settings['bupr_multi_reviews'];
}

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_auto_approve_reviews'] ) ) {
	$bupr_auto_approve_reviews = $bupr_admin_settings['bupr_auto_approve_reviews'];
}

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_member_dir_reviews'] ) ) {
	$bupr_member_dir_reviews = $bupr_admin_settings['bupr_member_dir_reviews'];
}

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_member_dir_add_reviews'] ) ) {
	$bupr_member_dir_add_reviews = $bupr_admin_settings['bupr_member_dir_add_reviews'];
}

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_enable_anonymous_reviews'] ) ) {
	$bupr_enable_anonymous_reviews = $bupr_admin_settings['bupr_enable_anonymous_reviews'];
}

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_allow_email'] ) ) {
	$bupr_allow_email = $bupr_admin_settings['bupr_allow_email'];
}
if ( ! empty( $bupr_admin_settings ) && $bupr_admin_settings['bupr_allow_notification'] ) {
	$bupr_allow_notification = $bupr_admin_settings['bupr_allow_notification'];
}
if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['profile_reviews_per_page'] ) ) {
	$profile_reviews_per_page = $bupr_admin_settings['profile_reviews_per_page'];
}
if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_exc_member'] ) ) {
	$bupr_exc_member = $bupr_admin_settings['bupr_exc_member'];
}

/* get all user for exclude for review */
$bupr_member_data = array();
foreach ( get_users() as $user ) {
	$bupr_key                      = $user->data->ID;
	$bupr_member_data[ $bupr_key ] = $user->data->display_name;
}
?>

<div class="bupr-adming-setting">
	<div class="bupr-tab-header">
		<h3>
			<?php esc_html_e( 'General Settings', 'bp-member-reviews' ); ?>
		</h3>
		<input type="hidden" class="bupr-tab-active" value="general"/>
	</div>

	<div class="bupr-admin-settings-block">

		<div id="bupr-settings-tbl" class="bupr-table">

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr-multi-review">
						<?php esc_html_e( 'Multiple Review', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr-multi-review" <?php checked( $bupr_multi_reviews, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option, if you want to add functionality to user can send multiple review to same user.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_review_auto_approval">
						<?php esc_html_e( 'Auto approve reviews ', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_review_auto_approval" <?php checked( $bupr_auto_approve_reviews, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option, if you want to have the reviews automatically approved, else manual approval will be required.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_member_dir_reviews">
						<?php esc_html_e( 'Show reviews at member directory', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_member_dir_reviews" <?php checked( $bupr_member_dir_reviews, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option, if you want to list reviews at member directory page.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_member_dir_add_reviews">
						<?php esc_html_e( 'Add view review link at member directory', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_member_dir_add_reviews" <?php checked( $bupr_member_dir_add_reviews, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option for Add Review link at member directory.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_review_email">
						<?php esc_html_e( 'Emails ', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_review_email" <?php checked( $bupr_allow_email, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option, if you want that member receive an email when someone add review in their profile.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_review_notification">
						<?php esc_html_e( 'BuddyPress notifications ', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_review_notification" <?php checked( $bupr_allow_notification, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option, if you want that member receive buddypress notification when someone add review in their profile.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="profile_reviews_per_page">
						<?php esc_html_e( 'Reviews pages show at most', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6">
					<input id="profile_reviews_per_page" class="small-text" name="profile_reviews_per_page" step="1" min="1" value="<?php echo $profile_reviews_per_page ? esc_attr( $profile_reviews_per_page ) : '3'; ?>" type="number">
					<?php esc_html_e( 'Reviews', 'bp-member-reviews' ); ?>
					<p class="description"><?php esc_html_e( 'This option lets you limit number of reviews in Member Reviews page.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>

			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_excluding_box">
						<?php esc_html_e( 'Exclude Members for review', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<select name="bupr_excluding_box[]" id="bupr_excluding_box" multiple class="bupr_excluding_member">
					<?php
					$counter = 0;
					foreach ( $bupr_member_data as $bupr_member_id => $bupr_member_name ) {
						if ( get_current_user_id() != $bupr_member_id ) {
							?>
							<option value="<?php echo esc_attr( $bupr_member_id ); ?>"
														<?php
														if ( in_array( $bupr_member_id, $bupr_exc_member ) ) {
															echo 'selected="selected"';
														}
							?>
							><?php echo esc_attr( $bupr_member_name ); ?></option>
							<?php
						}
					}
					?>
					</select>
					<p class="description"><?php esc_html_e( "This option lets you choose those members that you don't want to provide review functionality.", 'bp-member-reviews' ); ?></p>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6 bupr-label">
					<label for="bupr_enable_anonymous_reviews">
						<?php esc_html_e( 'Enable anonymous reviews', 'bp-member-reviews' ); ?>
					</label>
				</div>
				<div class="bupr-admin-col-6 ">
					<label class="bupr-switch">
						<input type="checkbox" id="bupr_enable_anonymous_reviews" <?php checked( $bupr_enable_anonymous_reviews, 'yes' ); ?>>
						<div class="bupr-slider bupr-round"></div>
					</label>
					<p class="description"><?php esc_html_e( 'Enable this option if you want users to review members anonymously.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>
			<div class="bupr-admin-row">
				<div class="bupr-admin-col-6">
					<input type="button" class="button button-primary" id="bupr-save-general-settings" value="<?php esc_html_e( 'Save Settings', 'bp-member-reviews' ); ?>">
					<img src="<?php echo esc_attr( $bupr_spinner_src ); ?>" class="bupr-admin-settings-spinner" />
				</div>
			</div>
		</div>
	</div>
</div>
