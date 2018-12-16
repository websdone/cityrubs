<?php if ( ! class_exists( 'GhostPool_Voting' ) ) {

	class GhostPool_Voting {

		public function __construct() {
		
			/**
			 * Enqueues scripts and styles.
			 *
			 */	
			if ( ! function_exists( 'ghostpool_voting_scripts' ) ) {	
				function ghostpool_voting_scripts() {		
					wp_enqueue_script( 'ghostpool-voting', plugins_url( '/inc/assets/jquery.voting.js', dirname( __FILE__ ) ), array( 'jquery' ) );
					wp_localize_script( 'ghostpool-voting', 'ghostpool_voting', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ), 
						'nonce'   => wp_create_nonce( 'ghostpool_voting_action' ),
					) );	
				}
			}
			add_action( 'wp_enqueue_scripts', 'ghostpool_voting_scripts' );
		
			/**
			 * Add the voting panel to the page
			 *
			 */	
			if  ( ! function_exists( 'ghostpool_voting' ) ) {

				function ghostpool_voting( $post_id = '', $type_of_vote = '', $title = '' ) {

					// Sanatize params
					$post_id = intval( sanitize_text_field( $post_id ) );
					$type_of_vote = intval( sanitize_text_field( $type_of_vote ) );

					$voting_link = '';

					if ( $post_id == '' ) {
						$post_id = get_the_ID();
					}	
								
					$voting_up_count = get_post_meta( $post_id, 'ghostpool_voting_up', true ) != '' ? get_post_meta( $post_id, 'ghostpool_voting_up', true ) : '0';
	
					$voting_down_count = get_post_meta( $post_id, 'ghostpool_voting_down', true ) != '' ? get_post_meta( $post_id, 'ghostpool_voting_down', true ) : '0';

					$link_up = '<span class="gp-voting-up' . ( ( isset( $voting_up_count ) && intval( $voting_up_count ) > 0 ) ? ' gp-voted' : '' ) .'" onclick="ghostpool_vote(' . $post_id . ', 1);" data-text="' . esc_html__( 'Vote Up', 'aardvark-plugin' ) . ' +">' . $voting_up_count . '</span>';
	
					$link_down = '<span class="gp-voting-down' . ( ( isset( $voting_down_count ) && intval( $voting_down_count ) > 0 ) ? ' gp-voted' : '' ) .'" onclick="ghostpool_vote(' . $post_id . ', 2);" data-text="' . esc_html__( 'Vote Down', 'aardvark-plugin' ) . ' -">' . $voting_down_count . '</span>';

					$voting_link = '<div id="gp-voting-wrapper">';
					if ( $title ) {
						$voting_link .= '<div class="gp-divider-title-bg"><div class="gp-divider-title">' . esc_attr( $title ) . '</div></div>';
					}
					$voting_link .= '<div class="gp-voting-container" id="gp-voting-' . $post_id . '" data-content-id="' . $post_id . '"><div class="gp-voting-buttons">';
					$voting_link .= $link_up;
					$voting_link .= ' ';
					$voting_link .= $link_down;
					$voting_link .= '</div><span class="gp-already-voted" data-text="' . esc_html__( 'You already voted!', 'aardvark-plugin' ) . '"></span>';
					$voting_link .= '</div>';
					$voting_link .= '</div>';

					return $voting_link;
		
				}
			}

			/**
			 * Handle ajax request for up and down votes
			 *
			 */
			if  ( ! function_exists( 'ghostpool_add_vote_callback' ) ) {

				function ghostpool_add_vote_callback() {

					// Check the nonce - security
					check_ajax_referer( 'ghostpool_voting_action', 'nonce' );

					global $wpdb;

					// Get the POST values
					$post_id = intval( $_POST['postid'] );
					$type_of_vote = intval( $_POST['type'] );

					// Check the type and retrieve the meta values
					if ( $type_of_vote == 1 ) {

						$meta_name = "ghostpool_voting_up";

					} elseif ( $type_of_vote == 2 ) {

						$meta_name = "ghostpool_voting_down";

					}

					// Retrieve the meta value from the DB
					$voting_count = get_post_meta( $post_id, $meta_name, true ) != '' ? get_post_meta( $post_id, $meta_name, true ) : '0';
					$voting_count = $voting_count + 1;

					// Update the meta value
					update_post_meta( $post_id, $meta_name, $voting_count );

					$results = ghostpool_voting( $post_id, $type_of_vote );

					die( $results );
				}

				add_action( 'wp_ajax_ghostpool_add_vote', 'ghostpool_add_vote_callback' );
				add_action( 'wp_ajax_nopriv_ghostpool_add_vote', 'ghostpool_add_vote_callback' );

			}

			/**
			 * Show up and down votes on site
			 *
			 */
			if ( ! function_exists( 'ghostpool_voting_show_up_votes' ) ) {
				function ghostpool_voting_show_up_votes( $post_id = '' ) {
		
					if ( $post_id == '' ) {
						$post_id = get_the_ID();
					} else {
						$post_id = intval( sanitize_text_field( $post_id ) );
					}
		
					$voting_up = get_post_meta( $post_id, 'ghostpool_voting_up', true );
		
					if ( $voting_up == 0 OR $voting_up > 1 ) {
						$voting_up_text = esc_html__( 'likes', 'aardvark-plugin' );
					} else {
						$voting_up_text = esc_html__( 'like', 'aardvark-plugin' );
					}
		
					if ( $voting_up != '' ) {
						return $voting_up . ' ' . $voting_up_text; 
					} else {
						return '0 ' . $voting_up_text;
					}
		
				}
			}
			if ( ! function_exists( 'ghostpool_voting_show_down_votes' ) ) {
				function ghostpool_voting_show_down_votes ( $post_id = '' ) {
		
					if ( $post_id == '' ) {
						$post_id = get_the_ID();
					} else {
						$post_id = intval( sanitize_text_field( $post_id ) );
					}
		
					$voting_down = get_post_meta( $post_id, 'ghostpool_voting_down', true );
		
					if ( $voting_down == 0 OR $voting_up > 1 ) {
						$voting_down_text = esc_html__( 'likes', 'aardvark-plugin' );
					} else {
						$voting_down_text = esc_html__( 'like', 'aardvark-plugin' );
					}
		
					if ( $voting_down != '' ) {
						return $voting_down . ' ' . $voting_down_text; 
					} else {
						return '0 ' . $voting_down_text;
					}
		
				}
			}

		}
			
	}	
	
} 

$ghostpool_voting = new GhostPool_Voting();

?>