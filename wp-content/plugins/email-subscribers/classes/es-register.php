<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class es_cls_registerhook {
	public static function es_activation() {
		global $wpdb;

		add_option( 'email-subscribers', "2.9" );

		// Creating default tables
		global $wpdb;

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();

		$es_default_tables = "CREATE TABLE {$wpdb->prefix}es_emaillist (
									es_email_id INT unsigned NOT NULL AUTO_INCREMENT,
									es_email_name VARCHAR(255) NOT NULL,
									es_email_mail VARCHAR(255) NOT NULL,
									es_email_status VARCHAR(25) NOT NULL default 'Unconfirmed',
									es_email_created datetime NOT NULL default '0000-00-00 00:00:00',
									es_email_viewcount VARCHAR(100) NOT NULL,
									es_email_group VARCHAR(255) NOT NULL default 'Public',
									es_email_guid VARCHAR(255) NOT NULL,
									PRIMARY KEY  (es_email_id)
									) $charset_collate;
								CREATE TABLE {$wpdb->prefix}es_notification (
									es_note_id INT unsigned NOT NULL AUTO_INCREMENT,
									es_note_cat TEXT NULL,
									es_note_group VARCHAR(255) NOT NULL,
									es_note_templ INT unsigned NOT NULL,
									es_note_status VARCHAR(10) NOT NULL default 'Enable',
									PRIMARY KEY  (es_note_id)
								) $charset_collate;
								CREATE TABLE {$wpdb->prefix}es_sentdetails (
									es_sent_id INT unsigned NOT NULL AUTO_INCREMENT,
									es_sent_guid VARCHAR(255) NOT NULL,
									es_sent_qstring VARCHAR(255) NOT NULL,
									es_sent_source VARCHAR(255) NOT NULL,
									es_sent_starttime datetime NOT NULL default '0000-00-00 00:00:00',
									es_sent_endtime datetime NOT NULL default '0000-00-00 00:00:00',
									es_sent_count INT unsigned NOT NULL,
									es_sent_preview TEXT NULL,
									es_sent_status VARCHAR(25) NOT NULL default 'Sent',
									es_sent_type VARCHAR(25) NOT NULL default 'Immediately',
									es_sent_subject VARCHAR(255) NOT NULL,
									PRIMARY KEY  (es_sent_id)
								) $charset_collate;
								CREATE TABLE {$wpdb->prefix}es_deliverreport (
									es_deliver_id INT unsigned NOT NULL AUTO_INCREMENT,
									es_deliver_sentguid VARCHAR(255) NOT NULL,
									es_deliver_emailid INT unsigned NOT NULL,
									es_deliver_emailmail VARCHAR(255) NOT NULL,
									es_deliver_sentdate datetime NOT NULL default '0000-00-00 00:00:00',
									es_deliver_status VARCHAR(25) NOT NULL,
									es_deliver_viewdate datetime NOT NULL default '0000-00-00 00:00:00',
									es_deliver_sentstatus VARCHAR(25) NOT NULL default 'Sent',
									es_deliver_senttype VARCHAR(25) NOT NULL default 'Immediately',
									PRIMARY KEY  (es_deliver_id)
								) $charset_collate;
							";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $es_default_tables );

		$es_default_table_names = array( 'es_emaillist', 'es_notification', 'es_sentdetails', 'es_deliverreport' );

		$es_has_errors     = false;
		$es_missing_tables = array();
		foreach ( $es_default_table_names as $table_name ) {
			if ( strtoupper( $wpdb->get_var( "SHOW TABLES like  '" . $wpdb->prefix . $table_name . "'" ) ) != strtoupper( $wpdb->prefix . $table_name ) ) {
				$es_missing_tables[] = $wpdb->prefix . $table_name;
			}
		}

		if ( $es_missing_tables ) {
			$errors[]      = __( 'These tables could not be created on installation ' . implode( ', ', $es_missing_tables ), 'email-subscribers' );
			$es_has_errors = true;
		}

		// if error call wp_die()
		if ( $es_has_errors ) {
			wp_die( __( $errors[0], 'email-subscribers' ) );

			return false;
		} else {
			// Inserting dummy data on first activation
			es_cls_default::es_pluginconfig_default();
			es_cls_default::es_subscriber_default();
			es_cls_default::es_template_default();
			update_option( 'ig_es_sample_data_imported', 'yes' );

			//current version and date on activation
			$es_plugin_meta_data = get_plugin_data( WP_PLUGIN_DIR . '/email-subscribers/email-subscribers.php' );
			$es_current_version  = $es_plugin_meta_data['Version'];

			$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
			$es_current_date = date_i18n( $timezone_format );

			$es_current_version_date_details = array(
				'es_current_version' => '',
				'es_current_date'    => ''
			);

			$es_current_version_date_details['es_current_version'] = $es_current_version;
			$es_current_version_date_details['es_current_date']    = $es_current_date;

			update_option( 'ig_es_current_version_date_details', $es_current_version_date_details, 'no' );

		}

		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_es_activation_redirect', 1, 30 );
		}

		return true;
	}

	/**
	 * Sends user to the help & info page on activation.
	 */
	public static function es_welcome() {

		if ( ! get_transient( '_es_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_es_activation_redirect' );

		wp_redirect( admin_url( 'admin.php?page=es-general-information' ) );
		exit;
	}

	public static function es_synctables() {
		$es_c_email_subscribers_ver = get_option( "email-subscribers" );

		if ( $es_c_email_subscribers_ver != "2.9" ) {

			$guid     = es_cls_common::es_generate_guid( 60 );
			$home_url = home_url( '/' );
			$blogname = get_option( 'blogname' );
			$cronurl  = $home_url . "?es=cron&guid=" . $guid;

			add_option( "ig_es_cronurl", $cronurl );
			add_option( "ig_es_cron_mailcount", "50" );
			add_option( "ig_es_cron_adminmail", "Hi Admin,\r\n\r\nCron URL has been triggered successfully on {{DATE}} for the email '{{SUBJECT}}'. And it sent email to {{COUNT}} recipient(s).\r\n\r\nBest,\r\n" . $blogname . "" );

			update_option( "email-subscribers", "2.9" );
		}
	}

	public static function es_deactivation() {
		// do not generate any output here
	}

	public static function es_admin_option() {
		// do not generate any output here
	}

	public static function es_adminmenu() {
		$post                      = get_post_types();
		$es_c_rolesandcapabilities = get_option( 'ig_es_rolesandcapabilities', 'norecord' );
		if ( $es_c_rolesandcapabilities == 'norecord' || $es_c_rolesandcapabilities == "" ) {
			$es_roles_subscriber   = "manage_options";
			$es_roles_mail         = "manage_options";
			$es_roles_notification = "manage_options";
			$es_roles_sendmail     = "manage_options";
			$es_roles_sentmail     = "manage_options";
		} else {
			$es_roles_subscriber   = $es_c_rolesandcapabilities['es_roles_subscriber'];
			$es_roles_mail         = $es_c_rolesandcapabilities['es_roles_mail'];
			$es_roles_notification = $es_c_rolesandcapabilities['es_roles_notification'];
			$es_roles_sendmail     = $es_c_rolesandcapabilities['es_roles_sendmail'];
			$es_roles_sentmail     = $es_c_rolesandcapabilities['es_roles_sentmail'];
		}
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		add_menu_page( __( 'Email Subscribers', 'email-subscribers' ),
			__( 'Email Subscribers', 'email-subscribers' ), 'edit_posts', 'es-view-subscribers', array( 'es_cls_intermediate', 'es_subscribers' ), 'dashicons-email', 51 );

		add_submenu_page( 'es-view-subscribers', __( 'Subscribers', 'email-subscribers' ),
			__( 'Subscribers', 'email-subscribers' ), $es_roles_subscriber, 'es-view-subscribers', array( 'es_cls_intermediate', 'es_subscribers' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Templates', 'email-subscribers' ),
			__( 'Templates', 'email-subscribers' ), $es_roles_mail, 'edit.php?post_type=es_template', null );

		add_submenu_page( 'es-view-subscribers', __( 'Post Notifications', 'email-subscribers' ),
			__( 'Post Notifications', 'email-subscribers' ), $es_roles_notification, 'es-notification', array( 'es_cls_intermediate', 'es_notification' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Newsletters', 'email-subscribers' ),
			__( 'Newsletters', 'email-subscribers' ), $es_roles_sendmail, 'es-sendemail', array( 'es_cls_intermediate', 'es_sendemail' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Settings', 'email-subscribers' ),
			__( 'Settings', 'email-subscribers' ), 'manage_options', 'es-settings', array( 'es_cls_intermediate', 'es_settings' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Tools', 'email-subscribers' ),
			__( 'Tools', 'email-subscribers' ), 'manage_options', 'es-tools', array( 'es_cls_intermediate', 'es_tools' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Reports', 'email-subscribers' ),
			__( 'Reports', 'email-subscribers' ), $es_roles_sentmail, 'es-sentmail', array( 'es_cls_intermediate', 'es_sentmail' ) );

		add_submenu_page( 'es-view-subscribers', __( 'Help & Info', 'email-subscribers' ),
			__( '<span style="color:#f18500;font-weight:bolder;">Help & Info</span>', 'email-subscribers' ), 'edit_posts', 'es-general-information', array( 'es_cls_intermediate', 'es_information' ) );

		if ( ! ( in_array( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) || array_key_exists( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) ) ) {
			add_submenu_page( 'es-view-subscribers', __( 'Go Pro', 'email-subscribers' ),
				__( '<span style="color:#03a025;font-weight:bolder;">Go Pro</span>', 'email-subscribers' ), 'edit_posts', 'es-pricing', array( 'es_cls_intermediate', 'es_pricing' ) );
		}
	}

	//upsale functions
	public static function add_readymade_template_link() {
		global $post, $pagenow;
		$screen = get_current_screen();
		if ( $screen->id === 'edit-es_template' ) {
			?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery(".page-title-action").after("<span class='es_upsale' >Save time using beautiful readymade templates <a href='https://www.icegram.com/documentation/how-ready-made-template-in-in-email-subscribers-look/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale' target='_blank'>Checkout here</a></span>");
                });
            </script>
			<?php
		}
		if ( in_array( $screen->id, array( 'email-subscribers_page_es-notification', 'email-subscribers_page_es-sendemail' ), true ) ) {
			?>
            <span class="es_upsale"><?php _e( 'Save time using beautiful readymade templates <a href="https://www.icegram.com/documentation/how-ready-made-template-in-in-email-subscribers-look/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale" target="_blank">Checkout here</a>', 'email-subscribers' ) ?></span>
			<?php
		}

	}

	public static function add_test_send_newsletter_link() {
		echo "<span>Test Newsletter Emails Before Sending</span>";
	}

	public static function add_captcha_link() {
		?>
        <tr class="es-admin active-settings">
            <td class="es_upsale"><?php _e( 'Enable captcha to protect list from bot attacks <a href="https://www.icegram.com/documentation/es-how-to-add-captcha-in-subscribe-form-of-email-subscribers/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale" target="_blank">Lean more</a>', 'email-subscribers' ) ?></td>
        </tr>
		<?php
	}

	public static function add_optin_optout_link() {
		echo '<tr class="es-signup-confirmation hidden"><td><span  class="es_upsale">Customize confirmation and unsubscribe page <a href="https://www.icegram.com/documentation/how-to-change-simple-unsubscribe-confirmation-message-with-some-beautiful-design-page/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale" target="_blank">Lean more</a></span></td></tr>';
	}

	public static function add_cron_service() {
		$screen = get_current_screen();
		if ( $screen->id === 'email-subscribers_page_es-settings' ) {
			?>
            <tr class="es-cron hidden">
                <td><span class="es_upsale"><?php _e( 'Set automatic cron service <a href="https://www.icegram.com/documentation/how-to-enable-automatic-cron-in-es/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale" target="_blank">Lean more</a>' ) ?></span></td>
            </tr>
			<?php
		} elseif ( in_array( $screen->id, array( 'email-subscribers_page_es-notification', 'email-subscribers_page_es-sendemail' ), true ) ) {
			?>
            <span class="es_upsale"><?php _e( 'Set automatic cron service <a href="https://www.icegram.com/documentation/how-to-enable-automatic-cron-in-es/?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale" target="_blank">Lean more</a>' ) ?></span>
			<?php
		}
	}

	public static function add_spam_score_utm_link() {
		global $post, $pagenow;
		if ( $post->post_type !== 'es_template' ) {
			return;
		}
		if ( $pagenow !== 'post-new.php' ) {
			?>
            <script>
                jQuery('#submitdiv').after('<div class="es_upsale">Track email leads in Google using UTM tracking <a href="https://www.icegram.com/documentation/how-to-add-utm-parameters-to-email?utm_source=es&utm_medium=in_app&utm_campaign=es_upsale">Learn how</a></div>');
            </script>
			<?php
		}
	}

	public static function es_load_scripts() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'toplevel_page_es-view-subscribers', 'es_template', 'edit-es_template', 'email-subscribers_page_es-notification', 'email-subscribers_page_es-notification', 'email-subscribers_page_es-sendemail', 'email-subscribers_page_es-settings', 'email-subscribers_page_es-sentmail' ), true ) ) {
			?>
            <style type="text/css">
                .es_tmpl_select {
                    width: 50%;
                }

                .es_upsale {
                    margin-left: 2px;
                    vertical-align: text-bottom;
                    color: green;
                    background: #fbfbcd;
                    padding: 2px;
                    border: 1px dashed;
                }
            </style>
			<?php
		}
		if ( ! empty( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'es-view-subscribers':
					wp_register_script( 'es-view-subscribers', ES_URL . 'subscribers/view-subscriber.js', '', '', true );
					wp_enqueue_script( 'es-view-subscribers' );
					$es_select_params = array(
						'es_subscriber_email'          => _x( 'Please enter subscriber email address.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_email_status'   => _x( 'Please select subscriber email status.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_group'          => _x( 'Please select or create group for this subscriber.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_delete_record'  => _x( 'Do you want to delete this record?', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_bulk_action'    => _x( 'Please select the bulk action.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_confirm_delete' => _x( 'Are you sure you want to delete selected records?', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_resend_email'   => _x( 'Do you want to resend confirmation email? Also please note, this will update subscriber current status to \'Unconfirmed\'.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_new_group'      => _x( 'Please select new subscriber group.', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_new_status'     => _x( 'Please select new status for subscribers', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_group_update'   => _x( 'Do you want to update subscribers group?', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_status_update'  => _x( 'Do you want to update subscribers status?', 'view-subscriber-enhanced-select', 'email-subscribers' ),
						'es_subscriber_csv_file'       => _x( 'Please select only csv file. Please check official website for csv structure..', 'view-subscriber-enhanced-select', 'email-subscribers' )
					);
					wp_localize_script( 'es-view-subscribers', 'es_view_subscriber_notices', $es_select_params );
					break;
				case 'es-notification':
					wp_register_script( 'es-notification', ES_URL . 'notification/notification.js', '', '', true );
					wp_enqueue_script( 'es-notification' );
					$es_select_params = array(
						'es_notification_select_group'  => _x( 'Please select subscribers group.', 'notification-enhanced-select', 'email-subscribers' ),
						'es_notification_mail_subject'  => _x( 'Please select notification mail subject. Use templates menu to create new.', 'notification-enhanced-select', 'email-subscribers' ),
						'es_notification_status'        => _x( 'Please select notification status.', 'notification-enhanced-select', 'email-subscribers' ),
						'es_notification_delete_record' => _x( 'Do you want to delete this record?', 'notification-enhanced-select', 'email-subscribers' )
					);
					wp_localize_script( 'es-notification', 'es_notification_notices', $es_select_params );
					break;
				case 'es-sendemail':
					wp_register_script( 'sendmail', ES_URL . 'sendmail/sendmail.js', '', '', true );
					wp_enqueue_script( 'sendmail' );
					$es_select_params = array(
						'es_sendmail_subject' => _x( 'Please select your mail subject.', 'sendmail-enhanced-select', 'email-subscribers' ),
						'es_sendmail_status'  => _x( 'Please select your mail type.', 'sendmail-enhanced-select', 'email-subscribers' ),
						'es_sendmail_confirm' => _x( 'Have you double checked your selected group? If so, let\'s go ahead and send this.', 'sendmail-enhanced-select', 'email-subscribers' )
					);
					wp_localize_script( 'sendmail', 'es_sendmail_notices', $es_select_params );
					break;
				case 'es-sentmail':
					wp_register_script( 'es-sentmail', ES_URL . 'sentmail/sentmail.js', '', '', true );
					wp_enqueue_script( 'es-sentmail' );
					$es_select_params = array(
						'es_sentmail_delete'     => _x( 'Do you want to delete this record?', 'sentmail-enhanced-select', 'email-subscribers' ),
						'es_sentmail_delete_all' => _x( 'Do you want to delete all records except latest 10?', 'sentmail-enhanced-select', 'email-subscribers' )
					);
					wp_localize_script( 'es-sentmail', 'es_sentmail_notices', $es_select_params );
					break;
				case 'es-settings':
					wp_register_script( 'es-settings', ES_URL . 'settings/es-settings.js', '', '', true );
					wp_enqueue_script( 'es-settings' );
					$es_select_params = array(
						'es_cron_number'     => _x( 'Please select enter number of mails you want to send per hour/trigger.', 'cron-enhanced-select', 'email-subscribers' ),
						'es_cron_input_type' => _x( 'Please enter the mail count, only number.', 'cron-enhanced-select', 'email-subscribers' )
					);
					wp_localize_script( 'es-settings', 'es_cron_notices', $es_select_params );
					break;
			}
		}
	}

	public static function es_load_widget_scripts_styles() {

		wp_register_script( 'es-widget-page', ES_URL . 'widget/es-widget-page.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'es-widget-page' );
		$es_select_params = array(
			'es_email_notice'      => _x( 'Please enter email address', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_rate_limit_notice' => _x( 'You need to wait for sometime before subscribing again', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_success_message'   => _x( 'Successfully Subscribed.', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_success_notice'    => _x( 'Your subscription was successful! Kindly check your mailbox and confirm your subscription. If you don\'t see the email within a few minutes, check the spam/junk folder.', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_email_exists'      => _x( 'Email Address already exists!', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_error'             => _x( 'Oops.. Unexpected error occurred.', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_invalid_email'     => _x( 'Invalid email address', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_try_later'         => _x( 'Please try after some time', 'widget-page-enhanced-select', 'email-subscribers' ),
			'es_ajax_url'          => admin_url( 'admin-ajax.php' ),

		);
		wp_localize_script( 'es-widget-page', 'es_widget_page_notices', $es_select_params );

		wp_register_style( 'es-widget-css', ES_URL . 'widget/es-widget.css' );
		wp_enqueue_style( 'es-widget-css' );

	}

	public static function es_widget_loading() {
		register_widget( 'es_widget_register' );
	}

	// Function for Klawoo's Subscribe form on Help & Info page
	public static function klawoo_subscribe() {
		$url = 'http://app.klawoo.com/subscribe';

		if ( ! empty( $_POST ) ) {
			$params = $_POST;
		} else {
			exit();
		}
		$method = 'POST';
		$qs     = http_build_query( $params );

		$options = array(
			'timeout' => 15,
			'method'  => $method
		);

		if ( $method == 'POST' ) {
			$options['body'] = $qs;
		} else {
			if ( strpos( $url, '?' ) !== false ) {
				$url .= '&' . $qs;
			} else {
				$url .= '?' . $qs;
			}
		}

		$response = wp_remote_request( $url, $options );

		if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
			$data = $response['body'];
			if ( $data != 'error' ) {

				$message_start = substr( $data, strpos( $data, '<body>' ) + 6 );
				$remove        = substr( $message_start, strpos( $message_start, '</body>' ) );
				$message       = trim( str_replace( $remove, '', $message_start ) );
				echo( $message );
				exit();
			}
		}
		exit();
	}

	/**
	 * Update current_sa_email_subscribers_db_version
	 */
	public static function sa_email_subscribers_db_update() {

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === false ) {
			es_cls_registerhook::es_upgrade_database_for_3_2();
		}

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === '3.2' ) {
			es_cls_registerhook::es_upgrade_database_for_3_2_7();
		}

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === '3.2.7' ) {
			es_cls_registerhook::es_upgrade_database_for_3_3();
		}

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === '3.3' ) {
			es_cls_registerhook::es_upgrade_database_for_3_3_6();
		}

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === '3.3.6' ) {
			es_cls_registerhook::es_upgrade_database_for_3_4_0();
		}

		if ( get_option( 'current_sa_email_subscribers_db_version' ) === '3.4.0' ) {
			es_cls_registerhook::es_upgrade_database_for_3_5_16();
		}
	}

	/**
	 * To update sync email option to remove Commented user & it's group - ig_es_sync_wp_users
	 * ES version 3.2 onwards
	 */
	public static function es_upgrade_database_for_3_2() {

		$sync_subscribers = get_option( 'ig_es_sync_wp_users' );

		$es_unserialized_data = maybe_unserialize( $sync_subscribers );
		unset( $es_unserialized_data['es_commented'] );
		unset( $es_unserialized_data['es_commented_group'] );

		$es_serialized_data = serialize( $es_unserialized_data );
		update_option( 'ig_es_sync_wp_users', $es_serialized_data );

		update_option( 'current_sa_email_subscribers_db_version', '3.2' );
	}

	/**
	 * To rename a few terms in compose & reports menu
	 * ES version 3.2.7 onwards
	 */
	public static function es_upgrade_database_for_3_2_7() {

		global $wpdb;

		// Compose table
		$template_table_exists = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}es_templatetable'" );
		if ( $template_table_exists > 0 ) {
			$wpdb->query( "UPDATE {$wpdb->prefix}es_templatetable
						   SET es_email_type =
						   ( CASE
								WHEN es_email_type = 'Static Template' THEN 'Newsletter'
								WHEN es_email_type = 'Dynamic Template' THEN 'Post Notification'
								ELSE es_email_type
							 END ) " );
		}

		// Sent Details table
		$wpdb->query( "UPDATE {$wpdb->prefix}es_sentdetails
					   SET es_sent_type =
					   ( CASE
							WHEN es_sent_type = 'Instant Mail' THEN 'Immediately'
							WHEN es_sent_type = 'Cron Mail' THEN 'Cron'
							ELSE es_sent_type
						 END ),
						   es_sent_source =
					   ( CASE
							WHEN es_sent_source = 'manual' THEN 'Newsletter'
							WHEN es_sent_source = 'notification' THEN 'Post Notification'
							ELSE es_sent_source
					   END ) " );

		// Delivery Reports table
		$wpdb->query( "UPDATE {$wpdb->prefix}es_deliverreport
					   SET es_deliver_senttype =
					   ( CASE
							WHEN es_deliver_senttype = 'Instant Mail' THEN 'Immediately'
							WHEN es_deliver_senttype = 'Cron Mail' THEN 'Cron'
							ELSE es_deliver_senttype
						 END ) " );

		update_option( 'current_sa_email_subscribers_db_version', '3.2.7' );
	}

	/**
	 * To migrate Email Settings data from custom pluginconfig table to wordpress options table and to update user roles
	 * ES version 3.3 onwards
	 */
	public static function es_upgrade_database_for_3_3() {
		global $wpdb;

		$settings_to_rename = array(
			'es_c_fromname'         => 'ig_es_fromname',
			'es_c_fromemail'        => 'ig_es_fromemail',
			'es_c_mailtype'         => 'ig_es_emailtype',
			'es_c_adminmailoption'  => 'ig_es_notifyadmin',
			'es_c_adminemail'       => 'ig_es_adminemail',
			'es_c_adminmailsubject' => 'ig_es_admin_new_sub_subject',
			'es_c_adminmailcontant' => 'ig_es_admin_new_sub_content',
			'es_c_usermailoption'   => 'ig_es_welcomeemail',
			'es_c_usermailsubject'  => 'ig_es_welcomesubject',
			'es_c_usermailcontant'  => 'ig_es_welcomecontent',
			'es_c_optinoption'      => 'ig_es_optintype',
			'es_c_optinsubject'     => 'ig_es_confirmsubject',
			'es_c_optincontent'     => 'ig_es_confirmcontent',
			'es_c_optinlink'        => 'ig_es_optinlink',
			'es_c_unsublink'        => 'ig_es_unsublink',
			'es_c_unsubtext'        => 'ig_es_unsubcontent',
			'es_c_unsubhtml'        => 'ig_es_unsubtext',
			'es_c_subhtml'          => 'ig_es_successmsg',
			'es_c_message1'         => 'ig_es_suberror',
			'es_c_message2'         => 'ig_es_unsuberror',
		);

		$options_to_rename = array(
			'es_c_post_image_size'      => 'ig_es_post_image_size',
			'es_c_sentreport'           => 'ig_es_sentreport',
			'es_c_sentreport_subject'   => 'ig_es_sentreport_subject',
			'es_c_rolesandcapabilities' => 'ig_es_rolesandcapabilities',
			'es_c_cronurl'              => 'ig_es_cronurl',
			'es_cron_mailcount'         => 'ig_es_cron_mailcount',
			'es_cron_adminmail'         => 'ig_es_cron_adminmail',
			'es_c_emailsubscribers'     => 'ig_es_sync_wp_users',
		);

		// Rename options that were previously stored
		foreach ( $options_to_rename as $old_option_name => $new_option_name ) {
			$option_value = get_option( $old_option_name );
			if ( $option_value ) {
				update_option( $new_option_name, $option_value );
				delete_option( $old_option_name );
			}
		}

		// Do not pull data for new users as there is no pluginconfig table created on activation
		$table_exists = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}es_pluginconfig'" );

		if ( $table_exists > 0 ) {
			// Pull out ES settings data of existing users and move them to options table
			$settings_data = es_cls_settings::es_setting_select( 1 );
			if ( ! empty( $settings_data ) ) {
				foreach ( $settings_data as $name => $value ) {
					if ( array_key_exists( $name, $settings_to_rename ) ) {
						update_option( $settings_to_rename[ $name ], $value );
					}
				}
			}
		}

		//Update User Roles Settings
		$es_c_rolesandcapabilities = get_option( 'ig_es_rolesandcapabilities', 'norecord' );

		if ( $es_c_rolesandcapabilities != 'norecord' ) {
			$remove_roles = array( 'es_roles_setting', 'es_roles_help' );
			foreach ( $es_c_rolesandcapabilities as $role_name => $role_value ) {
				if ( in_array( $role_name, $remove_roles ) ) {
					unset( $es_c_rolesandcapabilities[ $role_name ] );
				}
			}
			update_option( 'ig_es_rolesandcapabilities', $es_c_rolesandcapabilities );
		}

		update_option( 'current_sa_email_subscribers_db_version', '3.3' );
	}

	/**
	 * To alter templatable for extra slug column - to support new template designs
	 * ES version 3.3.6 onwards
	 */
	public static function es_upgrade_database_for_3_3_6() {

		global $wpdb;

		$template_table_exists = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}es_templatetable'" );
		if ( $template_table_exists > 0 ) {

			// To check if column es_templ_slug exists or not
			$es_template_col      = "SHOW COLUMNS FROM {$wpdb->prefix}es_templatetable LIKE 'es_templ_slug' ";
			$results_template_col = $wpdb->get_results( $es_template_col, 'ARRAY_A' );
			$template_num_rows    = $wpdb->num_rows;

			// If column doesn't exists, then insert it
			if ( $template_num_rows != '1' ) {
				// Template table
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}es_templatetable
								ADD COLUMN es_templ_slug VARCHAR(255) NULL
								AFTER es_email_type" );
			}
		}

		update_option( 'current_sa_email_subscribers_db_version', '3.3.6' );

	}

	/**
	 * To convert Compose to Custom Post Type (to support new template designs) AND Converting keywords structure
	 * ES version 3.4.0 onwards
	 */
	public static function es_upgrade_database_for_3_4_0() {

		global $wpdb;

		// MIGRATION OF TEMPLATE TABLE TO CTP
		$es_template_table_exists = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}es_templatetable'" );
		if ( $es_template_table_exists > 0 ) {

			$es_migration_success = get_option( 'es_template_migration_done', 'nodata' );
			if ( $es_migration_success == 'yes' ) {
				return;
			}

			$sSql   = "SELECT es_tt.*,
							 IFNULL(es_not.es_note_id, '') as es_note_id
					FROM {$wpdb->prefix}es_templatetable AS es_tt
					LEFT JOIN {$wpdb->prefix}es_notification AS es_not
						ON(es_not.es_note_templ = es_tt.es_templ_id)";
			$arrRes = $wpdb->get_results( $sSql, ARRAY_A );

			if ( ! empty( $arrRes ) ) {

				$es_note_ids = array();

				foreach ( $arrRes as $tmpl ) {
					// Create post object
					$es_post = array(
						'post_title'   => wp_strip_all_tags( $tmpl['es_templ_heading'] ),
						'post_content' => $tmpl['es_templ_body'],
						'post_status'  => 'publish',
						'post_type'    => 'es_template',
						'meta_input'   => array(
							'es_template_type' => $tmpl['es_email_type']
						)
					);
					// Insert the post into the database
					$last_inserted_id = wp_insert_post( $es_post );

					if ( $tmpl['es_email_type'] == 'Post Notification' && ! empty( $tmpl['es_note_id'] ) ) {
						$es_note_ids[] = 'WHEN es_note_id = ' . $tmpl['es_note_id'] . ' THEN ' . $last_inserted_id;
					}
				}

				if ( ! empty( $es_note_ids ) ) {
					// To update the 'es_note_templ' ids
					$sSql = "UPDATE {$wpdb->prefix}es_notification SET es_note_templ = (CASE " . implode( " ", $es_note_ids ) . " END)";
					$wpdb->query( $sSql );
				}

			}

			update_option( 'es_template_migration_done', 'yes' );
		}
		// END

		// Keywords in Compose table
		$keywords_to_rename_in_compose = array(
			'###NAME###'               => '{{NAME}}',
			'###EMAIL###'              => '{{EMAIL}}',
			'###DATE###'               => '{{DATE}}',
			'###POSTTITLE###'          => '{{POSTTITLE}}',
			'###POSTIMAGE###'          => '{{POSTIMAGE}}',
			'###POSTDESC###'           => '{{POSTDESC}}',
			'###POSTFULL###'           => '{{POSTFULL}}',
			'###POSTAUTHOR###'         => '{{POSTAUTHOR}}',
			'###POSTLINK###'           => '{{POSTLINK}}',
			'###POSTLINK-ONLY###'      => '{{POSTLINK-ONLY}}',
			'###POSTLINK-WITHTITLE###' => '{{POSTLINK-WITHTITLE}}',
		);

		// Keywords in Settings
		$keywords_in_settings_to_rename = array(
			'###NAME###'      => '{{NAME}}',
			'###EMAIL###'     => '{{EMAIL}}',
			'###GROUP###'     => '{{GROUP}}',
			'###COUNT###'     => '{{COUNT}}',
			'###UNIQUE###'    => '{{UNIQUE}}',
			'###STARTTIME###' => '{{STARTTIME}}',
			'###ENDTIME###'   => '{{ENDTIME}}',
			'###LINK###'      => '{{LINK}}',
			'###DATE###'      => '{{DATE}}',
			'###SUBJECT###'   => '{{SUBJECT}}',
			'###DBID###'      => '{{DBID}}',
			'###GUID###'      => '{{GUID}}',
		);

		// Updating keywords in post_title column where `post_type` = 'es_template'
		$es_post_title_query = "UPDATE {$wpdb->prefix}posts SET post_title = REPLACE(post_title,'###POSTTITLE###','{{POSTTITLE}}') WHERE post_type = 'es_template'";
		$wpdb->query( $es_post_title_query );

		// Updating keywords in post_content column where `post_type` = 'es_template'
		$compose_keywords = array();
		foreach ( $keywords_to_rename_in_compose as $key => $value ) {
			$compose_keywords[] = "post_content = REPLACE(post_content,'" . $key . "','" . $value . "')";
		}

		$es_post_content_query = "UPDATE {$wpdb->prefix}posts SET " . implode( ", ", $compose_keywords ) . " WHERE post_type = 'es_template'";
		$wpdb->query( $es_post_content_query );

		// Updating keywords in options
		$es_admin_new_sub_content = get_option( 'ig_es_admin_new_sub_content', 'nodata' );
		$es_sent_report_content   = get_option( 'ig_es_sentreport', 'nodata' );
		$es_confirm_content       = get_option( 'ig_es_confirmcontent', 'nodata' );
		$es_welcome_content       = get_option( 'ig_es_welcomecontent', 'nodata' );
		$es_unsub_content         = get_option( 'ig_es_unsubcontent', 'nodata' );
		$es_cron_admin_mail       = get_option( 'ig_es_cron_adminmail', 'nodata' );
		$es_optin_link            = get_option( 'ig_es_optinlink', 'nodata' );
		$es_unsub_link            = get_option( 'ig_es_unsublink', 'nodata' );

		foreach ( $keywords_in_settings_to_rename as $key => $value ) {
			if ( $es_admin_new_sub_content != 'nodata' ) {
				$es_admin_new_sub_content = str_replace( $key, $value, $es_admin_new_sub_content );
				update_option( 'ig_es_admin_new_sub_content', $es_admin_new_sub_content );
			}

			if ( $es_sent_report_content != 'nodata' ) {
				$es_sent_report_content = str_replace( $key, $value, $es_sent_report_content );
				update_option( 'ig_es_sentreport', $es_sent_report_content );
			}

			if ( $es_confirm_content != 'nodata' ) {
				$es_confirm_content = str_replace( $key, $value, $es_confirm_content );
				update_option( 'ig_es_confirmcontent', $es_confirm_content );
			}

			if ( $es_welcome_content != 'nodata' ) {
				$es_welcome_content = str_replace( $key, $value, $es_welcome_content );
				update_option( 'ig_es_welcomecontent', $es_welcome_content );
			}

			if ( $es_unsub_content != 'nodata' ) {
				$es_unsub_content = str_replace( $key, $value, $es_unsub_content );
				update_option( 'ig_es_unsubcontent', $es_unsub_content );
			}

			if ( $es_cron_admin_mail != 'nodata' ) {
				$es_cron_admin_mail = str_replace( $key, $value, $es_cron_admin_mail );
				update_option( 'ig_es_cron_adminmail', $es_cron_admin_mail );
			}

			if ( $es_optin_link != 'nodata' ) {
				$es_optin_link = str_replace( $key, $value, $es_optin_link );
				update_option( 'ig_es_optinlink', $es_optin_link );
			}

			if ( $es_unsub_link != 'nodata' ) {
				$es_unsub_link = str_replace( $key, $value, $es_unsub_link );
				update_option( 'ig_es_unsublink', $es_unsub_link );
			}
		}

		update_option( 'current_sa_email_subscribers_db_version', '3.4.0' );

	}

	/**
	 * Add es_subscriber_ips table to handle rate limit.
	 * ES version 3.5.16 onwards
	 */
	public static function es_upgrade_database_for_3_5_16() {

		global $wpdb;

		$charset_collate         = $wpdb->get_charset_collate();
		$es_subscriber_ips_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}es_subscriber_ips (
									ip varchar(45) NOT NULL, 
									created_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
									PRIMARY KEY  (created_on, ip),
									KEY ip (ip)
							  ) $charset_collate";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $es_subscriber_ips_table );

		update_option( 'current_sa_email_subscribers_db_version', '3.5.16' );
	}


	// Function to show any notices in admin section
	public static function es_add_admin_notices() {

		$screen = get_current_screen();
		if ( ! in_array( $screen->id, array( 'toplevel_page_es-view-subscribers', 'es_template', 'edit-es_template', 'email-subscribers_page_es-notification', 'email-subscribers_page_es-notification', 'email-subscribers_page_es-sendemail', 'email-subscribers_page_es-settings', 'email-subscribers_page_es-sentmail' ), true ) ) {
			return;
		}

		// Show if - more than 2 post notifications or Newsletters sent OR more than 10 subscribers
		$total_subscribers             = es_cls_dbquery::es_view_subscriber_count( 0 );
		$total_email_sent              = es_cls_sentmail::es_sentmail_count( $id = 0 );
		$es_star_review                = get_option( 'es_star_review_email_subscribers' );
		$es_rating_text                = array();
		$es_rating_text['star_review'] = __( 'If you like <strong>Email Subscribers</strong>, please consider leaving us a <a target="_blank" href="https://wordpress.org/support/plugin/email-subscribers/reviews/?filter=5#new-post">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thank you from the team in advance!', 'email-subscribers' );
		$es_rating_text['help_review'] = __( 'If you like <strong>Email Subscribers</strong>, tell us more about your experience and leave us <a target="_blank" href="https://wordpress.org/support/plugin/email-subscribers/reviews/?filter=5#new-post">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thank you from the team in advance!', 'email-subscribers' );

		if ( ( $total_subscribers >= 10 || $total_email_sent > 2 ) && $es_star_review != 'no' ) {
			$key            = array_rand( $es_rating_text );
			$es_rating_text = $es_rating_text[ $key ];
			echo '<div class="notice notice-warning" style="background-color: #FFF;"><p style="letter-spacing: 0.6px;">' . $es_rating_text . ' <a style="float:right" class="es-admin-btn es-admin-btn-secondary" href="?dismiss_admin_notice=1&option_name=es_star_review">' . __( 'No, I don\'t like it', 'email-subscribers' ) . '</a></p></div>';
		}

		//halloween 2018 :start
		$timezone_format = _x( 'Y-m-d', 'timezone date format' );
		$ig_current_date = strtotime( date_i18n( $timezone_format ) );
		$ig_offer_start  = strtotime( "2018-11-22" );
		$ig_offer_end    = strtotime( "2018-11-28" );
		if ( ( $ig_current_date >= $ig_offer_start ) && ( $ig_current_date <= $ig_offer_end ) ) {
			include_once( 'es-offer.php' );
		}
		//halloween 2018 :end
	}

	// Function to dismiss any admin notice
	public static function dismiss_admin_notice() {
		if ( isset( $_GET['dismiss_admin_notice'] ) && $_GET['dismiss_admin_notice'] == '1' && isset( $_GET['option_name'] ) ) {
			$option_name = sanitize_text_field( $_GET['option_name'] );
			update_option( $option_name . '_email_subscribers', 'no' );
			if ( $option_name === 'es_offer_bfcm_done_2018' ) {
				header( "Location: https://www.icegram.com/latest-valid-coupons-discounts-offers-deals/?utm_source=in_app&utm_medium=es_banner&utm_campaign=bfcm_2018" );
				exit();
			} else {
				$referer = wp_get_referer();
				wp_safe_redirect( $referer );
				exit();
			}

		}

	}

	public static function es_footer_text( $es_rating_text ) {

		global $post;

		if ( ( isset( $_GET['page'] ) && ( $_GET['page'] == 'es-view-subscribers' || $_GET['page'] == 'es-notification' || $_GET['page'] == 'es-sendemail' || $_GET['page'] == 'es-settings' || $_GET['page'] == 'es-sentmail' || $_GET['page'] == 'es-general-information' || $_GET['page'] == 'es-pricing' ) ) || ( is_object( $post ) && $post->post_type == 'es_template' ) ) {
			$es_rating_text = __( 'Thank you for using Email Subscribers! A huge thank you from Icegram!', 'email-subscribers' );
		}

		return $es_rating_text;
	}

	public static function es_update_footer_text( $es_text ) {

		global $post;

		$es_plugin_data     = get_plugin_data( WP_PLUGIN_DIR . '/email-subscribers/email-subscribers.php' );
		$es_current_version = $es_plugin_data['Version'];

		if ( ( isset( $_GET['page'] ) && ( $_GET['page'] == 'es-view-subscribers' || $_GET['page'] == 'es-notification' || $_GET['page'] == 'es-sendemail' || $_GET['page'] == 'es-settings' || $_GET['page'] == 'es-sentmail' || $_GET['page'] == 'es-general-information' || $_GET['page'] == 'es-pricing' ) ) || ( is_object( $post ) && $post->post_type == 'es_template' ) ) {
			$es_text = sprintf( __( 'Email Subscribers version: <strong>%s</strong>', 'email-subscribers' ), $es_current_version );
		}

		return $es_text;
	}

	public static function es_register_post_type() {

		$labels = array(
			'name'               => __( 'Templates', 'email-subscribers' ),
			'singular_name'      => __( 'Templates', 'email-subscribers' ),
			'add_new'            => __( 'Add new Template', 'email-subscribers' ),
			'add_new_item'       => __( 'Add new Template', 'email-subscribers' ),
			'edit_item'          => __( 'Edit Templates', 'email-subscribers' ),
			'new_item'           => __( 'New Templates', 'email-subscribers' ),
			'all_items'          => __( 'Templates', 'email-subscribers' ),
			'view_item'          => __( 'View Templates', 'email-subscribers' ),
			'search_items'       => __( 'Search Templates', 'email-subscribers' ),
			'not_found'          => __( 'No Templates found', 'email-subscribers' ),
			'not_found_in_trash' => __( 'No Templates found in Trash', 'email-subscribers' ),
			'parent_item_colon'  => __( '', 'email-subscribers' ),
			'menu_name'          => __( 'Email Subscribers', 'email-subscribers' ),
			'featured_image'     => __( 'Thumbnail (For Visual Representation only)', 'email-subscribers' ),
			'set_featured_image' => __( 'Set thumbnail', 'email-subscribers' )
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=es_template',
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'es_template' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array( 'title', 'editor', 'thumbnail' )
		);

		register_post_type( 'es_template', $args );
	}

	public static function es_highlight( $parent_file ) {
		global $submenu_file, $current_screen;

		if ( $current_screen->post_type == 'es_template' ) {
			$parent_file = 'es-view-subscribers';
		}

		return $parent_file;
	}

	public static function es_custom_template_column( $existing_columns ) {

		$date = $existing_columns['date'];
		unset( $existing_columns['date'] );

		$existing_columns['es_templ_type']      = __( 'Template Type', 'email-subscribers' );
		$existing_columns['es_templ_thumbnail'] = __( 'Thumbnail', 'email-subscribers' );
		$existing_columns['date']               = $date;

		return $existing_columns;
	}

	public static function es_template_edit_columns( $column ) {
		global $post;

		$es_post_thumbnail  = get_the_post_thumbnail( $post->ID );
		$es_templ_thumbnail = ( ! empty( $es_post_thumbnail ) ) ? get_the_post_thumbnail( $post->ID, array( '200', '200' ) ) : '<img src="' . ES_URL . 'images/envelope.png" />';

		switch ( $column ) {
			case 'es_templ_type':
				echo get_post_meta( $post->ID, 'es_template_type', true );
				break;
			case 'es_templ_thumbnail' :
				echo $es_templ_thumbnail;
				break;
			default:
				break;
		}

		return $column;
	}

	public static function es_add_admin_css() {

		global $current_screen;

		if ( $current_screen->post_type != 'es_template' ) {
			return;
		}

		?>
        <style type="text/css">
            .column-es_templ_thumbnail, #es_templ_thumbnail,
            .column-es_templ_type, #es_templ_type {
                text-align: center !important;
            }
        </style>
		<?php
	}

	public static function es_add_template_action( $actions, $post ) {
		if ( $post->post_type != 'es_template' ) {
			return $actions;
		}

		$es_templ_type               = get_post_meta( $post->ID, 'es_template_type', true );
		$page                        = ( ( $es_templ_type == 'Newsletter' ) ? 'es-sendemail' : 'es-notification' );
		$preview_url                 = ES_ADMINURL . "?page=" . $page . "&amp;ac=preview&did=" . $post->ID;
		$actions['preview_campaign'] = '<a class="es-preview-template" target="_blank" href="' . $preview_url . '" >' . __( 'Preview', 'email-subscribers' ) . '</a>';

		return $actions;
	}

	public static function es_add_template_type_metaboxes() {

		global $post, $pagenow;

		if ( $post->post_type != 'es_template' ) {
			return;
		}

		$es_templ_type = '';
		if ( $pagenow != 'post-new.php' ) {
			$es_templ_type = get_post_meta( $post->ID, 'es_template_type', true );
		}

		if ( $es_templ_type == 'Post Notification' || $pagenow == 'post-new.php' ) {
			?>
            <p style="margin-top: 0em; !important;">
				<?php
				echo sprintf( __( '%s for Post Notification: {{POSTTITLE}}', 'email-subscribers' ), '<a href="https://www.icegram.com/documentation/es-what-are-the-available-keywords-in-the-post-notifications/?utm_source=es&utm_medium=in_app&utm_campaign=view_docs_help_page" target="_blank">' . __( 'Available Keyword', 'email-subscribers' ) . '</a>' );
				?>
            </p>
			<?php
		}
		?>
        <p>
            <label for="tag-link"><?php echo __( 'Select your Email Template Type', 'email-subscribers' ); ?></label><br/>
            <select name="es_template_type" id="es_template_type">
                <option value='Newsletter' <?php if ( $es_templ_type == 'Newsletter' ) {
					echo 'selected="selected"';
				} ?>><?php echo __( 'Newsletter', 'email-subscribers' ); ?></option>
                <option value='Post Notification' <?php if ( $es_templ_type == 'Post Notification' ) {
					echo 'selected="selected"';
				} ?>><?php echo __( 'Post Notification', 'email-subscribers' ); ?></option>
            </select>
        </p>
		<?php
	}

	public static function es_save_template_type( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type != 'es_template' ) {
			return;
		}
		if ( isset( $_POST['es_template_type'] ) ) {
			update_post_meta( $post_id, 'es_template_type', $_POST['es_template_type'] );
		}
	}

	public static function es_process_template_body( $content, $tmpl_id = 0 ) {
		$content = convert_chars( convert_smilies( wptexturize( $content ) ) );
		if ( isset( $GLOBALS['wp_embed'] ) ) {
			$content = $GLOBALS['wp_embed']->autoembed( $content );
		}
		$content         = wpautop( $content );
		$content         = do_shortcode( shortcode_unautop( $content ) );
		$data['content'] = $content;
		$data['tmpl_id'] = $tmpl_id;
		$data            = apply_filters( 'es_after_process_template_body', $data );
		$content         = $data['content'];

		return $content;
	}

	public static function add_preview_button() {

		global $post;
		if ( $post->post_type != 'es_template' ) {
			return;
		}

		$es_templ_type = get_post_meta( $post->ID, 'es_template_type', true );
		$page          = ( $es_templ_type == 'Newsletter' ) ? 'es-sendemail' : 'es-notification';
		$preview_url   = ES_ADMINURL . "?page=" . $page . "&amp;ac=preview&did=" . $post->ID;

		//Adding a preview button in side bar widget
		$script         = "<script>
		var prvw_button = jQuery('.es_preview_button');
		jQuery('#submitdiv .submitbox #minor-publishing-actions').after(prvw_button)
		prvw_button.fadeIn('fast');</script>";
		$preview_button = '<style>.es_preview_button{display: none;padding: 10px 10px 0;}</style><div id="" class="es_preview_button">
									<a href="' . $preview_url . '" target="_blank" class="button button-primary es_preview">' . __( 'Preview Template', 'email-subscribers' ) . '</a>
									<div class="clear"></div></div>';
		echo $preview_button;
		echo $script;

	}

	public static function es_add_keyword() {

		global $post, $pagenow;

		if ( $post->post_type != 'es_template' ) {
			return;
		}

		if ( $pagenow == 'post-new.php' ) {
			?>
            <p>
				<?php
				echo sprintf( __( '%s for Post Notification: {{NAME}}, {{EMAIL}}, {{DATE}}, {{POSTTITLE}}, {{POSTIMAGE}}, {{POSTEXCERPT}}, {{POSTDESC}}, {{POSTAUTHOR}}, {{POSTLINK}}, {{POSTLINK-WITHTITLE}}, {{POSTLINK-ONLY}}, {{POSTFULL}}', 'email-subscribers' ), '<a href="https://www.icegram.com/documentation/es-what-are-the-available-keywords-in-the-post-notifications/?utm_source=es&utm_medium=in_app&utm_campaign=view_docs_help_page" target="_blank">' . __( 'Available Keywords', 'email-subscribers' ) . '</a>' );
				echo sprintf( __( '<br/><br/>%s for Newsletter: {{NAME}}, {{EMAIL}}', 'email-subscribers' ), '<a href="https://www.icegram.com/documentation/es-what-are-the-available-keywords-in-the-newsletters/?utm_source=es&utm_medium=in_app&utm_campaign=view_docs_help_page" target="_blank">' . __( 'Available Keywords', 'email-subscribers' ) . '</a>' );
				?>
            </p>
			<?php
		}

		$es_templ_type = '';
		if ( $pagenow != 'post-new.php' ) {
			$es_templ_type = get_post_meta( $post->ID, 'es_template_type', true );
		}

		if ( $es_templ_type == 'Post Notification' ) {
			?>
            <p>
				<?php
				echo sprintf( __( '%s for Post Notification: {{NAME}}, {{EMAIL}}, {{DATE}}, {{POSTTITLE}}, {{POSTIMAGE}}, {{POSTEXCERPT}}, {{POSTDESC}}, {{POSTAUTHOR}}, {{POSTLINK}}, {{POSTLINK-WITHTITLE}}, {{POSTLINK-ONLY}}, {{POSTFULL}}', 'email-subscribers' ), '<a href="https://www.icegram.com/documentation/es-what-are-the-available-keywords-in-the-post-notifications/?utm_source=es&utm_medium=in_app&utm_campaign=view_docs_help_page" target="_blank">' . __( 'Available Keywords', 'email-subscribers' ) . '</a>' );
				?>
            </p>
			<?php
		} elseif ( $es_templ_type == 'Newsletter' ) {
			?>
            <p>
				<?php
				echo sprintf( __( '%s for Newsletter: {{NAME}}, {{EMAIL}}', 'email-subscribers' ), '<a href="https://www.icegram.com/documentation/es-what-are-the-available-keywords-in-the-newsletters/?utm_source=es&utm_medium=in_app&utm_campaign=view_docs_help_page" target="_blank">' . __( 'Available Keywords', 'email-subscribers' ) . '</a>' );
				?>
            </p>
			<?php
		}
	}

	public static function es_get_form( $instance ) {

		global $es_includes;

		// Compatibility for GDPR
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		if ( ! isset( $es_includes ) || $es_includes !== true ) {
			$es_includes = true;
		}
		$es_desc  = $instance['es_desc'];
		$es_name  = $instance['es_name'];
		$es_group = $instance['es_group'];
		$es_pre   = $instance['es_pre'];
		ob_start();
		?>
        <div class="es_form_container">
            <form class="es_<?php echo $es_pre ?>_form" data-es_form_id="es_<?php echo $es_pre ?>_form">
				<?php if ( $es_desc != "" ) { ?>
                    <div class="es_caption"><?php echo $es_desc; ?></div>
				<?php } ?>
				<?php if ( $es_name == "YES" ) { ?>
                    <div class="es_lablebox">
                        <label class="es_<?php echo $es_pre ?>_form_name"><?php echo __( 'Name', 'email-subscribers' ); ?></label>
                    </div>
                    <div class="es_textbox">
                        <input type="text" id="es_txt_name" class="es_textbox_class" name="es_txt_name" value="" maxlength="60">
                    </div>
				<?php } ?>
                <div class="es_lablebox">
                    <label class="es_<?php echo $es_pre ?>_form_email"><?php echo __( 'Email *', 'email-subscribers' ); ?></label>
                </div>
                <div class="es_textbox">
                    <input type="email" id="es_txt_email" class="es_textbox_class" name="es_txt_email" value="" maxlength="60" required>
                </div>
				<?php if ( ( in_array( 'gdpr/gdpr.php', $active_plugins ) || array_key_exists( 'gdpr/gdpr.php', $active_plugins ) ) ) {
					echo GDPR::consent_checkboxes();
				} ?>
                <div class="es_button">
                    <input type="submit" id="es_txt_button" class="es_textbox_button es_submit_button" name="es_txt_button" value="<?php echo __( 'Subscribe', 'email-subscribers' ); ?>">
                </div>
                <div class="es_msg" id="es_<?php echo $es_pre ?>_msg">
                    <span id="es_msg"></span>
                </div>
				<?php if ( $es_name != "YES" ) { ?>
                    <input type="hidden" id="es_txt_name" name="es_txt_name" value="">
				<?php
				}

				$hp_style = "position:absolute;top:-99999px;" . ( is_rtl() ? 'right' : 'left' ) . ":-99999px;z-index:-99;";

				?>
                <input type="hidden" id="es_txt_group" name="es_txt_group" value="<?php echo $es_group; ?>">
	            <?php $nonce = wp_create_nonce( 'es-subscribe' ); ?>
                <input type="hidden" name="es-subscribe" id="es-subscribe" value="<?php echo $nonce; ?>"/>
                <label style="<?php echo $hp_style; ?>"><input type="text" name="es_hp_<?php echo wp_create_nonce('es_hp'); ?>" class="es_required_field" tabindex="-1" autocomplete="off"/></label>
            </form>
			<?php do_action( 'es_after_form' ) ?>
        </div>
		<?php return $es_form = ob_get_clean();

	}

	public static function es_add_home_url( $es_url, $qs ) {
		$qs       = ! empty( $es_url ) ? "?" . parse_url( $es_url, PHP_URL_QUERY ) : $qs;
		$home_url = home_url( '/' );
		$es_url   = $home_url . $qs;

		return $es_url;
	}

}

function es_sync_registereduser( $user_id ) {

	$es_c_emailsubscribers = get_option( 'ig_es_sync_wp_users', 'norecord' );

	if ( $es_c_emailsubscribers == 'norecord' || $es_c_emailsubscribers == "" ) {
		// No action is required
	} else {
		$es_sync_unserialized_data = maybe_unserialize( $es_c_emailsubscribers );
		if ( ( $es_sync_unserialized_data['es_registered'] == "YES" ) && ( $user_id != "" ) ) {
			$es_registered       = $es_sync_unserialized_data['es_registered'];
			$es_registered_group = $es_sync_unserialized_data['es_registered_group'];

			$user_info      = get_userdata( $user_id );
			$user_firstname = $user_info->user_firstname;

			if ( $user_firstname == "" ) {
				$user_firstname = $user_info->user_login;
			}
			$user_mail = $user_info->user_email;

			$form['es_email_name']   = $user_firstname;
			$form['es_email_mail']   = $user_mail;
			$form['es_email_group']  = $es_registered_group;
			$form['es_email_status'] = "Confirmed";
			$form['es_nonce']        = wp_create_nonce( 'es-subscribe' );
			$action                  = es_cls_dbquery::es_view_subscriber_ins( $form, "insert" );

			if ( $action == "sus" ) {
				//Inserted successfully. Below 3 line of code will send WELCOME email to subscribers.
				$subscribers = array();
				$subscribers = es_cls_dbquery::es_view_subscriber_one( $user_mail, $form['es_email_group'] );
				es_cls_sendmail::es_sendmail( "welcome", $template = 0, $subscribers, "welcome", 0 );
			}
		}
	}
}

class es_widget_register extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'widget_text elp-widget', 'description' => __( ES_PLUGIN_DISPLAY, 'email-subscribers' ), ES_PLUGIN_NAME );
		parent::__construct( ES_PLUGIN_NAME, __( ES_PLUGIN_DISPLAY, 'email-subscribers' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		$es_title = apply_filters( 'widget_title', empty( $instance['es_title'] ) ? '' : $instance['es_title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( ! empty( $es_title ) ) {
			echo $args['before_title'] . $es_title . $args['after_title'];
		}

		// display widget method
		$instance['es_pre'] = 'widget';
		echo es_cls_registerhook::es_get_form( $instance );
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['es_title'] = ( ! empty( $new_instance['es_title'] ) ) ? strip_tags( $new_instance['es_title'] ) : '';
		$instance['es_desc']  = ( ! empty( $new_instance['es_desc'] ) ) ? strip_tags( $new_instance['es_desc'] ) : '';
		$instance['es_name']  = ( ! empty( $new_instance['es_name'] ) ) ? strip_tags( $new_instance['es_name'] ) : '';
		$instance['es_group'] = ( ! empty( $new_instance['es_group'] ) ) ? strip_tags( $new_instance['es_group'] ) : '';

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'es_title' => '',
			'es_desc'  => '',
			'es_name'  => '',
			'es_group' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$es_title = $instance['es_title'];
		$es_desc  = $instance['es_desc'];
		$es_name  = $instance['es_name'];
		$es_group = $instance['es_group'];
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'es_title' ); ?>"><?php echo __( 'Widget Title', 'email-subscribers' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'es_title' ); ?>" name="<?php echo $this->get_field_name( 'es_title' ); ?>" type="text" value="<?php echo $es_title; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'es_desc' ); ?>"><?php echo __( 'Short description about subscription form', 'email-subscribers' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'es_desc' ); ?>" name="<?php echo $this->get_field_name( 'es_desc' ); ?>" type="text" value="<?php echo $es_desc; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'es_name' ); ?>"><?php echo __( 'Display Name Field', 'email-subscribers' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'es_name' ); ?>" name="<?php echo $this->get_field_name( 'es_name' ); ?>">
                <option value="YES" <?php $this->es_selected( $es_name == 'YES' ); ?>><?php echo __( 'YES', 'email-subscribers' ); ?></option>
                <option value="NO" <?php $this->es_selected( $es_name == 'NO' ); ?>><?php echo __( 'NO', 'email-subscribers' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'es_group' ); ?>"><?php echo __( 'Subscriber Group', 'email-subscribers' ); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'es_group' ); ?>" id="<?php echo $this->get_field_id( 'es_group' ); ?>">
				<?php
				$groups = array();
				$groups = es_cls_dbquery::es_view_subscriber_group();
				if ( count( $groups ) > 0 ) {
					$i = 1;
					foreach ( $groups as $group ) {
						?>
                        <option value="<?php echo esc_html( stripslashes( $group["es_email_group"] ) ); ?>" <?php if ( stripslashes( $es_group ) == $group["es_email_group"] ) {
							echo 'selected="selected"';
						} ?>>
							<?php echo stripslashes( $group["es_email_group"] ); ?>
                        </option>
						<?php
					}
				}
				?>
            </select>
        </p>
		<?php
	}

	function es_selected( $var ) {
		if ( $var == 1 || $var == true ) {
			echo 'selected="selected"';
		}
	}
}