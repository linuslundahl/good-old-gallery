<?php

/** @ignore */
if ($_SERVER['PHP_SELF'] == '/wp-admin/network/plugins.php') {
	require_once('../../wp-load.php');
	require_once('../includes/admin.php');
}
else {
	require_once('../wp-load.php');
	require_once('includes/admin.php');
}

/**
 * Adds Good Old Gallery media tab.
 */
function goodold_gallery_media_tab($tabs) {
	global $wpdb;

	$posts = $wpdb->get_results($wpdb->prepare("
		SELECT ID FROM $wpdb->posts
			WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
	));

	if ($posts) {
		$newtab = array('gogallery' => __( GOG_PLUGIN_NAME, 'gogallery' ));
		$tabs = array_merge($tabs, $newtab);
	}

	return $tabs;
}
add_filter( 'media_upload_tabs', 'goodold_gallery_media_tab' );

/**
 * Good Old Gallery tab page.
 */
function goodold_gallery_media_process() {
	global $wpdb, $gog_settings, $gog_plugin;

	media_upload_header();

	// Build dropdown with galleries
	$posts = $wpdb->get_results($wpdb->prepare("
		SELECT ID, post_title FROM $wpdb->posts
			WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
	));

	$options = !$posts ? "<option value=\"\">No galleries found</option>" : "";

	$gallery_options = '';
	foreach ( $posts as $p ) {
		$selected = '';
		$gallery_options .= "<option value=\"$p->ID\">$p->post_title</option>";
	}

	// Build dropdown with themes
	$theme_options = '';
	if ($gog_settings['themes']['active']) {
		foreach ( $gog_settings['themes']['available'] as $class => $name ) {
			$theme_options .= "<option value=\"$class\">$name</option>";
		}
	}
?>
<div id="go-gallery-generator">
	<h3 class="media-title"><?php echo GOG_PLUGIN_NAME; ?> shortcode generator</h3>

	<div class="postbox submitdiv">
		<h3 style="margin: 0; padding: 10px;">Generator</h3>
		<div class="inside" style="margin: 10px;">
			<span class="description">
				<p>
					<?php echo __( 'Copy and paste the full generated shortcode into your page/post in HTML mode.' ); ?>
				</p>
			</span>

			<div id="good-old-gallery-shortcode">
				<p>
					<code>[good-old-gallery]</code>
				</p>
			</div>

		<form action="media.php" method="post" id="shortcode-form">

			<input id="gog-shortcode" name="gog-shortcode" type="hidden" value="true" />
			<input class="button submit" type="submit" name="submit" value="<?php echo __( 'Insert into post' ); ?>" />

<?php if ($gallery_options): ?>
			<p>
				<label for="id" title="<?php echo __( 'Select gallery' ); ?>" style="line-height:25px;"><?php echo __( 'Gallery' ); ?>:</label>
				<select id="id" name="id">
					<option value=""><?php echo __( 'Select gallery' ); ?></option>
					<?php echo $gallery_options; ?>
				</select>
			</p>
<?php endif; ?>

<?php if ($theme_options): ?>
			<p>
				<label for="theme" title="<?php echo __( 'Select theme' ); ?>" style="line-height:25px;"><?php echo __( 'Theme' ); ?>:</label>
				<select id="theme" name="Theme">
					<option value="">- Select theme -</option>
					<?php echo $theme_options; ?>
				</select>
			</p>
<?php endif; ?>

			<p>
				<label for="size" title="Select gallery size" style="line-height:25px;">Image size:</label>
				<select id="size" name="size">
					<option value="">- Select size -</option>
					<option value="thumbnail">Thumbnail</option>
					<option value="medium">Medium</option>
					<option value="large">Large</option>
					<option value="full">Full</option>
				</select>
			</p>

		<?php goodold_gallery_parse_form_custom( $gog_plugin['settings_form'] ); ?>

		</form>
		</div>
	</div>
</div>
<?php
}

/**
 * Loads Good Old Gallery tab page.
 */
function goodold_gallery_media_menu_handle() {
	wp_enqueue_style( 'media' );
	wp_enqueue_script( 'gallery-insert', GOG_PLUGIN_URL . '/js/good-old-gallery-admin.js', 'jquery', false, true );
	wp_enqueue_style( 'good-old-gallery', GOG_PLUGIN_URL . '/style/good-old-gallery-admin.css' );
	return wp_iframe( 'goodold_gallery_media_process' );
}

// Insert into post, else load page
if ( isset($_POST['gog-shortcode']) ) {
	media_send_to_editor(goodold_gallery_build_shortcode($_POST));
}
else {
	add_action( 'media_upload_gogallery', 'goodold_gallery_media_menu_handle' );
}

/**
 * Adds custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_edit($form_fields, $post) {
	$form_fields["goodold_gallery_image_link"] = array(
		"label" => __( "Link" ),
		"input" => "text",
		"value" => get_post_meta( $post->ID, "_goodold_gallery_image_link", true ), "helps" => __( 'Used by ' . GOG_PLUGIN_NAME . '.' ),
	);

	return $form_fields;
}
add_filter( "attachment_fields_to_edit", "goodold_gallery_image_attachment_fields_to_edit", null, 2 );

/**
 * Saves custom image link field.
 */
function goodold_gallery_image_attachment_fields_to_save($post, $attachment) {
	if( isset($attachment['goodold_gallery_image_link']) ){
		update_post_meta( $post['ID'], '_goodold_gallery_image_link', $attachment['goodold_gallery_image_link'] );
	}
	return $post;
}
add_filter( "attachment_fields_to_save", "goodold_gallery_image_attachment_fields_to_save", null , 2 );

/**
 * Hide unwanted tabs in Good Old Gallery uploader.
 */
function goodold_gallery_remove_media_tabs($tabs) {
	unset($tabs['type_url']);
	unset($tabs['library']);
	unset($tabs['gogallery']);
	return $tabs;
}
if ( is_admin() && isset($_GET['post_id']) && (get_post_type($_GET['post_id']) == 'goodoldgallery') ) {
	add_filter( 'media_upload_tabs', 'goodold_gallery_remove_media_tabs' );
}

/**
 * Custom upload media button.
 */
function goodold_gallery_upload_button($title, $type) {
	return '<a href="' . esc_url( get_upload_iframe_src() ) . '" id="content-add_media" class="thickbox button" title="' . $title . '" onclick="return false;">' . $title . '</a>';
}


/**
 * Adds Good Old Gallery media button to posts.
 */
function goodold_gallery_media_button($context) {
	$post = wp_get_single_post();

	$button = ' %s';
	if (get_post_type() != 'goodoldgallery') {
		$image = GOG_PLUGIN_URL . '/img/good-old-gallery-small.png';
		$button .= '<a href="media-upload.php?post_id=' . $post->ID . '&tab=gogallery&TB_iframe=1" id="add_gogallery" class="thickbox add_media" title="Insert ' . GOG_PLUGIN_NAME . '"><img src="' . $image . '" /></a>';
	}

	 return sprintf($context, $button);
}
add_filter( 'media_buttons_context', 'goodold_gallery_media_button' );

/**
 * Delete attachments from gallery posts with ajax.
 */
function goodold_gallery_delete_attachment( $post ) {
	if( wp_delete_attachment( $_POST['att_ID'], true )) {
		echo sprintf(__( 'Attachment ID: %s has been deleted.' ), $_POST['att_ID']);
	}
	exit();
}
add_filter( 'wp_ajax_delete_attachment', 'goodold_gallery_delete_attachment' );

/**
 * Helper function that builds a shortcode for TinyMCE
 */
function goodold_gallery_build_shortcode($post) {
	$ret = '[good-old-gallery';

	foreach ( $post as $key => $item ) {
		if ($key != 'gog-shortcode' && $key != 'submit') {
			if (!empty($item)) {
				if ( is_array($item) ) {
					$old_item = $item;
					reset($item);
					$new_key = key($item);
					$ret .= $old_item[$new_key] ? ' ' . $new_key . '="' . $old_item[$new_key] . '"' : '';
				}
				else {
					$ret .= $item ? ' ' . $key . '="' . $item . '"' : '';
				}
			}
		}
	}

	return $ret . ']';
}
