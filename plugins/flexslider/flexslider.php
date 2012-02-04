<?php

function goodold_gallery_flexslider_setup() {
	return array(
		'title' => 'Flexslider',
		'files' => array('jquery.flexslider-min.js'),
		'class' => '.go-gallery',
	);
}

function goodold_gallery_flexslider_settings() {
	return array(
		'animation'         => "slide",
		'slideshow'         => TRUE,
		'slideshowSpeed'    => 7000,
		'animationDuration' => 600,
		'directionNav'      => TRUE,
		'controlNav'        => TRUE,
		'keyboardNav'       => TRUE,
		'touchSwipe'        => TRUE,
		'prevText'          => "Prev",
		'nextText'          => "Next",
		'pausePlay'         => FALSE,
		'pauseText'         => 'Pause',
		'playText'          => 'Play',
		'randomize'         => FALSE,
		'slideToStart'      => 0,
		'animationLoop'     => TRUE,
		'pauseOnAction'     => TRUE,
		'pauseOnHover'      => FALSE,
	);
}

/**
 * Extends the settings form
 */
function goodold_gallery_flexslider_settings_form() {
	return array(
		// Section
		'flexslider_settings' => array(
			'title'    => 'Flexslider Settings',
			'callback' => 'settings_header',
			'fields'   => array(
				'animation' => array(
					'title' => 'Transition animation',
					'type'  => 'dropdown',
					'args'  => array(
						'items' => array(
							'' => '- Select animation -',
							'fade' => 'Fade',
							'slide' => 'Slide',
						),
						'desc' => 'Animation that should be used.',
					),
				),
				'slideshow' => array(
					'title' => 'Slide automatically',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Animate slider automatically by default.',
					),
				),
				'slideshowSpeed' => array(
					'title' => 'Slideshow speed',
					'type'  => 'text',
					'args'  => array(
						'size' => 4,
						'desc' => 'ms',
					),
				),
				'animationDuration' => array(
					'title' => 'Animation speed',
					'type'  => 'text',
					'args'  => array(
						'size' => 4,
						'desc' => 'ms',
					),
				),
				'randomize' => array(
					'title' => 'Randomize',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Randomize slide order on page load.',
					),
				),
				'animationLoop' => array(
					'title' => 'Loop',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Loop galleries when the final slide is reached.',
					),
				),
			),
		),
		// Section
		'flexslider_controls' => array(
			'title'    => 'Flexslider Controls',
			'callback' => 'settings_header',
			'fields'   => array(
				'controlNav' => array(
					'title' => 'Show pager',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Show pager marker for each image.',
					),
				),
				'directionNav' => array(
					'title' => 'Show navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Add PREV and NEXT buttons.',
					),
				),
				'prevText' => array(
					'title' => 'Prev',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the PREV button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
				'nextText' => array(
					'title' => 'Next',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the NEXT button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
				'keyboardNav' => array(
					'title' => 'Keyboard navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Allow users to navigate with the keyboards left/right arrows.',
					),
					'ignore' => TRUE,
				),
				'touchSwipe' => array(
					'title' => 'Swipe navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Allow users to use swipe gestures to navigate.',
					),
					'ignore' => TRUE,
				),
				'pausePlay' => array(
					'title' => 'Show Play/Pause button',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Add a PLAY/PAUSE button.',
					),
				),
				'playText' => array(
					'title' => 'Play',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the PLAY button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
				'pauseText' => array(
					'title' => 'Pause',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the PAUSE button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
				'pauseOnAction' => array(
					'title' => 'Pause on action',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Pause the slideshow when interacting with control elements.',
					),
				),
				'pauseOnHover' => array(
					'title' => 'Pause on hover',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Pause the slideshow when hovering over slider.',
					),
				),
			),
		),
	);
}

function goodold_gallery_flexslider_shortcode_extras( $settings ) {
	extract($settings);

	$ret = array(
		'navigation' => !empty($settings['directionnav']['val']) ? TRUE : '',
		'pager' => !empty($settings['controlnav']['val']) ? TRUE : '',
		'script_extras' => '',
		'settings_extras' => array(
			'controlscontainer' => array(
				'key' => 'controlsContainer',
				'val' => "#go-gallery-" . $id . '-' . $i . ' .go-gallery',
			),
		),
	);

	return $ret;
}
