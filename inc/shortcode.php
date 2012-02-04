<?php

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
	global $post, $gog_default_settings, $gog_settings, $gog_default_themes, $gog_themes, $gog_plugin;

	static $i = 1;

	// Build Slider default settings
	$slider = array();
	foreach ( $gog_plugin['settings'] as $key => $value ) {
		$new_key = strtolower($key);
		$slider[$new_key] = $key;
	}

	foreach( $slider as $key => $large ) {
		$settings[$key] = array(
			'key' => $large,
			'val' => $gog_settings[$large] ? $gog_settings[$large] : $gog_default_settings[$large],
		);
	}

	// Get settings from shortcode
	$set_settings = !empty($gog_settings) ? $gog_settings : $gog_default_settings;
	$attr = shortcode_atts( array(
		'id'                => null,
		'order'             => 'ASC',
		'orderby'           => 'menu_order ID',
		'exclude'           => array(),
		'theme'             => isset($gog_themes['default'])       ? $gog_themes['theme']['class'] : $gog_default_themes['default'],
		'size'              => $set_settings['size'],
		'set_width'         => isset($set_settings['set_width'])   ? $set_settings['set_width']    : FALSE,
		'title'             => isset($set_settings['title'])       ? $set_settings['title']        : FALSE,
		'description'       => isset($set_settings['description']) ? $set_settings['description']  : FALSE,
	) + $settings, $attr );

	// Setup Slider settings array
	$settings = array_slice($attr, 8);
	foreach( $settings as $key => $setting ) {
		$settings[$key] = array(
			'key' => $slider[$key],
			'val' => is_array($setting) ? $setting['val'] : $setting,
		);
	}

	// Extract GOG settings to vars
	extract(array_slice($attr, 0, 8));

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
	$sc_extras = goodold_gallery_load_plugin( array( 'function' => 'shortcode_extras', 'settings' => $sc_extra_settings ) );
	if ( is_array($sc_extras) && !empty($sc_extras) ) {
		extract($sc_extras);
		$settings += $settings_extras;
	}

	$ret = '';
	if ( $animation == 'none' ) {
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

		$classes = "";

		// Add theme class
		if ( $theme ) {
			$classes .= ' ' . $theme;
		}
		else if ( empty($theme) && $gog_themes['default'] ) {
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

		if ( $attachments ) {

			// Generate images
			$width = 0;
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
					if ( $title && $attachment->post_title ) {
						$order['title'] = '<span class="title">' . $attachment->post_title . '</span>' . "\n";
					}

					// Add description
					if ( $description && $attachment->post_content ) {
						$order['desc'] = '<span class="description">' . $attachment->post_content . '</span>' . "\n";
					}

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

				foreach ( $order as $field ) {
					$images .= !is_numeric($field) ? $field : '';
				}

				// End list item
				$images .= '</li>' . "\n";

				// Get widest image width
				$gallery_width = '';
				if ( $set_width ) {
					$img_data = wp_get_attachment_image_src( $gallery_id, $size, false );
					$width = $width < $img_data[1] ? $img_data[1] : $width;
					$gallery_width = ' style="width: ' . $width . 'px;"';
				}

			}

			// Begin gallery div and ul
			$ret .= '<div id="go-gallery-' . $id . '-' . $i . '" class="go-gallery-container' . $classes . '">' . "\n";
			$ret .= '<div class="go-gallery go-gallery-' . $id . '"' . $gallery_width . '>' . "\n";
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
			$ret .= '<script type="text/javascript" charset="utf-8">jQuery(function($) { $("#go-gallery-' . $id . '-' . $i . ' ' . $gog_plugin['setup']['class'] . '").' . $gog_settings['plugin'] . '({' . rtrim($script, ', ') . '}); });</script>' . "\n";

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
