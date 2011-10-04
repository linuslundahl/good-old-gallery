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

	add_settings_field( 'size', '<label for="size">' . __( 'Size' ) . '</label>', 'goodold_gallery_setting_dropdown', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'size', 'items' => array('thumbnail' => __( 'Thumbnail' ), 'medium' => __( 'Medium' ), 'large' => __( 'Large' ), 'full' => __( 'Full' )), 'desc' => __( 'Select what image size the galleries should use.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['size']) );
	add_settings_field( 'fx', '<label for="fx">' . __( 'Transition animation' ) . '</label>', 'goodold_gallery_setting_dropdown', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'fx', 'items' => array('fade' => __( 'Fade' ), 'scrollHorz' => __( 'Horizontal scroll' ), 'scrollVert' => __( 'Vertical scroll' ), 'none' => __( 'None (Standard WP gallery)' )), 'desc' => __( 'Animation that should be used.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['fx']) );
	add_settings_field( 'timeout', '<label for="timeout">' . __( 'Transition speed' ) .'</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'timeout', 'desc' => 'milliseconds', 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['timeout']) );
	add_settings_field( 'speed', '<label for="speed">' . __( 'Animation speed' ) . '</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'speed', 'desc' => __( 'milliseconds' ), 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['speed']) );
	add_settings_field( 'title', __( 'Show title' ), 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'title', 'label' => __( 'Select if the title for each image should be displayed.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['title']) );
	add_settings_field( 'description', __( 'Show description' ), 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'description', 'label' => __( 'Select if the description for each image should be displayed.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['description']) );
	add_settings_field( 'pager', __( 'Show pager' ), 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'pager', 'label' => __( 'Select if the pager should be displayed.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['pager']) );
	add_settings_field( 'navigation', __( 'Show navigation' ), 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'navigation', 'label' => __( 'Select if you would like to add PREV and NEXT buttons.' ), 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['navigation']) );
	add_settings_field( 'prev', '<label for="prev">' . __( 'Prev' ) . '</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'prev', 'desc' => __( 'Text used for prev button.' ), 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['prev']) );
	add_settings_field( 'next', '<label for="next">' . __( 'Next' ) . '</label>', 'goodold_gallery_setting_text', __FILE__, GOG_PLUGIN_SHORT . '_settings', array('id' => 'next', 'desc' => __( 'Text used for next button.' ), 'size' => 4, 'name' => GOG_PLUGIN_SHORT . '_settings', 'default' => $gog_settings['next']) );
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
		<div class="go-flattr"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/good-old-gallery/"></a></div>
		<p><?php echo sprintf(__( 'These settings are the defaults that will be used by Cycle (if an animation is chosen), settings can be overridden by adding variables to the %s shortcode.' ), '<code>[good-old-gallery]</code>'); ?><br />
		<?php echo sprintf(__( 'Use the built in generator found under the %s tab, just click the %s button to find the tab on the far right.' ), '<em>' . GOG_PLUGIN_NAME . '</em>', '<em>' . __( 'Add an Image' ) . '</em>'); ?><br /><br />
		<?php echo sprintf(__( 'More help can be found at %s' ), '<a href="http://wordpress.org/extend/plugins/good-old-gallery/installation/">wordpress.org</a>'); ?><br /><br />
		<?php echo sprintf(__( '%s created and maintained by %s.' ), GOG_PLUGIN_NAME, '<a href="http://unwi.se/">Linus Lundahl</a>'); ?></p>
		<div class="tip"><strong><?php echo __( 'Tip' ); ?>!</strong> <?php echo sprintf(__( "You can use %s with regular galleries that you have uploaded on a page/post, just don't enter any id in the shortcode and it will look for a gallery attached to the current page/post."), GOG_PLUGIN_NAME); ?></div>
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
