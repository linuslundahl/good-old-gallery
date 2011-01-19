<?php

/**
 *
 * Plugin Name: Good Old Gallery
 * Plugin URI: http://unwi.se/good-old-gallery
 * Description: Good Old Gallery is a WordPress plugin that helps you upload image galleries that can be used on more than one page/post, it utilizes the built in gallery functionality in WP. Other features include built in jQuery Cycle support and Widgets.
 * Author: Linus Lundahl
 * Version: 0.1-dev
 * Author URI: http://unwi.se/
 *
 */

// Globals
define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_NAME', 'Good Old Gallery');
define('GOG_PLUGIN_SHORT', 'gog');

// Just tag the page for fun
function goodold_gallery_add_head_tag() {
	echo "\n" . '<!-- This site uses Good Old Gallery, get it from http://unwi.se/good-old-gallery -->' . "\n\n";
}
add_action('wp_head', 'goodold_gallery_add_head_tag');

// Register style and js for this plugin
if ( !is_admin() ) {
	wp_enqueue_style( 'good-old-gallery', GOG_PLUGIN_URL . '/style/good-old-gallery.css' );
	wp_enqueue_script( 'cycle', GOG_PLUGIN_URL . '/js/jquery.cycle.all.min.js', array('jquery'), '2.81', FALSE );
}

// Load up media upload when administering gallery content
if ( is_admin() && (get_post_type( $_GET['post'] ) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery') ) {
	add_thickbox();
	wp_enqueue_script('media-upload');
}

// Add css and js for admin section
if ( is_admin() && (get_post_type( $_GET['post_id'] ) == 'goodoldgallery' || get_post_type( $_GET['post'] ) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery') ) {
	wp_enqueue_style( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/style/good-old-gallery-admin.css' );
}

// Load up different features
require_once('inc/content.php');
require_once('inc/widget.php');
require_once('inc/settings.php');
require_once('inc/shortcode.php');
require_once('inc/media.php');

/**
 * Returns an array of valid paths
 */
function goodold_gallery_paths($paths) {
	$paths = explode("\n", $paths);

	foreach( $paths as $key => $path ) {
		$paths[$key] = trim($path);
	}

	return $paths;
}
