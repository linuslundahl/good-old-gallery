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

		// Build settings for shortcode
		if ( $show ) {
			$settings = '';
			foreach ( $instance as $key => $value ) {
				$key = strtolower($key);
				if ( $key != 'title' ) {
					if ( $value ) {
						$settings .= $key . '="' . $value . '" ';
					}
					else if ( $value === NULL ) {
						$settings .= $key . '="false" ';
					}
					else if ( $value === '' ) {
						// Print nothing
					}
					else {
						$settings .= $key . '="false" ';
					}

					$settings = str_replace('"on"', '"true"', $settings);
				}
			}

			echo $before_widget;
			echo do_shortcode( '[good-old-gallery ' . rtrim($settings) . ']' );
			echo $after_widget;
		}
	}

	// UPDATE WIDGET SETTINGS
	function update($new_instance, $old_instance) {
		global $gog_settings;

		$instance['title']             = $new_instance['title'];
		$instance['id']                = $new_instance['id'];
		$instance['theme']             = $new_instance['theme'];
		$instance['size']              = $new_instance['size'];

		foreach ( $gog_settings->plugin['settings_form'] as $section ) {
			foreach ( $section['fields'] as $setting => $item ) {
				if ( !isset($item['ignore']) ) {
					$setting = $setting == 'title' ? 'g' . $setting : $setting;
					$instance[$setting] = $new_instance[$setting];
				}
			}
		}

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
		$gallery_options = array();
		foreach ($posts as $p) {
			$gallery_options[$p->ID] = $p->post_title;
		}

		$title = apply_filters( 'widget_title', $instance['title'] );

		// Prepare widget form
		$widget_form = array(
			'basic_settings' => array(
				'title'    => 'Basic Settings',
				'callback' => 'settings_header',
				'fields'   => array(
					'title' => array(
						'title' => 'Title of the widget',
						'type'  => 'text',
						'args'  => array(
							'desc' => 'The title is only used in the administration.',
							'size' => 28,
						),
					),
					'id' => array(
						'title' => 'Select gallery',
						'type'  => 'dropdown',
						'args'  => array(
							'items' => $gallery_options,
						),
					),
					'size' => array(
						'title' => 'Size',
						'type'  => 'dropdown',
						'args'  => array(
							'items' => array(
								'thumbnail' => 'Thumbnail',
								'medium' => 'Medium',
								'large' => 'Large',
								'full' => 'Full',
							),
							'desc' => 'Image size used for the galleries.',
						),
					),
				),
			),
		);

		// Add theme options
		$themes = $gog_settings->GetThemes();
		$theme_options = array();
		foreach ( $themes as $theme ) {
			$theme_options[$theme['Class']] = $theme['Name'];
		}

		if ( $theme_options ) {
			$widget_form['basic_settings']['fields'] += array(
				'theme' => array(
					'title' => 'Select theme',
					'type'  => 'dropdown',
					'args'  => array(
						'items' => $theme_options,
					),
				),
			);
		}

		// Add plugin id and name to settings form for plugin
		$widget_form += $gog_settings->plugin['settings_form'];
		foreach ( $widget_form as $section => $form ) {
			foreach ( $widget_form[$section]['fields'] as $key => $item) {
				$widget_form[$section]['fields'][$key]['widget_id'] = $this->get_field_id($key);
				$widget_form[$section]['fields'][$key]['widget_name'] = $this->get_field_name($key);
			}
		}
?>

	<?php goodold_gallery_parse_form_custom( $widget_form, $instance ); ?>

<?php
	}
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("GoodOldGalleryWidget");') );
