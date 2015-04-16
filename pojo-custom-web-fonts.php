<?php
/*
Plugin Name: Pojo Custom Web Fonts
Plugin URI: http://pojo.me/
Description: ...
Author: Pojo Team
Author URI: http://pojo.me/
Version: 1.0.0
Text Domain: pojo-cwf
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POJO_CWF__FILE__', __FILE__ );
define( 'POJO_CWF_BASE', plugin_basename( POJO_CWF__FILE__ ) );

final class Pojo_CWF_Main {

	/**
	 * @var Pojo_CWF_Main The one true Pojo_CWF_Main
	 * @since 1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var Pojo_CWF_Admin_UI
	 */
	public $admin_ui;

	/**
	 * @var Pojo_CWF_DB
	 */
	public $db;

	public function load_textdomain() {
		load_plugin_textdomain( 'pojo-cwf', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-cwf' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-cwf' ), '1.0.0' );
	}

	/**cd
	 * @return Pojo_CWF_Main
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new Pojo_CWF_Main();
		}
		return self::$_instance;
	}
	
	public function bootstrap() {
		include( 'includes/class-pojo-cwf-db.php' );
		include( 'includes/class-pojo-cwf-admin-ui.php' );

		$this->db        = new Pojo_CWF_DB();
		$this->admin_ui  = new Pojo_CWF_Admin_UI();
	}
	
	private function __construct() {
		add_action( 'init', array( &$this, 'bootstrap' ) );
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
	}

}

Pojo_CWF_Main::instance();