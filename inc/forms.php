<?php

/**
 * Loops through the form array and prints sections and fields accordingly.
 */
function goodold_gallery_parse_form( $form, $name, $page, $saved, $default ) {
	// Loop through form array
	foreach ( $form as $section_id => $section ) {
		// Print section
		add_settings_section(
			// $id
			GOG_PLUGIN_SHORT . '_' . $section_id,
			// $title
			__( $section['title'], "fokus" ),
			// $callbackf
			'goodold_gallery_' . $section['callback'],
			// $page
			$page
		);

		// Print fields in section
		if ( !empty($section['fields']) && is_array($section['fields']) ) {
			foreach ( $section['fields'] as $id => $field ) {
				$field['args']['id'] = $id;
				$field['args']['default'] = $saved[$id] ? $saved[$id] : $default[$id];
				$field['args']['name'] = GOG_PLUGIN_SHORT . '_' . $name . '[' . $id . ']';

				add_settings_field(
					// $id
					$id,
					// $title
					'<label for="' . $id . '">' . __( $field['title'], "fokus" ) .'</label>',
					// $callback (input type callback)
					'goodold_gallery_input_' . $field['type'],
					// $page
					$page,
					// $section
					GOG_PLUGIN_SHORT . '_' . $section_id,
					// $args
					$field['args']
				);
			}
		}
	}
}

/**
 * Register errors and notices
 */
function goodold_gallery_notices() {
	settings_errors();
}
add_action( 'admin_notices', 'goodold_gallery_notices' );

function goodold_gallery_input_dropdown($args) {
	extract($args);

	echo '<select id="' . $id . '" name="' . $name . '">';
	foreach($items as $key => $item) {
		$selected = ($default === $key) ? ' selected="selected"' : '';
		echo "<option value=\"$key\"$selected>" . __( $item ) . "</option>";
	}
	echo "</select>";
	echo isset($desc) ? '<span class="description"> ' . __( $desc ) . '</span>' : '';
}

function goodold_gallery_input_textarea($args) {
	extract($args);

	echo "<textarea id=\"$id\" name=\"$name\" rows=\"7\" cols=\"50\" type=\"textarea\">{$default}</textarea>";
}

function goodold_gallery_input_text($args) {
	extract($args);

	$size = isset($size) ? $size : 40;

	echo "<input id=\"$id\" name=\"$name\" size=\"$size\" type=\"text\" value=\"{$default}\" />";
	echo isset($desc) ? '<span class="description"> ' . __( $desc ) . '</span>' : '';
}

function goodold_gallery_input_password($args) {
	extract($args);

	echo "<input id=\"$id\" name=\"$name\" size=\"40\" type=\"password\" value=\"\" />";
}

function goodold_gallery_input_checkbox($args) {
	extract($args);

	$checked = $default ? ' checked="checked"' : '';
	echo "<input id=\"$id\" name=\"$name\" type=\"checkbox\"$checked />";
	echo isset($label) ? '<label for="' . $id . '"> ' . __ ( $label ) . '</label>' : '';
}

function goodold_gallery_input_radio($args) {
	extract($args);

	foreach($items as $key => $item) {
		$checked = ($default == $key) ? ' checked="checked"' : '';
		echo "<label><input value=\"$key\" name=\"$name\" type=\"radio\"$checked /> $item</label><br />";
	}
}
