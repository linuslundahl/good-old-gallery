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
			'rewrite'              => array('slug' => 'good-old-gallery', 'with_front' => false),
			'menu_icon'            => GOG_PLUGIN_URL . '/img/good-old-gallery.png',
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

		$edit = FALSE;
		$button = __( 'Upload images' );
		if ( $_GET['post'] ) {
			$button = __( 'Manage gallery' );
			$edit = TRUE;
		}

		echo '<div class="go-gallery-admin">';

		$out = goodold_gallery_upload_button( $button, 'image' );
		$context = apply_filters( 'media_buttons_context', __( '%s' ) );
		printf($context, $out);

		if ( !$edit ) {
			echo ' <span class="description">Click to upload your images.</span>';
		}
		else {
			echo ' <span class="description">Click to manage your images.</span>';
		}

		echo '</div>';

		if ( $edit ) {
			echo '<p>Shortcode for this gallery: <code>[good-old-gallery id="' . $_GET['post'] . '"]</code></p>';
			echo '<span class="description">';
			echo '<p>Shortcodes can be used to paste a gallery into a post or a page, just copy the full code and paste it into the post/page in HTML mode.</p>';
			echo '<p>To generate a shortcode with custom variables, click on the \'Add an Image\' icon on a page or a post, then click the \'Good Old Gallery\' tab.</p>';
			echo '</span>';
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
