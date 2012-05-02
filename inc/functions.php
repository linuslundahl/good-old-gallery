<?php

/**
 * Helper function that finds the path of wp-load.php which is the root of wordpress
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
