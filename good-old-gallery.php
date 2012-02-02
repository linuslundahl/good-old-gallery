<?php

/**
 *
 * Plugin Name: Good Old Gallery
 * Plugin URI: http://unwi.se/good-old-gallery
 * Description: Good Old Gallery is a WordPress plugin that helps you upload image galleries that can be used on more than one page/post, it utilizes the built in gallery functionality in WP. Other features include built in Flexslider and jQuery Cycle support and Widgets.
 * Author: Linus Lundahl
 * Version: 2.0-dev
 * Author URI: http://unwi.se/
 *
 */

// Globals
define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_NAME', 'Good Old Gallery');
define('GOG_PLUGIN_SHORT', 'gog');
$upload_url = wp_upload_dir();

// For checking in admin purposes
$gog_get = array(
	'post_type' => isset($_GET['post_type']) ? $_GET['post_type'] : NULL,
	'post_id'   => isset($_GET['post_id']) ? $_GET['post_id'] : NULL,
	'post'      => isset($_GET['post']) ? $_GET['post'] : NULL,
	'page'      => isset($_GET['page']) ? $_GET['page'] : NULL,
);

// Load plugin settings and themes
$gog_settings = get_option( GOG_PLUGIN_SHORT . '_settings' );
$gog_themes = get_option( GOG_PLUGIN_SHORT . '_themes' );

// Setup default plugin settings and themes
$gog_default_settings = array(
	'size'              => 'full',
	'title'             => 0,
	'description'       => 0,
	'plugin'            => 'flexslider',
);

if ( isset($gog_plugin['settings']) && is_array($gog_plugin['settings']) ) {
	$gog_default_settings += $gog_plugin['settings'];
}

$gog_default_themes = array(
	'default' => NULL,
	'themes' => NULL
);

// Load functionality
require_once('inc/widget.php');
require_once('inc/plugins.php');
require_once('inc/shortcode.php');

if (is_admin()) {
	// Load up different features
	require_once('inc/forms.php');
	require_once('inc/style.php');
	require_once('inc/content.php');
	require_once('inc/media.php');

	// Add pages
	require_once('inc/page-settings.php');
	require_once('inc/page-themes.php');
}

$gog_plugin = goodold_gallery_load_plugin();

// Just tag the page for fun
function goodold_gallery_add_head_tag() {
	echo "\n\t" . '<!-- This site uses Good Old Gallery, get it from http://unwi.se/good-old-gallery -->' . "\n\n";
}
add_action( 'wp_head', 'goodold_gallery_add_head_tag' );

// Use init to load up styles
$is_loaded = FALSE;
function goodold_gallery_load_styles() {
	global $gog_themes, $is_loaded;

	// Add minified css of all themes or the selected theme css
	if ( !is_admin() && (!empty($gog_themes['all']) && file_exists($upload_url['basedir'] . '/good-old-gallery-themes.css')) ) {
		wp_enqueue_style( 'good-old-gallery-themes', $upload_url['baseurl'] . '/good-old-gallery-themes.css' );
	}
	else if ( !is_admin() && ($gog_themes['default'] && empty($gog_themes['all'])) ) {
		wp_enqueue_style( 'good-old-gallery-theme', $gog_themes['theme']['url'] . '/' . $gog_themes['default'] );
	}

	// Check if the shortcode has been loaded
	if ( is_active_widget( FALSE, FALSE, 'goodoldgallerywidget', TRUE ) ) {
		goodold_gallery_load_scripts();
		$is_loaded = TRUE;
	}
}
add_action('init', 'goodold_gallery_load_styles');

if ( !$is_loaded ) {
function goodold_gallery_check_post_for_shortcode( $posts ) {
	$found = FALSE;
	if ( !empty($posts) ) {
		foreach ( $posts as $gog_get['post'] ) {
			if ( stripos($gog_get['post']->post_content, '[good-old-gallery') !== FALSE ) {
				$found = TRUE;
				break;
			}
		}

		if ( $found ) {
			goodold_gallery_load_scripts();
		}
	}

	return $posts;
}
add_action('the_posts', 'goodold_gallery_check_post_for_shortcode');
}

// Function that registers styles and js for this plugin
function goodold_gallery_load_scripts() {
	global $gog_settings, $gog_plugin;

	if ( !is_admin() ) {
		if ( !empty($gog_settings['plugin']) ) {
			foreach ( $gog_plugin['setup']['files'] as $file ) {
				wp_enqueue_script( 'slider ', GOG_PLUGIN_URL . '/plugins/' . $gog_settings['plugin'] . '/' . $file, array('jquery'), '', FALSE );
			}
		}
		wp_enqueue_style( 'good-old-gallery', GOG_PLUGIN_URL . '/style/good-old-gallery.css' );
	}
}

// Used to load styles and scripts for admin
function goodold_gallery_load_admin() {
	global $gog_get;
	add_thickbox();

	// Load up media upload when administering gallery content
	if ( get_post_type( $gog_get['post'] ) == 'goodoldgallery' || $gog_get['post_type'] == 'goodoldgallery' ) {
		wp_enqueue_script('media-upload');
	}

	// Add css and js for admin section
	if ( get_post_type( $gog_get['post_id'] ) == 'goodoldgallery' || get_post_type( $gog_get['post'] ) == 'goodoldgallery' || $gog_get['post_type'] == 'goodoldgallery' ) {
		wp_enqueue_style( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/style/good-old-gallery-admin.css' );
	}

	if ( $gog_get['post_type'] == 'goodoldgallery' && ($gog_get['page'] == 'gog_settings' || $gog_get['page'] == 'gog_themes') ) {
		wp_enqueue_script( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/js/good-old-gallery-admin.js', 'jquery', false, true );
		add_action( 'admin_head', 'goodold_gallery_flattr_button' );
	}
}
add_action( 'admin_enqueue_scripts', 'goodold_gallery_load_admin' );

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
