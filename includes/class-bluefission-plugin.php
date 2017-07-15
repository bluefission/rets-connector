<?php
/**
 * Rets Connector.
 *
 * @package   Bluefission_Plugin
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2015 BlueFission
 */

if ( !class_exists("BlueFission_Plugin_Init") ) {
	die;
}

/*
 Public:
Add "shortcode" base method
create flag for "enqueue if needed"
Add rewrite flush to base
*/

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-bluefission-plugin-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Bluefission_Plugin
 * @author  Your Name <dscott@bluefission.com>
 */
class Bluefission_Plugin {

	/**
	 * TODO - Rename "bluefission-plugin" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'bluefission-plugin';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instances = array();
	
	/**
	 * Instance of the init class for this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $plugin_init = null;

	protected $init = null;
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	protected function __construct() {
		$this->init = ( static::$plugin_init ) ? static::$plugin_init : Bluefission_Plugin_Init::get_instance();
		//$this->plugin_slug = ( $this->init ) ? $this->init->plugin_info('slug') : strtolower( preg_replace('/_Admin$/', '', get_class() ) );
		$this->plugin_slug = $this->init->plugin_info('slug');
		
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		
		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Set the instance of the plugin controller.
	 *
	 * @since    1.0.0
	 *
	 */
	public static function set_init( $plugin_init ) {
		static::$plugin_init = $plugin_init;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		$called_class = get_called_class();
		// If the single instance hasn't been set, set it now.
		if ( !isset(static::$instances[$called_class]) ) {
			static::$instances[$called_class] = new $called_class();
		}

		return static::$instances[$called_class];
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = static::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					static::single_activate();
				}

				restore_current_blog();

			} else {
				static::single_activate();
			}

		} else {
			static::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = static::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					static::single_deactivate();

				}

				restore_current_blog();

			} else {
				static::single_deactivate();
			}

		} else {
			static::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		static::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	protected static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	protected static function single_activate() {
		
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	protected static function single_deactivate() {
		
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$init = $this->init;
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( $init->plugin_info('location') ) ) ) . 'public/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$init = $this->init;
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'public/assets/css/public.css', $init->plugin_info('location') ), array(), $init::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$init = $this->init;
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'public/assets/js/public.js', $init->plugin_info('location') ), array( 'jquery' ), $init::VERSION );
	}
}