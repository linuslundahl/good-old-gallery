<?php

register_activation_hook( __FILE__, 'goodold_gallery_default_themes' );
add_action( 'admin_init', 'goodold_gallery_themes_init' );
add_action( 'admin_menu', 'goodold_gallery_add_themes_page' );

/**
 * Save default settings.
 */
function goodold_gallery_default_themes() {
	global $gog_themes, $gog_default_themes;

	if (!is_array($gog_themes)) {
		update_option( GOG_PLUGIN_SHORT . '_themes', $gog_default_themes );
		$gog_themes = $gog_default_themes;
	}
}

/**
 * Register form fields.
 */
function goodold_gallery_themes_init(){
	global $gog_themes;

	register_setting( GOG_PLUGIN_SHORT . '_themes', GOG_PLUGIN_SHORT . '_themes', 'goodold_gallery_themes_validate' );

	$themes_active = FALSE;
	$themes_extra = "";
	if ($gog_themes['all']) {
		$themes_active = TRUE;
		$themes_extra = " *";
	}

	// Get themes for select dropdown
	$theme_options = array('' => 'No theme');
	$theme_options += goodold_gallery_get_themes( true );

	if ($theme_options) {
		add_settings_section( GOG_PLUGIN_SHORT . '_themes', 'Themes', 'goodold_gallery_themes_header', __FILE__ );
		add_settings_field( 'default', '<label for="default">Theme</label>', 'goodold_gallery_setting_dropdown', __FILE__, GOG_PLUGIN_SHORT . '_themes', array('id' => 'default', 'items' => $theme_options, 'desc' => 'Select the default Cycle gallery theme.', 'name' => GOG_PLUGIN_SHORT . '_themes', 'default' => $gog_themes['default']) );
		add_settings_field( 'themes', 'Activate all themes', 'goodold_gallery_setting_checkbox', __FILE__, GOG_PLUGIN_SHORT . '_themes', array('id' => 'all', 'label' => 'If selected, you will be able to choose theme in the shortcode.' . $themes_extra, 'name' => GOG_PLUGIN_SHORT . '_themes', 'default' => $gog_themes['all']) );
		add_settings_section( 'themes_available', 'Themes available', 'goodold_gallery_themes_available', __FILE__ );
		if ($themes_active) {
			add_settings_section( 'themes_cache', 'Themes cache', 'goodold_gallery_themes_cache', __FILE__ );
		}
	}
}

/**
 * Add settings page.
 */
function goodold_gallery_add_themes_page() {
	add_submenu_page( 'edit.php?post_type=goodoldgallery', GOG_PLUGIN_NAME . ' themes', 'Themes', 'administrator', GOG_PLUGIN_SHORT . '_themes', 'goodold_gallery_themes_page' );
}

/**
 * Main section header.
 */
function goodold_gallery_themes_header() {
}

/**
 * Build themes function for settings page
 */
function goodold_gallery_themes_available() {
	global $gog_themes;

	$themes = goodold_gallery_get_themes();

	if ( $themes ) {
		echo '<ul class="themes-available">';
		foreach ( $themes as $file => $theme ) {
			$author = filter_var($theme['AuthorURI'], FILTER_VALIDATE_URL) ? '<a href="' . $theme['AuthorURI'] . '">' . $theme['Author'] . '</a>' : $theme['Author'];

			$scr_check = $theme['path']['path'] . '/' . substr($file, 0, -4) . '.png';
			$screenshot = $theme['path']['url'] . '/' . substr($file, 0, -4) . '.png';
			$screenshot = file_exists($scr_check) ? '<div class="screenshot"><img src="' . $screenshot . '" alt="' . $theme['Name'] . '" /></div>' : '';

			echo '<li class="theme">';
			echo $screenshot;
			echo '<div class="information">';
			echo '<span class="title">' . $theme['Name'] . '</span>';
			echo $theme['Version'] ? ' <span class="version">' . $theme['Version'] . '</span>' : '';
			echo $author ? ' by <span class="author">' . $author . '</span>' : '';
			echo $theme['Description'] ? '<div class="description">' . $theme['Description'] . '</div>' : '';
			echo '</div>';
			echo '</li>';
		}
		echo '</ul>';
	}
}

/**
 * Cache builder
 */
function goodold_gallery_themes_cache() {
	global $upload_url;

	if ( file_exists($upload_url['basedir'] . '/good-old-gallery-themes.css') ) {
		$upload_dir = explode('/', $upload_url['basedir']);
		$count = count($upload_dir);
		echo '<p>Cache location: <code>' . $upload_dir[$count-2] . '/' . $upload_dir[$count-1] . '/good-old-gallery-themes.css</code></p>';
		echo '<p>Cache last updated: ' . date('H:i, Y-m-d', filemtime($upload_url['basedir'] . '/good-old-gallery-themes.css')) . '</p>';
	}

	echo "<p>* To use all themes you need to <a href=\"" . GOG_PLUGIN_URL . '/inc/cache.php?redirect' . "\">rebuild the css cache</a>, otherwise the themes won't be loaded, you also need to rebuild the cache if you install or delete themes.</p>";
}
/**
 * Print settings page
 */
function goodold_gallery_themes_page() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo GOG_PLUGIN_NAME; ?> settings</h2>
		<?php goodold_gallery_error_handling(); // Needed, because for some reason add_settings_error() isn't registered when a settings page is added outside of options-general.php ?>
		<div class="go-flattr"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/good-old-gallery/"></a></div>
		<form action="options.php" method="post" id="go-settings-form">
		<?php settings_fields( GOG_PLUGIN_SHORT . '_themes' ); ?>
		<?php do_settings_sections( __FILE__ ); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

function goodold_gallery_themes_validate($input) {
	return $input;
}
