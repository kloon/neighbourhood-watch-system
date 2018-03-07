<?php
/*
Plugin Name: Neighbourhood Watch System
Description: A system for managing a neighbourhood watch.
Version:     1.0.0
Author:      Gerhard Potgieter
Author URI:  http://gerhardpotgieter.com/
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NHW_SYSTEM_VERSION', '1.0.0' );

if ( ! class_exists( 'Neighbourhood_Watch_System' ) ) :

	class Neighbourhood_Watch_System {

		protected static $_instance = null;
		public $residents = null;
		public $export = null;
		public $sms = null;
		public $shortcodes = null;
		public $patrol_schedule_generator = null;

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
			$this->includes();
		} // end __construct()

		/**
		 * Add top level menu
		 * @return void
		 */
		public function admin_menu() {
			add_menu_page( __( 'FernPark' ), __( 'FernPark' ), 'edit_posts', 'nhw-system', null, 'dashicons-visibility', '4.1' );
		}

		/**
		 * Get the single instance of the plugin
		 * @return object Neighbourhood_Watch_System
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		} // end get_instance()

		/**
		 * Include all the files and initiate the objects
		 * @return void
		 */
		public function includes() {
			// Global functions
			require_once( plugin_dir_path( __FILE__ ) . 'includes/nws-functions.php' );

			// Member functionality
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-residents.php' );
			$this->residents = NWS_Residents::get_instance();

			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-sms.php' );
			$this->sms = new NWS_SMS();

			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-export.php' );
			$this->export = new NWS_Export();

			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-shortcodes.php' );
			$this->shortcodes = new NWS_Shortcodes();

			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-adverts.php' );
			$this->adverts = new NWS_Adverts();
			//print_r( NWS_SMS::retrieve_balance() );
			//print_r( NWS_SMS::send_message( array( '27731500203' ), 'Testing SMS message from REST API' ) );
			//
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-nws-patrol-schedule-generator.php' );
			$this->patrol_schedule_generator = new NWS_Patrol_Schedule_Generator();
		} // End includes()

		/**
		 * Enqueue the admin stylesheet
		 * @return void
		 */
		public function enqueue_admin_scripts() {
			wp_register_style( 'nhw-system-css', plugin_dir_url( __FILE__ ) . '/assets/admin/admin-style.css', false, NHW_SYSTEM_VERSION );
        	wp_enqueue_style( 'nhw-system-css' );
		} // End enqueue_admin_scripts()

		/**
		 * Enqueue fronent scripts and styles
		 * @return void
		 */
		public function enqueue_frontend_scripts() {
			wp_register_style( 'flexslider', plugin_dir_url( __FILE__ ) . 'libs/flexslider/flexslider.css', false, '2.6.0' );
			wp_register_script( 'flexslider', plugin_dir_url( __FILE__ ) . 'libs/flexslider/jquery.flexslider-min.js', array( 'jquery' ), '2.6.0' );
			wp_enqueue_style( 'flexslider' );
			wp_enqueue_script( 'flexslider' );
		} // End enqueue_frontend_scripts()
	}

endif;

add_action( 'plugins_loaded', array( 'Neighbourhood_Watch_System', 'get_instance' ), 0 );