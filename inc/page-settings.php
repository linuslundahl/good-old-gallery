<?php

register_activation_hook( __FILE__, 'goodold_gallery_default_settings' );
add_action( 'admin_init', 'goodold_gallery_settings_init' );
add_action( 'admin_menu', 'goodold_gallery_add_settings_page' );

/**
 * Save default settings.
 */
function goodold_gallery_default_settings() {
	global $gog_settings, $gog_default_settings;

	if (!is_array($gog_settings)) {
		update_option( GOG_PLUGIN_SHORT . '_settings', $gog_default_settings );
		$gog_settings = $gog_default_settings;
	}
}

/**
 * Register form fields.
 */
function goodold_gallery_settings_init(){
	global $gog_settings;

	register_setting( GOG_PLUGIN_SHORT . '_settings', GOG_PLUGIN_SHORT . '_settings', 'goodold_gallery_settings_validate' );

	add_settings_section( GOG_PLUGIN_SHORT . '_settings', 'Settings', 'goodold_gallery_settings_header', __FILE__ );

	add_settings_field( 'size', '<label for="size">Size</label>', 'goodold_gallery_setting_dropdown', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'size', 'items' => array('thumbnail' => 'Thumbnail', 'medium' => 'Medium', 'large' => 'Large', 'full' => 'Full'), 'desc' => 'Select what image size the galleries should use.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['size']) );
	add_settings_field( 'fx', '<label for="fx">Transition animation</label>', 'goodold_gallery_setting_dropdown', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'fx', 'items' => array('fade' => 'Fade', 'scrollHorz' => 'Horizontal scroll', 'scrollVert' => 'Vertical scroll', 'none' => 'None (Standard WP gallery)'), 'desc' => 'Animation that should be used.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['fx']) );
	add_settings_field( 'speed', '<label for="speed">Speed</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'speed', 'desc' => 'milliseconds', 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['speed']) );
	add_settings_field( 'timeout', '<label for="timeout">Transition speed</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'timeout', 'desc' => 'milliseconds', 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['timeout']) );
	add_settings_field( 'title', 'Show title', 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'title', 'label' => 'Select if the title for each image should be displayed.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['title']) );
	add_settings_field( 'description', 'Show description', 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'description', 'label' => 'Select if the description for each image should be displayed.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['description']) );
	add_settings_field( 'pager', 'Show pager', 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'pager', 'label' => 'Select if the pager should be displayed.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['pager']) );
	add_settings_field( 'navigation', 'Show navigation', 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'navigation', 'label' => 'Select if you would like to add PREV and NEXT buttons.', 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['navigation']) );
	add_settings_field( 'prev', '<label for="prev">Prev</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'prev', 'desc' => 'Text used for prev button.', 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['prev']) );
	add_settings_field( 'next', '<label for="next">Next</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'next', 'desc' => 'Text used for next button.', 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['next']) );
}

/**
 * Add settings page.
 */
function goodold_gallery_add_settings_page() {
	add_submenu_page( 'edit.php?post_type=goodoldgallery', GOG_PLUGIN_NAME . ' settings', 'Settings', 'administrator', GOG_PLUGIN_SHORT . '_settings', 'goodold_gallery_settings_page' );
}

/**
 * Main section header.
 */
function goodold_gallery_settings_header() {
}

/**
 * Print settings page
 */
function goodold_gallery_settings_page() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo GOG_PLUGIN_NAME; ?> settings</h2>
		<?php goodold_gallery_error_handling(); // Needed, because for some reason add_settings_error() isn't registered when a settings page is added outside of options-general.php ?>
		<div class="go-flattr"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/good-old-gallery/"></a></div>
		<p>These settings are the defaults that will be used by Cycle (if an animation is chosen), settings can be overridden by adding variables to the <code>[good-old-gallery]</code> shortcode.<br />
		Use the built in generator found under the <em><?php echo GOG_PLUGIN_NAME; ?></em> tab, just click the <em>Add an Image</em> button to find the tab on the far right.<br /><br />
		More help can be found at <a href="http://wordpress.org/extend/plugins/good-old-gallery/installation/">wordpress.org</a><br /><br />
		<?php echo GOG_PLUGIN_NAME; ?> created and maintained by <a href="http://unwi.se/">Linus Lundahl</a>.</p>
		<div class="tip"><strong>Tip!</strong> You can use <?php echo GOG_PLUGIN_NAME; ?> with regular galleries that you have uploaded on a page/post, just don't enter any id in the shortcode and it will look for a gallery attached to the current page/post.</div>
		<form action="options.php" method="post" id="go-settings-form">
		<?php settings_fields( GOG_PLUGIN_SHORT . '_settings' ); ?>
		<?php do_settings_sections( __FILE__ ); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

function goodold_gallery_settings_validate($input) {
	$input['speed'] = is_numeric($input['speed']) ? $input['speed'] : '';
	$input['timeout'] = is_numeric($input['timeout']) ? $input['timeout'] : '';
	return $input;
}
