=== Plugin Name ===
Contributors: tuxlog, woodstock
Donate link: http://www.tuxlog.de/
Tags: wordpress, plugin, smiley, smilies, monalisa, comments, post, edit
Requires at least: 2.7
Tested up to: 3.1
Stable tag: 1.2

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

The video shows a short overview of what wp-monalisa can do for you. [youtube http://www.youtube.com/watch?v=uHXlELn27ko]

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

= Are there any Tutorials? =

Yes, there are have a look at http://www.tuxlog.de/keinwindowsmehr/2009/wp-monalisa/ or try these screencasts to learn how to install, configure and use wp-monalisa 
Installation: [youtube http://www.youtube.com/watch?v=5w8hiteU8gA]
Configure: [youtube http://www.youtube.com/watch?v=614Gso38v5g]
Use: [youtube http://www.youtube.com/watch?v=uHXlELn27ko]
Import/Export of Smilies: [youtube http://www.youtube.com/watch?v=cedwN0u_XRI]


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
* added russian translation, thanks to Fat Cow (http://www.blog.fatcow.com)
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

