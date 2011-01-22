=== Good Old Gallery ===
Contributors: linuslundahl
Donate link: https://flattr.com/thing/119963/Good-Old-Gallery
Tags: simple, image, gallery, slideshow, jQuery, cycle, slide, sliding, fade, fading, multiple, widget
Requires at least: 3.0
Tested up to: 3.0.4
Stable tag: 1.0

Good Old Gallery helps you use galleries on multiple pages and posts, it also uses jQuery Cycle for sliding and fading transitions.

== Description ==

Good Old Gallery is a WordPress plugin that helps you upload image galleries that can be used on more than one page/post, it utilizes the built in gallery functionality in WP. Other features include built in jQuery Cycle support and Widgets.

= Main features =

* Uses built in WP gallery functionality
* jQuery Cycle
* Shortcode generator
* Widgets
* Stylesheet theme support
* Instant on, no need for coding.
* Plus much more...

== Installation ==

1. Upload `good-old-gallery` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You now have a new Galleries section underneath Media in the admin menu.
4. Go to *Galleries -> Settings* to setup the basic settings.

= Uploading =

1. Click on *Add New* in the Galleries menu.
2. Give your gallery an administrative title.
3. Click on *Upload images*.
4. Upload your images and add Title, Description and Link if needed.
5. Click on *Save all changes*, close the pop-up.
6. Click on *Publish* to enable your gallery.

= Shortcodes =

To use your new gallery in a page/post you use the `[good-old-gallery]` shortcode, use the generator to build one.

1. Go to a page or a post.
2. Click on the *Add an Image* icon.
3. Click on the *Good Old Gallery* tab.
4. Generate your shortcode and copy it, close the pop-up.
5. Enter HTML mode in the editor.
6. Paste your shortcode where it should be shown.

= Themes =

You can make your own themes, just create a `gog-themes` directory in either `wp-content` or your active themes directory, and the plugin will automatically find any themes located there. 

Creating a theme is rather simple, just start out with one of the themes found in the `themes` catalog in `good-old-gallery`. 

The structure of a theme is:

`gog-themes 
|- my-theme/ (folder with resources)
|- my-theme.css (this is the only required file) 
|- my-theme.png (must be a png) `

Fill in the file headers in the css, only *Style Name* is required, but the more you fill in the better. 

Now add some css to style your Good Old Galleries.

= Widgets =

You are also given a new widget called *Good Old Gallery*, use it in regions where a selected gallery always should be shown.

== Frequently Asked Questions ==

= What's with Good Old? =

This plugin was actually conceived when the company I work for [Good Old](http://goodold.se/) was redoing their site, I made this plugin to help maintain galleries and specifically galleries in widgets.

Then I figured that this plugin is probably something the general WP user could have use for, so I decided to further develop it on my spare time.

And as you've probably figured out by now, the name Good Old comes from the company I work for.

== Screenshots ==

1. Galleries listing page
2. Add new gallery page
3. Default settings page
4. Gallery shortcode generator
5. Widget settings

== Changelog ==

= 1.1 =
* Add your own themes in `wp-content/gog-themes` or `wp-content/themes/[active-theme]/gog-themes`, see [Installation](http://wordpress.org/extend/plugins/good-old-gallery/installation/) for more info.

= 1.0 =
* First version on wordpress.org.

== Upgrade Notice ==

= 1.1 =
The new theme system allows you to add your own themes. *When upgrading you need to re-save the settings on the settings page.*