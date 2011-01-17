<?php
/** Load WordPress Administration Bootstrap */
require_once('../../../../wp-admin/admin.php');

wp_enqueue_script('media-upload');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

// Build dropdown with galleries
$posts = $wpdb->get_results($wpdb->prepare("
  SELECT ID, post_title FROM $wpdb->posts
    WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
));

$options = !$posts ? "<option value=\"\">No galleries found</option>" : "";

foreach ($posts as $p) {
  $selected = '';
  $options .= "<option value=\"$p->ID\">$p->post_title</option>";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Shortcode generator</title>
<script type='text/javascript' src='<?php echo WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) ?>/goodold-gallery-admin.js'></script>
</head>
<body>

  <h2 class="title">Good Old Gallery shortcode generator</h2>

  <div id="goodold-gallery-shortcode">
    <p>
      Your shortcode: <code>[goodold-gallery]</code>
    </p>
  </div>

  <div class="postbox">
    <h3 style="margin: 0; padding: 10px;">Generator</h3>
    <div class="inside" style="margin: 10px;">
      <p>
        <label for="gallery" title="Select gallery" style="line-height:25px;">Gallery:</label>
        <select id="gallery" name="Gallery">
          <option value="">Select gallery</option>
          <?php echo $options; ?>
        </select>
      </p>

      <p>
        <label for="fx" title="Animation" style="line-height:25px;">Animation:</label>
        <select id="fx" name="fx">
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
            <option value="thumbnail">Thumbnail</option>
            <option value="medium">Medium</option>
            <option value="large">Large</option>
            <option value="full">Full</option>
          </select>
        </p>

        <p>
          <input id="pager" type="checkbox" name="pager" />
          <label for="pager" title="Select gallery" style="line-height:25px;">Show pager</label>
        </p>
      </div>
    </div>
  </div>

  <input type="button" class="button" style="" name="insert-gallery" id="insert-gallery" value="Insert gallery">

</body>
</html>