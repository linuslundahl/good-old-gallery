<?php

/**
 * Finds available plugins.
 * @return Array
 */
function goodold_gallery_get_plugins( $select = FALSE ) {
	$plugins = array();
	// $plugin_path = get_stylesheet_directory();
	// $plugin_url = get_bloginfo( 'template_url' );

	$paths = array(
		GOG_PLUGIN_DIR . '/plugins' => GOG_PLUGIN_URL . '/plugins',
		// WP_CONTENT_DIR . '/gog-plugins' => WP_CONTENT_URL . '/gog-plugins',
		// $plugin_path . '/gog-plugins' => $plugin_url . '/gog-plugins'
	);

	foreach ( $paths as $path => $url ) {
		if ( is_dir($path) ) {
			$folder = opendir($path);

			while ( false !== ($filename = readdir($folder)) ) {
				if ( substr($filename, 0, 1) != '.' ) {
					include_once($path . '/' . $filename . '/' . $filename . '.php');
					if ( function_exists('goodold_gallery_' . $filename . '_setup') ) {
						$settings = call_user_func('goodold_gallery_' . $filename . '_setup');
						$plugins[$filename] = $settings['title'];
					}
				}
			}
		}
	}

	return $plugins;
}

function goodold_gallery_load_plugin( $args = array() ) {
	global $gog_settings, $gog_default_settings;

	$ret = array();

	$default = !empty($gog_settings['plugin']) ? $gog_settings['plugin'] : $gog_default_settings['plugin'];
	$plugin = isset($args['plugin']) && !empty($args['plugin']) ? $args['plugin'] : $default;
	if ($plugin != 'none') {
		include_once(GOG_PLUGIN_DIR . '/plugins/' . $plugin . '/' . $plugin . '.php');

		if ( isset($args['function']) ) {
			if ( function_exists('goodold_gallery_' . $plugin . '_' . $args['function']) ) {
				$settings = isset($args['settings']) ? $args['settings'] : array();
				$ret = call_user_func('goodold_gallery_' . $plugin . '_' . $args['function'], $settings);
			}
		}
		else {
			$callbacks = array('setup', 'settings', 'settings_form', 'widget');
			foreach ($callbacks as $callback)
			if ( function_exists('goodold_gallery_' . $plugin . '_' . $callback) ) {
				$ret[$callback] = call_user_func('goodold_gallery_' . $plugin . '_' . $callback);
			}
		}
	}

	return $ret;
}