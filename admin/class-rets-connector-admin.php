<?php
/**
 * Rets Connector.
 *
 * @package   Rets_Connector_Admin
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2017 BlueFission, LLC
 */

if ( !class_exists("BlueFission_Plugin_Admin") ) {
	require_once( plugin_dir_path( __FILE__ ) . '../includes/class-bluefission-plugin-admin.php');
}
/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-rets-connector.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Rets_Connector_Admin
 * @author  Devon Scott <dscott@bluefission.com>
 */
class Rets_Connector_Admin extends BlueFission_Plugin_Admin {

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	protected function __construct() {
		parent::__construct();

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * TODO:
		 *
		 * - Rename "Rets_Connector" to the name of your initial plugin class
		 *
		 */

		// Add the options page and menu item.
		add_action( 'admin_init', array( $this, 'plugin_admin_init' ) );
		add_action( 'admin_init', array( $this, 'metabox' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_filter( 'default_content', array( $this, 'prepare_new_content' ), 10, 2 );

		add_action( 'save_post', array($this, 'add_post_fields'), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'custom_post_order') );

		add_action( "manage_{$this->plugin_slug}_posts_custom_column", array( $this, 'custom_columns') );
		add_filter( "manage_edit-{$this->plugin_slug}_columns", array( $this, 'edit_columns') );


		add_action( 'TODO', array( $this, 'action_method_name' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
	}


	public function plugin_admin_init() {
		register_setting( $this->plugin_slug . '-options', $this->option_var, array($this, 'plugin_admin_validate') );
		add_settings_section($this->plugin_slug . '-main', static::$plugin_init->plugin_info('name').' Settings', array($this, 'plugin_admin_section_text'), $this->plugin_slug);
		add_settings_field('plugin_admin_setting_1', 'Setting One', array($this, 'plugin_admin_field_0'), $this->plugin_slug, $this->plugin_slug . '-main');
		add_settings_field('plugin_admin_setting_2', 'Setting Two', array($this, 'plugin_admin_field_1'), $this->plugin_slug, $this->plugin_slug . '-main');
	}

	public function plugin_admin_field_0() {
		$options = get_option($this->option_var);
		$var = $options['var1'];
		
		echo "<input id='plugin_admin_include_directory' name='" . $this->option_var . "[var2]' type='text' value='{$var}' />";	
	}
	
	public function plugin_admin_field_1() {
		$options = get_option($this->option_var);
		$var = $options['var2'];
		
		echo "<textarea id='plugin_admin_file_filter' name='" . $this->option_var . "[var2]'>{$var}</textarea>";	
	}

	// validate our options
	public function plugin_admin_validate($input) {
		
		if( $input['var1'] == '' ) {
			$input['var1'] = '';
		}

		if( $input['var2'] == '' ) {
			$input['var2'] = '';
		}
		return $input;
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
		<h3 class="bluefission-plugin-handle handle"><span><?php _e(static::$plugin_init->plugin_info('name')); ?></span></h3>
		<div class="bluefission-plugin-content inside">
			<label class="screen-reader-text" for="bluefission-plugin-option">Option</label>
			<input name="bluefission_plugin" type="text" size="20" id="bluefission-plugin-option" placeholder="option" value="<?php echo ""; ?>" />
		</div>

		</fieldset>
		</div>
		<?php
	}

	public function update($pID) {
		
	}

	public function display_meta_box( $post ) {
		$meta_name = esc_attr( get_post_meta( $post->ID, 'meta_name', true ) );
		?>
		<table width="100%" style="width: 100%">
			<tr>
				<td style="width: 20%">Meta Option</td>
				<td><input style="width: 100%" type="text" name="site_meta_name" placeholder="Enter your value here" value="<?php echo $meta_name ; ?>" /></td>
			</tr>
		</table>
		<?php
	}

	// TODO: Clean this up to make it generic
	public function add_post_fields( $post_id ) {
		// Check post type
		$type = get_post_type( $post_id );
		if ( $type == 'page' ) {
			// Store data in post meta table if present in post data
			if ( isset( $_POST['site_meta_name'] ) ) {
				update_post_meta( $post_id, 'meta_name', htmlentities($_POST['site_meta_name']) );
			}
		}
	}

	// custom fields
 	public function custom_post_order($query) {
	    /* 
	        Set post types.
	        _builtin => true returns WordPress default post types. 
	        _builtin => false returns custom registered post types. 
	    */
	    $post_types = get_post_types(array('_builtin' => false), 'names');
	    $custom_post_type = $this->plugin_slug;
	    /* The current post type. */
	    $post_type = $query->get('post_type');
	    /* Check post types. */
	    if(in_array($post_type, $post_types)){
	        /* Post Column: e.g. title */
	        if($query->get('orderby') == ''){
	            $query->set('orderby', 'title');
	        }
	        /* Post Order: ASC / DESC */
	        if($query->get('order') == ''){
	            $query->set('order', 'ASC');
	        }
	    }
	}

	public function order_columns( $vars ) {
		if ( !isset( $vars['orderby'] ) || ( isset( $vars['orderby'] ) && 'title' == $vars['orderby'] ) ) {
			$vars = array_merge( $vars, array(
				'meta_key' => 'title',
				//'orderby' => 'meta_value_num', // does not work
				'orderby' => 'meta_value'
				//'order' => 'asc' // don't use this; blocks toggle UI
			) );
		}
		return $vars;
	}
	public function edit_columns($columns){
		$columns = array(
			"cb" => "<input type='checkbox' />",
			"photo" => __("Image"),
			"title" => __("Quote"),
			"date" => __("Date")
		);

		return $columns;
	}

	public function custom_columns($column){
		global $post;
		switch ($column){
			case "photo":
				if(has_post_thumbnail()) the_post_thumbnail(array(50,50));
			break;
		}
	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
