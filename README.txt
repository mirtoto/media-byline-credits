=== Image and Media Byline Credits ===
Contributors: devbackroom
Tags: image byline, image credit, byline, credit, image, featured, thumbnail, attachment, media, Gutenberg block, image block, gallery
Donate link: https://www.devbackroom.com/donate
Requires at least: 4.6.0
Tested up to: 5.4.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

A simple way to add a byline credit to your images, media and other content.

== Description ==
We are blessed with free resources like plugins, apps, images, audio and video. Content creators give their time, skills and love to provide us with their creations. Show your appreciation by giving due credit and a link back to the provider, this could be to their donate page, website or hosted page.

This simple and flexible plugin makes it easy for you to add a byline credit to your images and other media. You can do the following:

* Add a byline credit field when uploading media.
* Automatically display the byline credit under featured images
* Automatically display the byline credit under images added with the Gutenberg image block.
* Include an optional link to the web page of the content owner or creator.
* Add freeform byline credit via a shortcode e.g. `[byline]Picture by Green Ant Media[/byline]` for more complex attribution or for a variety of media types.
* Use a list of users with a specified role or a simple list to autocomplete the names on the Byline field.
* Easily apply your own styles to the byline credit.

== Installation ==
1. Upload `image-byline.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the \"Plugins\" menu in WordPress

== Frequently Asked Questions ==
= Can I add a byline credit to audio or video? =

Yes. The byline shortcode can be used anywhere a shortcode can be used in a page or post. The shortcode takes the format `[byline]Picture by Green Ant Media[/byline]`. We plan to support more blocks in future, including audio, video and gallery blocks.

= Does the byline credit work with the classic editor? =

Yes. The byline shortcode can be used anywhere a shortcode can be used in a page or post. The shortcode takes the format `[byline]Picture by Green Ant Media[/byline]`.

= Does the byline credit work with Gutenberg image block? =

Yes. The byline credit is automatically added to the caption for the Gutenberg core image block.

= Are other Gutenberg blocks supported? =

As of this release, the credit is only added to the image block however you can use the shortcode to place a byline credit under the media. The shortcode takes the format `[byline]Picture by Green Ant Media[/byline]`. We plan to support more blocks in future, including audio, video and gallery blocks.

= Why is the byline credit not showing under the featured images? =

The byline credit gets added to the caption so if your theme is not outputting the caption for the featured image you will not see the credit. You can output the caption in your template or theme functions by using something like `the_post_thumbnail_caption(get_the_ID());`.

= Can I change how the byline credit looks? =

You can change how the byline credit looks by adding some custom css to your theme.

= Can I hide the byline credit on some images? =

If you have added a byline credit to your image in the media library, it will be output under the image. The simplest way would be to remove the byline. However, if you want to leave the creator data with your image, you can hide the byline credit adding a class `no-credit` to your image block in Additional CSS class(es). This hides the credit but still displays the caption if there is one. To hide both add `no-credit no-caption`.

= Can I show the byline credit but not the caption? =

You can show the byline credit without the caption by adding a class `no-caption` to your image block in Additional CSS class(es).

= Can I use the byline credit with my custom blocks or page builder? =

The byline shortcode can be used anywhere a shortcode can be used in a page or post. We are considering adding a support plan in future which will allow us to support more options. If your budget allows, you can contact us for developing a custom add-on for you for a cost. And you can always donate so that we can provide more options and support to you.

== Screenshots ==
1. Byline/credit on a post featured image.
2. Byline and Byline Link fields in the Media Library attachment editor page.
3. Image Byline Options page in dashboard.

== Changelog ==
= 1.0.0 =
* Initial version
