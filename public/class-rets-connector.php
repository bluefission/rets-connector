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
  // 		add_filter( 'get_the_content', array( $this, 'content' ), 12 );
  		
  		add_shortcode( 'bluefission_shortcode', array( $this, 'shortcode') );
  		add_shortcode( 'rets_custom_list', array( $this, 'custom_list') );
  		add_shortcode( 'rets_show_property', array( $this, 'custom_list') );

		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
	}

	public function shortcode( $atts ) {
		$a = shortcode_atts( array(
			'foo' => 'something',
			'bar' => 'something else',
		), $atts );

		$this->load_properties();

		$content = "Magical Shortcode!";

		return $content;
	}

	public function custom_list( $atts ) {
		$a = shortcode_atts( array(
			'field' => 'mls_id',
			'value' => '1000000',
		), $atts );

		// $this->load_properties();
		$content = "";
		$args = array(
		   'meta_query' => array(
		       array(
		           'key' => $shortcode_atts['mls_id'],
		           'value' => $shortcode_atts['value'],
		           'compare' => '=',
		       )
		   )
		);
		$query = new \WP_Query($args);
		if ( $query->have_posts() ) {
			// The 2nd Loop
			while ( $query->have_posts() ) {
				$query->the_post();
				// echo '<li>' . get_the_title( $query->post->ID ) . '</li>';
				$content .= "Place listing.php output here";
				$this->_post_id = $query->post->ID;
			}

			// Restore original Post Data
			wp_reset_postdata();
		}

		return $content;
	}

	public function show_property( $atts ) {
		$a = shortcode_atts( array(
			'field' => 'mls_id',
			'value' => '1000000',
		), $atts );

		// $this->load_properties();
		$content = "";
		$args = array(
		   'meta_query' => array(
		       array(
		           'key' => $shortcode_atts['mls_id'],
		           'value' => $shortcode_atts['value'],
		           'compare' => '=',
		       )
		   )
		);
		$query = new \WP_Query($args);
		if ( $query->have_posts() ) {
			// The 2nd Loop
			while ( $query->have_posts() ) {
				$query->the_post();
				// echo '<li>' . get_the_title( $query->post->ID ) . '</li>';
				$this->_post_id = $query->post->ID;
				$theme = new \BlueFission\HTML\Template();
				$theme->contents($template);
				$data = new BlueFission\Rets\Listing();
				$data->setID( $query->post->ID);
				$data->load();
				$theme->set($data->get_data());
				$content = $theme->render();
				$content = do_shortcode($content);
				break;
			}

			// Restore original Post Data
			wp_reset_postdata();
		}

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
			'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
		);
		register_post_type( 'listing', $args );
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
		$option_var =  $this->init->plugin_info('options');
		$options = get_option($option_var);

		$url = $options['bfrc_url'];
		$username = $options['bfrc_username'];
		$password = $options['bfrc_password'];
		$mapping = $options['bfrc_mapping'];

		$map_r = explode("\n", $mapping);

		$final_map = array();

		foreach ($map_r as $map) {
			$values = explode(":", $map);
			$final_map[trim($values[0])] = isset($values[1]) ? trim($values[1]) : null;
		}
		$connector = new BlueFission\Rets\RetsConnector();
		$connector->connect($url, $username, $password);
		$listings = $connector->properties();
		foreach ($listings as $listing) {
			$data = new BlueFission\Rets\Listing();
			$data->mapping($final_map);
			$data->set($listing);
			$images = $connector->media($data->mls_id);
			$photos = array();
			foreach ($images as $image) {
				$photos[] = $image->getLocation();
			}
			$data->photos = serialize($photos);
			// die(var_dump($data->photos));
			$data->save();
		}
	}

	public function content($content) {
		$post_type = get_post_type();
		if ( $post_type == 'listing') {
			include( plugin_dir_path( __FILE__ ) . 'views/display.php');
			// $content = $template;
			$theme = new \BlueFission\HTML\Template();
			$theme->contents($template);
			$data = new BlueFission\Rets\Listing();
			$data->setID();
			$data->load();
			$theme->set($data->get_data());
			$content = $theme->render();
			$content = do_shortcode($content);
		}

		return $content;
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