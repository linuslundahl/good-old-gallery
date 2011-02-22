<?php

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
	global $wpdb, $gog_settings;

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

<?php if ($gallery_options): ?>
			<p>
				<label for="id" title="<?php echo __( 'Select gallery' ); ?>" style="line-height:25px;"><?php echo __( 'Gallery' ); ?>:</label>
				<select id="id" name="Gallery">
					<option value=""><?php echo __( 'Select gallery' ); ?></option>
					<?php echo $gallery_options; ?>
				</select>
			</p>
<?php endif; ?>

<?php if ($theme_options): ?>
			<p>
				<label for="id" title="<?php echo __( 'Select theme' ); ?>" style="line-height:25px;"><?php echo __( 'Theme' ); ?>:</label>
				<select id="theme" name="Theme">
					<option value="">Select theme</option>
					<?php echo $theme_options; ?>
				</select>
			</p>
<?php endif; ?>

			<p>
				<label for="fx" title="<?php echo __( 'Animation' ); ?>" style="line-height:25px;"><?php echo __( 'Animation' ); ?>:</label>
				<select id="fx" name="fx">
					<option value=""><?php echo __( 'Select animation' ); ?></option>
					<option value="none"><?php echo __( 'None (Standard gallery)' ); ?></option>
					<option value="scrollHorz"><?php echo __( 'Horizontal scroll' ); ?></option>
					<option value="scrollVert"><?php echo __( 'Vertical scroll' ); ?></option>
					<option value="fade"><?php echo __( 'Fade' ); ?></option>
				</select>
			</p>

			<div class="cycle-options">
				<p>
					<label for="timeout" title="<?php echo __( 'Cycle animation timeout' ); ?>"><?php echo __( 'Timeout' ); ?>:</label>
					<input id="timeout" name="timeout" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="speed" title="<?php echo __( 'Speed of animation' ); ?>"><?php echo __( 'Speed' ); ?>:</label>
					<input id="speed" name="speed" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="size" title="<?php echo __( 'Select gallery size' ); ?>" style="line-height:25px;"><?php echo __( 'Image size' ); ?>:</label>
					<select id="size" name="size">
						<option value="">Select size</option>
						<option value="thumbnail"><?php echo __( 'Thumbnail' ); ?></option>
						<option value="medium"><?php echo __( 'Medium' ); ?></option>
						<option value="large"><?php echo __( 'Large' ); ?></option>
						<option value="full"><?php echo __( 'Full' ); ?></option>
					</select>
				</p>

				<p>
					<input id="title" type="checkbox" name="title" value="true" />
					<label for="title" title="<?php echo __( 'Select if the title should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show title' ); ?></label>
				</p>

				<p>
					<input id="description" type="checkbox" name="description" value="true" />
					<label for="description" title="<?php echo __( 'Select if the description should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show description' ); ?></label>
				</p>

				<p>
					<input id="navigation" type="checkbox" name="navigation" value="true" />
					<label for="navigation" title="<?php echo __( 'Select if a navigation should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show navigation' ); ?></label>
				</p>

				<p>
					<input id="pager" type="checkbox" name="pager" value="true" />
					<label for="pager" title="<?php echo __( 'Select if a pager should be displayed' ); ?>" style="line-height:25px;"><?php echo __( 'Show pager' ); ?></label>
				</p>
			</div>
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
add_action( 'media_upload_gogallery', 'goodold_gallery_media_menu_handle' );

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
if ( is_admin() && get_post_type($_GET['post_id']) == 'goodoldgallery' ) {
	add_filter( 'media_upload_tabs', 'goodold_gallery_remove_media_tabs' );
}

/**
 * Custom upload media button.
 */
function goodold_gallery_upload_button($title, $type) {
	return "<a href='" . esc_url( get_upload_iframe_src($type) ) . "' id='add_$type' class='thickbox button' title='$title'>$title</a>";
}


/**
 * Adds Good Old Gallery media button. (Can't get this working properly yet)
 */
function goodold_gallery_media_button($context) {
	$post = wp_get_single_post();

	$button = ' %s';
	if (get_post_type() != 'goodoldgallery') {
		$image = GOG_PLUGIN_URL . '/img/good-old-gallery-small.png';
		$button .= '<a href="media-upload.php?post_id=' . $post->ID . '&type=image&tab=gogallery" id="add_gogallery" class="thickbox" title="Insert ' . GOG_PLUGIN_NAME . '"><img src="' . $image . '" /></a>';
	}

	 return sprintf($context, $button);
}
// add_filter('media_buttons_context', 'goodold_gallery_media_button');
