<?php
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

require_once('style.php');
$themes = $gog_settings->GetThemes();

function compress($buffer) {
	global $themes;

	// Remove comments
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	// Remove tabs, spaces, newlines, etc.
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '	 ', '		 ', '		 '), '', $buffer);
	// Add absolute paths
	foreach ( $themes as $file => $theme ) {
		$path = substr($file, 0, -4);
		$buffer = str_replace($path, $theme['path']['url'] . '/' . $path, $buffer);
	}

	return $buffer;
}

header('Content-type: text/css');

ob_start("compress");
	foreach ( $themes as $file => $theme ) {
		if (file_exists(include($theme['path']['path'] . '/' . $file))) {
			include($theme['path']['path'] . '/' . $file);
		}
	}
$content = compress(ob_get_contents());
ob_end_clean();

$upload_url = wp_upload_dir();
file_put_contents($upload_url['basedir'] . '/good-old-gallery-themes.css', $content);

// Redirect back to settings
if ( isset($_GET['redirect']) ) {
	header('Location: /wp-admin/edit.php?post_type=goodoldgallery&page=gog_themes');
}