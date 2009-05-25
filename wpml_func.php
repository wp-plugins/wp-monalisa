<?php
/* This file is part of the wp-monalisa plugin for wordpress */

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

//
// function to show an admin message on an admin page
//
if ( !function_exists('admin_message') )
{
    function admin_message($msg) {
	echo "<div class='updated'><p><strong>";
	echo $msg;
	echo "</strong></p></div>\n";
    }
}

//
// the next functions are an adoption of wordpress 2.8 functions
// to change the behaviour as wanted for wp-monalisa
// thanks to all who worked on this.
//
// this functions maps the emoticons to icons
// and stores them in an global array
// it also prepares a global search pattern for all smilies
//
function wpml_map_emoticons() 
{
    global $wpdb, $wpml_search, $wpml_smilies;
    
    $av = unserialize(get_option("wpml-opts"));

    // if disabled do nothing but return
    if ($av['onedit']==0 and $av['oncomment']==0)
	return;

    // table name
    $wpml_table = $wpdb->prefix . "monalisa";

    // extend array allowedtags with img tag if necessary
    // to make sure the comment smilies dont geat lost
    if ( $av['oncomment']==1 and $av['replaceicon']==1)
    {
	global $allowedtags;
	if ( ! array_key_exists("img",$allowedtags) )
	{
	    $allowedtags['img'] = array( 'src' => array(), 
					 'alt' => array(), 
					 'class' => array() );
	}
    }
    

    // select all valid smiley entries
    $sql="select tid,emoticon,iconfile from $wpml_table where ( oncomment=".$av['oncomment']." and oncomment=1 ) or ( onpost=".$av['onedit']." and onpost=1 ) order by tid;";

    $results = $wpdb->get_results($sql);
    
    // icon url begin including directory
    $ico_url = site_url($av['icondir']);
    
    foreach($results as $res)
    {
	// store emoticon mapping to array for smiley translation
	if ( ! array_key_exists($res->emoticon, $wpml_smilies) )
	{
	    $wpml_smilies[ wptexturize($res->emoticon) ] = $ico_url . "/" . $res->iconfile;	
	}
	
    }

    // build regexp search string
    $wpml_search = '/(?:\s|^)';
   
    $subchar = '';
    foreach ( (array) $wpml_smilies as $smiley => $img ) {
	$smiley = wptexturize($smiley);
	$firstchar = substr($smiley, 0, 1);
	$rest = substr($smiley, 1);

	// new subpattern?
	if ($firstchar != $subchar) {
	    if ($subchar != '') {
		$wpml_search .= ')|(?:\s|^)';
	    }
	    $subchar = $firstchar;
	    $wpml_search .= preg_quote($firstchar, '/') . '(?:';
	} else {
	    $wpml_search .= '|';
	}
	$wpml_search .= preg_quote($rest);
    }

    $wpml_search .= ')(?:\s|$)/m';

    if ( count($wpml_smilies) == 0 )
	$wpml_search="";
}

//
// translate an emoticon into a valid img tag
//
function wpml_translate_emoticon($smiley) {
    global $wpml_smilies;

    if (count($smiley) == 0) {
	return '';
    }

    $smiley = trim(reset($smiley));
    $img = $wpml_smilies[$smiley];
    $smiley_masked = attribute_escape($smiley);

    return " <img src='$img' alt='$smiley_masked' class='wp-smiley' /> "; 
}


//
// convert emoticons to icons in img tags
// 
function wpml_convert_emoticons($text) 
{
    global $wpml_search;

    // no smilies to change, return original text
    if ( empty($wpml_search) )
	return $text;
    
    // reset output
    $output = '';

    // taken from wordpress 2.8
    $textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    // capture the tags as well as in between
    $stop = count($textarr);// loop stuff
    for ($i = 0; $i < $stop; $i++) {
	$content = $textarr[$i];
	if ((strlen($content) > 0) && ('<' != $content{0})) { 
	    //If it's not a tag
	    $content = preg_replace_callback($wpml_search, 
					     'wpml_translate_emoticon', 
					     $content);
	    
	}
	$output .= $content;
    }
    return $output;
}

// php4 compatibility functions
//
// rebuilds the scandir function not available with php4
// copied from Cory S.N. LaViska, thank you :-)
//
if( !function_exists('scandir') ) {
    function scandir($directory, $sorting_order = 0) {
        $dh  = @opendir($directory);
	$files=array();

	if ($dh)
	{
	    while( false !== ($filename = readdir($dh)) ) {
		$files[] = $filename;
	    }
	    if( $sorting_order == 0 ) {
		sort($files);
	    } else {
		rsort($files);
	    }
	}
        return($files);
    }
}
?>