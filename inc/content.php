<?php

/**
 * The GoodOldGallery class.
 *
 * @category Good Old Gallery Wordpress Plugin
 * @package  Good Old Gallery
 * @author   Linus Lundahl
 *
 */
class GoodOldGallery {

	/**
	 * Constructor
	 */
	public function __construct() {
		register_post_type( 'goodoldgallery',
			array(
				'labels' => array(
					'name'               => _x( 'Galleries', 'gallery type general name' ),
					'singular_name'      => _x( 'Gallery', 'gallery type singular name' ),
					'add_new'            => __( 'Add New' ),
					'add_new_item'       => __( 'Add New Gallery' ),
					'edit_item'          => __( 'Edit Gallery' ),
					'new_item'           => __( 'New Gallery' ),
					'view_item'          => __( 'View Gallery' ),
					'search_items'       => __( 'Search Galleries' ),
					'not_found'          => __( 'No Galleries found' ),
					'not_found_in_trash' => __( 'No Galleries found in Trash' ),
					'parent_item_colon'  => ''
				),
			'public'               => false,
			'description'          => __( 'A Gallery that is used to display sliders on the Good Old site.' ),
			'publicly_queryable'   => false,
			'show_ui'              => true,
			'show_in_nav_menus'    => false,
			'query_var'            => 'goodoldgallery',
			'rewrite'              => true,
			'capability_type'      => 'post',
			'hierarchical'         => false,
			'menu_position'        => 10,
			'exclude_from_search'  => true,
			'supports'             => array('title'),
			'rewrite'              => array('slug' => 'goodold-gallery', 'with_front' => false),
			'menu_icon'            => GOG_PLUGIN_URL . '/img/goodold-gallery.png',
			'register_meta_box_cb' => array($this, 'addMeta')
			)
		);

		// Add action to save the gallery metadata
		// add_action('save_post', array($this, 'saveMetaContent'));
	}

	/**
	 * Registered function that adds a metadata box to the post edit screen for
	 * the fortunecookie type.
	 */
	public function addMeta() {
		add_meta_box( 'gog_sectionid',
			__( 'Gallery', 'gog_textdomain' ),
			array($this, 'addMetaContent'),
			'goodoldgallery',
			'advanced'
		);
	}

	/**
	 * Registered function that provides the content for the metadata box.
	 */
	public function addMetaContent() {
		$args = func_get_args();

		echo '<h4>Upload</h4>';
		echo '<div><p>';

		$out = _media_button( __( 'Add an Image' ), 'images/media-button-image.gif?ver=20100531', 'image' );
		$context = apply_filters( 'media_buttons_context', __( 'Upload/Manage: %s' ) );
		printf($context, $out);

		echo '</p></div>';

		if ( $_GET['post'] ) {
			echo '<h4>Shortcode</h4>';
			echo '<div><p>Shortcode for this gallery: <code>[goodold-gallery id="' . $_GET['post'] . '"]</code></p></div>';
			echo '<span class="description"><p>Shortcodes can be used to paste a gallery into a post or a page, just copy the full code and paste it into the post/page in HTML mode.</p></span>';
		}
	}

	/**
	 * Registered function that saves the metadata contet to the atabase,
	 *
	 * @param int $post_id The current post ID.
	 */
	public function saveMetaContent($post_id) {
	}

	/**
	 * Registered function that alters the WP_Query object to include the
	 * goodoldgallery type on certain pages.
	 * There are certain checks in place in order to prevent destroying
	 * the existing functionality.
	 *
	 * @param WP_Query $query The WP_Query object.
	 *
	 * @return WP_Query The modified (or not) WP_Query object.
	 */
	public function getGoodOldGallerys($query) {
		if (!is_page() || is_home() || is_archive()) {
				$query->set('post_type', array('post', 'goodoldgallery'));
		}
		return $query;
	}

}

/**
 * Registered function that creates the goodoldgallery content type.
 */
function create_goodoldgallery() {
	$goodOldGallery = new GoodOldGallery();
}
add_action( 'init', 'create_goodoldgallery' );
