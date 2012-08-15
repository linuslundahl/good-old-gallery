<?php

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode( $attr ) {
	global $post, $gog_settings;

	static $i = 1;

	// Build Slider default settings
	$slider = array();
	foreach ( $gog_settings->plugin['settings'] as $key => $value ) {
		$new_key = strtolower($key);
		$slider[$new_key] = $key;
	}

	$settings = array();
	foreach ( $slider as $key => $large ) {
		$settings[$key] = array(
			'key' => $large,
			'val' => $gog_settings->settings[$large],
		);
	}

	// Get settings from shortcode
	$attr = shortcode_atts( array(
		'id'                => null,
		'order'             => 'ASC',
		'orderby'           => 'menu_order ID',
		'exclude'           => array(),
		'theme'             => $gog_settings->themes['default'],
		'size'              => $gog_settings->settings['size'],
		'set_width'         => $gog_settings->settings['set_width'],
		'set_height'        => $gog_settings->settings['set_height'],
		'title'             => $gog_settings->settings['title'],
		'description'       => $gog_settings->settings['description'],
	) + $settings, $attr );

	// Setup Slider settings array
	$settings = array_slice($attr, 9);
	foreach( $settings as $key => $setting ) {
		$settings[$key] = array(
			'key' => $slider[$key],
			'val' => is_array($setting) ? $setting['val'] : $setting,
		);
	}

	// Extract GOG settings to vars
	extract(array_slice($attr, 0, 10));

	// Use post_id if no id is set in shortcode.
	$id = ( !$id && $post->ID ) ? $post->ID : $id;

	// Kill the function if no id is still not found.
	if ( !$id ) {
		return;
	}

	// Add settings from plugin shortcode extras function
	$sc_extra_settings = array(
		'id' => $id,
		'i' => $i,
		'settings' => $settings,
	);

	$sc_extras = $gog_settings->LoadPlugin( array( 'function' => 'shortcode_extras', 'settings' => $sc_extra_settings ) );
	if ( is_array($sc_extras) && !empty($sc_extras) ) {
		extract($sc_extras);
		$settings += $settings_extras;
	}

	$ret = '';
	if ( $settings['animation']['val'] == 'none' || empty($gog_settings->settings['plugin'])) {
		$ret .= do_shortcode( '[gallery id="' . $id . '"]' );
	}
	else {
		$attachments = get_children( array(
			'post_parent'     => $id,
			'post_status'     => 'inherit',
			'post_type'       => 'attachment',
			'post_mime_type'  => 'image',
			'order'           => $order,
			'orderby'         => $orderby,
			'exclude'         => $exclude
		) );

		$classes = ' go-gallery-' . $id;

		// Add theme class
		if ( $theme ) {
			$classes .= ' ' . $theme;
		}
		else if ( empty($theme) && $gog_settings->themes['default'] ) {
			$classes .= ' ' . $gog_theme['theme']['class'];
		}

		// Navigation class
		if ( $navigation ) {
			$classes .= " has-nav";
		}

		// Pager class
		if ( $pager ) {
			$classes .= " has-pager";
		}

		// Animation class
		if ( $settings['animation']['val'] ) {
			$classes .= " " . $settings['animation']['val'];
		}

		// Plugin class
		if ( $gog_settings->settings['plugin'] ) {
			$classes .= " " . $gog_settings->settings['plugin'];
		}

		if ( $attachments ) {

			// Generate images
			$width = 0;
			$height = 0;
			$images = '';
			foreach ( $attachments as $gallery_id => $attachment ) {
				$link = get_post_meta($attachment->ID, "_goodold_gallery_image_link", true);

				// Start list item
				$images .= '<li>' . "\n";

					// Sort fields in set order
					$order = goodold_gallery_order_fields($gog_settings);
					$order = array_flip($order);
					foreach ($order as $field => $key) {
						$order[$field] = '';
					}

					// Add title
					if ( $title != 'false' && $attachment->post_title ) {
						$order['title'] = '<div class="title">' . $attachment->post_title . '</div>' . "\n";
					}

					// Add description
					if ( $description != 'false' && $attachment->post_content ) {
						$order['desc'] = '<div class="description">' . $attachment->post_content . '</div>' . "\n";
					}

				$order['image'] .= '<div class="image">';

				// Start link
				if ( $link ) {
					$order['image'] .= '<a href="' . $link . '">' . "\n";
				}

				// Add image
				$order['image'] .= wp_get_attachment_image( $gallery_id, $size, false ) . "\n";

				// End link
				if ( $link ) {
					$order['image'] .= '</a>' . "\n";
				}

				$order['image'] .= '</div>';

				foreach ( $order as $field ) {
					$images .= !is_numeric($field) ? $field : '';
				}

				// End list item
				$images .= '</li>' . "\n";

				$gallery_style = '';
				if ( $set_width || $set_height ) {
					$img_data = wp_get_attachment_image_src( $gallery_id, $size, false );
					// Get width
					if ( $set_width ) {
						$width = goodold_gallery_get_img_size( $set_width, $img_data[1], $width );
						$gallery_style .= 'width: ' . $width . 'px;';
					}
					// Get height
					if ( $set_height ) {
						$height = goodold_gallery_get_img_size( $set_height, $img_data[2], $height );
						$gallery_style .= ' height: ' . $height . 'px;';
					}
				}

			}

			$gallery_style = !empty($gallery_style) ? ' style="' . $gallery_style . '"' : '';

			// Begin gallery div and ul
			$ret .= '<div id="go-gallery-' . $id . '-' . $i . '" class="go-gallery-container' . $classes . '"' . $gallery_style . '>' . "\n";
			$ret .= '<div class="go-gallery-inner">' . "\n";
			$ret .= '<ul class="slides">' . "\n";

			// Insert images
			$ret .= $images;

			// End gallery ul
			$ret .= '</ul>' . "\n";

			// Add pager and navigation markup if set
			$ret .= is_string($pager) ? $pager : '';
			$ret .= is_string($navigation) ? $navigation : '';

			$ret .= '</div>' . "\n";

			// Build script
			$script = '';
			foreach( $settings as $key => $setting ) {
				if ( !empty( $setting ) ) {
					if ( !empty($setting['key']) && !empty($setting['val']) ) {
						$script .= $setting['key'];
						if ( $setting['val'] == "on" || $setting['val'] == "true" ) {
							$script .= ': true, ';
						}
						else if ( $setting['val'] == "off" || $setting['val'] == "false" ) {
							$script .= ': false, ';
						}
						else {
							$script .= ': "' . $setting['val'] . '", ';
						}
					}
				}
			}

			// Add script extras if set
			$script .= $script_extras;

			// Finish script
			$ret .= '<script type="text/javascript" charset="utf-8">jQuery(function($) { $(function () { $("#go-gallery-' . $id . '-' . $i . ' ' . $gog_settings->plugin['setup']['class'] . '").' . $gog_settings->settings['plugin'] . '({' . rtrim($script, ', ') . '}); }); });</script>' . "\n";

			// End gallery div
			$ret .= '</div>' . "\n";
		}
	}

	$i++;

	return $ret;
}
add_shortcode( 'good-old-gallery', 'goodold_gallery_shortcode' );

/**
 * Returns the saved order of title, desc and image
 */
function goodold_gallery_order_fields($settings) {
	$ret = array();
	foreach ( $settings as $key => $val ) {
		if ( strpos($key, 'order_') !== FALSE ) {
			$items[$val] = $key;
		}
	}

	if ( !empty($items) ) {
		ksort($items);
		foreach ( $items as $val => $key ) {
			$id = str_replace('order_', '', $key);
			$ret[] = $id;
		}
	}

	return $ret;
}

/**
 * Returns largest/smallest image width/height
 */
function goodold_gallery_get_img_size($type, $size, $height) {
	$ret = 0;

	if ( $type == 'largest' ) {
		$ret = $height < $size ? $size : $height;
	}
	else if ( $type == 'smallest' ) {
		if ( $height === 0 ) {
			$ret = $size;
		}
		else {
			$ret = $height > $size ? $size : $height;
		}
	}

	return $ret;
}