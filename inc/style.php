<?php

/**
 * Finds available themes.
 * @return Array
 */
function goodold_gallery_get_themes($select = false) {
	$path = '..' . GOG_PLUGIN_PATH . '/themes';
	$folder = opendir($path);
	$exclude = explode("|", strtolower('.|..'));

	while (false !== ($filename = readdir($folder))) {
		if ($filename) {
			if (!in_array(strtolower($filename), $exclude)) {
				$info = goodold_gallery_fetch_stylesheets($path . '/' . $filename);
				if ($select) {
					$themes[$info['Name']] = $filename;
				}
				else {
					$themes[$filename] = $info;
				}
			}
		}
	}

	return $themes;
}

/**
 * Gets information about themes.
 * @return Array
 */
function goodold_gallery_fetch_stylesheets($file = null) {
	$default_headers = array(
		'Name' => 'Style Name',
		'Description' => 'Description',
		'Version' => 'Version',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
	);

	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	foreach ( $default_headers as $field => $regex ) {
		preg_match( '/^[ \t\/*#]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
		if ( !empty( ${$field} ) )
			${$field} = _cleanup_header_comment( ${$field}[1] );
		else
			${$field} = '';
	}

	$file_data = compact( array_keys( $default_headers ) );

	return $file_data;
}