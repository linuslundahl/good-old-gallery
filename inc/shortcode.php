<?php

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
	global $post, $gog_default_settings, $gog_settings, $gog_default_themes, $gog_themes;

	static $i = 1;

	// Build Flexslider default settings
	$flex = array(
		'animation'         => 'animation',
		'slideshow'         => 'slideshow',
		'slideshowspeed'    => 'slideshowSpeed',
		'animationduration' => 'animationDuration',
		'directionnav'      => 'directionNav',
		'controlnav'        => 'controlNav',
		'keyboardnav'       => 'keyboardNav',
		'touchswipe'        => 'touchSwipe',
		'prevtext'          => 'prevText',
		'nexttext'          => 'nextText',
		'pauseplay'         => 'pausePlay',
		'pausetext'         => 'pauseText',
		'playtext'          => 'playText',
		'randomize'         => 'randomize',
		'slidetostart'      => 'slideToStart',
		'animationloop'     => 'animationLoop',
		'pauseonaction'     => 'pauseOnAction',
		'pauseonhover'      => 'pauseOnHover',
	);

	foreach( $flex as $setting ) {
		$settings[strtolower($setting)] = array(
			'key' => $setting,
			'val' => $gog_settings[$setting] ? $gog_settings[$setting] : $gog_default_settings[$setting],
		);
	}

	// Get settings from shortcode
	$attr = shortcode_atts( array(
		'id'                => null,
		'order'             => 'ASC',
		'orderby'           => 'menu_order ID',
		'exclude'           => array(),
		'theme'             => $gog_themes['default']             ? $gog_themes['theme']['class']      : $gog_default_themes['default'],
		'size'              => $gog_settings['size']              ? $gog_settings['size']              : $gog_default_settings['size'],
		'title'             => $gog_settings['title']             ? $gog_settings['title']             : $gog_default_settings['title'],
		'description'       => $gog_settings['description']       ? $gog_settings['description']       : $gog_default_settings['description'],
	) + $settings, $attr );

	// Setup Flexslider settings array
	$settings = array_slice($attr, 8);
	foreach( $settings as $key => $setting ) {
		$settings[$key] = array(
			'key' => $flex[$key],
			'val' => is_array($setting) ? $setting['val'] : $setting,
		);
	}

	$settings['controlscontainer'] = array(
		'key' => 'controlsContainer',
		'val' => "#go-gallery-" . $id . '-' . $i,
	);

	// Extract GOG settings to vars
	extract(array_slice($attr, 0, 7));

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
		if ( $settings['directionNav'] ) {
			$classes .= " has-nav";
		}

		// Pager class
		if ( $settings['controlNav'] ) {
			$classes .= " has-pager";
		}

		if ( $attachments ) {

			// Generate images
			$images = '';
			$width = 0;
			foreach ( $attachments as $gallery_id => $attachment ) {
				$link = get_post_meta($attachment->ID, "_goodold_gallery_image_link", true);

				$images .= '<li>' . "\n";

				if ( ( $title && $attachment->post_title ) || ( $description && $attachment->post_content ) ) {
					$images .= '<div class="meta">' . "\n";
					if ( $title && $attachment->post_title ) {
						$images .= '<span class="title">' . $attachment->post_title . '</span>' . "\n";
					}
					if ( $description && $attachment->post_content ) {
						$images .= '<span class="description">' . $attachment->post_content . '</span>' . "\n";
					}
					$images .= '</div>' . "\n";
				}

				if ( $link ) {
					$images .= '<a href="' . $link . '">' . "\n";
				}

				$images .= wp_get_attachment_image( $gallery_id, $size, false ) . "\n";

				$img_data = wp_get_attachment_image_src( $gallery_id, $size, false );
				$width = $width < $img_data[1] ? $img_data[1] : $width;

				if ( $link ) {
					$images .= '</a>' . "\n";
				}

				$images .= '</li>' . "\n";
			}

			// Begin gallery div and ul
			$ret .= '<div id="go-gallery-' . $id . '-' . $i . '" class="go-gallery-container' . $classes . '">' . "\n";
			$ret .= '<div class="go-gallery go-gallery-' . $id . '" style="width: ' . $width . 'px;">';
			$ret .= '<ul class="slides">' . "\n";

			// Insert images
			$ret .= $images;

			// End gallery ul
			$ret .= '</ul>' . "\n";
			$ret .= '</div>' . "\n";

			// Build script
			$script = '';
			foreach( $settings as $key => $setting ) {
				if ( !empty( $setting ) ) {
					$script .= $setting['key'];
					if ( is_bool( $setting['val'] ) ) {
						$script .= ( $setting['val'] == TRUE ) ? ': true, ' : ': false, ';
					}
					else if ( is_numeric( $setting['val'] ) ) {
						$script .= ': ' . $setting['val'] . ', ';
					}
					else if ( $setting['val'] == "on" ) {
						$script .= ': true, ';
					}
					else {
						$script .= ': "' . $setting['val'] . '", ';
					}
				}
			}

			// Finish script
			$ret .= '<script type="text/javascript" charset="utf-8">jQuery(function($) { $("#go-gallery-' . $id . '-' . $i . ' .go-gallery").flexslider({' . rtrim($script, ', ') . '}); });</script>';

			// End gallery div
			$ret .= '</div>' . "\n";
		}
	}

	$i++;

	return $ret;
}
add_shortcode( 'good-old-gallery', 'goodold_gallery_shortcode' );
