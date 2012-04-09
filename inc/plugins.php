<?php

/**
 * Finds available plugins.
 * @return Array
 */
function goodold_gallery_get_plugins( $select = FALSE, $full = FALSE ) {
	$plugins = array();

	$paths = array(
		GOG_PLUGIN_DIR . '/plugins' => GOG_PLUGIN_URL . '/plugins',
	);

	foreach ( $paths as $path => $url ) {
		if ( is_dir($path) ) {
			$folder = opendir($path);

			while ( false !== ($filename = readdir($folder)) ) {
				if ( substr($filename, 0, 1) != '.' ) {
					include_once($path . '/' . $filename . '/' . $filename . '.php');
					if ( function_exists('goodold_gallery_' . $filename . '_setup') ) {
						$settings = call_user_func('goodold_gallery_' . $filename . '_setup');
						if ( $full ) {
							$plugins[$filename] = $settings;
						}
						else {
							$plugins[$filename] = $settings['title'];
						}
					}
				}
			}
		}
	}

	return $plugins;
}

/**
 * Loads plugins from the plugins dir.
 */
function goodold_gallery_load_plugin( $args = array() ) {
	global $gog_settings, $gog_default_settings;

	// Add default keys
	$ret = array(
		'setup' => array(),
		'settings' => array(),
		'settings_form' => array(),
		'widget' => array(),
	);

	$default = $gog_default_settings['plugin'];
	if ( isset($gog_settings['plugin']) && !empty($gog_settings['plugin']) ) {
		$default = $gog_settings['plugin'];
	}

	$plugin = isset($args['plugin']) && !empty($args['plugin']) ? $args['plugin'] : $default;
	if ( !empty($plugin) && $plugin != 'none') {
		require_once(GOG_PLUGIN_DIR . '/plugins/' . $plugin . '/' . $plugin . '.php');

		if ( isset($args['function']) ) {
			if ( function_exists('goodold_gallery_' . $plugin . '_' . $args['function']) ) {
				$settings = isset($args['settings']) ? $args['settings'] : array();
				$ret = call_user_func('goodold_gallery_' . $plugin . '_' . $args['function'], $settings);
			}
		}
		else {
			foreach ($ret as $callback => $item) {
				if ( function_exists('goodold_gallery_' . $plugin . '_' . $callback) ) {
					$ret[$callback] = call_user_func('goodold_gallery_' . $plugin . '_' . $callback);
				}
			}
		}
	}

	return $ret;
}