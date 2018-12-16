<?php
/**
 * BuddyPress Member Review criteria tab function file.
 *
 * @package BuddyPress_Member_Reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$bupr_spinner_src = includes_url() . '/images/spinner.gif';

/* admin setting on dashboard */
$bupr_admin_settings = get_option( 'bupr_admin_settings', true );
if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['profile_rating_fields'] ) ) {
	$profile_rating_fields = $bupr_admin_settings['profile_rating_fields'];
}

$bupr_multi_criteria_allowed     = 0;
$bupr_multi_rating_allowed_class = 'bupr-show-if-allowed';
if ( isset( $bupr_admin_settings['profile_multi_rating_allowed'] ) ) {
	$bupr_multi_criteria_allowed = $bupr_admin_settings['profile_multi_rating_allowed'];

	if ( $bupr_multi_criteria_allowed == 1 ) {
		$bupr_multi_rating_allowed_class = '';
	}
}

?>
<div class="bupr-adming-setting">
	<div class="bupr-tab-header">
		<h3>
			<?php esc_html_e( 'Review Criteria(s)', 'bp-member-reviews' ); ?>
		</h3>
		<input type="hidden" class="bupr-tab-active" value="criteria"/>
	</div>

	<div class="bupr-admin-row border">
		<div class="bupr-admin-col-6 bupr-label">
			<label for="bupr_allow_multiple_criteria">
				<?php esc_html_e( 'Allow Multiple Criteria(s)? ', 'bp-member-reviews' ); ?>
			</label>
		</div>
		<div class="bupr-admin-col-6 ">
			<label class="bupr-switch">
				<input type="checkbox" id="bupr_allow_multiple_criteria" <?php checked( $bupr_multi_criteria_allowed, '1' ); ?>>
				<div class="bupr-slider bupr-round"></div>
			</label>
			<p class="description"><?php esc_html_e( "Enable this option,if you want to allow members to be rated by 'Criteria(s)'.", 'bp-member-reviews' ); ?></p>
		</div>
	</div>

	<div class="bupr-admin-settings-block">
		<div id="bupr-settings-tbl" class="bupr-table bupr-criteria-settings-tbl">

			<div id="buprTextBoxContainer" class="<?php echo esc_attr( $bupr_multi_rating_allowed_class ); ?>">
				<?php
				if ( ! empty( $profile_rating_fields ) ) {
					foreach ( $profile_rating_fields as $profile_rating_field => $bupr_criteria_setting ) :
					?>
						<div class="bupr-admin-row bupr-criteria bupr-criteria-fields border draggable">
							<div class="bupr-admin-col-6">
								<span>&equiv;</span>
								<input name = "buprDynamicTextBox"   type="text" value = "<?php echo esc_attr( $profile_rating_field ); ?>" />
							</div>

							<div class="bupr-admin-col-6 buprcriteria">
								<p class="bupr-delete-tag">
								<input type="button" value="Delete" class="bupr-criteria-remove-button bupr-remove button button-secondary" />
								<span class="description">
								<?php esc_html_e( 'Remove criteria fields permanently.', 'bp-member-reviews' ); ?>
								</span>
								</p>
								<label class="bupr-switch">
									<input type="checkbox" class="bupr_enable_criteria" value="<?php echo $bupr_criteria_setting == 'yes' ? 'yes' : 'no'; ?>" <?php checked( $bupr_criteria_setting, 'yes' ); ?>>
									<div class="bupr-slider bupr-round"></div>
								</label>
								<span class="description">
								<?php esc_html_e( 'Enable/Disable criteria fields from review form.', 'bp-member-reviews' ); ?>
								</span>
							</div>
						</div>
						<?php
					endforeach;
				}
				?>
			</div>
			<div id="bupr-add-criteria-action" class="bupr-admin-row border <?php echo esc_attr( $bupr_multi_rating_allowed_class ); ?>">
				<div class="bupr-admin-col-12">
					<input id="bupr-btnAdd" type="button" value="Add Criteria" class="button button-secondary"/>
					<p class="description"><?php esc_html_e( 'This option provide you to add multiple rating criteria. By default, no criteria will be shown until you active it.', 'bp-member-reviews' ); ?></p>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-6">

					<input type="button" class="button button-primary" id="bupr-save-criteria-settings" value="<?php esc_html_e( 'Save Settings', 'bp-member-reviews' ); ?>">
					<img src="<?php echo $bupr_spinner_src; ?>" class="bupr-admin-settings-spinner" />
				</div>
			</div>

		</div>
	</div>
</div>
