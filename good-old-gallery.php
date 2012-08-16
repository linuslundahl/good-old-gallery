<?php

/**
 *
 * Plugin Name: Good Old Gallery
 * Plugin URI: http://unwi.se/good-old-gallery
 * Description: Good Old Gallery is a WordPress plugin that helps you upload image galleries that can be used on more than one page/post, it utilizes the built in gallery functionality in WP. Other features include built in Flexslider and jQuery Cycle support and Widgets.
 * Author: Linus Lundahl
 * Version: 2.0.3
 * Author URI: http://unwi.se/
 *
 */

// Load classes
require_once('inc/GogSettings.class.php');

// Globals
define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ));
define('GOG_PLUGIN_NAME', 'Good Old Gallery');
define('GOG_PLUGIN_SHORT', 'gog');
$upload_url = wp_upload_dir();
$is_loaded = FALSE;
$gog_settings = new GogSettings;

// WordPress hooks
register_activation_hook( __FILE__, 'goodold_gallery_activate' );

goodold_gallery_includes();

// For checking in admin purposes
$gog_get = array(
	'post_type' => isset($_GET['post_type']) ? $_GET['post_type'] : NULL,
	'post_id'		=> isset($_GET['post_id']) ? $_GET['post_id'] : NULL,
	'post'			=> isset($_GET['post']) ? $_GET['post'] : NULL,
	'page'			=> isset($_GET['page']) ? $_GET['page'] : NULL,
);

/**
 * Includes.
 */
function goodold_gallery_includes() {
	require_once('inc/functions.php');

	if (is_admin()) {
		// Load up different features
		require_once('inc/forms.php');
		require_once('inc/content.php');
		require_once('inc/media.php');

		// Add pages
		require_once('inc/page-settings.php');
		require_once('inc/page-themes.php');
	}

	require_once('inc/shortcode.php');
	require_once('inc/widget.php');
}

/**
 * Activation function.
 */
function goodold_gallery_activate() {
	$s = new GogSettings;

	$settings = get_option( GOG_PLUGIN_SHORT . '_settings' );
	if (!$settings) {
		add_option( GOG_PLUGIN_SHORT . '_settings', $s->settings );
	}

	$themes = get_option( GOG_PLUGIN_SHORT . '_themes' );
	if (!$themes) {
		add_option( GOG_PLUGIN_SHORT . '_themes', $s->themes );
	}
}

/**
 * Just tag the page for fun.
 */
function goodold_gallery_add_head_tag() {
	echo "\n\t" . '<!-- This site uses Good Old Gallery, get it from http://unwi.se/good-old-gallery -->' . "\n\n";
}
add_action( 'wp_head', 'goodold_gallery_add_head_tag' );

/**
 * Use init to load up styles.
 */
function goodold_gallery_load_styles() {
	global $is_loaded;

	// Add minified css of all themes or the selected theme css
	if ( !is_admin() && (!empty($gog_settings->themes['all']) && file_exists($upload_url['basedir'] . '/good-old-gallery-themes.css')) ) {
		wp_enqueue_style( 'good-old-gallery-themes', $upload_url['baseurl'] . '/good-old-gallery-themes.css' );
	}
	else if ( !is_admin() && ($gog_settings->themes['default'] && empty($gog_settings->themes['all'])) ) {
		wp_enqueue_style( 'good-old-gallery-theme', $gog_settings->themes['theme']['url'] . '/' . $gog_settings->themes['default'] );
	}

	// Check if the shortcode has been loaded
	if ( is_active_widget( FALSE, FALSE, 'goodoldgallerywidget', TRUE ) ) {
		goodold_gallery_load_scripts();
		$is_loaded = TRUE;
	}
}
add_action('init', 'goodold_gallery_load_styles');

if ( !$is_loaded ) {
	/**
	 * Check for shortcodes in posts.
	 */
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

/**
 * Register styles and js for selected slider plugin.
 */
function goodold_gallery_load_scripts() {
	global $gog_settings;

	if ( !is_admin() ) {
		if ( !empty($gog_settings->settings['plugin']) ) {
			foreach ( $gog_settings->plugin['setup']['files'] as $file ) {
				wp_enqueue_script( 'slider ', GOG_PLUGIN_URL . '/plugins/' . $gog_settings->settings['plugin'] . '/' . $file, array('jquery'), '', FALSE );
			}
		}
		wp_enqueue_style( 'good-old-gallery', GOG_PLUGIN_URL . '/style/good-old-gallery.css' );
	}
}

/**
 * Load styles and scripts for admin.
 */
function goodold_gallery_load_admin() {
	global $gog_get;

	$post_type = get_post_type( $gog_get['post'] );
	$post_id_type = get_post_type( $gog_get['post_id'] );

	// Load up media upload when administering gallery content
	if ( $post_type == 'goodoldgallery' || $gog_get['post_type'] == 'goodoldgallery' ) {
		add_thickbox();
		wp_enqueue_script('media-upload');
		wp_enqueue_script( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/js/good-old-gallery-admin.js', 'jquery', false, true );
	}

	// Add css and js for admin section
	if ( $post_id_type == 'goodoldgallery' || $post_type == 'goodoldgallery' || $gog_get['post_type'] == 'goodoldgallery' ) {
		wp_enqueue_style( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/style/good-old-gallery-admin.css' );
		wp_enqueue_style( 'bootstrap', GOG_PLUGIN_URL . '/style/bootstrap.min.css' );
	}

	if ( $gog_get['post_type'] == 'goodoldgallery' && ($gog_get['page'] == 'gog_settings' || $gog_get['page'] == 'gog_themes') ) {
		wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', 'jquery', false, true );
		wp_enqueue_script( 'good-old-gallery-admin', GOG_PLUGIN_URL . '/js/good-old-gallery-admin.js', 'jquery', false, true );
	}
}
add_action( 'admin_head', 'goodold_gallery_load_admin' );

/**
 * Add link to settings page on plugins page.
 */
function goodold_gallery_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/good-old-gallery.php' ) ) {
		$links[] = '<a href="edit.php?post_type=goodoldgallery&page=gog_settings">'.__('Settings').'</a>';
	}

	return $links;
}
add_filter( 'plugin_action_links', 'goodold_gallery_plugin_action_links', 10, 2 );
