<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Peform includes
if ( !class_exists("BlueFission_Plugin_Init") ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bluefission-plugin-init.php');
}
$autoloader = require 'vendor/autoload.php';

require_once( plugin_dir_path( __FILE__ ) . 'includes/RetsConnector.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/Listing.php');

// TODO: Create a plugin class factory. It's kind of important
// Pseudo-namespace our dynamic class, or else things can become impossible!!!!!
class Rets_Connector_Init extends BlueFission_Plugin_Init {
	protected $plugin_location = __FILE__;
	const VERSION = '1.0.0';
	protected $plugin_name = 'Rets Connector'; // Change this, then magic

	protected function __construct() {
		parent::__construct();
		$this->require_version('1.0.0');
	}
}

Rets_Connector_Init::get_instance();