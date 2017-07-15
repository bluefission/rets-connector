<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Rets_Connector
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2017 BlueFission, LLC
 */
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form method="post" action="options.php">
	<?php settings_fields( $this->plugin_slug . '-options' ); ?>
	<?php do_settings_sections( $this->plugin_slug ); ?>
 
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	<!-- TODO: Provide markup for your options page here. -->
	
</div>