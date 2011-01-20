<?php

/**
 * Setup settings form on settings page.
 */

$gog_options = array();
if ( is_admin() && $_GET['page'] == 'goodoldgallery' ) {
	$themes = array('No theme' => '');
	$themes += goodold_gallery_get_themes( true );

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
							 "Use the built in generator found under the '" . GOG_PLUGIN_NAME . "' tab, just click the 'Add an Image' button to find the tab on the far right.",
		"type" => "paragraph"
	);

	$gog_options['title-2'] = array(
		"name" => "Gallery Settings",
		"type" => "title"
	);

	$gog_options['start'] = array(
		"type" => 'start-table'
	);

	$gog_options['theme'] = array(
		"name" => "Theme",
		"desc" => "Select what theme Cycle galleries should use.",
		"id" => "theme",
		"type" => "select",
		"options" => $themes,
		"std" => $gog_cycle_settings['theme']
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
			'None (Standard gallery)' => 'none'
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
		"id" => "next",
		"type" => "text",
		"std" => 'prev',
		"size" => $gog_cycle_settings['prev']
	);

	$gog_options['next'] = array(
		"name" => "Next",
		"desc" => "Text used for next button",
		"id" => "prev",
		"type" => "text",
		"std" => 'next',
		"size" => $gog_cycle_settings['next']
	);

	$gog_options['end'] = array(
		'type' => 'end-table'
	);
}

/**
 * Generate settings form.
 */
function goodold_gallery_settings_page() {
	global $gog_options, $gog_settings;

	if ( 'save' == $_REQUEST['action'] ) {
		$values = array();
		foreach ( $gog_options as $value ) {
			if ( $value['id'] ) {
				$values[$value['id']] = $_REQUEST[$value['id']];
			}
		}
		update_option( GOG_PLUGIN_SHORT . '_settings', $values );
		$gog_settings = get_settings( GOG_PLUGIN_SHORT . '_settings' );
	}
	else if ( 'reset' == $_REQUEST['action'] ) {
		delete_option( GOG_PLUGIN_SHORT . '_settings' );
	}

	if ( $_REQUEST['save'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
	if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings restored.' ) . '</strong></p></div>';
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
			<label for="<?php echo $value['id']; ?>">
				<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="1" <?php echo $checked; ?> />
				<?php echo $value['desc']; ?>
			</label>
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
	add_submenu_page( 'edit.php?post_type=goodoldgallery', GOG_PLUGIN_NAME . ' Settings', 'Settings', 'manage_options', 'goodoldgallery', 'goodold_gallery_settings_page' );
}
add_action( 'admin_menu', 'goodold_gallery_create_menu' );
