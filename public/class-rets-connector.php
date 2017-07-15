<?php
/**
 * Rets Connector.
 *
 * @package   Rets_Connector
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2017 BlueFission, LLC
 */

if ( !class_exists("BlueFission_Plugin") ) {
	require_once( plugin_dir_path( __FILE__ ) . '../includes/class-bluefission-plugin.php');
}

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-rets-connector-admin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Rets_Connector
 * @author  Devon Scott <dscott@bluefission.com>
 */
class Rets_Connector extends BlueFission_Plugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * TODO - Rename "rets-connector" to the name your your plugin
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
	protected $plugin_slug = 'rets-connector';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	protected function __construct() {
		parent::__construct();
		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'the_content', array( $this, 'content' ), 12 );
  		add_filter( 'get_the_content', array( $this, 'content' ), 12 );
  		
  		add_shortcode( 'bluefission_shortcode', array( $this, 'shortcode') );

		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
	}

	public function shortcode( $atts ) {
		$a = shortcode_atts( array(
			'foo' => 'something',
			'bar' => 'something else',
		), $atts );

		$content = "Magical Shortcode!";

		return $content;
	}

	public function register_post_type() {
		$args = array(
			'description' => __( static::$plugin_init->plugin_info('name') ),
			'show_ui' => true,
			'menu_position' => 4,
			'menu_icon' => 'dashicons-exerpt-view',
			'exclude_from_search' => true,
			'labels' => array(
				'name'=> __( 'Listings'),
				'singular_name' => __( static::$plugin_init->plugin_info('name') ),
				'add_new' => __( 'Add New Listing' ),
				'add_new_item' => __( 'Add New Listing' ),
				'edit' => __( 'Edit Listing' ),
				'edit_item' => __( 'Edit Listing' ),
				'new-item' => __( 'New Listing' ),
				'view' => __( 'View Listing' ),
				'view_item' => __( 'View Listing' ),
				'search_items' => __( 'Search Listings' ),
				'not_found' => __( 'No Listings Found' ),
				'not_found_in_trash' => __( 'No Listings Found in Trash' ),
				'parent' => ''
				),
			'public' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array('slug' => 'listings'),
			'supports' => array('title', 'editor', 'thumbnail')
		);
		register_post_type( $this->plugin_slug, $args );
		/*
		register_taxonomy($this->plugin_slug.'-category',
			$this->plugin_slug,
			array (
				'labels' => array (
					'name' => static::$plugin_init->plugin_info('name').' Categories',
					'singular_name' => 'Category',
					'search_items' => 'Search Categories',
					'popular_items' => 'Popular Categories',
					'all_items' => 'All Categories',
					'parent_item' => 'Parent Category',
					'parent_item_colon' => 'Parent Category:',
					'edit_item' => 'Edit Category',
					'update_item' => 'Update Category',
					'add_new_item' => 'Add New Category',
					'new_item_name' => 'New Category',
				),
				'hierarchical' =>true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'rewrite' => false,
				'public'=>true
			)
		);
		*/
	}

	public function load_properties() {
		$connector = new RetsConnector();

		$connector->connect($url, $username, $password);
		$listings = $connector->properties();
		foreach ($listings as $listing) {
			$post = array(
				'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
				'post_content'  => ,
				'post_status'   => 'publish',
				'post_author'   => 1,
			}
			$id = wp_insert_post($post);

			$data = RetsConnector\Listing();
			$data->setID($id);
			$data->set($listing);
			$data->save();
		}
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}