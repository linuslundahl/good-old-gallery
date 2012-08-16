<?php

/**
 * Helper function that finds the path of wp-load.php which is the root of wordpress.
 */
function get_wp_root( $directory ) {
	global $wp_root;

	foreach( glob( $directory . "/*" ) as $f ) {
		if ( 'wp-load.php' == basename($f) ) {
			return $wp_root = str_replace( "\\", "/", dirname($f) );
		}

		if ( is_dir($f) ) {
			$newdir = dirname( dirname($f) );
		}
	}

	if ( isset($newdir) && $newdir != $directory ) {
		if ( get_wp_root ( $newdir ) ) {
			return $wp_root;
		}
	}

	return FALSE;
}

/**
 * Returns an array of valid paths.
 */
function goodold_gallery_paths($paths) {
	$paths = explode("\n", $paths);

	foreach( $paths as $key => $path ) {
		$paths[$key] = trim($path);
	}

	return $paths;
}

/**
 * Returns the saved order of title, desc and image
 */
function goodold_gallery_order_fields($settings) {
	$ret = array();
	foreach ( $settings as $key => $val ) {
		if ( strpos($key, 'order_') !== FALSE ) {
			$items[$val] = $key;
		}
	}

	if ( !empty($items) ) {
		ksort($items);
		foreach ( $items as $val => $key ) {
			$id = str_replace('order_', '', $key);
			$ret[] = $id;
		}
	}

	return $ret;
}

/**
 * Returns largest/smallest image width/height
 */
function goodold_gallery_get_img_size($type, $size, $height) {
	$ret = 0;

	if ( $type == 'largest' ) {
		$ret = $height < $size ? $size : $height;
	}
	else if ( $type == 'smallest' ) {
		if ( $height === 0 ) {
			$ret = $size;
		}
		else {
			$ret = $height > $size ? $size : $height;
		}
	}

	return $ret;
}
