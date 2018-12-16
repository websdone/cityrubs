<?php
/**
 * Class to add reviews shortcode.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BUPR_Shortcodes' ) ) {

	/**
	 * Class to serve AJAX Calls.
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	class BUPR_Shortcodes {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @author   Wbcom Designs
		 */
		public function __construct() {
			add_shortcode( 'add_profile_review_form', array( $this, 'bupr_shortcode_review_form' ) );
		}



		/**
		 * Display add review form on front-end.
		 *
		 * @since    1.0.0
		 * @author   Wbcom Designs
		 */
		public function bupr_display_review_form() {
			global $bp;
			/* get display tab setting from db */
			$bupr_star_color       = '#FFC400';
			$bupr_review_title     = __( 'Reviews', 'bp-member-reviews' );
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_review_title'] ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
			}
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_color'] ) ) {
				$bupr_star_color = $bupr_display_settings['bupr_star_color'];
			}

			$login_user          = get_current_user_id();
			$bupr_spinner_src    = includes_url() . 'images/spinner.gif';
			$bupr_admin_settings = get_option( 'bupr_admin_settings' );
			$bupr_general_tab    = get_option( BUPR_GENERAL_OPTIONS );

			if ( ! empty( $bupr_general_tab ) && array_key_exists( 'bupr_multi_reviews', $bupr_general_tab ) ) {
				$bupr_multi_reviews = $bupr_general_tab['bupr_multi_reviews'];
			} else {
				$bupr_multi_reviews = 'no';
			}

			if ( ! empty( $bupr_admin_settings ) && array_key_exists( 'profile_rating_fields', $bupr_admin_settings ) ) {
				$profile_rating_fields = $bupr_admin_settings['profile_rating_fields'];
			} else {
				$profile_rating_fields = array();
			}
			$bupr_review_succes = false;
			$bupr_flag          = false;
			$bupr_member        = array();
			foreach ( get_users() as $user ) {
				if ( $user->ID !== get_current_user_id() ) {
					$bupr_member[] = array(
						'member_id'   => $user->ID,
						'member_name' => $user->data->display_name,
					);
				}
			}

					$member_args = array(
						'post_type'      => 'review',
						'posts_per_page' => -1,
						'post_status'    => array(
							'draft',
							'publish',
						),
						'author'         => $login_user,
						'category'       => 'bp-member',
						'meta_query'     => array(
							array(
								'key'     => 'linked_bp_member',
								'value'   => bp_displayed_user_id(),
								'compare' => '=',
							),
						),
					);

					$reviews_args = new WP_Query( $member_args );

			if ( ! bp_is_members_component() && ! bp_is_user() ) { ?>
				<div id="message" class="success success_review_msg"><p></p></div>
				<?php
			}

			if ( 0 === bp_displayed_user_id() ) {
				$this->bupr_review_form( $bupr_review_title, $bupr_star_color, $login_user, $bupr_spinner_src, $bupr_admin_settings, $bupr_multi_reviews, $profile_rating_fields, $bupr_review_succes, $bupr_flag, $bupr_member );
			} else {
				if ( $bupr_multi_reviews == 'no' ) {
					$user_post_count = $reviews_args->post_count;
				} else {
					$user_post_count = 0;
				}
				if ( $user_post_count == 0 ) {
					$this->bupr_review_form( $bupr_review_title, $bupr_star_color, $login_user, $bupr_spinner_src, $bupr_admin_settings, $bupr_multi_reviews, $profile_rating_fields, $bupr_review_succes, $bupr_flag, $bupr_member );
				} else {
					?>
					<div id="message" class="error">
					<p><?php echo sprintf( __( 'You already posted a %s for this member.', 'bp-member-reviews' ), esc_html( $bupr_review_title ) ); ?> </p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Bupr review form.
		 *
		 * @since    1.0.0
		 * @param    string $bupr_review_title      Review Title.
		 * @param    string $bupr_star_color        Star   Color.
		 * @param    string $login_user             Login  User.
		 * @param    string $bupr_spinner_src       Spinner  User.
		 * @param    array  $bupr_admin_settings    Admin Settings array.
		 * @param    string $bupr_multi_reviews     Multiple Reviews.
		 * @param    string $profile_rating_fields  Rating fields.
		 * @param    string $bupr_review_succes     Review Success.
		 * @param    int    $bupr_flag              Flag.
		 * @param    array  $bupr_member            Member array
		 * @author   Wbcom Designs
		 */
		public function bupr_review_form( $bupr_review_title, $bupr_star_color, $login_user, $bupr_spinner_src, $bupr_admin_settings, $bupr_multi_reviews, $profile_rating_fields, $bupr_review_succes, $bupr_flag, $bupr_member ) {

			$bupr_admin_settings      = get_option( BUPR_GENERAL_OPTIONS, true );
			$flag = false;
			if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_enable_anonymous_reviews'] ) ) {
				$bupr_enable_anonymous_reviews = $bupr_admin_settings['bupr_enable_anonymous_reviews'];
				if ( $bupr_enable_anonymous_reviews == 'yes' ) {
					$flag = true;
				}
			}
		?>
			<div id="message" class="success add_review_msg success_review_msg"><p></p></div>
			<form action="" method="POST">
				<input type="hidden" value="<?php echo ! empty( $bupr_star_color ) ? esc_attr( $bupr_star_color ) : '#1fd9e0'; ?>" class="bupr-display-rating-color">
				<input type="hidden" id="reviews_pluginurl" value="<?php echo esc_url( BUPR_PLUGIN_URL ); ?>">
				<div class="bp-member-add-form">

				<p>
					<?php echo sprintf( __( 'Fill in details to submit %s', 'bp-member-reviews' ), esc_html( $bupr_review_title ) ); ?>
				</p>

				<?php if ( 0 === bp_displayed_user_id() ) { ?>
				<p>
					<select name="bupr_member_id" id="bupr_member_review_id">
						<option value=""><?php esc_html_e( '--Select--', 'bp-member-reviews' ); ?></option>
						<?php
						if ( ! empty( $bupr_member ) ) {
							foreach ( $bupr_member as $user ) {
								echo '<option value="' . esc_attr( $user['member_id'] ) . '">' . esc_attr( $user['member_name'] ) . '</option>';
							}
						}
						?>
					</select><br/>
					<span class="bupr-error-fields">*<?php esc_html_e( 'This field is required.', 'bp-member-reviews' ); ?></span>
				</p>
				<?php } ?>
				<input type="hidden" id="bupr_member_review_id" value="<?php echo esc_attr( bp_displayed_user_id() ); ?>">
				<p class="bupr-hide-subject">
					<input name="review-subject" id="review_subject" type="text" placeholder="<?php esc_html_e( 'Review Subject', 'bp-member-reviews' ); ?>" ><br/><span class="bupr-error-fields">*<?php esc_html_e( 'This field is required.', 'bp-member-reviews' ); ?></span>
				</p>
				<p>
					<textarea name="review-desc" id="review_desc" placeholder="<?php esc_html_e( 'Review Description', 'bp-member-reviews' ); ?>" rows="4" cols="50"></textarea><br/><span class="bupr-error-fields">*<?php esc_html_e( 'This field is required.', 'bp-member-reviews' ); ?></span>
				</p>
				<?php
				if ( ! empty( $profile_rating_fields ) ) {
					$field_counter = 1;
					$flage         = true;

					foreach ( $profile_rating_fields as $bupr_rating_fileds => $bupr_criteria_setting ) :

						if ( $bupr_criteria_setting == 'yes' ) {
						?>
							<div class="bupr-col-12">
								<div class="bupr-col-3 bupr-criteria-label">
									<?php esc_html_e( html_entity_decode( $bupr_rating_fileds ), 'bp-member-reviews' ); ?>
								</div>
								<div class="bupr-col-4 bupr-criteria-content" id="member_review<?php echo esc_attr( $field_counter ); ?>">
									<input type="hidden" id="<?php echo 'clicked' . esc_attr( $field_counter ); ?>" value="<?php echo 'not_clicked'; ?>">
									<input type="hidden" name="member_rated_stars[]" id="member_rated_stars" class="member_rated_stars bupr-star-member-rating" id="<?php echo 'member_rated_stars' . esc_attr( $field_counter ); ?>" value="0">
									<?php
									for ( $i = 1; $i <= 5; $i++ ) {
									?>
										<span class="member_stars <?php echo esc_attr( $i ); ?> fa fa-star-o bupr-stars bupr-star-rate <?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $field_counter ) . esc_attr( $i ); ?>" data-attr="<?php echo esc_attr( $i ); ?>" ></span>
									<?php } ?>
								</div>
								<div class="bupr-col-12 bupr-error-fields">*<?php esc_html_e( 'This field is required.', 'bp-member-reviews' ); ?></div>
							</div>
							<?php
							$field_counter++;
						}
					endforeach;
					?>
					<input type="hidden" id="member_rating_field_counter" value="<?php echo esc_attr( --$field_counter ); ?>">
					<?php } ?>
					<?php if( $flag ){ ?>
					<p>
						<label for="bupr_anonymous_review"><input style="width:auto !important" type="checkbox" id="bupr_anonymous_review" value="value"><?php esc_html_e( 'Send review anonymously.', 'bp-member-reviews' ); ?></label>
					</p>
					<?php } ?>
					<p>
						<?php wp_nonce_field( 'save-bp-member-review', 'security-nonce' ); ?>

						<button type="button" class="btn btn-default" id="bupr_save_review" name="submit-review">
						<?php echo sprintf( __( 'Submit %s', 'bp-member-reviews' ), esc_html( $bupr_review_title ) ); ?>
						</button>
						<input type="hidden" value="<?php echo esc_attr( $login_user ); ?>" id="bupr_current_user_id" />
						<img src="<?php echo esc_url( $bupr_spinner_src ); ?>" class="bupr-save-reivew-spinner" />
					</p>
				</div>
			</form>
			<?php
		}

		/**
		 * Create shortcode for review form.
		 *
		 * @since    1.0.0
		 * @author   Wbcom Designs
		 */
		public function bupr_shortcode_review_form() {
			$this->bupr_display_review_form();

		}

	}
	new BUPR_Shortcodes();
}
