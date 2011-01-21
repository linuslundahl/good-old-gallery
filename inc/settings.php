<?php

/**
 * Setup settings form on settings page.
 */
function goodold_gallery_setup_settings( $load_all = true ) {
	global $gog_cycle_settings;

	$gog_options = array();

	$gog_options['paragraph-1'] = array(
		"value" => GOG_PLUGIN_NAME . " created and maintained by <a href=\"http://unwi.se/\">Linus Lundahl</a>." .
							 '<div class="go-flattr"><a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/good-old-gallery/"></a></div>',
		"type" => "paragraph"
	);

	$gog_options['title-1'] = array(
		"name" => "Instructions",
		"type" => "title"
	);

	$gog_options['paragraph-2'] = array(
		"value" => "These settings are the defaults that will be used by Cycle (if an animation is chosen), settings can be overridden by adding variables to the <code>[good-old-gallery]</code> shortcode.<br />" .
		           "Use the built in generator found under the <em>" . GOG_PLUGIN_NAME . "</em> tab, just click the <em>Add an Image</em> button to find the tab on the far right.",
		"type" => "paragraph"
	);

	$gog_options['tip'] = array(
		"value" => "<div class=\"tip\"><strong>Tip!</strong> You can use " . GOG_PLUGIN_NAME . " with regular galleries that you have uploaded on a page/post, just don't enter any id in the shortcode and it will look for a gallery attached to the current page/post.</div>",
		"type" => "markdown"
	);


	$gog_options['title-2'] = array(
		"name" => "Gallery Settings",
		"type" => "title"
	);

	// Get themes for select dropdown
	$theme_options = array('No theme' => '');
	$theme_options += goodold_gallery_get_themes( true );

	if ($theme_options) {
		$gog_options['start-1'] = array(
			"type" => 'start-table'
		);

		$gog_options['theme'] = array(
			"name" => "Theme",
			"desc" => "Select what theme Cycle galleries should use.",
			"id" => "theme",
			"type" => "select",
			"options" => $theme_options,
			"std" => $gog_cycle_settings['theme']
		);

		$gog_options['end-1'] = array(
			"type" => 'end-table'
		);
	}

	if ( $load_all ) {
		$gog_options['markdown-1'] = array(
			"value" => goodold_gallery_settings_themes(),
			"type" => "markdown"
		);
	}

	$gog_options['start-2'] = array(
		"type" => 'start-table'
	);

	$gog_options['size'] = array(
		"name" => "Image size",
		"desc" => "Select what image size the galleries should use.",
		"id" => "size",
		"type" => "select",
		"options" => array(
			'Thumbnail' => 'thumbnail',
			'Medium' => 'medium',
			'Large' => 'large',
			'Full' => 'full'
		),
		"std" => $gog_cycle_settings['size-select']
	);

	$gog_options['fx'] = array(
		"name" => "Transition animation",
		"desc" => "Animation that should be used.",
		"id" => "fx",
		"type" => "select",
		"options" => array(
			'Fade'                    => 'fade',
			'Horizontal scroll'       => 'scrollHorz',
			'Vertical scroll'         => 'scrollVert',
			'None (Standard WP gallery)' => 'none'
		),
		"std" => $gog_cycle_settings['fx']
	);

	$gog_options['speed'] = array(
		"name" => "Speed",
		"desc" => "milliseconds",
		"id" => "speed",
		"type" => "text",
		"std" => $gog_cycle_settings['speed'],
		"size" => 4
	);

	$gog_options['timeout'] = array(
		"name" => "Transition speed",
		"desc" => "milliseconds",
		"id" => "timeout",
		"type" => "text",
		"std" => $gog_cycle_settings['timeout'],
		"size" => 4
	);

	$gog_options['title'] = array(
		"name" => "Show title",
		"desc" => "Select if the title for each image should be displayed.",
		"id" => "title",
		"type" => "checkbox",
		"std" => $gog_cycle_settings['title']
	);

	$gog_options['description'] = array(
		"name" => "Show description",
		"desc" => "Select if the description for each image should be displayed.",
		"id" => "description",
		"type" => "checkbox",
		"std" => $gog_cycle_settings['description']
	);

	$gog_options['pager'] = array(
		"name" => "Show pager",
		"desc" => "Select if the pager should be displayed.",
		"id" => "pager",
		"type" => "checkbox",
		"std" => $gog_cycle_settings['pager']
	);

	$gog_options['navigation'] = array(
		"name" => "Show navigation",
		"desc" => "Select if you would like to add PREV and NEXT buttons.",
		"id" => "navigation",
		"type" => "checkbox",
		"std" => $gog_cycle_settings['navigation']
	);

	$gog_options['prev'] = array(
		"name" => "Prev",
		"desc" => "Text used for prev button",
		"id" => "prev",
		"type" => "text",
		"std" => 'prev',
		"size" => $gog_cycle_settings['prev']
	);

	$gog_options['next'] = array(
		"name" => "Next",
		"desc" => "Text used for next button",
		"id" => "next",
		"type" => "text",
		"std" => 'next',
		"size" => $gog_cycle_settings['next']
	);

	$gog_options['end-2'] = array(
		'type' => 'end-table'
	);

	return $gog_options;
}

/**
 * Build themes function for settings page
 */
function goodold_gallery_settings_themes() {
	global $gog_settings;

	$themes = goodold_gallery_get_themes();

	$ret = '';
	if ( $themes ) {
		$ret .= '<ul class="available-themes">';
		foreach ( $themes as $file => $theme ) {
			$author = filter_var($theme['AuthorURI'], FILTER_VALIDATE_URL) ? '<a href="' . $theme['AuthorURI'] . '">' . $theme['Author'] . '</a>' : $theme['Author'];

			$screenshot = '..' . GOG_PLUGIN_PATH . '/themes/' . substr($file, 0, -4) . '.png';
			$screenshot = file_exists($screenshot) ? '<div class="screenshot"><img src="' . $screenshot . '" alt="' . $theme['Name'] . '" /></div>' : '';

			// $id = substr($file, 0, -4);

			// $active = '';
			// if ( is_array($gog_settings['active_themes']) ) {
			// 	$active = in_array($file, $gog_settings['active_themes']) ? ' checked="checked"' : '';
			// }

			// $disabled = '';
			// $id_addon = '';
			// if ( $gog_settings['theme'] == $file ) {
			// 	$disabled = ' disabled="disabled" checked="checked"';
			// 	$id_addon = '-disabled';
			// 	$active = '';
			// }

			$ret .= '<li class="theme">';
			$ret .= $screenshot;
			$ret .= '<span class="title">' . $theme['Name'] . '</span> <span class="version">' . $theme['Version'] . '</span> by <span class="author">' . $author . '</span>';
			$ret .= '<div class="description">' . $theme['Description'] . '</div>';
			// $ret .= '<input type="checkbox" value="' . $file . '" id="' . $id . $id_addon . '" name="active_themes' . $id_addon . '[]"' . $active . $disabled . '/> <label for="' . $id . $id_addon . '">Active</label>';
			// if ( $disabled ) {
			// 	$ret .= '<input type="hidden" value="' . $file . '" name ="active_themes[]" />';
			// }
			$ret .= '</li>';
		}
		$ret .= '</ul>';
	}

	return $ret;
}

/**
 * Generate settings form.
 */
function goodold_gallery_settings_page() {
	global $gog_settings;

	$gog_options = goodold_gallery_setup_settings( false );

	if ( 'save' == $_REQUEST['action'] ) {
		$values = array();
		foreach ( $gog_options as $value ) {
			if ( $value['id'] ) {
				$values[$value['id']] = $_REQUEST[$value['id']];
			}
		}
		// Save active themes too
		// $values['active_themes'] = $_REQUEST['active_themes'];

		update_option( GOG_PLUGIN_SHORT . '_settings', $values );
		$gog_settings = get_settings( GOG_PLUGIN_SHORT . '_settings' );
	}
	else if ( 'reset' == $_REQUEST['action'] ) {
		delete_option( GOG_PLUGIN_SHORT . '_settings' );
	}

	if ( $_REQUEST['save'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
	if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings restored.' ) . '</strong></p></div>';

	$gog_options = goodold_gallery_setup_settings();

?>

<div class="wrap">

<h2><?php echo GOG_PLUGIN_NAME; ?> settings</h2>

<form id="go-settings-form" method="post" action="">

<?php foreach ( $gog_options as $value ) {
switch ( $value['type'] ) {

case "start-table":
?>
<table class="form-table">
<?php
break;

case "end-table";
?>
</table>
<?php
break;

case "title":
?>
<h3><?php echo __( $value['name'] ); ?></h3>
<?php
break;

case 'markdown':
?>
<?php echo $value['value']; ?>
<?php
break;

case 'paragraph':
?>
<p><?php echo $value['value']; ?></p>
<?php
break;

case 'text':
$set_value = $gog_settings[$value['id']] ? $gog_settings[$value['id']] : $value['std'];
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<input size="<?php echo $value['size']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo htmlspecialchars(stripslashes($set_value)); ?>" />
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case 'textarea':
$set_value = $gog_settings[$value['id']] ? $gog_settings[$value['id']] : $value['std'];
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="40" rows="5"><?php echo $set_value; ?></textarea>
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case 'select':
?>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label></th>
		<td>
			<select style="width:200px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $key => $item) { ?>
<?php
	$selected = '';
	$saved = $gog_settings[$value['id']];
	if ( $saved && ( $saved === $item ) ) {
		$selected = ' selected="selected"';
	} elseif ( !$saved && ( $saved === $item ) ) {
		$selected = ' selected="selected"';
	} elseif ( !$saved && ( $value['std'] === $item ) ) {
		$selected = ' selected="selected"';
	}
?>
			<option<?php echo " value=\"$item\"" . $selected; ?>><?php echo $key; ?></option>
<?php } ?>
			</select>
			<span class="description"><?php echo $value['desc']; ?></span>
		</td>
	</tr>
<?php
break;

case "checkbox":
$checked = $gog_settings[$value['id']] ? 'checked="checked"' : '';
?>
	<tr valign="top">
		<th scope="row"><?php echo $value['name']; ?></th>
		<td>
			<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="1" <?php echo $checked; ?> />
			<label for="<?php echo $value['id']; ?>"><?php echo $value['desc']; ?></label>
		</td>
	</tr>
<?php
break;
}
}
?>

	<p class="submit">
		<input class="button-primary" name="save" type="submit" value="Save settings" />
		<input type="hidden" name="action" value="save" />
	</p>
	</form>

	<form method="post" action="">
	<p class="submit reset">
		<input name="reset" type="submit" value="Reset default settings" />
		<input type="hidden" name="action" value="reset" />
	</p>
	</form>

	</div>

<?php
}

function goodold_gallery_create_menu() {
	add_submenu_page( 'edit.php?post_type=goodoldgallery', GOG_PLUGIN_NAME . ' settings', 'Settings', 'manage_options', 'goodoldgallery', 'goodold_gallery_settings_page' );
}
add_action( 'admin_menu', 'goodold_gallery_create_menu' );
