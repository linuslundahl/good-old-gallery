<?php
	header('Content-type: text/css');

	$wp_load = realpath("../../../../wp-load.php");
	if ( !file_exists($wp_load) ) {
		$wp_config = realpath("../../../../wp-config.php");
		if ( !file_exists($wp_config) ) {
				exit("Can't find wp-config.php or wp-load.php");
		}
		else {
				require_once($wp_config);
		}
	}
	else {
		require_once($wp_load);
	}

	function compress($buffer) {
		global $gog_settings;

		// Remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		// Remove tabs, spaces, newlines, etc.
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '	 ', '		 ', '		 '), '', $buffer);
		// Add absolute paths
		foreach ( $gog_settings['themes'] as $file => $theme ) {
			$path = substr($file, 0, -4);
			$buffer = str_replace($path, $theme['url'] . '/' . $path, $buffer);
		}

		return $buffer;
	}

	ob_start("compress");
		foreach ( $gog_settings['themes'] as $file => $theme ) {
			include($theme['path'] . '/' . $file);
		}
	ob_end_flush();
