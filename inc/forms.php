<?php

function goodold_gallery_error_handling() {
	if ( isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) ) {
		$value = __('Settings saved.');
		echo "<div id='setting-error-settings_updated' class='updated settings-error'>";
		echo "<p><strong>$value</strong></p>";
		echo "</div>";
	}
}

function goodold_gallery_setting_dropdown($args) {
	extract($args);

	echo '<select id="' . $id . '" name="' . $name . '[' . $id . ']">';
	foreach($items as $key => $item) {
		$selected = ($default == $key) ? ' selected="selected"' : '';
		echo "<option value='$key'$selected>$item</option>";
	}
	echo "</select>";
	echo $desc ? '<span class="description"> ' . $desc . '</span>' : '';
}

function goodold_gallery_setting_textarea($args) {
	extract($args);

	echo "<textarea id=\"" . $id . "\" name=\"" . $name . '[' . $id . "]\" rows='7' cols='50' type='textarea'>{$default}</textarea>";
}

function goodold_gallery_setting_text($args) {
	extract($args);

	$size = $size ? $size : 40;

	echo "<input id=\"" . $id . "\" name=\"" . $name . '[' . $id . "]\" size='$size' type='text' value='{$default}' />";
	echo $desc ? '<span class="description"> ' . $desc . '</span>' : '';
}

function goodold_gallery_setting_password($args) {
	extract($args);

	echo "<input id=\"" . $id . "\" name=\"" . $name . '[' . $id . "]\" size='40' type='password' value='{$default}' />";
}

function goodold_gallery_setting_checkbox($args) {
	extract($args);

	$checked = $default ? ' checked="checked"' : '';
	echo "<input id=\"" . $id . "\" name=\"" . $name . '[' . $id . "]\" type='checkbox'$checked />";
	echo $label ? '<label for="' . $id . '"> ' . $label . '</label>' : '';
}

function goodold_gallery_setting_radio($args) {
	extract($args);

	foreach($items as $key => $item) {
		$checked = ($default == $key) ? ' checked="checked"' : '';
		echo "<label><input value='$key' name=\"" . $name . '[' . $id . "]\" type='radio'$checked /> $item</label><br />";
	}
}
