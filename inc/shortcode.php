<?php

/**
 * Handles output for galleries
 */
function goodold_gallery_shortcode($attr) {
	global $post;

	static $i = 1;

	$gog_settings = get_settings( GOG_PLUGIN_SHORT . '_settings' );

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

			// BUILD VARIABLES IN SCRIPT
			$script = 'fx: "' . $fx . '",' .
								'speed: ' . $speed . ',' .
								'timeout: ' . $timeout . ',' .
								$pager .
								$navigation;


			// FINISH SCRIPT
			$ret .= '<script type="text/javascript">' .
								'jQuery(function($) { ' .
									'$("#go-gallery-' . $id . '-' . $i . ' .gallery").cycle({' .
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
