<?php

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
	global $post, $gog_default_settings, $gog_settings, $gog_default_themes, $gog_themes;

	static $i = 1;

	extract(shortcode_atts( array(
		'id'          => null,
		'order'       => 'ASC',
		'orderby'     => 'menu_order ID',
		'exclude'     => array(),
		'theme'       => $gog_themes['default']       ? $gog_themes['theme']['class'] : $gog_default_themes['default'],
		'size'        => $gog_settings['size']        ? $gog_settings['size']         : $gog_default_settings['size'],
		'fx'          => $gog_settings['fx']          ? $gog_settings['fx']           : $gog_default_settings['fx'],
		'speed'       => $gog_settings['speed']       ? $gog_settings['speed']        : $gog_default_settings['speed'],
		'timeout'     => $gog_settings['timeout']     ? $gog_settings['timeout']      : $gog_default_settings['timeout'],
		'title'       => $gog_settings['title']       ? $gog_settings['title']        : $gog_default_settings['title'],
		'description' => $gog_settings['description'] ? $gog_settings['description']  : $gog_default_settings['description'],
		'navigation'  => $gog_settings['navigation']  ? $gog_settings['navigation']   : $gog_default_settings['navigation'],
		'pager'       => $gog_settings['pager']       ? $gog_settings['pager']        : $gog_default_settings['pager'],
		'prev'        => $gog_settings['prev']        ? $gog_settings['prev']         : $gog_default_settings['prev'],
		'next'        => $gog_settings['next']        ? $gog_settings['next']         : $gog_default_settings['next']
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

		// NAVIGATION CLASS
		if ( $navigation = $navigation ? $navigation : '' ) {
			$classes .= " has-nav";
		}

		// PAGER CLASS
		if ( $pager = $pager ? $pager : '' ) {
			$classes .= " has-pager";
		}

		if ( $attachments ) {
			// GO-GALLERY ID
			$ret .= '<div id="go-gallery-' . $id . '-' . $i . '" class="go-gallery go-gallery-' . $id . $classes . '">' . "\n";

			// INNER CLASS
			$ret .= '<div class="inner">' . "\n";

			// INSERT ATTACHMENTS
			foreach ( $attachments as $gallery_id => $attachment ) {
				$link = get_post_meta($attachment->ID, "_goodold_gallery_image_link", true);

				$ret .= '<div class="image">' . "\n";

				if ( ($title || $description) && ($attachment->post_title || $attachment->post_content) ) {
					$ret .= '<div class="meta">' . "\n";
					if ( $title && $attachment->post_title ) {
						$ret .= '<span class="title">' . $attachment->post_title . '</span>' . "\n";
					}
					if ( $description && $attachment->post_content ) {
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

			// END INNER CLASS
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


			// BUILD VARIABLES IN SCRIPT
			$script = 'fx: "' . $fx . '",' .
								'speed: "' . $speed . '",' .
								'timeout: "' . $timeout . '",' .
								$pager .
								$navigation;


			// FINISH SCRIPT
			$ret .= '<script type="text/javascript">' .
								'jQuery(function($) { ' .
									'$("#go-gallery-' . $id . '-' . $i . ' .inner").cycle({' .
										rtrim($script, ',') .
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
add_shortcode( 'good-old-gallery', 'goodold_gallery_shortcode' );
