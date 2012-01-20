<?php

register_activation_hook( __FILE__, 'goodold_gallery_default_settings' );
add_action( 'admin_init', 'goodold_gallery_settings_init' );
add_action( 'admin_menu', 'goodold_gallery_add_settings_page' );

/**
 * Save default settings.
 */
function goodold_gallery_default_settings() {
	global $gog_settings, $gog_default_settings;

	if (!is_array($gog_settings)) {
		update_option( GOG_PLUGIN_SHORT . '_settings', $gog_default_settings );
		$gog_settings = $gog_default_settings;
	}
}

/**
 * Register form fields.
 */
function goodold_gallery_settings_init(){
	global $gog_settings, $gog_default_settings;

	register_setting( GOG_PLUGIN_SHORT . '_settings', GOG_PLUGIN_SHORT . '_settings', 'goodold_gallery_settings_validate' );

	// Setup form
	$form = array(
		// Section
		'settings' => array(
			'title'    => 'Settings',
			'callback' => 'settings_header',
			'fields'   => array(
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
				'title' => array(
					'title' => 'Show title',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Show title for each image.',
					),
				),
				'description' => array(
					'title' => 'Show description',
					'type'  => 'checkbox',
					'args'  => array(
						'label' => 'Show description for each image.',
					),
				),
				'animation' => array(
					'title' => 'Transition animation',
					'type'  => 'dropdown',
					'args'  => array(
						'items' => array(
							'fade' => 'Fade',
							'slide' => 'Slide',
							'none' => 'None (Standard WP gallery)',
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
		'controls' => array(
			'title'    => 'Controls',
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

	goodold_gallery_parse_form( $form, 'settings', __FILE__, $gog_settings, $gog_default_settings );
}

/**
 * Add settings page.
 */
function goodold_gallery_add_settings_page() {
	add_submenu_page( 'edit.php?post_type=goodoldgallery', GOG_PLUGIN_NAME . ' settings', 'Settings', 'administrator', GOG_PLUGIN_SHORT . '_settings', 'goodold_gallery_settings_page' );
}

/**
 * Main section header.
 */
function goodold_gallery_settings_header() {
}

/**
 * Print settings page
 */
function goodold_gallery_settings_page() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo GOG_PLUGIN_NAME; ?> settings</h2>
		<div class="go-flattr"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/good-old-gallery/"></a></div>
		<p><?php echo sprintf(__( 'These settings are the defaults that will be used by Flexslider (if an animation is chosen), settings can be overridden by adding variables to the %s shortcode.' ), '<code>[good-old-gallery]</code>'); ?><br />
		<?php echo sprintf(__( 'Use the built in generator found under the %s tab, just click the %s button to find the tab on the far right.' ), '<em>' . GOG_PLUGIN_NAME . '</em>', '<em>' . __( 'Add an Image' ) . '</em>'); ?><br /><br />
		<?php echo sprintf(__( 'More help can be found at %s' ), '<a href="http://wordpress.org/extend/plugins/good-old-gallery/installation/">wordpress.org</a>'); ?><br /><br />
		<?php echo sprintf(__( '%s created and maintained by %s.' ), GOG_PLUGIN_NAME, '<a href="http://unwi.se/">Linus Lundahl</a>'); ?></p>
		<div class="tip"><strong><?php echo __( 'Tip' ); ?>!</strong> <?php echo sprintf(__( "You can use %s with regular galleries that you have uploaded on a page/post, just don't enter any id in the shortcode and it will look for a gallery attached to the current page/post."), GOG_PLUGIN_NAME); ?></div>
		<form action="options.php" method="post" id="go-settings-form">
		<?php settings_fields( GOG_PLUGIN_SHORT . '_settings' ); ?>
		<?php do_settings_sections( __FILE__ ); ?>
		<p class="submit">
			<?php submit_button( __( 'Save Settings' ), 'primary', GOG_PLUGIN_SHORT . '_settings[save]', false ); ?>
		</p>
		</form>
	</div>
<?php
}

function goodold_gallery_settings_validate($input) {
	$input['speed'] = is_numeric($input['speed']) ? $input['speed'] : '';
	$input['timeout'] = is_numeric($input['timeout']) ? $input['timeout'] : '';
	return $input;
}
