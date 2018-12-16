<?php
/**
 * Class to serve filter Calls.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BUPR_Custom_Hooks' ) ) {

	/**
	 * Class to add custom hooks for this plugin
	 *
	 * @since    1.0.0
	 * @author   Wbcom Designs
	 */
	class BUPR_Custom_Hooks {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			add_action( 'bp_setup_nav', array( $this, 'bupr_member_profile_reviews_tab' ) );
			add_action( 'bp_before_member_header_meta', array( $this, 'bupr_member_average_rating' ) );

			add_action( 'bp_setup_admin_bar', array( $this, 'bupr_setup_admin_bar' ), 80 );

			add_action( 'init', array( $this, 'bupr_add_bp_member_reviews_taxonomy_term' ) );
			add_filter( 'post_row_actions', array( $this, 'bupr_bp_member_reviews_row_actions' ), 10, 2 );
			add_filter( 'bulk_actions-edit-review', array( $this, 'bupr_remove_edit_bulk_actions' ), 10, 1 );

			add_action( 'bp_member_header_actions', array( $this, 'bupr_add_review_button_on_member_header' ) );

			/* Add review link at member's directory if option admin setting is enabled. */
			$bupr_admin_settings      = get_option( BUPR_GENERAL_OPTIONS, true );
			if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_member_dir_add_reviews'] ) ) {
				$bupr_member_dir_add_reviews = $bupr_admin_settings['bupr_member_dir_add_reviews'];
				if ( $bupr_member_dir_add_reviews == 'yes' ) {
					add_action( 'bp_directory_members_actions', array( $this, 'bupr_view_review_button_on_member_directory' ) );
				}
			}
			add_action( 'bp_directory_members_item', array( $this, 'bupr_rating_directory' ), 50 );
			add_action( 'init', array( $this, 'bupr_set_default_rating_criteria' ) );
		}


		/**
		 * To add default criteria review settings.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_set_default_rating_criteria() {
			$bupr_admin_settings = get_option( 'bupr_admin_settings', true );
			if ( empty( $bupr_admin_settings ) || ! is_array( $bupr_admin_settings ) ) {
				$default_admin_criteria = array(
					'profile_multi_rating_allowed' => '1',
					'profile_rating_fields'        => array(
						'Member Response' => 'yes',
						'Member Skills'   => 'yes',
					),
				);
				update_option( 'bupr_admin_settings', $default_admin_criteria );
			}
		}

		/**
		 * BuddyPress Rating diresctory.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_rating_directory() {

			/* List reviews at member directory if setting is enabled. */
			$bupr_admin_settings      = get_option( BUPR_GENERAL_OPTIONS, true );
			if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['bupr_member_dir_reviews'] ) ) {
				$bupr_member_dir_reviews = $bupr_admin_settings['bupr_member_dir_reviews'];
				if ( $bupr_member_dir_reviews != 'yes' ) {
					return;
				}
			}

			/* get display tab setting from db */
			global $members_template;
			$bupr_star_color       = '#FFC400';
			$bupr_review_title     = __( 'Reviews', 'bp-member-reviews' );
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_review_title'] ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
			}
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_color'] ) ) {
				$bupr_star_color = $bupr_display_settings['bupr_star_color'];
			}

			$bupr_type       = 'integer';
			$bupr_avg_rating = 0;
			/* Gather all the members reviews */
			$bupr_args = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => $members_template->member->id,
						'compare' => '=',
					),
				),
			);

			$reviews             = get_posts( $bupr_args );
			$bupr_admin_settings = get_option( 'bupr_admin_settings' );
			if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['profile_rating_fields'] ) ) {
				$bupr_review_rating_fields = $bupr_admin_settings['profile_rating_fields'];
			}

			$bupr_total_rating       = $rate_counter  = 0;
			$bupr_reviews_count      = count( $reviews );
			$bupr_total_review_count = '';
			if ( $bupr_reviews_count != 0 ) {
				foreach ( $reviews as $review ) {
					$rate                = 0;
					$reviews_field_count = 0;
					$review_ratings      = get_post_meta( $review->ID, 'profile_star_rating', false );
					if ( ! empty( $review_ratings[0] ) ) {
						// $reviews_field_count  = count( $bupr_review_rating_fields );
						if ( ! empty( $bupr_review_rating_fields ) && ! empty( $review_ratings[0] ) ) :
							foreach ( $review_ratings[0] as $field => $value ) {
								if ( array_key_exists( $field, $bupr_review_rating_fields ) ) {
									$rate += $value;
									$reviews_field_count++;
								}
							}

							if ( $reviews_field_count != 0 ) {
								$bupr_total_rating += (int) $rate / $reviews_field_count;
								$bupr_total_review_count ++;
								$rate_counter++;
							}
						endif;
					}
				}

				if ( $bupr_total_review_count != 0 ) {
					$bupr_avg_rating = $bupr_total_rating / $bupr_total_review_count;
					$bupr_type       = gettype( $bupr_avg_rating );
				}

				$bupr_stars_on   = $stars_off = $stars_half = '';
				$bupr_half_squar = '';
				$remaining       = $bupr_avg_rating - (int) $bupr_avg_rating;
				if ( $remaining > 0 ) {
					$stars_on        = intval( $bupr_avg_rating );
					$stars_half      = 1;
					$bupr_half_squar = 1;
					$stars_off       = 5 - ( $stars_on + $stars_half );
				} else {
					$stars_on   = $bupr_avg_rating;
					$stars_off  = 5 - $bupr_avg_rating;
					$stars_half = 0;
				}
				$bupr_avg_rating = round( $bupr_avg_rating, 2 );
				if ( $bupr_avg_rating > 0 ) { ?>
				<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					<span itemprop="ratingValue"  content="<?php echo esc_attr( $bupr_avg_rating ); ?>"></span>
					<span itemprop="bestRating"   content="5"></span>
					<span itemprop="ratingCount"  content="<?php echo esc_attr( $rate_counter ); ?>"></span>
					<span itemprop="reviewCount"  content="<?php echo esc_attr( $bupr_reviews_count ); ?>"></span>
					<span itemprop="itemReviewed" content="Person"></span>
					<span itemprop="name" content="<?php echo esc_attr( bp_core_get_username( $members_template->member->id ) ); ?>"></span>
					<span itemprop="url" content="<?php echo esc_attr( bp_core_get_userlink( $members_template->member->id, false, true ) ); ?>"></span>
					<?php
					echo "<div class='member-review-stars'>";
					for ( $i = 1; $i <= $stars_on; $i++ ) {
						?>
						<span class="fa fa-star bupr-star-rate"></span>
						<?php
					}

					for ( $i = 1; $i <= $stars_half; $i++ ) {
						?>
						<span class="fa fa-star-half-o bupr-star-rate"></span>
						<?php
					}

					for ( $i = 1; $i <= $stars_off; $i++ ) {
						?>
						<span class="fa fa-star-o bupr-star-rate"></span>
						<?php
					}
					echo '</div>';
					?>
				</div>
				<?php } ?>
				<?php
			}
		}

		/**
		 * Actions performed to add a review button on member header.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_add_review_button_on_member_header() {
			global $bp;
			$bupr_admin_settings   = get_option( BUPR_GENERAL_OPTIONS, true );
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && isset( $bupr_display_settings['bupr_review_title'] ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
			} else {
				$bupr_review_title = __( 'Reviews', 'bp-member-reviews' );
			}
			$review_div      = 'form';
			$bupr_exc_member = $bupr_admin_settings['bupr_exc_member'];

			if( bp_is_user() ){
				$member_id = bp_displayed_user_id();
			}else{
				$member_id = bp_get_member_user_id();
			}

			if ( ! empty( $bupr_exc_member ) ) {
				if ( ( $member_id != bp_loggedin_user_id() ) && ! in_array( $member_id, $bupr_exc_member ) ) {
					$review_url = bp_core_get_userlink( $member_id, false, true ) . 'reviews/add-review';
					?>
					<div id="bupr-add-review-btn" class="generic-button">
						<a href="<?php echo esc_url( $review_url ); ?>" class="add-review" show ="<?php echo esc_attr( $review_div ); ?>">
							<?php
							esc_html_e( 'Add ', 'bp-member-reviews' );
							echo esc_attr( $bupr_review_title );
							?>
						</a>
					</div>
					<?php
				}
			} else {
				if ( $member_id != bp_loggedin_user_id() ) {
					$review_url = bp_core_get_userlink( $member_id, false, true ) . 'reviews/add-review';
					?>
					<div id="bupr-add-review-btn" class="generic-button">
						<a href="<?php echo esc_url( $review_url ); ?>" class="add-review" show ="<?php echo esc_attr( $review_div ); ?>">
							<?php
							esc_html_e( 'Add ', 'bp-member-reviews' );
							echo esc_attr( $bupr_review_title );
							?>
						</a>
					</div>
					<?php
				}
			}
		}

		/**
		 * Actions performed to add a review button on member header.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_view_review_button_on_member_directory() {
			global $bp;
			$bupr_admin_settings   = get_option( BUPR_GENERAL_OPTIONS, true );
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && isset( $bupr_display_settings['bupr_review_title'] ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
			} else {
				$bupr_review_title = __( 'Reviews', 'bp-member-reviews' );
			}
			$review_div      = 'form';
			$bupr_exc_member = $bupr_admin_settings['bupr_exc_member'];

			if( bp_is_user() ){
				$member_id = bp_displayed_user_id();
			}else{
				$member_id = bp_get_member_user_id();
			}

			if ( ! empty( $bupr_exc_member ) ) {
				if ( ( $member_id != bp_loggedin_user_id() ) && ! in_array( $member_id, $bupr_exc_member ) ) {
					$review_url = bp_core_get_userlink( $member_id, false, true ) . 'reviews';
					?>
					<div id="bupr-view-review-btn" class="generic-button">
						<a href="<?php echo esc_url( $review_url ); ?>" class="add-review" show ="<?php echo esc_attr( $review_div ); ?>">
							<?php
							esc_html_e( 'View ', 'bp-member-reviews' );
							echo esc_attr( $bupr_review_title );
							?>
						</a>
					</div>
					<?php
				}
			} else {
				if ( $member_id != bp_loggedin_user_id() ) {
					$review_url = bp_core_get_userlink( $member_id, false, true ) . 'reviews';
					?>
					<div id="bupr-view-review-btn" class="generic-button">
						<a href="<?php echo esc_url( $review_url ); ?>" class="add-review" show ="<?php echo esc_attr( $review_div ); ?>">
							<?php
							esc_html_e( 'View ', 'bp-member-reviews' );
							echo esc_attr( $bupr_review_title );
							?>
						</a>
					</div>
					<?php
				}
			}
		}

		/**
		 * Setup Reviews link in admin bar.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $wp_admin_nav Member Review add menu array.
		 * @author   Wbcom Designs
		 */
		public function bupr_setup_admin_bar( $wp_admin_nav = array() ) {
			global $wp_admin_bar;
			$bupr_review_title     = __( 'Reviews', 'bp-member-reviews' );
			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_review_title'] ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
			}

			$bupr_args = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => get_current_user_id(),
						'compare' => '=',
					),
				),
			);

			$reviews       = get_posts( $bupr_args );
			$reviews_count = count( $reviews );

			$profile_menu_slug = 'reviews';

			$base_url = bp_loggedin_user_domain() . $profile_menu_slug;
			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'my-account-buddypress',
						'id'     => 'my-account-' . $profile_menu_slug,
						'title'  => $bupr_review_title . ' <span class="count">' . $reviews_count . '</span>',
						'href'   => trailingslashit( $base_url ),
					)
				);
			}
		}

		/**
		 * Actions performed to show average rating on a bp member's profile
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_member_average_rating() {
			?>
			<br>
			<?php
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

			$bupr_type       = 'integer';
			$bupr_avg_rating = 0;
			/* Gather all the members reviews */
			$bupr_args = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => bp_displayed_user_id(),
						'compare' => '=',
					),
				),
			);

			$reviews             = get_posts( $bupr_args );
			$bupr_admin_settings = get_option( 'bupr_admin_settings' );
			if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['profile_rating_fields'] ) ) {
				$bupr_review_rating_fields = $bupr_admin_settings['profile_rating_fields'];
			}

			$bupr_total_rating       = $rate_counter  = 0;
			$bupr_reviews_count      = count( $reviews );
			$bupr_total_review_count = '';
			if ( $bupr_reviews_count != 0 ) {
				foreach ( $reviews as $review ) {
					$rate                = 0;
					$reviews_field_count = 0;
					$review_ratings      = get_post_meta( $review->ID, 'profile_star_rating', false );
					if ( ! empty( $review_ratings[0] ) ) {
						// $reviews_field_count  = count( $bupr_review_rating_fields );
						if ( ! empty( $bupr_review_rating_fields ) && ! empty( $review_ratings[0] ) ) :
							foreach ( $review_ratings[0] as $field => $value ) {
								if ( array_key_exists( $field, $bupr_review_rating_fields ) ) {
									$rate += $value;
									$reviews_field_count++;
								}
							}
							if ( $reviews_field_count != 0 ) {
								$bupr_total_rating += (int) $rate / $reviews_field_count;
								$bupr_total_review_count ++;
								$rate_counter++;
							}
						endif;
					}
				}

				if ( $bupr_total_review_count != 0 ) {
					$bupr_avg_rating = $bupr_total_rating / $bupr_total_review_count;
					$bupr_type       = gettype( $bupr_avg_rating );
				}

				$bupr_stars_on   = $stars_off = $stars_half = '';
				$bupr_half_squar = '';
				$remaining       = $bupr_avg_rating - (int) $bupr_avg_rating;
				if ( $remaining > 0 ) {
					$stars_on        = intval( $bupr_avg_rating );
					$stars_half      = 1;
					$bupr_half_squar = 1;
					$stars_off       = 5 - ( $stars_on + $stars_half );
				} else {
					$stars_on   = $bupr_avg_rating;
					$stars_off  = 5 - $bupr_avg_rating;
					$stars_half = 0;
				}
				$bupr_avg_rating = round( $bupr_avg_rating, 2 );
				if ( $bupr_avg_rating > 0 ) {
					?>
					<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						<span itemprop="ratingValue"  content="<?php echo esc_attr( $bupr_avg_rating ); ?>"></span>
						<span itemprop="bestRating"   content="5"></span>
						<span itemprop="ratingCount"  content="<?php echo esc_attr( $rate_counter ); ?>"></span>
						<span itemprop="reviewCount"  content="<?php echo esc_attr( $bupr_reviews_count ); ?>"></span>
						<span itemprop="itemReviewed" content="Person"></span>
						<span itemprop="name" content="<?php echo esc_attr( bp_core_get_username( bp_displayed_user_id() ) ); ?>"></span>
						<span itemprop="url" content="<?php echo esc_url( bp_core_get_userlink( bp_displayed_user_id(), false, true ) ); ?>"></span>
						<?php
						echo "<div class='member-review-stars'>";
						for ( $i = 1; $i <= $stars_on; $i++ ) {
							?>
							<span class="fa fa-star bupr-star-rate"></span>
							<?php
						}

						for ( $i = 1; $i <= $stars_half; $i++ ) {
							?>
							<span class="fa fa-star-half-o bupr-star-rate"></span>
							<?php
						}

						for ( $i = 1; $i <= $stars_off; $i++ ) {
							?>
							<span class="fa fa-star-o bupr-star-rate"></span>
							<?php
						}
						echo '</div>';
						echo "<div class='member-review-stats'>";
						?>
						<span>

							<?php
							esc_html_e( 'Rating ', 'bp-member-reviews' );
							echo ' : ' . esc_attr( $bupr_avg_rating ) . '/5 - ';
							echo esc_attr( $bupr_reviews_count ) . ' ' . esc_attr( $bupr_review_title );

							?>
						</span>
					</div>
				</div>
				<?php } ?>

				<?php
			}
		}

		/**
		 * Actions performed to remove edit from bulk options
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $actions Actions array.
		 * @author   Wbcom Designs
		 */
		public function bupr_remove_edit_bulk_actions( $actions ) {
			unset( $actions['edit'] );
			return $actions;
		}

		/**
		 * Actions performed to hide row actions
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $actions Actions array.
		 * @param    array $post    Posts array.
		 * @author   Wbcom Designs
		 */
		public function bupr_bp_member_reviews_row_actions( $actions, $post ) {
			global $bp;
			if ( $post->post_type == 'review' ) {
				unset( $actions['edit'] );
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );

				if ( wp_get_object_terms( $post->ID, 'review_category' )[0]->name == 'BP Member' ) {
					// Add a link to view the review.
					$review_title     = $post->post_title;
					$linked_bp_member = get_post_meta( $post->ID, 'linked_bp_member', true );

					$review_url             = bp_core_get_userlink( $linked_bp_member, false, true ) . 'reviews/view/' . $post->ID;
					$actions['view_review'] = '<a href="' . $review_url . '" title="' . $review_title . '">View Review</a>';

					// Add Approve Link for draft reviews.
					if ( $post->post_status == 'draft' ) {
						$actions['approve_review'] = '<a href="javascript:void(0);" title="' . $review_title . '" class="bupr-approve-review" data-rid="' . $post->ID . '">Approve</a>';
					}
				}
			}
			return $actions;
		}

		/**
		 * Action performed to add taxonomy term for group reviews
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_add_bp_member_reviews_taxonomy_term() {
			$termexists = term_exists( 'BP Member', 'review_category' );
			if ( $termexists === 0 || $termexists === null ) {
				wp_insert_term( 'BP Member', 'review_category' );
			}
		}

		/**
		 * Action performed to add a tab for member profile reviews
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_member_profile_reviews_tab() {

			/* get display tab setting from db */
			$bp_pages    = bp_core_get_directory_pages();
			$member_slug = $bp_pages->members->slug;

			$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
			if ( ! empty( $bupr_display_settings ) ) {
				$bupr_review_title = $bupr_display_settings['bupr_review_title'];
				$bupr_star_color   = $bupr_display_settings['bupr_star_color'];
			}
			if ( empty( $bupr_review_title ) ) {
				$bupr_review_title = __( 'Reviews', 'bp-member-reviews' );
			}

			$bupr_admin_settings = get_option( BUPR_GENERAL_OPTIONS, true );
			if ( ! empty( $bupr_admin_settings ) ) {
				$bupr_exc_member = $bupr_admin_settings['bupr_exc_member'];
			}
			if ( ! empty( $bupr_exc_member ) && array_key_exists( bp_displayed_user_id(), $bupr_exc_member ) ) {

			} else {
				global $bp;
				/* count member's review */
				$bupr_args = array(
					'post_type'      => 'review',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'category'       => 'bp-member',
					'meta_query'     => array(
						array(
							'key'     => 'linked_bp_member',
							'value'   => bp_displayed_user_id(),
							'compare' => '=',
						),
					),
				);

				$bupr_reviews = new WP_Query( $bupr_args );
				if ( ! empty( $bupr_reviews->posts ) ) {
					$bupr_reviews = count( $bupr_reviews->posts );
					if ( ! empty( $bupr_reviews ) ) {
						$bupr_notification = '<span class="no-count">' . $bupr_reviews . '</span>';
					} else {
						$bupr_notification = '<span class="no-count">' . 0 . '</span>';
					}
				} else {
					$bupr_notification = '<span class="no-count">' . 0 . '</span>';
				}

				$name     = bp_get_displayed_user_username();
				$tab_args = array(
					'name'                    => $bupr_review_title . ' ' . $bupr_notification,
					'slug'                    => 'reviews',
					'screen_function'         => array( $this, 'bupr_reviews_tab_function_to_show_screen' ),
					'position'                => 75,
					'default_subnav_slug'     => 'view',
					'show_for_displayed_user' => true,
				);
				bp_core_new_nav_item( $tab_args );

				$parent_slug = 'reviews';

				// Add subnav to view a review.
				bp_core_new_subnav_item(
					array(
						'name'            => __( 'View', 'bp-member-reviews' ),
						'slug'            => 'view',
						'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
						'parent_slug'     => $parent_slug,
						'screen_function' => array( $this, 'bupr_view_review_tab_function_to_show_screen' ),
						'position'        => 100,
						'link'            => site_url() . "/$member_slug/$name/$parent_slug/",
					)
				);

				// Add subnav to add a review.
				if ( bp_displayed_user_id() !== get_current_user_id() ) {
					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Add ', 'bp-member-reviews' ) . $bupr_review_title,
							'slug'            => 'add-review',
							'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bupr_reviews_form_tab_function_to_show_screen' ),
							'position'        => 200,
							'link'            => site_url() . "/$member_slug/$name/$parent_slug/add-review/",
						)
					);
				}
			}
		}

		/**
		 * Action performed to show screen of reviews listing tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_reviews_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Action performed to show screen of reviews form tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_form_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_reviews_form_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Actions performed to show the content of reviews list tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_tab_function_to_show_content() {
			include 'templates/bupr-reviews-tab-template.php';
		}

		/**
		 * Action performed to show the content of add review tab
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_form_to_show_content() {
			?>
			<div class="bupr-bp-member-review-no-popup-add-block">
				<?php
				if ( is_user_logged_in() ) {
					do_shortcode( '[add_profile_review_form]' );
				} else {
					?>
					<div id="message" class="info">
						<p>
							<?php esc_html_e( 'You must login !.', 'bp-member-reviews' ); ?>
						</p>
					</div>
					<?php } ?>
				</div>
				<?php
		}

		/**
		 * Action performed to show screen of single review view tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_view_review_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_view_review_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Action performed to show the content of reviews list tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_view_review_tab_function_to_show_content() {
			include 'templates/bupr-single-review-template.php';
		}

	}
	new BUPR_Custom_Hooks();
}
