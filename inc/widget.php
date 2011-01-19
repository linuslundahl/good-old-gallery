<?php

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
		$widget_ops = array('classname' => 'good-old-gallery-widget', 'description' => __( 'Widget that displays a selected gallery.', 'GoodOldGalleryWidget' ));

		/* Create the widget. */
		$this->WP_Widget( 'GoodOldGalleryWidget', __( GOG_PLUGIN_NAME, 'GoodOldGalleryWidget' ), $widget_ops );
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
			$size        = $instance['size']         ? ' size="' . $instance['size'] . '"' : '';
			$title       = $instance['title']        ? ' title="true"' : '';
			$description = $instance['description']  ? ' description="true"' : '';
			$navigation  = $instance['navigation']   ? ' navigation="true"' : '';
			$pager       = $instance['pager']        ? ' pager="true"' : '';
			$fx          = $instance['fx']           ? ' fx="' . $instance['fx'] . '"' : '';
			$timeout     = $instance['timeout']      ? ' timeout="' . $instance['timeout'] . '"' : '';
			$speed       = $instance['speed']        ? ' speed="' . $instance['speed'] . '"' : '';

			echo $before_widget;
			echo do_shortcode( '[good-old-gallery id="' . $instance['post-ID'] . '"' . $size . $navigation . $pager . $fx . $timeout . $speed . ']' );
			echo $after_widget;
		}
	}

	// UPDATE WIDGET SETTINGS
	function update($new_instance, $old_instance) {
		$instance['title']       = $new_instance['title'];
		$instance['post-ID']     = $new_instance['post-ID'];
		$instance['size']        = $new_instance['size'];
		$instance['fx']          = $new_instance['fx'];
		$instance['timeout']     = $new_instance['timeout'];
		$instance['speed']       = $new_instance['speed'];
		$instance['title']       = $new_instance['title'];
		$instance['description'] = $new_instance['description'];
		$instance['navigation']  = $new_instance['navigation'];
		$instance['pager']       = $new_instance['pager'];
		$instance['pages']       = $new_instance['pages'];

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
		<input id="<?php echo $this->get_field_id('title'); ?>" type="checkbox" name="<?php echo $this->get_field_name('title'); ?>"<?php echo $instance['title'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('title'); ?>" title="Select if the title should be displayed" style="line-height:25px;">Show pager</label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('description'); ?>" type="checkbox" name="<?php echo $this->get_field_name('description'); ?>"<?php echo $instance['description'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('description'); ?>" title="Select if the description should be displayed" style="line-height:25px;">Show description</label>
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
