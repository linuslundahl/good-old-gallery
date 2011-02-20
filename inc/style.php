<?php

/**
 * Finds available themes.
 * @return Array
 */
function goodold_gallery_get_themes($select = false) {
	$themes = array();
	$theme_path = get_stylesheet_directory();
	$theme_url = get_bloginfo( 'template_url' );

	$paths = array(
		GOG_PLUGIN_DIR . '/themes' => GOG_PLUGIN_URL . '/themes',
		WP_CONTENT_DIR . '/gog-themes' => WP_CONTENT_URL . '/gog-themes',
		$theme_path . '/gog-themes' => $theme_url . '/gog-themes'
	);

	foreach ( $paths as $path => $url ) {
		if ( is_dir($path) ) {
			$folder = opendir($path);

			while ( false !== ($filename = readdir($folder)) ) {
				if ( $filename ) {
					if ( substr(strtolower($filename), -3) == 'css' ) {
						$info = goodold_gallery_fetch_stylesheets($path . '/' . $filename);
						if ( $select ) {
							$themes[$filename] = $info['Name'];
						}
						else {
							$info['path'] = array('path' => $path, 'url' => $url);
							$themes[$filename] = $info;
						}
					}
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
		'Class' => 'Class',
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