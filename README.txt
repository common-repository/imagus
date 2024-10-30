=== Imagus image optimizer ===
Contributors: sonirico, rdelblanco
Tags: image optimizer, image compression, page speed, SEO, optimize jpeg, optimize png, optimize gif, optimize image,
optimize avif, optimize webp, optimize tiff, jpeg, gif, png, avif, webp, tiff
Requires at least: 4.1
Tested up to: 5.8.1
Stable tag: 0.8.2
License: GPLv2 or later
Requires PHP: 7.1
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ultimate plugin to optimize media images and recover original backup feature.

== Description ==

This plugin connects to imagus API and optimize images in jpeg/png/gif/avif/webp/tiff formats.
In "Settings" => "Imagus" you can customize the plugin. The options are:

- Quality percentage: The % of compression. Imagus uses lossy compression algorithm, so the lower compression, the lower image quality.
You might use a percentage quality that prevents a higher pixelation. 70% should be enough.
- Automatic compression: Imagus optimizes all the images you upload automatically.
- Leave original copies in media folder: if you wanna preserve the original image before imagus acts, enable this option.
You can replace the compressed image with the original if you aren't satisfied with the compression result.
- Enable modal customized options window: Activate this option if you wanna change the default settings in the media gallery.
Only available if you have the "Automatic compression" disabled (otherwhise, it has no sense!).

You can compress individually or raw in the media library menu (the "Automatic compression" option must be disabled). Just press
the "Imagus compression" button (the button shows in list mode of the media gallery page) or select that raw action after select a images group on the list.

== Installation ==

1. Upload `imagus.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. This is it

== Frequently Asked Questions ==

= How this plugin works =

This plugin sends the images to the imagus API server. The server generates a fast real-time stream that returns you the optimized image.

= How much is the subscription =

Imagus is free.

= The image compression result is 0 Kb saved. How is it possible? =

If this happens, try with a lower compression ratio.

= Why is that? =

Usually, because the image already has a small size by default.

= Do you (or can I) store my images in your cloud? =

No. The imagus API only collects data for log errors and statistics purposes. That's all, no image storaging.

== Changelog ==

= 0.8.0 =
* The first release.
= 0.8.1 =
* Avif, webp and tiff formats support.
= 0.8.2 =
* FIX: Bulk action optimize now appears only in upload.php page (Media page)
* Update guzzle V7.4.0

