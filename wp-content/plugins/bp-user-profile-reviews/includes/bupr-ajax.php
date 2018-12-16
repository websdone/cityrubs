<?php
/**
 * Class to serve AJAX Calls
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

defined( 'ABSPATH' ) || exit;

/**
* Class to serve AJAX Calls
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if ( ! class_exists( 'BUPR_AJAX' ) ) {
	/**
	 * The ajax functionality of the plugin.
	 *
	 * @package    BuddyPress_Member_Reviews
	 * @author     wbcomdesigns <admin@wbcomdesigns.com>
	 */
	class BUPR_AJAX {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			/* add action for approving reviews */
			add_action( 'wp_ajax_bupr_approve_review', array( $this, 'bupr_approve_review' ) );
			add_action( 'wp_ajax_nopriv_bupr_approve_review', array( $this, 'bupr_approve_review' ) );

			/* add action for general tab admin setting */
			add_action( 'wp_ajax_bupr_admin_tab_generals', array( $this, 'bupr_admin_tab_general_settings' ) );
			add_action( 'wp_ajax_nopriv_bupr_admin_tab_generals', array( $this, 'bupr_admin_tab_general_settings' ) );

			/* add action for criteria tab admin setting */
			add_action( 'wp_ajax_bupr_admin_tab_criteria', array( $this, 'bupr_admin_tab_criteria' ) );
			add_action( 'wp_ajax_nopriv_bupr_admin_tab_criteria', array( $this, 'bupr_admin_tab_criteria' ) );

			/* add action for display tab admin setting */
			add_action( 'wp_ajax_bupr_admin_tab_display', array( $this, 'bupr_admin_tab_display_settings' ) );
			add_action( 'wp_ajax_nopriv_bupr_admin_tab_display', array( $this, 'bupr_admin_tab_display_settings' ) );

			add_action( 'wp_ajax_allow_bupr_member_review_update', array( $this, 'wp_allow_bupr_my_member' ) );
			add_action( 'wp_ajax_nopriv_allow_bupr_member_review_update', array( $this, 'wp_allow_bupr_my_member' ) );

			/*** Filter post_date_gmt for prevent update post date on update_post_data */
			add_filter( 'wp_insert_post_data', array( $this, 'bupr_filter_review_post' ), 10, 1 );
		}

		/**
		 * Actions performed on inserting post data.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $data Post data array.
		 * @author   Wbcom Designs
		 */
		public function bupr_filter_review_post( $data ) {
			if ( $data['post_type'] == 'review' ) {
				$post_date             = $data['post_date'];
				$post_date_gmt         = get_gmt_from_date( $post_date );
				$data['post_date_gmt'] = $post_date_gmt;
			}
			return $data;
		}

		/**
		 * Actions performed to approve review at admin end.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_approve_review() {
			if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) === 'bupr_approve_review' ) {
				$rid  = sanitize_text_field( filter_input( INPUT_POST, 'review_id' ) );
				$args = array(
					'ID'          => $rid,
					'post_status' => 'publish',
				);
				wp_update_post( $args );
				echo 'review-approved-successfully';
				die;
			}
		}

		/**
		 * Actions performed for saving admin settings general tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_tab_general_settings() {
			if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) === 'bupr_admin_tab_generals' ) {
				$bupr_multi_reviews        = filter_input( INPUT_POST, 'bupr_multi_reviews', FILTER_SANITIZE_STRING );
				$bupr_auto_approve_reviews = filter_input( INPUT_POST, 'bupr_auto_approve_reviews', FILTER_SANITIZE_STRING );
				$bupr_allow_email          = filter_input( INPUT_POST, 'bupr_allow_email', FILTER_SANITIZE_STRING );
				$bupr_allow_notification   = filter_input( INPUT_POST, 'bupr_allow_notification', FILTER_SANITIZE_STRING );
				$bupr_reviews_per_page     = filter_input( INPUT_POST, 'bupr_reviews_per_page', FILTER_SANITIZE_STRING );
				$bupr_member_dir_reviews   = filter_input( INPUT_POST, 'bupr_member_dir_reviews', FILTER_SANITIZE_STRING );
				$bupr_member_dir_add_reviews = filter_input( INPUT_POST, 'bupr_member_dir_add_reviews', FILTER_SANITIZE_STRING );
				$bupr_enable_anonymous_reviews = filter_input( INPUT_POST, 'bupr_enable_anonymous_reviews', FILTER_SANITIZE_STRING );
				$bupr_exc_member           = array_map( 'sanitize_text_field', wp_unslash( $_POST['bupr_exc_member'] ) );
				$bupr_exclude_id           = array();
				if ( ! empty( $bupr_exc_member ) ) {
					foreach ( $bupr_exc_member as $bupr_id ) {
						$bupr_exclude_id[ $bupr_id ] = $bupr_id;
					}
				}

				$bupr_general_options = array(
					'bupr_multi_reviews'        => $bupr_multi_reviews,
					'bupr_auto_approve_reviews' => $bupr_auto_approve_reviews,
					'bupr_member_dir_reviews'   => $bupr_member_dir_reviews,
					'bupr_member_dir_add_reviews' => $bupr_member_dir_add_reviews,
					'profile_reviews_per_page'  => $bupr_reviews_per_page,
					'bupr_allow_email'          => $bupr_allow_email,
					'bupr_allow_notification'   => $bupr_allow_notification,
					'bupr_enable_anonymous_reviews' => $bupr_enable_anonymous_reviews,
					'bupr_exc_member'           => $bupr_exclude_id,
				);
				update_option( BUPR_GENERAL_OPTIONS, $bupr_general_options );
				echo 'gen-setting-saved-successfully'; die;
			}
		}

		/**
		 * Actions performed for saving admin settings criteria tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_tab_criteria() {
			if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) === 'bupr_admin_tab_criteria' ) {
				$bupr_multiple_criteria_allowed = filter_input( INPUT_POST, 'bupr_multiple_criteria_allowed', FILTER_SANITIZE_STRING );
				$bupr_review_criterias          = array();

				if ( $bupr_multiple_criteria_allowed == 1 ) {
					$bupr_review_criteria = array_map( 'sanitize_text_field', wp_unslash( $_POST['bupr_review_criteria'] ) );
					$bupr_criteria_encode = array();
					if ( ! empty( $bupr_review_criteria ) ) {
						foreach ( $bupr_review_criteria as $buprcriteria ) {
							$bupr_criteria_encode[] = htmlspecialchars( $buprcriteria );
						}
					}
					$bupr_criteria_setting = array_map( 'sanitize_text_field', wp_unslash( $_POST['bupr_criteria_setting'] ) );
					if ( ! empty( $bupr_criteria_encode ) && ! empty( $bupr_criteria_setting ) ) {
						$bupr_review_criterias = array_combine( $bupr_criteria_encode, $bupr_criteria_setting );
					}
				}

				$bupr_admin_settings = array(
					'profile_multi_rating_allowed' => $bupr_multiple_criteria_allowed,
					'profile_rating_fields'        => $bupr_review_criterias,
				);
				update_option( 'bupr_admin_settings', $bupr_admin_settings );
				echo 'admin-settings-saved';
				die;
			}
		}

		/**
		 * Actions performed for saving admin settings display tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_tab_display_settings() {
			if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) === 'bupr_admin_tab_display' ) {

				$bupr_review_title = filter_input( INPUT_POST, 'bupr_review_title', FILTER_SANITIZE_STRING );
				$bupr_review_color = filter_input( INPUT_POST, 'bupr_review_color', FILTER_SANITIZE_STRING );
				if ( empty( $bupr_review_title ) ) {
					$bupr_review_title = 'Review';
				}
				$bupr_display_setting = array(
					'bupr_review_title' => $bupr_review_title,
					'bupr_star_color'   => $bupr_review_color,
				);
				update_option( BUPR_DISPLAY_OPTIONS, $bupr_display_setting );
				echo 'admin-settings-saved';
				die;
			}
		}


		/**
		 * Add review to member's profile
		 *
		 * @since    1.0.0
		 * @author   Wbcom Designs
		 */
		public function wp_allow_bupr_my_member() {
			if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) == 'allow_bupr_member_review_update' ) {

				$bupr_admin_settings = get_option( 'bupr_admin_settings' );
				if ( ! empty( $bupr_admin_settings ) ) {
					$profile_rating_fields = $bupr_admin_settings['profile_rating_fields'];
				}
				

				$bupr_rating_criteria = array();
				if ( ! empty( $profile_rating_fields ) ) {
					foreach ( $profile_rating_fields as $bupr_keys => $bupr_fields ) {
						if ( $bupr_fields == 'yes' ) {
							$bupr_rating_criteria[] = $bupr_keys;
						}
					}
				}

				$bupr_auto_approve_reviews   = 'no';
				$bupr_multi_reviews          = 'no';
				$bupr_reviews_status         = 'draft';
				$bupr_admin_general_settings = get_option( BUPR_GENERAL_OPTIONS, true );
				if ( ! empty( $bupr_admin_general_settings ) && ! empty( $bupr_admin_general_settings['bupr_auto_approve_reviews'] ) ) {
					$bupr_auto_approve_reviews = $bupr_admin_general_settings['bupr_auto_approve_reviews'];
					if ( $bupr_auto_approve_reviews == 'yes' ) {
						$bupr_reviews_status = 'publish';
					}
				}

				if ( ! empty( $bupr_admin_general_settings ) && array_key_exists( 'bupr_multi_reviews', $bupr_admin_general_settings ) ) {
					$bupr_multi_reviews = $bupr_admin_general_settings['bupr_multi_reviews'];
				}

				$bupr_current_user          = filter_input( INPUT_POST, 'bupr_current_user', FILTER_SANITIZE_STRING );
				$review_subject             = filter_input( INPUT_POST, 'bupr_review_title', FILTER_SANITIZE_STRING );
				$review_desc                = filter_input( INPUT_POST, 'bupr_review_desc', FILTER_SANITIZE_STRING );
				$bupr_member_id             = filter_input( INPUT_POST, 'bupr_member_id', FILTER_SANITIZE_STRING );
				$review_count               = filter_input( INPUT_POST, 'bupr_field_counter', FILTER_SANITIZE_STRING );
				$anonymous_review           = filter_input( INPUT_POST, 'bupr_anonymous_review', FILTER_SANITIZE_STRING );
				$profile_rated_field_values = array_map( 'sanitize_text_field', wp_unslash( $_POST['bupr_review_rating'] ) );

				$bupr_count = 0;

				$bupr_member_star = array();
				$member_args      = array(
					'post_type'      => 'review',
					'posts_per_page' => -1,
					'post_status'    => array(
						'draft',
						'publish',
					),
					'author'         => $bupr_current_user,
					'category'       => 'bp-member',
					'meta_query'     => array(
						array(
							'key'     => 'linked_bp_member',
							'value'   => $bupr_member_id,
							'compare' => '=',
						),
					),
				);
				$reviews_args     = new WP_Query( $member_args );

				if ( $bupr_multi_reviews == 'no' ) {
					$user_post_count = $reviews_args->post_count;
				} else {
					$user_post_count = 0;
				}

				if ( $user_post_count == 0 ) {
					if ( ! empty( $profile_rated_field_values ) ) {
						foreach ( $profile_rated_field_values as $bupr_stars_rate ) {
							if ( $bupr_count == $review_count ) {
								break;
							} else {
								$bupr_member_star[] = $bupr_stars_rate;
							}
							$bupr_count++;
						}
					}

					if ( ! empty( $bupr_member_id ) && $bupr_member_id != 0 ) {
						$bupr_rated_stars = array();
						if ( ! empty( $bupr_rating_criteria ) ) :
							$bupr_rated_stars = array_combine( $bupr_rating_criteria, $bupr_member_star );
						endif;

						$add_review_args = array(
							'post_type'    => 'review',
							'post_title'   => $review_subject,
							'post_content' => $review_desc,
							'post_status'  => $bupr_reviews_status
						);

						$review_id = wp_insert_post( $add_review_args );
						if ( $review_id ) {
							$bupr_email_notification = get_option( 'bupr_admin_general_options' );
							if ( ! empty( $bupr_email_notification ) ) {
								$bupr_allow_email = $bupr_email_notification['bupr_allow_email'];
							}
							if ( ! empty( $bupr_email_notification ) ) {
								$bupr_allow_notifi = $bupr_email_notification['bupr_allow_notification'];
							}

							if ( ! empty( $bupr_current_user ) && ! empty( $bupr_member_id ) ) {
								$bupr_sender_data    = get_userdata( $bupr_current_user );
								$bupr_sender_email   = $bupr_sender_data->data->user_email;
								$bupr_reciever_data  = get_userdata( $bupr_member_id );
								$bupr_reciever_email = $bupr_reciever_data->data->user_email;
								$bupr_reciever_name  = $bupr_reciever_data->data->user_nicename;
								$bupr_reciever_login = $bupr_reciever_data->data->user_login;
								$bupr_review_url     = home_url() . '/members/' . $bupr_reciever_login . '/reviews/view/' . $review_id;
							}

							/* send notification to member if  notification is enable */
							if ( $bupr_allow_notifi == 'yes' ) {
								do_action( 'bupr_sent_review_notification', $bupr_member_id, $review_id );
							}

							/* send email to member if email notification is enable */
							if ( $bupr_allow_email == 'yes' ) {
								$bupr_to       = $bupr_reciever_email;
								$bupr_subject  = $review_subject;
								$bupr_message .= sprintf( __( '%s Welcome ! %s %s %s You have a new review on your member profile %s.', 'bp-member-reviews' ), '<p>','<b>', $bupr_reciever_name, '</b>', '</p>' );
								$bupr_message .= __( 'To read your review click on the link given below.', 'bp-member-reviews' );
								$bupr_message .= '<a href="' . $bupr_review_url . '">' . $review_subject . '</a>';
								$bupr_header   = "From:$bupr_sender_email \r\n";
								$bupr_header  .= "MIME-Version: 1.0\r\n";
								$bupr_header  .= "Content-type: text/html\r\n";
								wp_mail( $bupr_to, $bupr_subject, $bupr_message, $bupr_header );

							}

							if ( $bupr_auto_approve_reviews == 'no' ) {
								esc_html_e( ' Thank you for taking the time to write this wonderful review. Your review will display on member profile after moderator approval.', 'bp-member-reviews' );
							} else {
								esc_html_e( ' Thank you for taking the time to write this wonderful review!', 'bp-member-reviews' );
							}
						} else {
							echo '<p class="bupr-error">';
							esc_html_e( 'Please try again!', 'bp-member-reviews' );
							echo '</p>';
						}

						wp_set_object_terms( $review_id, 'BP Member', 'review_category' );
						update_post_meta( $review_id, 'linked_bp_member', $bupr_member_id );
						
						if( $anonymous_review == 'yes' ){
							update_post_meta( $review_id, 'bupr_anonymous_review_post', $anonymous_review );
						}
						if ( ! empty( $bupr_rated_stars ) ) :
							update_post_meta( $review_id, 'profile_star_rating', $bupr_rated_stars );
						endif;
					} else {
						echo '<p class="bupr-error">';
						esc_html_e( 'Please select a member.', 'bp-member-reviews' );
						echo '</p>';
					}
				} else {
					esc_html_e( 'You already posted a review for this member.', 'bp-member-reviews' );
				}
				die;
			}
		}
	}
	new BUPR_AJAX();
}
