<?php

/**
 *
 * Plugin Name: Good Old Gallery
 * Plugin URI:
 * Description: A plugin that adds gallery functionality to goodold.se.
 * Author: Good Old
 * Version: 0.1-dev
 * Author URI: http://goodold.se/
 *
 */

// Globals
define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ));
$gog_pluginname = "Good Old Gallery";
$gog_shortname = "gog";

// Register style and js for this plugin
if ( !is_admin() ) {
	wp_enqueue_style( 'goodold-gallery', GOG_PLUGIN_URL . '/style/goodold-gallery.css' );
	wp_enqueue_script( 'cycle', GOG_PLUGIN_URL . '/js/jquery.cycle.all.min.js', array('jquery'), '2.81', FALSE );
}

// Load up media upload when administering gallery content
if ( is_admin() && ( get_post_type($_GET['post']) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery' ) ) {
	add_thickbox();
	wp_enqueue_script('media-upload');
}

// Add css and js for admin section
if ( is_admin() && ( get_post_type($_GET['post_id'] ) == 'goodoldgallery' || get_post_type( $_GET['post'] ) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery' ) ) {
	wp_enqueue_style( 'goodold-gallery-admin', GOG_PLUGIN_URL . '/admin/goodold-gallery-admin.css' );
	wp_enqueue_script( 'goodold-gallery-admin', GOG_PLUGIN_URL . '/admin/goodold-gallery-admin.js', array('jquery', 'cycle'), TRUE );
}

// ------- GALLERY CONTENT TYPE ----------------------------------------------
// ---------------------------------------------------------------------------

/**
 * The GoodOldGallery class.
 *
 * @category Good Old Gallery Wordpress Plugin
 * @package  Good Old Gallery
 * @author   Linus Lundahl
 *
 */
class GoodOldGallery {

	/**
	 * Constructor
	 */
	public function __construct() {
		register_post_type( 'goodoldgallery',
			array(
				'labels' => array(
					'name'               => _x( 'Galleries', 'gallery type general name' ),
					'singular_name'      => _x( 'Gallery', 'gallery type singular name' ),
					'add_new'            => __( 'Add New' ),
					'add_new_item'       => __( 'Add New Gallery' ),
					'edit_item'          => __( 'Edit Gallery' ),
					'new_item'           => __( 'New Gallery' ),
					'view_item'          => __( 'View Gallery' ),
					'search_items'       => __( 'Search Galleries' ),
					'not_found'          => __( 'No Galleries found' ),
					'not_found_in_trash' => __( 'No Galleries found in Trash' ),
					'parent_item_colon'  => ''
				),
			'public'               => false,
			'description'          => __( 'A Gallery that is used to display sliders on the Good Old site.' ),
			'publicly_queryable'   => false,
			'show_ui'              => true,
			'show_in_nav_menus'    => false,
			'query_var'            => 'goodoldgallery',
			'rewrite'              => true,
			'capability_type'      => 'post',
			'hierarchical'         => false,
			'menu_position'        => 10,
			'exclude_from_search'  => true,
			'supports'             => array('title'),
			'rewrite'              => array('slug' => 'goodold-gallery', 'with_front' => false),
			'menu_icon'            => GOG_PLUGIN_URL . '/img/goodold-gallery.png',
			'register_meta_box_cb' => array($this, 'addMeta')
			)
		);

		// Add action to save the gallery metadata
		// add_action('save_post', array($this, 'saveMetaContent'));
	}

	/**
	 * Registered function that adds a metadata box to the post edit screen for
	 * the fortunecookie type.
	 */
	public function addMeta() {
		add_meta_box( 'gog_sectionid',
			__( 'Gallery', 'gog_textdomain' ),
			array($this, 'addMetaContent'),
			'goodoldgallery',
			'advanced'
		);
	}

	/**
	 * Registered function that provides the content for the metadata box.
	 */
	public function addMetaContent() {
		$args = func_get_args();

		echo '<h4>Upload</h4>';
		echo '<div><p>';

		$out = _media_button( __( 'Add an Image' ), 'images/media-button-image.gif?ver=20100531', 'image' );
		$context = apply_filters( 'media_buttons_context', __( 'Upload/Manage: %s' ) );
		printf($context, $out);

		echo '</p></div>';

		if ( $_GET['post'] ) {
			echo '<h4>Shortcode</h4>';
			echo '<div><p>Shortcode for this gallery: <code>[goodold-gallery id="' . $_GET['post'] . '"]</code></p></div>';
			echo '<span class="description"><p>Shortcodes can be used to paste a gallery into a post or a page, just copy the full code and paste it into the post/page in HTML mode.</p></span>';
		}
	}

	/**
	 * Registered function that saves the metadata contet to the atabase,
	 *
	 * @param int $post_id The current post ID.
	 */
	public function saveMetaContent($post_id) {
	}

	/**
	 * Registered function that alters the WP_Query object to include the
	 * goodoldgallery type on certain pages.
	 * There are certain checks in place in order to prevent destroying
	 * the existing functionality.
	 *
	 * @param WP_Query $query The WP_Query object.
	 *
	 * @return WP_Query The modified (or not) WP_Query object.
	 */
	public function getGoodOldGallerys($query) {
		if (!is_page() || is_home() || is_archive()) {
				$query->set('post_type', array('post', 'goodoldgallery'));
		}
		return $query;
	}

}

/**
 * Registered function that creates the goodoldgallery content type.
 */
function create_goodoldgallery() {
	$goodOldGallery = new GoodOldGallery();
}
add_action( 'init', 'create_goodoldgallery' );


// ------- DEFAULT SETTINGS PAGE ---------------------------------------------
// ---------------------------------------------------------------------------

// Build form
$gog_options = array();
$gog_options['paragraph-1'] = array(
	"value" => "Good Old Gallery created and maintained by <a href=\"http://unwi.se/\">Linus Lundahl</a>.",
	"type" => "paragraph"
);

$gog_options['title-1'] = array(
	"name" => "Instructions",
	"type" => "title"
);

$gog_options['paragraph-2'] = array(
	"value" => "These settings are the defaults that will be used by Cycle, settings can be overridden by adding variables to the <code>[goodold-gallery]</code> shortcode.<br />" .
						 "Use the built in generator found under the 'Good Old Gallery' tab, just click the 'Add an Image' button to find the tab.",
	"type" => "paragraph"
);

$gog_options['title-2'] = array(
	"name" => "Gallery Settings",
	"type" => "title"
);

$gog_options['start'] = array(
	"type" => 'start-table'
);

$gog_options['size'] = array(
	"name" => "Image size",
	"desc" => "Select what image size the galleries should use.",
	"id" => "size",
	"type" => "select",
	"options" => array(
		'Thumbnail' => 'thumbnail',
		'Medium' => 'medium',
		'Large' => 'large',
		'Full' => 'full'
	),
	"std" => 'large'
);

$gog_options['fx'] = array(
	"name" => "Transition animation",
	"desc" => "Animation that should be used.",
	"id" => "fx",
	"type" => "select",
	"options" => array(
		'Fade'                    => 'fade',
		'Horizontal scroll'       => 'scrollHorz',
		'Vertical scroll'         => 'scrollVert',
		'None (Standard gallery)' => 'none'
	),
	"std" => 'fade'
);

$gog_options['speed'] = array(
	"name" => "Speed",
	"desc" => "milliseconds",
	"id" => "speed",
	"type" => "text",
	"std" => '500',
	"size" => 4
);

$gog_options['timeout'] = array(
	"name" => "Transition speed",
	"desc" => "milliseconds",
	"id" => "timeout",
	"type" => "text",
	"std" => '10000',
	"size" => 4
);

$gog_options['pager'] = array(
	"name" => "Show pager",
	"desc" => "Select if the page should be displayed.",
	"id" => "pager",
	"type" => "checkbox",
	"std" => 0
);

$gog_options['navigation'] = array(
	"name" => "Show navigation",
	"desc" => "Select if you would like to add PREV and NEXT buttons.",
	"id" => "navigation",
	"type" => "checkbox",
	"std" => 0
);

$gog_options['prev'] = array(
	"name" => "Prev",
	"desc" => "Text used for prev button",
	"id" => "next",
	"type" => "text",
	"std" => 'prev',
	"size" => 4
);

$gog_options['next'] = array(
	"name" => "Next",
	"desc" => "Text used for next button",
	"id" => "prev",
	"type" => "text",
	"std" => 'next',
	"size" => 4
);

$gog_options['end'] = array(
	'type' => 'end-table'
);

/**
 * Generate settings form.
 */
function goodold_gallery_settings_page() {
	global $gog_pluginname, $gog_shortname, $gog_options;

	if ( 'save' == $_REQUEST['action'] ) {
		$values = array();
		foreach ( $gog_options as $value ) {
			if ( $value['id'] ) {
				$values[$value['id']] = $_REQUEST[$value['id']];
			}
		}

		update_option( $gog_shortname . '_settings', $values );
	}

	$gog_settings = get_settings( $gog_shortname . '_settings' );

	if ( $_REQUEST['save'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Options saved.' ) . '</strong></p></div>';
	if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Options restored.' ) . '</strong></p></div>';
?>

<div class="wrap">

<h2><?php echo $gog_pluginname; ?> settings</h2>

<form method="post" action="">

<?php foreach ( $gog_options as $value ) {
switch ( $value['type'] ) {

case "start-table":
?>
<table class="form-table">
<?php
break;

case "end-table";
?>
</table>
<?php
break;

case "title":
?>
<h3><?php echo __( $value['name'] ); ?></h3>
<?php
break;

case 'paragraph':
?>
<p><?php echo $value['value']; ?></p>
<?php
break;

case 'text':
$set_value = $gog_settings[$value['id']] ? $gog_settings[$value['id']] : $value['std'];
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<input size="<?php echo $value['size']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo htmlspecialchars(stripslashes($set_value)); ?>" />
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case 'textarea':
$set_value = $gog_settings[$value['id']] ? $gog_settings[$value['id']] : $value['std'];
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="40" rows="5"><?php echo $set_value; ?></textarea>
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case 'select':
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<select style="width:140px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $key => $item) { ?>
<?php
	$selected = '';
	$saved = $gog_settings[$value['id']];
	if ( $saved && ( $saved === $item ) ) {
		$selected = ' selected="selected"';
	} elseif ( !$saved && ( $saved === $item ) ) {
		$selected = ' selected="selected"';
	} elseif ( !$saved && ( $value['std'] === $item ) ) {
		$selected = ' selected="selected"';
	}
?>
			<option<?php echo " value=\"$item\"" . $selected; ?>><?php echo $key; ?></option>
<?php } ?>
			</select>
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case "checkbox":
$checked = $gog_settings[$value['id']] ? 'checked="checked"' : '';
?>
	<tr valign="top">
		<th scope="row"><?php echo $value['name']; ?></th>
		<td>
			<label for="<?php echo $value['id']; ?>">
				<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="1" <?php echo $checked; ?> />
				<?php echo $value['desc']; ?>
			</label>
		</td>
	</tr>
<?php
break;
}
}
?>

	<p class="submit">
	<input class="button-primary" name="save" type="submit" value="Save changes" />
	<input type="hidden" name="action" value="save" />
	</p>
	</form>
	</div>

<?php
}

function goodold_gallery_create_menu() {
	add_submenu_page( 'edit.php?post_type=goodoldgallery', 'Good Old Gallery Settings', 'Settings', 'manage_options', 'goodoldgallery', 'goodold_gallery_settings_page' );
}
add_action( 'admin_menu', 'goodold_gallery_create_menu' );


// ------- SHORTCODE ---------------------------------------------------------
// ---------------------------------------------------------------------------

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
	global $post, $gog_shortname;

	static $i = 1;

	$gog_settings = get_settings( $gog_shortname . '_settings' );

	extract(shortcode_atts( array(
		'id'         => null,
		'size'       => $gog_settings['size']       ? $gog_settings['size']       : 'Full',
		'fx'         => $gog_settings['fx']         ? $gog_settings['fx']         : 'fade',
		'speed'      => $gog_settings['speed']      ? $gog_settings['speed']      : 500,
		'timeout'    => $gog_settings['timeout']    ? $gog_settings['timeout']    : 10000,
		'navigation' => $gog_settings['navigation'] ? $gog_settings['navigation'] : FALSE,
		'pager'      => $gog_settings['pager']      ? $gog_settings['pager']      : FALSE,
		'prev'       => $gog_settings['prev']       ? $gog_settings['prev']       : 'prev',
		'next'       => $gog_settings['next']       ? $gog_settings['next']       : 'next'
	), $attr ));

	// Use post_id if no id is set in shortcode.
	$id = ( !$id && $post->ID ) ? $post->ID : $id;

	// Kill the function if no id is still not found.
	if ( !$id ) {
		return;
	}

	$ret = '';
	if ( $fx == 'none' ) {
		$ret .= do_shortcode( '[gallery id="' . $id . '"]' );
	}
	else {
		$attachments = get_children( array(
			'post_parent'     => $id,
			'post_status'     => 'inherit',
			'post_type'       => 'attachment',
			'post_mime_type'  => 'image',
		) );

		if ( $attachments ) {
			// GO-GALLERY ID
			$ret .= '<div id="go-gallery-' . $id . '-' . $i . '" class="go-gallery go-gallery-' . $id . '">' . "\n";

			// GALLERY CLASS
			$ret .= '<div class="gallery">' . "\n";

			// INSERT ATTACHMENTS
			foreach ( $attachments as $gallery_id => $attachment ) {
				$link = get_post_meta($attachment->ID, "_goodold_gallery_image_link", true);

				$ret .= '<div class="image">' . "\n";

				if ( $attachment->post_title || $attachment->post_content ) {
					$ret .= '<div class="meta">' . "\n";
					if ( $attachment->post_title ) {
						$ret .= '<span class="title">' . $attachment->post_title . '</span>' . "\n";
					}
					if ( $attachment->post_content ) {
						$ret .= '<span class="description">' . $attachment->post_content . '</span>' . "\n";
					}
					$ret .= '</div>' . "\n";
				}

				if ( $link ) {
					$ret .= '<a href="' . $link . '">' . "\n";
				}

				$ret .= wp_get_attachment_image($gallery_id, $size, false) . "\n";

				if ( $link ) {
					$ret .= '</a>' . "\n";
				}

				$ret .= '</div>' . "\n";
			}

			// END GO-GALLERY CLASS
			$ret .= '</div>' . "\n";

			// NAVIGATION
			if ( $navigation ) {
				$ret .= '<div class="nav">' . "\n";
				$ret .= '<span class="prev">' . $prev . '</span><span class="next">' . $next . '</span>' . "\n";
				$ret .= '</div>' . "\n";
				$navigation = 'prev: "#go-gallery-' . $id . '-' . $i . ' .prev",' .
											'next: "#go-gallery-' . $id . '-' . $i . ' .next"';
			}

			// PAGER
			if ( $pager ) {
				$ret .= '<div class="pager"></div>' . "\n";
				$pager = 'pager: "#go-gallery-' . $id . '-' . $i . ' .pager",';
			}

			// SCRIPT
			$ret .= '<script type="text/javascript">' .
								'jQuery(function($) { ' .
									'$("#go-gallery-' . $id . '-' . $i . ' .gallery").cycle({' .
										'fx: "' . $fx . '",' .
										'speed: ' . $speed . ',' .
										'timeout: ' . $timeout . ',' .
										$pager .
										$navigation .
									'});' .
								'});' .
							'</script>' . "\n";

			// END GO-GALLERY ID
			$ret .= '  </div>' . "\n";
		}
	}

	$i++;

	return $ret;
}
add_shortcode( 'goodold-gallery', 'goodold_gallery_shortcode' );


// ------- WIDGET ------------------------------------------------------------
// ---------------------------------------------------------------------------

/**
 * The GoodOldGalleryWidget class.
 *
 * @category Good Old Gallery Wordpress Plugin
 * @package  Good Old Gallery
 * @author   Linus Lundahl
 *
 */
class GoodOldGalleryWidget extends WP_Widget {
	function GoodOldGalleryWidget() {
		/* Widget settings. */
		$widget_ops = array('classname' => 'goodold-gallery-widget', 'description' => __( 'Widget that displays a selected gallery.', 'GoodOldGalleryWidget' ));

		/* Create the widget. */
		$this->WP_Widget( 'GoodOldGalleryWidget', __( 'Good Old Gallery', 'GoodOldGalleryWidget' ), $widget_ops );
	}

	// PRINT WIDGET
	function widget($args, $instance) {
		extract($args);

		$show = FALSE;
		if ( $instance['gallery-pages'] ) {
			$paths = goodold_gallery_paths( $instance['gallery-pages'] );
			if ( in_array($_SERVER['REQUEST_URI'], $paths) ) {
				$show = TRUE;
			}
		}
		else {
			$show = TRUE;
		}

		if ( $show ) {
			$size       = $instance['size']         ? ' size="' . $instance['size'] . '"' : '';
			$navigation = $instance['navigation']   ? ' navigation="true"' : '';
			$pager      = $instance['pager']        ? ' pager="true"' : '';
			$fx         = $instance['fx']           ? ' fx="' . $instance['fx'] . '"' : '';
			$timeout    = $instance['timeout']      ? ' timeout="' . $instance['timeout'] . '"' : '';
			$speed      = $instance['speed']        ? ' speed="' . $instance['speed'] . '"' : '';

			echo $before_widget;
			echo do_shortcode( '[goodold-gallery id="' . $instance['post-ID'] . '"' . $size . $navigation . $pager . $fx . $timeout . $speed . ']' );
			echo $after_widget;
		}
	}

	// UPDATE WIDGET SETTINGS
	function update($new_instance, $old_instance) {
		$instance['title']      = $new_instance['title'];
		$instance['post-ID']    = $new_instance['post-ID'];
		$instance['size']       = $new_instance['size'];
		$instance['fx']         = $new_instance['fx'];
		$instance['timeout']    = $new_instance['timeout'];
		$instance['speed']      = $new_instance['speed'];
		$instance['navigation'] = $new_instance['navigation'];
		$instance['pager']      = $new_instance['pager'];
		$instance['pages']      = $new_instance['pages'];

		return $instance;
	}

	// WIDGET SETTINGS FORM
	function form($instance) {
		global $wpdb;

		// Build dropdown with galleries
		$posts = $wpdb->get_results($wpdb->prepare("
			SELECT ID, post_title FROM $wpdb->posts
				WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
		));

		$options = !$posts ? "<option value=\"\">No galleries found</option>" : "";

		foreach ($posts as $p) {
			$selected = '';
			if ($instance['post-ID']) {
				$selected = $p->ID == $instance['post-ID'] ? ' selected="yes"' : '';
			}
			$options .= "<option value=\"$p->ID\"$selected>$p->post_title</option>";
		}

		$title = apply_filters( 'widget_title', $instance['title'] );
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>" title="Title of the widget">Title:</label>
		<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		<span class="description"><p><?php echo __("The title is only used in the administration."); ?></p></span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('post-ID'); ?>" title="Select gallery" style="line-height:25px;">Gallery:</label>
		<select id="<?php echo $this->get_field_id('post-ID'); ?>" name="<?php echo $this->get_field_name('post-ID'); ?>">
			<?php echo $options; ?>
		</select>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('fx'); ?>" title="Animation" style="line-height:25px;">Animation:</label>
		<select id="<?php echo $this->get_field_id('fx'); ?>" name="<?php echo $this->get_field_name('fx'); ?>">
			<option value="scrollHorz"<?php echo $instance['fx'] == 'scrollHorz' ? ' selected="yes"' : ''; ?>>Horizontal scroll</option>
			<option value="scrollVert"<?php echo $instance['fx'] == 'scrollVert' ? ' selected="yes"' : ''; ?>>Vertical scroll</option>
			<option value="fade"<?php echo $instance['fx'] == 'fade' ? ' selected="yes"' : ''; ?>>Fade</option>
			<option value="none"<?php echo $instance['fx'] == 'none' ? ' selected="yes"' : ''; ?>>None (Standard gallery)</option>
		</select>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('timeout'); ?>" title="Cycle animation timeout">Timeout:</label>
		<input id="<?php echo $this->get_field_id('timeout'); ?>" name="<?php echo $this->get_field_name('timeout'); ?>" type="text" value="<?php echo $instance['timeout']; ?>" size="5" /> <span>ms</span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('speed'); ?>" title="Speed of animation">Speed:</label>
		<input id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo $instance['speed']; ?>" size="5" /> <span>ms</span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('size'); ?>" title="Select gallery size" style="line-height:25px;">Image size:</label>
		<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
			<option value="thumbnail"<?php echo $instance['size'] == 'thumbnail' ? ' selected="yes"' : ''; ?>>Thumbnail</option>
			<option value="medium"<?php echo $instance['size'] == 'medium' ? ' selected="yes"' : ''; ?>>Medium</option>
			<option value="large"<?php echo $instance['size'] == 'large' ? ' selected="yes"' : ''; ?>>Large</option>
			<option value="full"<?php echo $instance['size'] == 'full' ? ' selected="yes"' : ''; ?>>Full</option>
		</select>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('pager'); ?>" type="checkbox" name="<?php echo $this->get_field_name('pager'); ?>"<?php echo $instance['pager'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('pager'); ?>" title="Select if a pager should be displayed" style="line-height:25px;">Show pager</label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('navigation'); ?>" type="checkbox" name="<?php echo $this->get_field_name('navigation'); ?>"<?php echo $instance['navigation'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('navigation'); ?>" title="Select if a navigation should be displayed" style="line-height:25px;">Show navigation</label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('pages'); ?>" title="Title of the widget">Show on:</label>
		<textarea id="<?php echo $this->get_field_id('pages'); ?>" name="<?php echo $this->get_field_name('pages'); ?>" rows=3><?php echo $instance['pages']; ?></textarea><br />
		<span class="description"><p><?php echo __("Type paths that the gallery should be visible on, separate them with a line break.<br />Don't type full url's, only paths with a starting and ending slash.<br />Example: /my-page-path/"); ?><p></span>
	</p>
<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("GoodOldGalleryWidget");') );


// ------- EXTRAS ------------------------------------------------------------
// ---------------------------------------------------------------------------

/**
 * Adds Good Old Gallery media button. (Can't get this working properly)
 */
// function goodold_gallery_media_button($context) {
//	 $post = wp_get_single_post();
//
//	 $button = ' %s';
//	 if (get_post_type() != 'goodoldgallery') {
//		 $image = GOG_PLUGIN_URL . '/img/goodold-gallery-small.png';
//		 $button .= '<a href="media-upload.php?post_id=' . $post->ID . '&type=image&tab=gogallery" id="add_gogallery" class="thickbox" title="Insert Good Old Gallery"><img src="' . $image . '" /></a>';
//	 }
//
//	 return sprintf($context, $button);
// }
// add_filter('media_buttons_context', 'goodold_gallery_media_button');

/**
 * Adds Good Old Gallery media tab.
 */
function goodold_gallery_media_tab($tabs) {
	$newtab = array('gogallery' => __( 'Good Old Gallery', 'gogallery' ));
	return array_merge($tabs, $newtab);
}
add_filter( 'media_upload_tabs', 'goodold_gallery_media_tab' );


/**
 * Good Old Gallery tab page.
 */
function goodold_gallery_media_process() {
	global $wpdb;

	media_upload_header();

	// Build dropdown with galleries
	$posts = $wpdb->get_results($wpdb->prepare("
		SELECT ID, post_title FROM $wpdb->posts
			WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
	));

	$options = !$posts ? "<option value=\"\">No galleries found</option>" : "";

	foreach ( $posts as $p ) {
		$selected = '';
		$options .= "<option value=\"$p->ID\">$p->post_title</option>";
	}
?>
<div id="goodold-gallery-generator">
	<h3 class="media-title">Good Old Gallery shortcode generator</h3>

	<div class="postbox">
		<h3 style="margin: 0; padding: 10px;">Generator</h3>
		<div class="inside" style="margin: 10px;">
			<p>
				Copy and paste the generated shortcode into your post or page in HTML mode.
			</p>

			<div id="goodold-gallery-shortcode">
				<p>
					<code>[goodold-gallery]</code>
				</p>
			</div>

			<p>
				<label for="id" title="Select gallery" style="line-height:25px;">Gallery:</label>
				<select id="id" name="Gallery">
					<option value="">Select gallery</option>
					<?php echo $options; ?>
				</select>
			</p>

			<p>
				<label for="fx" title="Animation" style="line-height:25px;">Animation:</label>
				<select id="fx" name="fx">
					<option value="">Select animation</option>
					<option value="none">None (Standard gallery)</option>
					<option value="scrollHorz">Horizontal scroll</option>
					<option value="scrollVert">Vertical scroll</option>
					<option value="fade">Fade</option>
				</select>
			</p>

			<div class="cycle-options">
				<p>
					<label for="timeout" title="Cycle animation timeout">Timeout:</label>
					<input id="timeout" name="timeout" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="speed" title="Speed of animation">Speed:</label>
					<input id="speed" name="speed" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="size" title="Select gallery size" style="line-height:25px;">Image size:</label>
					<select id="size" name="size">
						<option value="">Select size</option>
						<option value="thumbnail">Thumbnail</option>
						<option value="medium">Medium</option>
						<option value="large">Large</option>
						<option value="full">Full</option>
					</select>
				</p>

				<p>
					<input id="navigation" type="checkbox" name="navigation" value="true" />
					<label for="navigation" title="Select if a navigation should be displayed" style="line-height:25px;">Show navigation</label>
				</p>

				<p>
					<input id="pager" type="checkbox" name="pager" value="true" />
					<label for="pager" title="Select if a pager should be displayed" style="line-height:25px;">Show pager</label>
				</p>
			</div>
		</div>
	</div>
</div>
<?php
}

/**
 * Loads Good Old Gallery tab page.
 */
function goodold_gallery_media_menu_handle() {
	wp_enqueue_style( 'media' );
	wp_enqueue_script( 'gallery-insert', GOG_PLUGIN_URL . '/admin/goodold-gallery-admin.js', 'jquery', false, true );
	wp_enqueue_style( 'goodold-gallery', GOG_PLUGIN_URL . '/admin/goodold-gallery-admin.css' );
	return wp_iframe( 'goodold_gallery_media_process' );
}
add_action( 'media_upload_gogallery', 'goodold_gallery_media_menu_handle' );

/**
 * Adds custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_edit($form_fields, $post) {
	$form_fields["goodold_gallery_image_link"] = array(
		"label" => __( "Link" ),
		"input" => "text",
		"value" => get_post_meta( $post->ID, "_goodold_gallery_image_link", true ), "helps" => __( "To be used with Good Old Gallery slider." ),
	);

	return $form_fields;
}
add_filter( "attachment_fields_to_edit", "goodold_gallery_image_attachment_fields_to_edit", null, 2 );

/**
 * Saves custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_save($post, $attachment) {
	if( isset($attachment['goodold_gallery_image_link']) ){
		update_post_meta( $post['ID'], '_goodold_gallery_image_link', $attachment['goodold_gallery_image_link'] );
	}
	return $post;
}
add_filter( "attachment_fields_to_save", "goodold_gallery_image_attachment_fields_to_save", null , 2 );

/**
 * Hide unwanted tabs in Good Old Gallery uploader
 */
function goodold_gallery_remove_media_tabs($tabs) {
	unset($tabs['type_url']);
	unset($tabs['library']);
	unset($tabs['gogallery']);
	return $tabs;
}
if ( is_admin() && get_post_type($_GET['post_id']) == 'goodoldgallery' ) {
	add_filter( 'media_upload_tabs', 'goodold_gallery_remove_media_tabs' );
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
