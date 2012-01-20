<?php

function goodold_gallery_cycle_setup() {
	return array(
		'title' => 'jQuery Cycle',
		'files' => array('jquery.cycle.all.min.js'),
		'class' => '.slides',
	);
}

function goodold_gallery_cycle_settings() {
	return array(
		'fx'          => 'fade',
		'speed'       => 500,
		'timeout'     => 10000,
		'title'       => 0,
		'description' => 0,
		'navigation'  => 0,
		'pager'       => 0,
		'prev'        => 'prev',
		'next'        => 'next'
	);
}

function goodold_gallery_cycle_settings_form() {
	return array(
		// Section
		'cycle_settings' => array(
			'title'    => 'jQuery Cycle Settings',
			'callback' => 'settings_header',
			'fields'   => array(
				'fx' => array(
					'title' => 'Transition animation',
					'type'  => 'dropdown',
					'args'  => array(
						'items' => array(
							'fade' => 'Fade',
							'scrollHorz' => 'Horizontal scroll',
							'scrollVert' => 'Vertical scroll',
							// 'none' => 'None (Standard WP gallery)',
						),
						'desc' => 'Animation that should be used.',
					),
				),
				'timeout' => array(
					'title' => 'Transition speed',
					'type'  => 'text',
					'args'  => array(
						'size' => 4,
					),
				),
				'speed' => array(
					'title' => 'Animation speed',
					'type'  => 'text',
					'args'  => array(
						'size' => 4,
					),
				),
				'title' => array(
					'title' => 'Show title',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Select if the title for each image should be displayed.',
					),
				),
				'description' => array(
					'title' => 'Show description',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Select if the description for each image should be displayed.',
					),
				),
				'pager' => array(
					'title' => 'Show pager',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Select if the pager should be displayed.',
					),
				),
				'navigation' => array(
					'title' => 'Show navigation',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Select if you would like to add PREV and NEXT buttons.',
					),
				),
				'prev' => array(
					'title' => 'Prev',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for PREV button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
				'next' => array(
					'title' => 'Next',
					'type'  => 'text',
					'args'  => array(
						'desc' => 'Text used for NEXT button.',
						'size' => 4,
					),
					'ignore' => TRUE,
				),
			),
		),
	);
}

function goodold_gallery_cycle_widget() {
	return array();
}
