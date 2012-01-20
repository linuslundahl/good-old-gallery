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
							'fade' => 'Fade',
							'slide' => 'Slide',
							// 'none' => 'None (Standard WP gallery)',
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
				),
				'nextText' => array(
					'title' => 'Next',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the NEXT button.',
						'size' => 4,
					),
				),
				'keyboardNav' => array(
					'title' => 'Keyboard navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Allow users to navigate with the keyboards left/right arrows.',
					),
				),
				'touchSwipe' => array(
					'title' => 'Swipe navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Allow users to use swipe gestures to navigate.',
					),
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
				),
				'pauseText' => array(
					'title' => 'Pause',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for the PAUSE button.',
						'size' => 4,
					),
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

function goodold_gallery_flexslider_generator() {
	return <<<END
		<div class="flex-options">
			<p>
				<label for="animation" title="Animation'" style="line-height:25px;">Animation':</label>
				<select id="animation" name="animation">
					<option value="">Select animation'</option>
					<option value="none">None (Standard gallery)'</option>
					<option value="slide">Slide'</option>
					<option value="fade">Fade'</option>
				</select>
			</p>

			<p>
				<label for="slideshowspeed" title="Slideshow speed">Slideshow speed:</label>
				<input id="slideshowspeed" name="slideshowspeed" type="text" /> <span>ms</span>
			</p>

			<p>
				<label for="animationduration" title="Animation speed">Animation speed:</label>
				<input id="animationduration" name="animationduration" type="text" /> <span>ms</span>
			</p>

			<p>
				<input id="slideshow" type="checkbox" name="slideshow" value="1" />
				<label for="slideshow" title="Slide automatically" style="line-height:25px;">Slide automatically</label>
			</p>

			<p>
				<input id="randomize" type="checkbox" name="randomize" value="1" />
				<label for="randomize" title="Randomize" style="line-height:25px;">Randomize</label>
			</p>

			<p>
				<input id="loop" type="checkbox" name="loop" value="1" />
				<label for="loop" title="Loop" style="line-height:25px;">Loop</label>
			</p>

			<p>
				<input id="title" type="checkbox" name="title" value="1" />
				<label for="title" title="Select if the title should be displayed" style="line-height:25px;">Show title</label>
			</p>

			<p>
				<input id="description" type="checkbox" name="description" value="1" />
				<label for="description" title="Select if the description should be displayed" style="line-height:25px;">Show description</label>
			</p>

			<p>
				<input id="directionnav" type="checkbox" name="directionnav" value="1" />
				<label for="directionnav" title="Select if a navigation should be displayed" style="line-height:25px;">Show navigation</label>
			</p>

			<p>
				<input id="controlnav" type="checkbox" name="controlnav" value="1" />
				<label for="controlnav" title="Select if a pager should be displayed" style="line-height:25px;">Show pager</label>
			</p>

			<p>
				<input id="pauseplay" type="checkbox" name="pauseplay" value="1" />
				<label for="pauseplay" title="Select if a Play/Pause button should be displayed" style="line-height:25px;">Show Play/Pause</label>
			</p>
		</div>
END;
}

function goodold_gallery_flexslider_widget() {
	return array();
}
