=== Plugin Name ===
Contributors: tuxlog, woodstock
Donate link: http://www.tuxlog.de/
Tags: wordpress, plugin, smiley, smilies, monalisa, comments, post, edit
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 0.4

wp-monalisa is the plugin that smiles at you like monalisa does. place the smilies of your choice in posts, pages or comments.

== Description ==

wp-monalisa is the plugin that smiles at you like monalisa does. place the smilies of your choice in posts, pages or comments.

There are a lot plugins for smiley support out there and some of them are really useful. 
Most of them don't work out of the box and this is what wp-monalisa tries to achieve, giving you the ability to maintain your smilies and even turn them into img tags. 

it's easy and it smiles at you...what else do you want?

Features:

* maintain your smilies in a separate directory
* activate or deactivate smilies for posts or comments
* replace smilies with img tags
* extend or replace wordpress smiley replacement
* while edit posts or pages, pops-up in a draggable meta-box
* extends your comment form to give you visitors the freedom to smile :-)

Credits:
Thanks go to all who support this plugin, with  hints and suggestions for improvment and specially to

* Michal Maciejewski, polish translation

== Installation ==

1. Upload the contents of the zip archive to your plugins folder, usually wp-content/plugins/`, keeping the directory structure intact (i.e. `wp-monalisa.php` should end up in `wp-content/plugins/wp-monalisa/`).
1. Activate the plugin on the plugin screen.
1. Visit the configuration page (wp-Monalisa) to configure the plugin (do not forget to check the comment forms id)
1. Optional If you would like to change the style, just edit wp-monalisa.css

Update:
If you update from a former version please do not forget to deactivate and actiate the plugin once, since database changes will only take effect on activation. You settings will not be deleted during deactivation.



== Frequently Asked Questions ==

= wp-monalisa does not work with comments. What now? =

Please, check and double check that the id given in the admin dialog of wp-monalisa is the correct id of the comment form textare. This can usually happen if you changed your theme.

= My smilies are gone? What's wrong? =

Plase check and double check the path to your smiley directory.

== Screenshots ==

1. wp-Monalisa admin dialog
2. wp-Monalisa in the wordpress edit dialog
3. wp-Monalisa extends the comment form
4. wp-Monalisa import thickbox dialog

== History ==
2009-05-17 v0.1	   Initial release 

2009-05-22 v0.2	   added alt attribute to img tags, to produce correct xhtml,
	   	   fixed german translation, added import dialog to import 
		   phpbb3 smiley packages, added space after shortcode insertion
		   automatically extend array allowedtags when oncomment and 
		   replace options are set, improve error handling with 
		   directory a bit, added polish translation
2009-05-29 v0.3	   renamed default icons with prefix wpml_ to get a more or less
		   unique name and prevent override, modified row width 
		   of row emoticon to 25, add maxlength attribute=25 to input 
		   fields for emoticons, added screenshot for import dialog, 
		   styled admin dialog a bit more wordpress like (alternate 
		   background color for table, buttons outside the table, 
		   added checkall box)
2009-05-30 v0.4    fixed trimming whitespace from emoticons in admin dialog, fixed 
	   	   replace algorithm, now search for longest substring first and can
		   handle any whitespace situation
