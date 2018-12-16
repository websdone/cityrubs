<?php

class GhostPool_Setup {

	/**
	 * @var GhostPool_Setup The reference to *GhostPool_Setup* instance of this class
	 */
	protected static $_instance = null;

	public $slug = 'aardvark-setup';

	public function __construct() {
		$this->set_hooks();
		$this->load_dependencies();
	}

	/**
	 * Returns the GhostPool_Setup instance of this class.
	 *
	 * @return GhostPool_Setup - Main instance
	 */
	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function load_dependencies() {
		require_once( get_template_directory() . '/lib/framework/theme-setup/theme-addons.php' );
		if ( ! class_exists( 'GhostPool_Importer' ) ) {
			require_once( get_template_directory() . '/lib/framework/importer/import.php' );
		}
	}

	public function set_hooks() {

		add_action( 'admin_menu', array( $this, 'register_setup_page' ) );
		add_action( 'admin_init', array( $this, 'redirect_to_setup' ), 0 );

		if ( isset( $_GET['page'] ) && $_GET['page'] == $this->slug OR ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'ghostpool_do_plugin_action' ) ) {
			add_action( 'admin_init', array( $this, 'config_addons' ), 12 );
		}
		
		if ( ( isset( $_GET['page'] ) && $_GET['page'] == $this->slug ) OR ( isset( $_GET['page'] ) && $_GET['page'] == 'ghostpool-importer' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_scripts' ) );
		}
		
	}

	/**
	 * Register CSS & JS Files
	 */
	function setup_scripts() {
		wp_enqueue_style( 'theme-setup', get_template_directory_uri() . '/lib/framework/css/theme-setup.css', array() );
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_enqueue_script( 'theme-setup', get_template_directory_uri() . '/lib/framework/scripts/theme-setup.js', array( 'jquery' ) );
	}

	public function register_setup_page() {
		add_theme_page(
			esc_html__( 'Aardvark Setup', 'aardvark' ),
			esc_html__( 'Aardvark Setup', 'aardvark' ),
			'manage_options',
			$this->slug,
			array( $this, 'setup_page' )
		);
	}

	function setup_page() {
		require( get_template_directory() . '/lib/framework/theme-setup/welcome.php' );
	}

	public function redirect_to_setup() {
		// Theme activation redirect
		global $pagenow;
		if ( isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
			wp_redirect( admin_url( 'themes.php?page=aardvark-setup' ) );
			exit;
		}
	}

	public function config_addons() {
	
		GhostPool_Addons_Manager()->plugins = array( 'events-manager' => GhostPool_Addons_Manager()->plugins['events-manager'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'woocommerce' => GhostPool_Addons_Manager()->plugins['woocommerce'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'paid-memberships-pro' => GhostPool_Addons_Manager()->plugins['paid-memberships-pro'] ) + GhostPool_Addons_Manager()->plugins;		
		GhostPool_Addons_Manager()->plugins = array( 'bbpress' => GhostPool_Addons_Manager()->plugins['bbpress'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'buddypress' => GhostPool_Addons_Manager()->plugins['buddypress'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'theia-sticky-sidebar' => GhostPool_Addons_Manager()->plugins['theia-sticky-sidebar'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'LayerSlider' => GhostPool_Addons_Manager()->plugins['LayerSlider'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'vc_responsive_design' => GhostPool_Addons_Manager()->plugins['vc_responsive_design'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'js_composer' => GhostPool_Addons_Manager()->plugins['js_composer'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'envato-market' => GhostPool_Addons_Manager()->plugins['envato-market'] ) + GhostPool_Addons_Manager()->plugins;
		GhostPool_Addons_Manager()->plugins = array( 'aardvark-plugin' => GhostPool_Addons_Manager()->plugins['aardvark-plugin'] ) + GhostPool_Addons_Manager()->plugins;

		$prepend = array(
			'aardvark-child' => array(
				'addon_type'  => 'child_theme',
				'name'        => 'Aardvark Child Theme',
				'slug'        => 'aardvark-child',
				'source'      => get_template_directory() . '/lib/plugins/aardvark-child.zip',
				'source_type' => 'bundled',
				'version'     => '1.0',
				'required'    => true,
				'description' => esc_html__( 'Always activate the child theme to safely update Aardvark.', 'aardvark' ),
			)
		);

		GhostPool_Addons_Manager()->plugins = $prepend + GhostPool_Addons_Manager()->plugins;
	}

}

GhostPool_Setup::getInstance();