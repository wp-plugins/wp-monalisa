<?php 
/* 
Plugin Name: wp-Monalisa
Plugin URI: http://www.tuxlog.de/wordpress/2009/wp-monalisa/
Description: wp-Monalisa is the plugin that smiles at you like monalisa does. place the smilies of your choice in posts, pages or comments. 
Version: 0.6
Author: Hans Matzen <webmaster at tuxlog dot de>
Author URI: http://www.tuxlog.de
*/

/*  Copyright 2009  Hans Matzen  (email : webmaster at tuxlog dot de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// include setup functions
require_once("setup.php");
// include functions
require_once("wpml_func.php");
// admin dialog
require_once("wpml_admin.php");
// comment form functions
require_once("wpml_comment.php");
// edit dialog functions
require_once("wpml_edit.php");

// global vars for emoticon replace in comments and posts 
global $wpml_smilies, $wpml_search;
$wpml_smilies = array();
$wpml_search = "";

// plugin init funktion
function wp_monalisa_init()
{
    // get translation 
    $locale = get_locale();
    if ( empty($locale) )
	$locale = 'en_US';
    if(function_exists('load_textdomain') and $locale != "en_US") 
	load_textdomain("wpml",ABSPATH . "wp-content/plugins/wp-monalisa/lang/".$locale.".mo");
    
    //
    // just return the css link
    // this function is called via the wp_head hook
    //
    function wpml_css() 
    {
	$def  = "wp-monalisa-default.css";
	$user = "wp-monalisa.css";
	
	if (file_exists( WP_PLUGIN_DIR . "/wp-monalisa/" . $user))
	    $def =$user;
	
	$plugin_url = plugins_url("wp-monalisa/");
	
	echo '<link rel="stylesheet" id="wp-monalisa-css" href="'. 
	    $plugin_url . $def . '" type="text/css" media="screen" />' ."\n";
	
    }
    
    // add css im header hinzufügen 
    add_action('wp_head', 'wpml_css');
    add_filter('admin_head', 'wpml_css');

    // javascript hinzufügen
    wp_enqueue_script('wpml_script',
        	      '/' . PLUGINDIR . '/wp-monalisa/wpml_script.js',
		      array(), "9999");
}


// activating deactivating the plugin
register_activation_hook(__FILE__, 'wp_monalisa_install');
// uncomment this to loose everything when deactivating the plugin
register_deactivation_hook(__FILE__, 'wp_monalisa_deinstall');

// add option page 
add_action('admin_menu','wpml_admin_init');

// init plugin
add_action('init', 'wp_monalisa_init');
// add comment support
add_action('init', 'wpml_comment_init');
// add edit dialog support
add_action('admin_menu', 'wpml_edit_init');
// add filters for smiley replace and make sure we are called last
add_filter('init','wpml_map_emoticons','99');
add_filter('the_content',  'wpml_convert_emoticons',99);
add_filter('the_excerpt',  'wpml_convert_emoticons',99);
add_filter('comment_text', 'wpml_convert_emoticons', 99);

?>
