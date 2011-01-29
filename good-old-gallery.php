<?php

/**
 *
 * Plugin Name: Good Old Gallery
 * Plugin URI: http://unwi.se/good-old-gallery
 * Description: Good Old Gallery is a WordPress plugin that helps you upload image galleries that can be used on more than one page/post, it utilizes the built in gallery functionality in WP. Other features include built in jQuery Cycle support and Widgets.
 * Author: Linus Lundahl
 * Version: 1.11
 * Author URI: http://unwi.se/
 *
 */

// Globals
define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_NAME', 'Good Old Gallery');
define('GOG_PLUGIN_SHORT', 'gog');
$upload_url = wp_upload_dir();
$gog_settings = get_settings( GOG_PLUGIN_SHORT . '_settings' );
$gog_cycle_settings = array(
	'theme'       => array(),
	'size'        => 'Full',
	'size-select' => 'full',
	'fx'          => 'fade',
	'speed'       => 500,
	'timeout'     => 10000,
	'title'       => 0,
	'description' => 0,
	'navigation'  => 0,
	'pager'       => 0,
	'prev'        => 'prev',
	'next'        => 'next'
);

// Just tag the page for fun
function goodold_gallery_add_head_tag() {
	echo "\n\t" . '<!-- This site uses Good Old Gallery, get it from http://unwi.se/good-old-gallery -->' . "\n\n";
}
add_action( 'wp_head', 'goodold_gallery_add_head_tag' );

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

// Add minified css of all themes or the selected theme css
if ( !is_admin() && ($gog_settings['themes'] && file_exists($upload_url['basedir'] . '/good-old-gallery-themes.css')) ) {
	wp_enqueue_style( 'good-old-gallery-themes', $upload_url['baseurl'] . '/good-old-gallery-themes.css' );
}
else if ( !is_admin() && ($gog_settings['theme'] && empty($gog_settings['themes'])) ) {
	wp_enqueue_style( 'good-old-gallery-theme', $gog_settings['theme']['url'] . '/' . $gog_settings['theme']['file'] );
}

// Add flattr button js and admin js
function goodold_gallery_flattr_button() {
	echo <<<FLATTR

	<script type="text/javascript">
	/* <![CDATA[ */
			(function() {
				var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
				s.type = 'text/javascript';
				s.async = true;
				s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
				t.parentNode.insertBefore(s, t);
			})();
	/* ]]> */
	</script>

FLATTR;
}
if ( is_admin() && $_GET['page'] == 'goodoldgallery' ) {
	wp_enqueue_script( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/js/good-old-gallery-admin.js', 'jquery', false, true );
	add_action( 'admin_head', 'goodold_gallery_flattr_button' );
}

// Load up different features
require_once('inc/style.php');
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
