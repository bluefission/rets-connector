<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Rets_Connector
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2017 BlueFission, LLC
 */
$option_var =  $this->init->plugin_info('options');

$options = get_option($option_var);
$template = $options['bfrc_listing_template'];
?>

<!-- This file is used to markup the public facing aspect of the plugin. -->

There will be an option for an html template here.