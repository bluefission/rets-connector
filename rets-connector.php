<?php
/**
 * The BlueFission WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Bluefission_Plugin
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 BlueFission
 *
 * @wordpress-plugin
 * Plugin Name:       Rets Connector
 * Plugin URI:        http://bluefission.com
 * Description:       Connects Wordpress to authorized IDX feed
 * Version:           1.0.0
 * Author:            Devon Scott
 * Author URI:        http://bluefission.com
 * Text Domain:       bluefission-plugin-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */


function rets_connector() {
	require_once( plugin_dir_path( __FILE__ ) . 'class-rets-connector-init.php');
}

register_activation_hook(__FILE__, 'rets_activation');
register_deactivation_hook(__FILE__, 'rets_deactivation');

function rets_activation() {
    if (! wp_next_scheduled ( 'update_rets_listings' )) {
		wp_schedule_event(time(), 'twicedaily', 'update_rets_listings');
    }
}

function rets_deactivation() {
   $timestamp = wp_next_scheduled( 'update_rets_listings' );
   wp_unschedule_event( $timestamp, 'update_rets_listings' );
}

add_action('my_hourly_event', 'do_this_hourly');

function do_this_hourly() {
	// do something every hour
}

add_action( 'plugins_loaded', 'rets_connector' );
