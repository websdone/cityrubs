<?php
/**
 * Reviews tab template.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

global $bp,$post;
global $allowedtags,$allowedposttags;
/* get display tab setting from db */
$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
if ( ! empty( $bupr_display_settings ) ) {
	$bupr_review_title = $bupr_display_settings['bupr_review_title'];
	$bupr_star_color   = $bupr_display_settings['bupr_star_color'];
}
if ( empty( $bupr_review_title ) ) {
	$bupr_review_title = 'Reviews';
}

$bupr_review_succes = false;
$current_user       = wp_get_current_user();
$member_id          = $current_user->ID;

/* admin general tab setting value */
$bupr_general_tab = get_option( BUPR_GENERAL_OPTIONS );

if ( ! empty( $bupr_general_tab ) ) {
	$profile_reviews_per_page = $bupr_general_tab['profile_reviews_per_page'];
}

/* Admin Settings */
$bupr_admin_settings = get_option( 'bupr_admin_settings' );

if ( ! empty( $bupr_admin_settings ) && ! empty( $bupr_admin_settings['profile_rating_fields'] ) ) {
	$profile_rating_fields = $bupr_admin_settings['profile_rating_fields'];
}

if ( empty( $profile_reviews_per_page ) ) {
	$profile_reviews_per_page = 3;
}
// Gather all the bp member reviews.
$args = array(
	'post_type'      => 'review',
	'post_status'    => 'publish',
	'posts_per_page' => $profile_reviews_per_page,
	'paged'          => get_query_var( 'page', 1 ),
	'category'       => 'bp-member',
	'meta_query'     => array(
		array(
			'key'     => 'linked_bp_member',
			'value'   => bp_displayed_user_id(),
			'compare' => '=',
		),
	),
);

$reviews = new WP_Query( $args ); ?>

<div class="bupr-bp-member-reviews-block">

	<!-- MODAL FOR USER LOGIN -->
	<input type="hidden" id="reviews_pluginurl" value="<?php echo esc_attr( BUPR_PLUGIN_URL ); ?>">

	<div class="bp-member-reviews">
		<div id="bp-member-reviews-list" cellspacing="0">
			<div id="request-review-list" class="item-list">
				<?php
				if ( $reviews->have_posts() ) {
					while ( $reviews->have_posts() ) :
						$reviews->the_post();
						
						$anonymous_post_review = get_post_meta( $post->ID, 'bupr_anonymous_review_post', true );
						?>
						<div class="bupr-row">
							<div class="bupr-col-3 bupr-members-profiles">
								<div class="item-avatar">
									<?php
									$author = $reviews->post->post_author;
									if( $anonymous_post_review == 'yes' ){
										$avatar_url =  bp_core_avatar_default($type='local',array(
											'height'  => 96,
											'width'   => 96,
											'html'    => true
										));
										echo "<img src='".$avatar_url."' class='avatar avatar-96 photo' alt='Profile Photo' width='96' height='96'></img>";
									}else{
										
										echo bp_core_fetch_avatar(
											array(
												'item_id' => $author,
												'height'  => 96,
												'width'   => 96,
											)
										);
									}
									?>
								</div>
								<div class="reviewer">
									<h4>
										<?php
										if( $anonymous_post_review == 'yes' ){
											esc_html_e( 'anonymous', 'bp-member-reviews' );
										}else{
											echo wp_kses( bp_core_get_userlink( $author ), $allowedtags );
										}
										
										?>
									</h4>
								</div>
							</div>

							<div class="bupr-col-9 bupr-members-content">
								<?php $url           = 'view/' . get_the_id(); ?>
								<div class="bupr-review-description">
									<div class="bupr-full-description">
										<?php
										$trimcontent = get_the_content();
										if ( ! empty( $trimcontent ) ) {
											$len = strlen( $trimcontent );
											if ( $len > 150 ) {
												$shortexcerpt = substr( $trimcontent, 0, 150 );
												echo '<p>' . esc_attr( $shortexcerpt ) . '</p>';
												?>
												<a href="<?php echo esc_attr( $url ); ?>"><i><?php esc_html_e( 'read more...', 'bp-member-reviews' ); ?></i></a>
												<?php
											} else {
												echo '<p>' . esc_attr( $trimcontent ) . '</p>';
											}
										}
										?>
										<?php
										$bupr_admin_settings = get_option( 'bupr_admin_settings' );

										if ( ! empty( $bupr_admin_settings['profile_rating_fields'] ) ) {
											$member_review_rating_fields = $bupr_admin_settings['profile_rating_fields'];
										}
										$bupr_rating_criteria = array();
										if ( ! empty( $member_review_rating_fields ) ) {
											foreach ( $member_review_rating_fields as $bupr_keys => $bupr_fields ) {
												$bupr_rating_criteria[] = $bupr_keys;
											}
										}
										$member_review_ratings = get_post_meta( $post->ID, 'profile_star_rating', false );
										
										if ( ! empty( $member_review_rating_fields ) && ! empty( $member_review_ratings[0] ) ) :
											foreach ( $member_review_ratings[0] as $field => $bupr_value ) {
												if ( in_array( $field, $bupr_rating_criteria, true ) ) {

													echo '<div class="bupr-row multi-review inline-content"><div class="bupr-col-4 ">' . esc_attr( $field ) . '</div>';

													/*** Star rating Ratings */
													$stars_on  = $bupr_value;
													$stars_off = 5 - $stars_on;
													echo '<div class="bupr-col-4 ">';
													for ( $i = 1; $i <= $stars_on; $i++ ) {
														?>
														<span class="fa fa-star bupr-star-rate"></span>
														<?php
													}

													for ( $i = 1; $i <= $stars_off; $i++ ) {
														?>
														<span class="fa fa-star-o stars bupr-star-rate"></span>
														<?php
													}
													/*star rating end */

													echo '</div>';
													echo '</div>';
												}
											}
										endif;
										?>
									</div>
								</div>
							</div>
						</div>
						<?php
					endwhile;

					$total_pages = $reviews->max_num_pages;
					if ( $total_pages > 1 ) {
						?>
						<div class="bupr-row bupr-pagination">
							<?php
							/*** Posts pagination ***/
							echo "<div class='bupr-posts-pagination'>";
							echo wp_kses(paginate_links(
								array(
									'base'    => add_query_arg( 'page', '%#%' ),
									'format'  => '',
									'current' => max( 1, get_query_var( 'page' ) ),
									'total'   => $reviews->max_num_pages,
								)
							), $allowedposttags);
							echo '</div>';
							?>
						</div>
						<?php
					}
					wp_reset_postdata();
				} else {
					?>
					<div id="message" class="info">
						<p><?php esc_html_e( 'Sorry, no reviews were found.', 'bp-member-reviews' ); ?></p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
