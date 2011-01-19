<?php

/**
 * Adds Good Old Gallery media tab.
 */
function goodold_gallery_media_tab($tabs) {
	$newtab = array('gogallery' => __( GOG_PLUGIN_NAME, 'gogallery' ));
	return array_merge($tabs, $newtab);
}
add_filter( 'media_upload_tabs', 'goodold_gallery_media_tab' );

/**
 * Good Old Gallery tab page.
 */
function goodold_gallery_media_process() {
	global $wpdb;

	media_upload_header();

	// Build dropdown with galleries
	$posts = $wpdb->get_results($wpdb->prepare("
		SELECT ID, post_title FROM $wpdb->posts
			WHERE post_type = 'goodoldgallery' AND post_status = 'publish';"
	));

	$options = !$posts ? "<option value=\"\">No galleries found</option>" : "";

	foreach ( $posts as $p ) {
		$selected = '';
		$options .= "<option value=\"$p->ID\">$p->post_title</option>";
	}
?>
<div id="good-old-gallery-generator">
	<h3 class="media-title"><?php echo GOG_PLUGIN_NAME; ?> shortcode generator</h3>

	<div class="postbox">
		<h3 style="margin: 0; padding: 10px;">Generator</h3>
		<div class="inside" style="margin: 10px;">
			<span class="description">
				<p>
					Copy and paste the full generated shortcode into your post/page in HTML mode.
				</p>
			</span>

			<div id="good-old-gallery-shortcode">
				<p>
					<code>[good-old-gallery]</code>
				</p>
			</div>

			<p>
				<label for="id" title="Select gallery" style="line-height:25px;">Gallery:</label>
				<select id="id" name="Gallery">
					<option value="">Select gallery</option>
					<?php echo $options; ?>
				</select>
			</p>

			<p>
				<label for="fx" title="Animation" style="line-height:25px;">Animation:</label>
				<select id="fx" name="fx">
					<option value="">Select animation</option>
					<option value="none">None (Standard gallery)</option>
					<option value="scrollHorz">Horizontal scroll</option>
					<option value="scrollVert">Vertical scroll</option>
					<option value="fade">Fade</option>
				</select>
			</p>

			<div class="cycle-options">
				<p>
					<label for="timeout" title="Cycle animation timeout">Timeout:</label>
					<input id="timeout" name="timeout" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="speed" title="Speed of animation">Speed:</label>
					<input id="speed" name="speed" type="text" /> <span>ms</span>
				</p>

				<p>
					<label for="size" title="Select gallery size" style="line-height:25px;">Image size:</label>
					<select id="size" name="size">
						<option value="">Select size</option>
						<option value="thumbnail">Thumbnail</option>
						<option value="medium">Medium</option>
						<option value="large">Large</option>
						<option value="full">Full</option>
					</select>
				</p>

				<p>
					<input id="navigation" type="checkbox" name="navigation" value="true" />
					<label for="navigation" title="Select if a navigation should be displayed" style="line-height:25px;">Show navigation</label>
				</p>

				<p>
					<input id="pager" type="checkbox" name="pager" value="true" />
					<label for="pager" title="Select if a pager should be displayed" style="line-height:25px;">Show pager</label>
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
 * Hide unwanted tabs in Good Old Gallery uploader
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
