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
			$theme       = $instance['theme']        ? ' theme="' . $instance['theme'] . '"' : '';
			$title       = $instance['gtitle']       ? ' title="true"' : '';
			$description = $instance['description']  ? ' description="true"' : '';
			$navigation  = $instance['navigation']   ? ' navigation="true"' : '';
			$pager       = $instance['pager']        ? ' pager="true"' : '';
			$fx          = $instance['fx']           ? ' fx="' . $instance['fx'] . '"' : '';
			$timeout     = $instance['timeout']      ? ' timeout="' . $instance['timeout'] . '"' : '';
			$speed       = $instance['speed']        ? ' speed="' . $instance['speed'] . '"' : '';

			echo $before_widget;
			echo do_shortcode( '[good-old-gallery id="' . $instance['post-ID'] . '"' . $theme . $size . $navigation . $pager . $fx . $timeout . $speed . $title . $description . ']' );
			echo $after_widget;
		}
	}

	// UPDATE WIDGET SETTINGS
	function update($new_instance, $old_instance) {
		$instance['title']       = $new_instance['title'];
		$instance['post-ID']     = $new_instance['post-ID'];
		$instance['theme']       = $new_instance['theme'];
		$instance['size']        = $new_instance['size'];
		$instance['fx']          = $new_instance['fx'];
		$instance['timeout']     = $new_instance['timeout'];
		$instance['speed']       = $new_instance['speed'];
		$instance['gtitle']       = $new_instance['gtitle'];
		$instance['description'] = $new_instance['description'];
		$instance['navigation']  = $new_instance['navigation'];
		$instance['pager']       = $new_instance['pager'];
		$instance['pages']       = $new_instance['pages'];

		return $instance;
	}

	// WIDGET SETTINGS FORM
	function form($instance) {
		global $wpdb, $gog_settings;

		// Build dropdown with galleries
		$posts = $wpdb->get_results($wpdb->prepare("
			SELECT ID, post_title FROM $wpdb->posts
				WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
		));

	if (!$posts) {
?>
	<p><?php echo __( 'No galleries found.' ); ?></p>
<?php
	}
	else {
		foreach ($posts as $p) {
			$selected = '';
			if ($instance['post-ID']) {
				$selected = $p->ID == $instance['post-ID'] ? ' selected="yes"' : '';
			}
			$gallery_options .= "<option value=\"$p->ID\"$selected>$p->post_title</option>";
		}

		// Build dropdown with themes
		$theme_options = '';
		if ($gog_settings['themes']['active']) {
			foreach ( $gog_settings['themes']['available'] as $class => $name ) {
				$selected = '';
				if ($instance['theme']) {
					$selected = $class == $instance['theme'] ? ' selected="yes"' : '';
				}
				$theme_options .= "<option value=\"$class\"$selected>$name</option>";
			}
		}

		$title = apply_filters( 'widget_title', $instance['title'] );
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>" title="<?php echo __( 'Title of the widget' ); ?>"><?php echo __( 'Title' ); ?>:</label>
		<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br />
		<span class="description"><?php echo __( "The title is only used in the administration." ); ?></span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('post-ID'); ?>" title="<?php echo __( 'Select gallery' ); ?>" style="line-height:25px;"><?php echo __( 'Gallery' ); ?>:</label>
		<select id="<?php echo $this->get_field_id('post-ID'); ?>" name="<?php echo $this->get_field_name('post-ID'); ?>" style="width: 150px;">
			<?php echo $gallery_options; ?>
		</select>
	</p>

<?php if ($theme_options): ?>
	<p>
		<label for="<?php echo $this->get_field_id('theme'); ?>" title="<?php echo __( 'Select theme' ); ?>" style="line-height:25px;"><?php echo __( 'Theme' ); ?>:</label>
		<select id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>" style="width: 150px;">
			<option value="">Select theme</option>
			<?php echo $theme_options; ?>
		</select>
	</p>
<?php endif; ?>

	<p>
		<label for="<?php echo $this->get_field_id('fx'); ?>" title="<?php echo __( 'Animation' ); ?>" style="line-height:25px;"><?php echo __( 'Fx' ); ?>:</label>
		<select id="<?php echo $this->get_field_id('fx'); ?>" name="<?php echo $this->get_field_name('fx'); ?>" style="width: 150px;">
			<option value="scrollHorz"<?php echo $instance['fx'] == 'scrollHorz' ? ' selected="yes"' : ''; ?>><?php echo __( 'Horizontal scroll' ); ?></option>
			<option value="scrollVert"<?php echo $instance['fx'] == 'scrollVert' ? ' selected="yes"' : ''; ?>><?php echo __( 'Vertical scroll' ); ?></option>
			<option value="fade"<?php echo $instance['fx'] == 'fade' ? ' selected="yes"' : ''; ?>><?php echo __( 'Fade' ); ?></option>
			<option value="none"<?php echo $instance['fx'] == 'none' ? ' selected="yes"' : ''; ?>><?php echo __( 'None (Standard gallery)' ); ?></option>
		</select>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('timeout'); ?>" title="<?php echo __( 'Cycle animation timeout' ); ?>"><?php echo __( 'Timeout' ); ?>:</label>
		<input id="<?php echo $this->get_field_id('timeout'); ?>" name="<?php echo $this->get_field_name('timeout'); ?>" type="text" value="<?php echo $instance['timeout']; ?>" size="5" /> <span>ms</span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('speed'); ?>" title="<?php echo __( 'Speed of animation' ); ?>"><?php echo __( 'Speed' ); ?>:</label>
		<input id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo $instance['speed']; ?>" size="5" /> <span>ms</span>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('size'); ?>" title="<?php echo __( 'Select gallery size' ); ?>" style="line-height:25px;"><?php echo __( 'Image size' ); ?>:</label>
		<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
			<option value="thumbnail"<?php echo $instance['size'] == 'thumbnail' ? ' selected="yes"' : ''; ?>><?php echo __( 'Thumbnail' ); ?></option>
			<option value="medium"<?php echo $instance['size'] == 'medium' ? ' selected="yes"' : ''; ?>><?php echo __( 'Medium' ); ?></option>
			<option value="large"<?php echo $instance['size'] == 'large' ? ' selected="yes"' : ''; ?>><?php echo __( 'Large' ); ?></option>
			<option value="full"<?php echo $instance['size'] == 'full' ? ' selected="yes"' : ''; ?>><?php echo __( 'Full' ); ?></option>
		</select>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('gtitle'); ?>" type="checkbox" name="<?php echo $this->get_field_name('gtitle'); ?>"<?php echo $instance['gtitle'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('gtitle'); ?>" title="<?php echo __( 'Select if the title should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show title' ); ?></label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('description'); ?>" type="checkbox" name="<?php echo $this->get_field_name('description'); ?>"<?php echo $instance['description'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('description'); ?>" title="<?php echo __( 'Select if the description should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show description' ); ?></label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('pager'); ?>" type="checkbox" name="<?php echo $this->get_field_name('pager'); ?>"<?php echo $instance['pager'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('pager'); ?>" title="<?php echo __( 'Select if a pager should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show pager' ); ?></label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id('navigation'); ?>" type="checkbox" name="<?php echo $this->get_field_name('navigation'); ?>"<?php echo $instance['navigation'] ? ' checked="checked"' : ''; ?> />
		<label for="<?php echo $this->get_field_id('navigation'); ?>" title="<?php echo __( 'Select if a navigation should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show navigation' ); ?></label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('pages'); ?>" title="<?php echo __( 'Title of the widget' ); ?>"><?php echo __( 'Show on' ); ?>:</label><br />
		<textarea id="<?php echo $this->get_field_id('pages'); ?>" name="<?php echo $this->get_field_name('pages'); ?>" rows="3" cols="24"><?php echo $instance['pages']; ?></textarea><br />
		<span class="description"><?php echo __("Type paths that the gallery should be visible on, separate them with a line break. Leave empty to show in all places.<br />Don't type full url's, only paths with a starting and ending slash.<br />Example: /my-page-path/"); ?></span>
	</p>
<?php
	}
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("GoodOldGalleryWidget");') );
