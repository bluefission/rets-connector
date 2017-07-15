<?php
/**
 * Rets Connector.
 *
 * @package   Bluefission_Plugin_Admin
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 BlueFission
 */

if ( !class_exists("BlueFission_Plugin_Init") ) {
	die;
}

/*
 Admin:
abstract/api add filter/action functions
abstract/api add form field/option field functions
Create validation class (if ('blah' == type) validate(); )
Create basic form field class
Abstract Develation when available
static set,get "option var" within class, but not parent or child.
Add_meta function. 
include upload logic
*/

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-bluefission-plugin.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Bluefission_Plugin_Admin
 * @author  Your Name <dscott@bluefission.com>
 */
class Bluefission_Plugin_Admin {
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
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instances = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */

	/**
	 * Name of options settings variable
	 * TODO: Abstract this 'effer out!
	 * 
	 * @since	   1.0.0
	 * 
	 * @var      array
	 */	 	 	 	 	 	
	protected $option_var = "";

	protected function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * TODO:
		 *
		 * - Rename "Bluefission_Plugin" to the name of your initial plugin class
		 *
		 */

		$this->init = ( static::$plugin_init ) ? static::$plugin_init : Bluefission_Plugin_Init::get_instance();
		//$this->plugin_slug = ( $this->init ) ? $this->init->plugin_info('slug') : strtolower( preg_replace('/_Admin$/', '', get_class() ) );
		$this->plugin_slug = $this->init->plugin_info('slug');
		$this->option_var =  $this->init->plugin_info('options');
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add plugin options menu
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * TODO:
	 *
	 * - Rename "Bluefission_Plugin" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id) {
			$init = $this->init;
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'admin/assets/css/admin.css', $init->plugin_info('location') ), array(), $init::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * TODO:
	 *
	 * - Rename "Bluefission_Plugin" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id || $screen->id == 'page' ) {
			$init = $this->init;
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'admin/assets/js/admin.js', $init->plugin_info('location') ), array( 'jquery' ), $init::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */

		$this->plugin_screen_hook_suffix = add_options_page(
			__( $this->init->plugin_info('name'), $this->plugin_slug ),
			__( $this->init->plugin_info('name').' Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	// TODO: clean this up with generic names
	public function metabox() {
		add_meta_box( 'meta_information_meta_box',
			'Meta Information',
			array( $this, 'display_meta_box' ),
			'page', 'normal', 'high'
		);
	}

	public function sidebar() {
		global $post;

		$options = get_option( $this->option_var);
		?>
		<div class="postbox">
		<fieldset id="bluefission-plugin" class="dbx-box">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="bluefission-plugin-handle handle"><span><?php _e($this->init->plugin_info('name')); ?></span></h3>
		<div class="bluefission-plugin-content inside">
			<label class="screen-reader-text" for="bluefission-plugin-option">Option</label>
			<input name="bluefission_plugin" type="text" size="20" id="bluefission-plugin-option" placeholder="option" value="<?php echo ""; ?>" />
		</div>

		</fieldset>
		</div>
		<?php
	}

	public function update($post_id) {
		
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( dirname($this->init->plugin_info('location')).'/admin/views/admin.php' );
	}

	public function plugin_admin_section_text() {
		// Pretty much do nothing
	}
	
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}
}
