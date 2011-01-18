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

define('GOG_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)));

// Register style and js for this plugin
if (!is_admin()) {
  wp_enqueue_style( 'goodold-gallery', GOG_PLUGIN_URL . '/style/goodold-gallery.css' );
  wp_enqueue_script( 'cycle', GOG_PLUGIN_URL . '/js/jquery.cycle.all.min.js', array('jquery'), '2.81', FALSE );
}

// Load up media upload when administering gallery content
if (is_admin() && (get_post_type($_GET['post']) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery')) {
  add_thickbox();
  wp_enqueue_script('media-upload');
}

// Add css and js for admin section
if (is_admin() && (get_post_type($_GET['post_id']) == 'goodoldgallery' || get_post_type($_GET['post']) == 'goodoldgallery' || $_GET['post_type'] == 'goodoldgallery')) {
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
      'supports'             => array( 'title' ),
      'rewrite'              => array( 'slug' => 'goodold-gallery', 'with_front' => false ),
      'menu_icon'            => GOG_PLUGIN_URL . '/img/goodold-gallery.png',
      'register_meta_box_cb' => array( $this, 'addMeta' )
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
    add_meta_box('gog_sectionid',
      __('Gallery', 'gog_textdomain'),
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

    $out = _media_button(__('Add an Image'), 'images/media-button-image.gif?ver=20100531', 'image');
    $context = apply_filters('media_buttons_context', __('Upload/Manage: %s'));
    printf($context, $out);

    echo '</p></div>';

    if ($_GET['post']) {
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


// ------- SETTINGS PAGE -----------------------------------------------------
// ---------------------------------------------------------------------------

// Setup form
$gog_pluginname = "Good Old Gallery";
$gog_shortname = "gog";
$gog_options = array();

// Build form
$gog_options['paragraph-1'] = array(
  "value" => "sexyCycle for WordPress created and maintained by <a href=\"http://unwise.se/\">Linus Lundahl</a>.<br />
              <a href=\"http://suprb.com/apps/sexyCycle/\">sexyCycle jQuery plugin</a> created by <a href=\"http://suprb.com/\">Andreas Pihlstr&ouml;m</a>.<br />
              Contributions by <a href=\"http://thewebpreneur.com/\">Nick O'Neill</a>.<br />",
  "type" => "paragraph"
);

$gog_options['title-1'] = array(
  "name" => "Instructions",
  "type" => "title"
);

$gog_options['paragraph-2'] = array(
  "value" => "You can choose to override the default <strong>[gallery]</strong> shortcode, or you can use the shortcode <strong>[sexy-gallery]</strong>, and still have the regular galleries enabled.
              Galleries are created the same way <em>(Add an image -> Gallery)</em> even if you use the default <strong>[gallery]</strong> shortcode or the <strong>[sexy-gallery]</strong> shortcode,
              but pasting a <strong>[sexy-gallery]</strong> shortcode must be done manually in HTML mode.<br /><br />
              If the default shortcode is overriden, these settings affects <strong>all galleries</strong>
              in <strong>all posts and pages</strong>, though custom settings can be added with <a href=\"http://wordpress.org/extend/plugins/sexycycle-for-wordpress/installation/\">shortcode variables</a>.<br />
              Pasting both shortcodes in a single post or page is not recommended, but it works if you don't override the default shortcode.<br /><br />
              When images are linked to the original image, this plugin works together with plugins such as <a href=\"http://wordpress.org/extend/plugins/shadowbox-js/\">Shadowbox JS</a> and
              <a href=\"http://wordpress.org/extend/plugins/jquery-lightbox-balupton-edition/\">jQuery Lightbox</a>. It might also work with other similar plugins, but there are no guarantees.",
  "type" => "paragraph"
);

$gog_options['title-2'] = array(
  "name" => "Gallery Settings",
  "type" => "title"
);

$gog_options['start-1'] = array(
  "type" => 'start-table'
);

$gog_options['override'] = array(
  "name" => "Override default gallery",
  "desc" => "If checked, all [gallery] shortcode tags will be overridden.",
  "id" => $gog_shortname . "_override",
  "type" => "checkbox",
  "std" => 0
);

$gog_options['image-size'] = array(
  "name" => "Image size",
  "desc" => "Select what image size the galleries should use.",
  "id" => $gog_shortname . "_img_size",
  "type" => "select",
  "options" => array(
    'thumbnail' => 'thumbnail',
    'medium' => 'medium',
    'large' => 'large',
    'full' => 'full'
  ),
  "std" => 'large'
);

$gog_options['animation'] = array(
  "name" => "jQuery Easing animation",
  "desc" => "Slide animation that should be used.",
  "id" => $gog_shortname . "_animation",
  "type" => "select",
  "options" => array(
    'easeInQuad' => 'easeInQuad',
    'easeOutQuad' => 'easeOutQuad',
    'easeInOutQuad' => 'easeInOutQuad',
    'easeInCubic' => 'easeInCubic',
    'easeOutCubic' => 'easeOutCubic',
    'easeInOutCubic' => 'easeInOutCubic',
    'easeInQuart' => 'easeInQuart',
    'easeOutQuart' => 'easeOutQuart',
    'easeInOutQuart' => 'easeInOutQuart',
    'easeInQuint' => 'easeInQuint',
    'easeOutQuint' => 'easeOutQuint',
    'easeInOutQuint' => 'easeInOutQuint',
    'easeInSine' => 'easeInSine',
    'easeOutSine' => 'easeOutSine',
    'easeInOutSine' => 'easeInOutSine',
    'easeInExpo' => 'easeInExpo',
    'easeOutExpo' => 'easeOutExpo',
    'easeInOutExpo' => 'easeInOutExpo',
    'easeInCirc' => 'easeInCirc',
    'easeOutCirc' => 'easeOutCirc',
    'easeInOutCirc' => 'easeInOutCirc',
    'easeInElastic' => 'easeInElastic',
    'easeOutElastic' => 'easeOutElastic',
    'easeInOutElastic' => 'easeInOutElastic',
    'easeInBack' => 'easeInBack',
    'easeOutBack' => 'easeOutBack',
    'easeInOutBack' => 'easeInOutBack',
    'easeInBounce' => 'easeInBounce',
    'easeOutBounce' => 'easeOutBounce',
    'easeInOutBounce' => 'easeInOutBounce'
  ),
  "std" => 'easeOutExpo'
);

$gog_options['speed'] = array(
  "name" => "Speed",
  "desc" => "The speed of the animation. (milliseconds)",
  "id" => $gog_shortname . "_speed",
  "type" => "text",
  "std" => '400',
  "size" => 4
);

$gog_options['interval'] = array(
  "name" => "Auto cycle speed",
  "desc" => "Leave empty to disable auto cycling. (milliseconds)",
  "id" => $gog_shortname . "_interval",
  "type" => "text",
  "size" => 4
);

$gog_options['controls'] = array(
  "name" => "Type of controls",
  "desc" => "Select if you would like to add PREV and NEXT buttons.",
  "id" => $gog_shortname . "_controls",
  "type" => "select",
  "options" => array(
    'none' => 0,
    'Above image' => 'above',
    'Under image' => 'under',
    'Before / After image' => 'beforeafter'
  ),
  "std" => 0
);

$gog_options['controls_stop'] = array(
  "name" => "Use stop button",
  "desc" => "If checked, a button to stop auto cycling will be added.",
  "id" => $gog_shortname . "_controls_stop",
  "type" => "checkbox",
  "std" => 0
);

$gog_options['imgclick'] = array(
  "name" => "Image click",
  "desc" => "Select what clicking the gallery image should default to.",
  "id" => $gog_shortname . "_imgclick",
  "type" => "select",
  "options" => array(
    'Swap to next image' => 0,
    'Link original image' => 'link',
    'Do nothing' => 'nothing'
  ),
  "std" => 0
);

$gog_options['prev'] = array(
  "name" => "PREV title",
  "desc" => "Choose a different title.",
  "id" => $gog_shortname . "_prev",
  "type" => "text",
  "size" => 4,
  "std" => 'Prev'
);

$gog_options['next'] = array(
  "name" => "NEXT title",
  "desc" => "Choose a different title.",
  "id" => $gog_shortname . "_next",
  "type" => "text",
  "size" => 4,
  "std" => 'Next'
);

$gog_options['stop'] = array(
  "name" => "STOP title",
  "desc" => "Choose a different title.",
  "id" => $gog_shortname . "_stop",
  "type" => "text",
  "size" => 4,
  "std" => 'Stop'
);

$gog_options['caption'] = array(
  "name" => "Caption",
  "desc" => "Choose which field to use as caption.",
  "id" => $gog_shortname . "_caption",
  "type" => "select",
  "options" => array(
    'none' => 0,
    'Use caption' => 'caption',
    'Use description' => 'desc'
  ),
  "std" => 0
);

$gog_options['counter'] = array(
  "name" => "Slide counter",
  "desc" => "If checked, slideshow will include a slide counter.",
  "id" => $gog_shortname ."_counter",
  "type" => "checkbox",
  "std" => 0
);

$gog_options['cycle'] = array(
  "name" => "Disable cycling",
  "desc" => "Prevent the gallery from cycling",
  "id" => $gog_shortname . "_cycle",
  "type" => "checkbox",
  "std" => 0
);

$gog_options['end-1'] = array(
  'type' => 'end-table'
);

$gog_options['title-3'] = array(
  'name' => 'CSS Classes',
  'type' => 'title'
);

$gog_options['paragraph-3'] = array(
  "value" => "Adding your own CSS classes can come in handy if you for example use a 960 based layout.<br />Add multiple classes separated by a space: <em>grid-2 my-class</em>",
  "type" => "paragraph"
);

$gog_options['start-2'] = array(
  'type' => 'start-table'
);

$gog_options['class-1'] = array(
  "name" => "<code>.gallery</code>",
  "id" => $gog_shortname . "_class_gallery",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['class-2'] = array(
  "name" => "<code>.gallery-wrapper</code>",
  "id" => $gog_shortname . "_class_galleryw",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['class-3'] = array(
  "name" => "<code>.controllers.above</code>",
  "id" => $gog_shortname . "_class_cabove",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['class-4'] = array(
  "name" => "<code>.controllers.under</code>",
  "id" => $gog_shortname . "_class_cunder",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['class-5'] = array(
  "name" => "<code>.controllers.before</code>",
  "id" => $gog_shortname . "_class_cbefore",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['class-6'] = array(
  "name" => "<code>.controllers.after</code>",
  "id" => $gog_shortname . "_class_cafter",
  "type" => "text",
  "size" => 20,
  "std" => ''
);

$gog_options['end-2'] = array(
  'type' => 'end-table'
);

$gog_options['title-4'] = array(
  'name' => 'Other',
  'type' => 'title'
);

$gog_options['start-3'] = array(
  'type' => 'start-table'
);

$gog_options['jquery'] = array(
  "name" => "Disable jQuery",
  "desc" => "Don't load latest version of jQuery from Google Libraries API<strong>*</strong>",
  "id" => $gog_shortname . "_jquery",
  "type" => "checkbox",
  "std" => 0
);

$gog_options['end-3'] = array(
  'type' => 'end-table'
);

$gog_options['paragraph-4'] = array(
  'value' => '<small><strong>*</strong> sexyCycle depends on jQuery 1.4+ and if you disable it from loading here, make sure it\'s loaded elsewhere.</small>',
  'type' => 'paragraph'
);

/**
 * Generate settings form.
 */
function goodold_gallery_settings_page() {
  global $gog_pluginname, $gog_shortname, $gog_options;

  if ( 'save' == $_REQUEST['action'] ) {

    $values = array();
    foreach ($gog_options as $value) {
      if ($value['id']) {
        $values[$value['id']] = $_REQUEST[$value['id']];
      }
    }

    if ($values[$gog_shortname . '_interval']) {
      // Disable controls if an interval is set
      $values[$gog_shortname . '_controls'] = '';
      unset($gog_options['controls']);
      unset($gog_options['prev']);
      unset($gog_options['next']);
      unset($gog_options['imgclick']);
    }
    else {
      // Otherwise disable stop function
      $values[$gog_shortname . '_controls_stop'] = NULL;
      unset($gog_options['controls_stop']);
      unset($gog_options['stop']);
    }

    update_option($gog_shortname . '_settings', $values);

    echo '<div id="message" class="updated fade"><p><strong>' . __('Options saved.') . '</strong></p></div>';
  }

  $gog_settings = get_settings('gog_settings');

  if ($gog_settings[$gog_shortname . '_interval']) {
    // Disable controls if an interval is set
    unset($gog_options['controls']);
    unset($gog_options['prev']);
    unset($gog_options['next']);
    unset($gog_options['imgclick']);
  }
  else {
    // Otherwise disable stop function
    unset($gog_options['controls_stop']);
    unset($gog_options['stop']);
  }
?>

<div class="wrap">
<h2><?php echo $gog_pluginname; ?> settings</h2>

<form method="post" action="">

<?php foreach ($gog_options as $value) {
switch ($value['type']) {

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
<h3><?php echo __($value['name']); ?></h3>
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
  if ($saved && ($saved === $item)) {
    $selected = ' selected="selected"';
  } elseif (!$saved && ($saved === $item)) {
    $selected = ' selected="selected"';
  } elseif (!$saved && ($value['std'] === $item)) {
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
  add_submenu_page('edit.php?post_type=goodoldgallery', 'Good Old Gallery Settings', 'Settings', 'manage_options', 'goodoldgallery', 'goodold_gallery_settings_page');
}
add_action('admin_menu', 'goodold_gallery_create_menu');


// ------- SHORTCODE ---------------------------------------------------------
// ---------------------------------------------------------------------------

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
  global $post;

  extract(shortcode_atts(array(
    'id'      => null,
    'size'    => 'Full',
    'fx'      => 'scrollHorz',
    'speed'   => 300,
    'timeout' => 10000,
    'pager'   => FALSE
  ), $attr));

  // Use post_id if no id is set in shortcode.
  $id = (!$id && $post->ID) ? $post->ID : $id;

  // Kill the function if no id is still not found.
  if (!$id) {
    return;
  }

  $ret = '';
  if ($fx == 'none') {
    $ret .= do_shortcode('[gallery id="' . $id . '"]');
  }
  else {
    $attachments = get_children(array(
      'post_parent'     => $id,
      'post_status'     => 'inherit',
      'post_type'       => 'attachment',
      'post_mime_type'  => 'image',
    ));

    if ($attachments) {
      // GO-GALLERY ID
      $ret .= '  <div id="go-gallery-' . $id . '" class="go-gallery">' . "\n";

      // GALLERY CLASS
      $ret .= '    <div class="gallery">' . "\n";

      // INSERT ATTACHMENTS
      foreach ( $attachments as $gallery_id => $attachment ) {
        $link = get_post_meta($attachment->ID, "_goodold_gallery_image_link", true);

        $ret .= '      <div class="image">' . "\n";

        if ($link) {
          $ret .= '        <a href="' . $link . '">' . "\n";
        }

        if ($attachment->post_title || $attachment->post_content) {
          $ret .= '          <div class="meta">' . "\n";
          if ($attachment->post_title) {
            $ret .= '            <h1 class="title">' . $attachment->post_title . '</h1>' . "\n";
          }
          if ($attachment->post_content) {
            $ret .= '          <span class="description">' . $attachment->post_content . '</span>' . "\n";
          }
          $ret .= '          </div>' . "\n";
        }

        $ret .= "          " . wp_get_attachment_image($gallery_id, $size, false) . "\n";

        if ($link) {
          $ret .= '        </a>' . "\n";
        }

        $ret .= '      </div>' . "\n";
      }

      // END GO-GALLERY CLASS
      $ret .= '    </div>' . "\n";

      // NAVIGATION
      $ret .= '    <div class="nav">' . "\n";
      $ret .= '      <div class="prev">&nbsp;</div><div class="next">&nbsp;</div>' . "\n";
      $ret .= '    </div>' . "\n";

      // PAGER
      $pager = '';
      if ($attr['pager']) {
        $ret .= '    <div class="pager"></div>' . "\n";
        $pager = 'pager: "#go-gallery-' . $id . ' .pager",';
      }

      // SCRIPT
      $ret .= '    <script type="text/javascript">jQuery(function($) { ' .
                   '$("#go-gallery-' . $id . ' .gallery").cycle({' .
                     'fx: "' . $fx . '",' .
                     'speed: ' . $speed . ',' .
                     'timeout: ' . $timeout . ',' .
                     $pager .
                     'prev: "#go-gallery-' . $id . ' .prev",' .
                     'next: "#go-gallery-' . $id . ' .next"' .
                     '});' .
                   '});' .
                 '</script>' . "\n";

      // END GO-GALLERY ID
      $ret .= '  </div>' . "\n";
    }
  }

  return $ret;
}
add_shortcode('goodold-gallery', 'goodold_gallery_shortcode');


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
   $widget_ops = array( 'classname' => 'goodold-gallery-widget', 'description' => __('Widget that displays a selected gallery.', 'GoodOldGalleryWidget'));

   /* Create the widget. */
   $this->WP_Widget( 'GoodOldGalleryWidget', __('Goodold Gallery', 'GoodOldGalleryWidget'), $widget_ops );
  }

  // PRINT WIDGET
  function widget($args, $instance) {
    extract($args);

    $show = FALSE;
    if ($instance['gallery-pages']) {
      $paths = goodold_gallery_paths($instance['gallery-pages']);
      if (in_array($_SERVER['REQUEST_URI'], $paths)) {
        $show = TRUE;
      }
    }
    else {
      $show = TRUE;
    }

    if ($show) {
      $size    = $instance['gallery-size']    ? ' size="' . $instance['gallery-size'] . '"' : '';
      $pager   = $instance['gallery-pager']   ? ' pager="true"' : '';
      $fx      = $instance['gallery-fx']      ? ' fx="' . $instance['gallery-fx'] . '"' : '';
      $timeout = $instance['gallery-timeout'] ? ' timeout="' . $instance['gallery-timeout'] . '"' : '';
      $speed   = $instance['gallery-speed']   ? ' speed="' . $instance['gallery-speed'] . '"' : '';

      echo $before_widget;
      echo do_shortcode('[goodold-gallery id="' . $instance['gallery-post-ID'] . '"' . $size . $pager . $fx . $timeout . $speed . ']');
      echo $after_widget;
    }
  }

  // UPDATE WIDGET SETTINGS
  function update($new_instance, $old_instance) {
    $instance['title'] = $new_instance['title'];
    $instance['gallery-post-ID'] = $new_instance['gallery-post-ID'];
    $instance['gallery-size'] = $new_instance['gallery-size'];
    $instance['gallery-fx'] = $new_instance['gallery-fx'];
    $instance['gallery-timeout'] = $new_instance['gallery-timeout'];
    $instance['gallery-speed'] = $new_instance['gallery-speed'];
    $instance['gallery-pager'] = $new_instance['gallery-pager'];
    $instance['gallery-pages'] = $new_instance['gallery-pages'];

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
      if ($instance['gallery-post-ID']) {
        $selected = $p->ID == $instance['gallery-post-ID'] ? ' selected="yes"' : '';
      }
      $options .= "<option value=\"$p->ID\"$selected>$p->post_title</option>";
    }

    $title = apply_filters('widget_title', $instance['title']);
?>
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>" title="Title of the widget">Title:</label>
    <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    <span class="description"><p><?php echo __("The title is only used in the administration."); ?></p></span>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-post-ID'); ?>" title="Select gallery" style="line-height:25px;">Gallery:</label>
    <select id="<?php echo $this->get_field_id('gallery-post-ID'); ?>" name="<?php echo $this->get_field_name('gallery-post-ID'); ?>">
      <?php echo $options; ?>
    </select>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-fx'); ?>" title="Animation" style="line-height:25px;">Animation:</label>
    <select id="<?php echo $this->get_field_id('gallery-fx'); ?>" name="<?php echo $this->get_field_name('gallery-fx'); ?>">
      <option value="scrollHorz"<?php echo $instance['gallery-fx'] == 'scrollHorz' ? ' selected="yes"' : ''; ?>>Horizontal scroll</option>
      <option value="scrollVert"<?php echo $instance['gallery-fx'] == 'scrollVert' ? ' selected="yes"' : ''; ?>>Vertical scroll</option>
      <option value="fade"<?php echo $instance['gallery-fx'] == 'fade' ? ' selected="yes"' : ''; ?>>Fade</option>
      <option value="none"<?php echo $instance['gallery-fx'] == 'none' ? ' selected="yes"' : ''; ?>>None (Standard gallery)</option>
    </select>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-timeout'); ?>" title="Cycle animation timeout">Timeout:</label>
    <input id="<?php echo $this->get_field_id('gallery-timeout'); ?>" name="<?php echo $this->get_field_name('gallery-timeout'); ?>" type="text" value="<?php echo $instance['animation']; ?>" /> <span>ms</span>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-speed'); ?>" title="Speed of animation">Speed:</label>
    <input id="<?php echo $this->get_field_id('gallery-speed'); ?>" name="<?php echo $this->get_field_name('gallery-speed'); ?>" type="text" value="<?php echo $instance['speed']; ?>" /> <span>ms</span>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-size'); ?>" title="Select gallery size" style="line-height:25px;">Image size:</label>
    <select id="<?php echo $this->get_field_id('gallery-size'); ?>" name="<?php echo $this->get_field_name('gallery-size'); ?>">
      <option value="thumbnail"<?php echo $instance['gallery-size'] == 'thumbnail' ? ' selected="yes"' : ''; ?>>Thumbnail</option>
      <option value="medium"<?php echo $instance['gallery-size'] == 'medium' ? ' selected="yes"' : ''; ?>>Medium</option>
      <option value="large"<?php echo $instance['gallery-size'] == 'large' ? ' selected="yes"' : ''; ?>>Large</option>
      <option value="full"<?php echo $instance['gallery-size'] == 'full' ? ' selected="yes"' : ''; ?>>Full</option>
    </select>
  </p>

  <p>
    <input id="<?php echo $this->get_field_id('gallery-pager'); ?>" type="checkbox" name="<?php echo $this->get_field_name('gallery-pager'); ?>"<?php echo $instance['gallery-pager'] ? ' checked="checked"' : ''; ?> />
    <label for="<?php echo $this->get_field_id('gallery-pager'); ?>" title="Select gallery" style="line-height:25px;">Show pager</label>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('gallery-pages'); ?>" title="Title of the widget">Show on:</label>
    <textarea id="<?php echo $this->get_field_id('gallery-pages'); ?>" name="<?php echo $this->get_field_name('gallery-pages'); ?>" rows=3><?php echo $instance['gallery-pages']; ?></textarea><br />
    <span class="description"><p><?php echo __("Type paths that the gallery should be visible on, separate them with a line break.<br />Don't type full url's, only paths with a starting and ending slash.<br />Example: /my-page-path/"); ?><p></span>
  </p>
<?php
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("GoodOldGalleryWidget");') );


// ------- EXTRAS ------------------------------------------------------------
// ---------------------------------------------------------------------------

/**
 * Adds custom gallery media button.
 */
function goodold_gallery_media_button($context) {
  $button = ' %s';
  if (get_post_type() != 'goodoldgallery') {
    $image = GOG_PLUGIN_URL . '/img/goodold-gallery-small.png';
    $button .= '<a href="' . GOG_PLUGIN_URL . '/admin/goodold-gallery-insert.php?post_id=null" class="thickbox" title="Insert Good Old Gallery"><img src="' . $image . '" /></a>';
  }

  return sprintf($context, $button);
}
add_filter('media_buttons_context', 'goodold_gallery_media_button');


/**
 * Adds custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_edit($form_fields, $post) {
  $form_fields["goodold_gallery_image_link"] = array(
    "label" => __("Custom Link"),
    "input" => "text",
    "value" => get_post_meta($post->ID, "_goodold_gallery_image_link", true), "helps" => __("To be used with Good Old Gallery slider."),
  );

  return $form_fields;
}
add_filter("attachment_fields_to_edit", "goodold_gallery_image_attachment_fields_to_edit", null, 2);

/**
 * Saves custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_save($post, $attachment) {
  if( isset($attachment['goodold_gallery_image_link']) ){
    update_post_meta($post['ID'], '_goodold_gallery_image_link', $attachment['goodold_gallery_image_link']);
  }
  return $post;
}
add_filter("attachment_fields_to_save", "goodold_gallery_image_attachment_fields_to_save", null , 2);

/**
 * Hide unwanted tabs in Good Old Gallery uploader
 */
function goodold_gallery_remove_media_tabs($tabs) {
  unset($tabs['type_url']);
  unset($tabs['library']);
  return $tabs;
}
if (is_admin() && get_post_type($_GET['post_id']) == 'goodoldgallery') {
  add_filter('media_upload_tabs', 'goodold_gallery_remove_media_tabs');
}

/**
 * Returns an array of valid paths
 */
function goodold_gallery_paths($paths) {
  $paths = explode("\n", $paths);

  foreach($paths as $key => $path) {
    $paths[$key] = trim($path);
  }

  return $paths;
}
