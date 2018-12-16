<?php
/*
Plugin Name: You Have a New (BuddyPress) Message
Description: Widget and Shortcode to notify users about new messages
Plugin URI: http://wordpress.org/extend/plugins/you-have-a-new-message/
Author: Markus Echterhoff
Author URI: https://www.markusechterhoff.com
Version: 2.0
License: GPLv3 or later
Text Domain: you-have-a-new-message
Domain Path: /languages
*/

add_action( 'plugins_loaded', 'yhanm_load_plugin_textdomain' );
function yhanm_load_plugin_textdomain() {
    load_plugin_textdomain( 'you-have-a-new-message', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action('widgets_init', create_function('', 'register_widget("YouHaveANewBuddyPressMessageWidget");'));
class YouHaveANewBuddyPressMessageWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'you-have-a-new-message-widget',
			'You Have a New (BuddyPress) Message',
			array('description' => 'Notifies you about new BuddyPress messages', 'text_domain')
		);
	}

	public function widget( $args, $instance ) {
		$single = ( ! empty( $instance['single'] ) ) ? $instance['single'] : '';
		$multiple = ( ! empty( $instance['multiple'] ) ) ? $instance['multiple'] : '';
		echo yhanm_get_notice( $single, $multiple );
	}

	public function form( $instance ) {
		$single = ! empty( $instance['single'] ) ? $instance['single'] : esc_html__( 'You have a new message', 'you-have-a-new-message' );
	
		?>
			<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'single' ) ); ?>"><?php esc_attr_e( 'Single Message:', 'you-have-a-new-message' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'single' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'single' ) ); ?>" type="text" value="<?php echo esc_attr( $single ); ?>">
			</p>
		<?php
	
		$multiple = ! empty( $instance['multiple'] ) ? $instance['multiple'] : esc_html__( 'You have %s new messages', 'you-have-a-new-message' );
	
		?>
			<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'multiple' ) ); ?>"><?php esc_attr_e( 'Multiple Messages: (%s = message count)', 'you-have-a-new-message' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'multiple' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'multiple' ) ); ?>" type="text" value="<?php echo esc_attr( $multiple ); ?>">
			</p> 
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['single'] = ( ! empty( $new_instance['single'] ) ) ? strip_tags( $new_instance['single'] ) : '';
		$instance['multiple'] = ( ! empty( $new_instance['multiple'] ) ) ? strip_tags( $new_instance['multiple'] ) : '';
		return $instance;
	}
}

add_shortcode( 'you-have-a-new-message', 'sc_yhanm_get_notice' );

function sc_yhanm_get_notice( $atts, $content = null ) {
	extract(shortcode_atts(array(
			"single" => '',
			"multiple" => '',
		), $atts, 'you-have-a-new-message'));
	return yhanm_get_notice( $single, $multiple );
}

function yhanm_get_notice( $single = '', $multiple = '' ) {
	
	if ( !is_user_logged_in() ) {
		return;
	}
	
	if ( bp_is_messages_component() && bp_is_current_action( 'inbox' ) ) {
		return;
	}
	
	$count = bp_get_total_unread_messages_count();
	if ( !$count ) {
		return;
	}

	$out = '<a class="yhanm" href="'. bp_loggedin_user_domain() . bp_get_messages_slug() .'">';
	if ( $count == 1 ) {
		$out .= $single ? $single : __( 'You have a new message', 'you-have-a-new-message' );
	} else {
		$out .= sprintf( $multiple ? $multiple : __( 'You have %s new messages', 'you-have-a-new-message' ), $count );
	}
	$out .= '</a>';
	
	return $out;			
}

?>
