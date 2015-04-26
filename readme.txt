=== Plugin Name ===
Contributors: tuxlog, woodstock
Donate link: http://www.tuxlog.de/
Tags: wordpress, plugin, smiley, smilies, monalisa, comments, post, edit, buddypress, bbpress
Requires at least: 2.7
Tested up to: 4.2
Stable tag: 3.5
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
* support for fckeditor (tested with v3.3.1)
* fully integrated ith BuddyPress

The video shows a short overview of what wp-monalisa can do for you. [youtube http://www.youtube.com/watch?v=uHXlELn27ko]

Credits:
Thanks go to all who support this plugin, with  hints and suggestions for improvment and specially to

* Michal Maciejewski, polish translation
* Denny from http://www.vau3.de for testing and giving input for the BuddyPress integration

== Installation ==

1. Upload the contents of the zip archive to your plugins folder, usually wp-content/plugins/`, keeping the directory structure intact (i.e. `wp-monalisa.php` should end up in `wp-content/plugins/wp-monalisa/`).
1. Activate the plugin on the plugin screen.
1. Visit the configuration page (wp-Monalisa) to configure the plugin (do not forget to check the comment forms id)
1. Optional If you would like to change the style, just edit wp-monalisa.css

Update:
If you update from a former version please do not forget to deactivate and actiate the plugin once, since database changes will only take effect on activation. You settings will not be deleted during deactivation.



== Frequently Asked Questions ==

= Are there any Tutorials? =

Yes, there are have a look at http://www.tuxlog.de/keinwindowsmehr/2009/wp-monalisa/ or try these screencasts to learn how to install, configure and use wp-monalisa 
Installation: [youtube http://www.youtube.com/watch?v=5w8hiteU8gA]
Configure: [youtube http://www.youtube.com/watch?v=614Gso38v5g]
Use: [youtube http://www.youtube.com/watch?v=uHXlELn27ko]
Import/Export of Smilies: [youtube http://www.youtube.com/watch?v=cedwN0u_XRI]

= The smilies for BuddyPress activities are only shown when the page is reloaded. Is this a bug? =

No, BuddyPress uses local ajax to add new activities to your timeline. Therefore the earliest time the Smilies can be loaded is when the page is loaded from the server again.

= I can't see the smilies in the notices shown in the sidebar of BuddyPress. What's wrong? =

Nothing, the current version of BuddyPress does not offer a filter to show the smilies in there. But there is a workaround editing one line of bb-messages-template.php, change line 546 to 
<?php echo apply_filters('bp_get_message_notice_text', stripslashes( wp_filter_kses( $notice->message) )) ?>
or use the Activity-Stream-Widget for BuddyPress, which is supported by wp-monalisa 

= wp-monalisa does not work with comments. What now? =

Please, check and double check that the id given in the admin dialog of wp-monalisa is the correct id of the comment form textare. This can usually happen if you changed your theme.

= My smilies are gone? What's wrong? =

Plase check and double check the path to your smiley directory.

== Screenshots ==

1. wp-Monalisa admin dialog
2. wp-Monalisa in the wordpress edit dialog
3. wp-Monalisa extends the comment form
4. wp-Monalisa import thickbox dialog

== Changelog ==

= v3.5 (2015-04-26) =
* fixed a layout issue with WordPress 4.2.

= v3.4 (2014-06-19) =
* added urkaine translation. thanks to Michael Yunat

= v3.3 (2014-05-04) =
* fixed visual TinyMCE 4 mode with bbPress
* added contextual help
* removed support dialog since it was used rarely

= v3.2 (2013-04-20) =
* fixed a poblem with Firefox and comments in javascript
* adopt to TinyMCE 4

= v3.1 (2013-12-03) =
* added spanish translation. Thanks to Andrew Kurtis from WebHostingHub

= v3.0 (2013-08-04) =
* fixed some php warnings

= v2.9 (2013-07-01) =
* fixed problems with smilies and BuddyPress profiles

= v2.8 (2013-07-01) =
* added support to integrate smilies into bp profile messaging ux free plugin

= v2.7 (2013-04-20) =
* extended wpml to allow using :yes: and :YES: as different emoticons
* separate the support for bbPress and BuddyPress and support bbPress tinyMCE

= v2.6 (2013-03-16) =
* changed hint text to new WordPress labels
* extendd support of use from within php
* fixed bug with BuddyPress when using tables for output

= v2.5 (2012-10-26) =
* with special configurations smilies disappeared or where shown with wrong dimensions

= v2.4 (2012-10-24) =
* with special configurations smilies disappeared due to lack of dimensions

= v2.3 (2012-10-01) =
* added width and height attribute to img tags speeding up browser rendering if many smilies are used
* added deferred loading for the hidden smilies if pulldown smilies are active
* added "more..." Smilies are inserted when "more..." is clicked 
* removed an incompatibility with Better WP Minify

= v2.2 (2012-09-28) =
* fixed warning during plugin activation

= v2.1 (2012-08-05) =
* swtiched to load_plugin_textdomain for compatibility
* load js only when applicable
* added support for BuddyPress (Acitivties, Messages, Notices, Groups, bbpress-Forums)

= v2.0 (2012-06-10) =
* extended multisite support for easier handling

= v1.9 (2012-03-05) =
* fixed a typo with trailing spaces in emoticons
* added default admin email to support form
* added theme name to support form 
* work around a bug in bwp minify with jquery events

= v1.8 (2012-02-19) =
* add new support and donation feature
* add posibility to disable comments smilies on a single post/page
* use standard load for wordpress includes
 
= v1.7 (2011-12-21) =
* clean up more (maybe all?) html5 code errors for 3.3 compatibility

= v1.6 (2011-12-14) =
* now using wp_enqueue_style for css
* clean up html5 code errors for 3.3 compatibility

= v1.5 (2011-10-22) =
* removed russian translation because of a restricton from wordpress.org
* added hebrew translation thanks to Sagive

= v1.4 (2011-08-08) =
* added function get_wpml_comment() which returns the smiley-html-code to integrate within comment_form theme code

= v1.3 (2011-05-03) =
* added simple support for multisite installtions (smilies can be only maintained from mainblog and work on every blog which it is activated for) 

= v1.2 (2011-03-13) =
* fixed a problem with wp 3.1 in network mode, due to a different search path the wrong setup.php was included
* added tooltip support for icons

= v1.1 (2011-01-23) =
* added support for fckeditor (tested with v3.3.1)

= v1.0 (2010-01-17) =
* fixed wrong initial value for show as table option
* added alt attribute to admin dialog icons (xhtml fix)
* set floating control div to display:none in wpml_comments.php
* added support for autoupdate to prevent auto delete of private smilies and custom css
* fixed undefined index warning in wpml_admin.php

= v0.9 (2009-12-19) =
* fixed invalid xhtml in admin dialog
* mark iconfiles not yet mapped with a star

= v0.8 (2009-11-30) =
* fixed invalid XHTML in admin dialog
* fixed strange behaviour when deactivating smilies on comments results in null
* added hint to deactivate wordpress smilies fpr wp-monalisa

= v0.7 (2009-09-27) =
* added russian translation
* added belorussian translation, thanks to ilyuha (http://antsar.info) 
* added .pak export functionality
* divided smiley-list into pages (smiley list navigator using jquery ajax)

= v0.6 (2009-08-18) =
* changed readme to support new changelog feature at wordpress.org
* new option, smilies can also be output in a table (only for comments)
* added support for user specific css file to improve support for automatic update
* fixed handling of slashes in emoticons
* fixed handling of trailing spaces in emoticons

= v0.5 (2009-06-16) =
* added dummy version to javascript includes to hide wordpress version
* insert smilies with trailing space to make sure the shortcodes can be found
* set default smiley to correct file name
* now png icons are also supported
* surpress showing smilies more than once if more than one shortcode is defined for the same file
* modified column width of column iconfile to 80

= v0.4 (2009-05-30) =
* fixed trimming whitespace from emoticons in admin dialog
* fixed replace algorithm, now search for longest substring first and can handle any whitespace situation

= v0.3 (2009-05-29) =
* renamed default icons with prefix wpml_ to get a more or less unique name and prevent override
* modified row width of column emoticon to 25
* add maxlength attribute=25 to input fields for emoticons
* added screenshot for import dialog
* styled admin dialog a bit more wordpress like (alternate background color for table, buttons outside the table, added checkall box)

= v0.2 (2009-05-22) =
* added alt attribute to img tags, to produce correct xhtml
* fixed german translation
* added import dialog to import phpbb3 smiley packages
* added space after shortcode insertion
* automatically extend array allowedtags when oncomment and replace options are set
* improve error handling with directory a bit
* added polish translation

= v0.1 (2009-05-17) =
* Initial release

