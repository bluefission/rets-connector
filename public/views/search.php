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
?>
<div>   
    <h3>Search Listings</h3>
    <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform">
    <input type="text" name="s" placeholder="Search Listings"/>
    <input type="hidden" name="post_type" value="listing" /> <!-- // hidden 'listing' value -->
    <input type="submit" alt="Search" value="Search" />
  </form>
 </div>