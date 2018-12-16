<?php
/**
 * Single Reviews tab template.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
global $allowedtags,$allowedposttags;
/* get display tab setting from db */
$bupr_star_color       = '#eeee22';
$bupr_star_type        = 'Stars Rating';
$bupr_review_title     = 'Reviews';
$bupr_display_settings = get_option( BUPR_DISPLAY_OPTIONS, true );
if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_review_title'] ) ) {
	$bupr_review_title = $bupr_display_settings['bupr_review_title'];
}
if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_color'] ) ) {
	$bupr_star_color = $bupr_display_settings['bupr_star_color'];
}
if ( ! empty( $bupr_display_settings ) && ! empty( $bupr_display_settings['bupr_star_type'] ) ) {
	$bupr_star_type = $bupr_display_settings['bupr_star_type'];
}
$url = filter_input( INPUT_SERVER, 'REQUEST_URI' );
preg_match_all( '!\d+!', $url, $matches );
$review_id = (int) basename( $url );
if ( ! empty( $review_id ) ) {
	$review = get_post( $review_id );

	$review_title = $review->post_title;
	$review_url   = get_permalink( $review_id );

	$author            = $review->post_author;
	$author_details    = get_userdata( $author );
	$review_author     = $author_details->data->user_login;
	$author_id         = $author_details->data->ID;
	$member_profile    = bp_core_get_userlink( $author_id );
	$review_author_url = home_url() . '/author/' . $review_author;
	$review_date_time  = $review->post_date;
	$review_date       = explode( ' ', $review_date_time )[0];

	$anonymous_post_review = get_post_meta( $review_id, 'bupr_anonymous_review_post', true );

	/* Hide user avatar and username if it was a anonymous review. */
	if( $anonymous_post_review == 'yes' ){
			$avatar =  bp_core_avatar_default($type='local',array(
				'height'  => 96,
				'width'   => 96,
				'html'    => false
			));
			$member_profile	= __( 'anonymous', 'bp-member-reviews' );						
	}else{
		// Author Thumbnail.
		$avatar = bp_core_fetch_avatar(
			array(
				'item_id' => $author,
				'object'  => 'user',
				'html'    => false,
			)
		);
	}
	 ?>
		<!-- wbcom Display members review on review tab -->
		<div class="bgr-single-review">
			<article id="post-<?php echo esc_attr( $review_id ); ?>" class="post-<?php echo esc_attr( $review_id ); ?> post type-review status-publish format-standard hentry bupr-single-reivew">
				<div class="bupr-col-3 bupr-members-profiles">
					<div class="author">
						<img src="<?php echo esc_url( $avatar ); ?>" class="avatar user-<?php echo esc_attr( $author ); ?>-avatar avatar-128 photo" alt="Profile photo of <?php echo esc_attr( $review_author ); ?>" width="50" height="50">
						<div class="reviewer">
							<h4>
								<?php echo wp_kses( $member_profile, $allowedtags ); ?>
							</h4>
						</div>
					</div>
				</div>
				<div class="bupr-col-8">
				<span class="posted-on">
					<b>
						<?php esc_html_e( 'Posted on', 'bp-member-reviews' ); ?>
						<time class="entry-date published updated">
							<?php echo esc_attr( date_format( date_create( $review_date ), 'F d, Y' ) ); ?>
						</time>
					</b>
				</span>
				<div class="bupr-col-12"><?php echo wp_kses( $review->post_content, $allowedposttags ); ?></div>
				<?php
				$bupr_admin_settings = get_option( 'bupr_admin_settings' );

				$member_review_rating_fields = $bupr_admin_settings['profile_rating_fields'];
				$bupr_rating_criteria        = array();
				if ( ! empty( $member_review_rating_fields ) ) {
					foreach ( $member_review_rating_fields as $bupr_keys => $bupr_fields ) {
						$bupr_rating_criteria[] = $bupr_keys;
					}
				}

				$member_review_ratings = get_post_meta( $review->ID, 'profile_star_rating', false );
				if ( ! empty( $member_review_rating_fields ) && ! empty( $member_review_ratings[0] ) ) :
					foreach ( $member_review_ratings[0] as $field => $bupr_value ) {

						if ( in_array( $field, $bupr_rating_criteria, true ) ) {

							echo '<div class="bupr-row multi-review inline-content"><div class="bupr-col-6 ">' . esc_attr( $field ) . '</div>';
							/*** Star rating Ratings */
							$stars_on  = $bupr_value;
							$stars_off = 5 - $stars_on;
							echo '<div class="bupr-col-6 ">';
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
							echo '</div></div>';
						}
					}
				endif;
				?>
			</div>
		</article>
	</div>

	<?php } ?>
