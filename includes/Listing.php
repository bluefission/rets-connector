<?php 
namespace RetsConnector;

use BlueFission\Net\HTTP;
use BlueFission\DevString;
use BlueFission\HTML\Template;

require_once ( plugin_dir_path( __FILE__ ) . 'WPUpdateable.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'RSVP.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'Theme.php' );

class Listing extends WPUpdateable {
	const CONFIG_META = '_listing_data';

	protected $_config = array(
	);

	protected $_data = array(
		'address'=>'',
		'title'=>'',
		'description'=>'',
		'price'=>'',
		'sqft'=>'',
	);
	
	public function load() {
		// Get this card to also load in related gravity form data
		if ( parent::load() ) {

			// $config_meta = self::CONFIG_META;

			$config = get_post_meta( $this->_post_id, self::CONFIG_META, true );
			
			if ( is_array($config) ) {
				$this->config($config);
			}

			if ( !is_numeric($this->mls_id) ) {
				$this->mls_id = get_post_custom_values( 'mls_id' );
			}

			$mls_id = $this->mls_id;
		}
	}

	private function prepare() {
		// Generate Address
		// Place mapping here
	}

	public function save() {
		$status = parent::save();

		$status = update_post_meta($this->_post_id, self::CONFIG_META, $this->_config);

		return $status;
	}

	public function get_data() {
		return $this->_data;
	}
}