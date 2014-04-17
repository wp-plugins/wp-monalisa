<?php 
/* 
Plugin Name: wp-Monalisa
Plugin URI: http://www.tuxlog.de/wordpress/2009/wp-monalisa/
Description: wp-Monalisa is the plugin that smiles at you like monalisa does. place the smilies of your choice in posts, pages or comments. 
Version: 3.1
Author: Hans Matzen <webmaster at tuxlog dot de>
Author URI: http://www.tuxlog.de
*/

/*  Copyright 2009-2013 Hans Matzen  (email : webmaster at tuxlog dot de)

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
require_once("wpml_setup.php");
// include autoupdate support
require_once("wpml_autoupdate.php");
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

// global var for printing imagelist for preload once
global $wpml_first_preload;
$wpml_first_preload=true;

// plugin init funktion
function wp_monalisa_init()
{
    // get translation 
    load_plugin_textdomain('wpml', false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");      
    
    // add css im header hinzufügen 
    add_action('wp_enqueue_scripts', 'wpml_css');
    add_action('admin_print_styles', 'wpml_css');

    
    // javascript hinzufügen
    if (! is_admin()) 
    	wp_enqueue_script('wpml_script', plugins_url('wpml_script.js', __FILE__),  array('jquery'),"9999");    
}


// activating deactivating the plugin
register_activation_hook(__FILE__, 'wp_monalisa_install');
// uncomment this to loose everything when deactivating the plugin
register_deactivation_hook(__FILE__, 'wp_monalisa_deinstall');

// add option page 
add_action('admin_menu','wpml_admin_init');

// init plugin
add_action('init', 'wp_monalisa_init');
// add comment supportbp_activity_comment_content
add_action('init', 'wpml_comment_init');
// add edit dialog support
add_action('admin_menu', 'wpml_edit_init');
// add filters for smiley replace and make sure we are called last
add_filter('init','wpml_map_emoticons','99');
add_filter('the_content',  'wpml_convert_emoticons',99);
add_filter('the_excerpt',  'wpml_convert_emoticons',99);
add_filter('comment_text', 'wpml_convert_emoticons', 99);

// show smilies in buddypress and bbpress
// optionen einlesen
$av=array();
if (function_exists('is_multisite') && is_multisite()) 
	$av = maybe_unserialize(get_blog_option(1, "wpml-opts"));
else
	$av = unserialize(get_option("wpml-opts"));
	
if (defined('BP_VERSION') && $av['wpml4buddypress'] == "1") {
	
	
	add_filter( 'bp_activity_comment_content',          'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_action',  				'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_content_body',			'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_content',     			'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_parent_content',		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_latest_update', 		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_activity_latest_update_excerpt','wpml_convert_emoticons', 99);
	add_filter( 'bp_core_render_message_content', 		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_the_topic_title', 				'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_the_topic_latest_post_excerpt', 'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_the_topic_post_content', 		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_group_description',      		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_group_description_excerpt',		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_message_notice_subject', 		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_message_notice_text', 			'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_message_thread_subject',		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_message_thread_excerpt',		'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_the_thread_message_content', 	'wpml_convert_emoticons', 99);
	add_filter( 'bp_get_the_profile_field_value',    	'wpml_convert_emoticons', 99);  
	
	// BP Profile Message UX filters
    $plugins = get_option('active_plugins');
    $required_plugin = 'bp-profile-message-ux/bp-profile-message-ux.php';
    if ( in_array( $required_plugin , $plugins ) ) {
    	add_filter( 'bp_get_send_public_message_button',    'wpml_convert_emoticons', 99);
		add_filter( 'bp_get_send_message_button',    		'wpml_convert_emoticons', 99);
	}
	
	// add img tag so that smilies can be displayed
	add_filter('init','wpml_bp_allow_tags');
	
	function wpml_bp_allow_tags($data) {
		global $allowedtags;
		$allowedtags['img'] = 	$allowedtags['img'] = array('src' => array(), 'alt' => array(), 'title' => array(), 'height' => array(), 'width' => array(), 'style'=>array());
		//$allowedtags['p'] = array();
		return $data;
	}
}

if (class_exists( 'bbPress' ) && $av['wpml4bbpress'] == "1") {
	add_filter( 'bbp_get_reply_content',     			'wpml_convert_emoticons', 99);
	add_filter( 'bbp_get_topic_content', 		    	'wpml_convert_emoticons', 99);
}

?>
