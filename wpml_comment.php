<?php

/* This file is part of the wp-monalisa plugin for wordpress */

/*  Copyright 2009  Hans Matzen  (email : webmaster at tuxlog.de)

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

// sicherheitshalber pruefen, ob wir direkt aufgerufen werden
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You 
are not allowed to call this page directly.'); }

// init funktion fuer die kommentarunterstuetzung
function wpml_comment_init()
{
    // optionen einlesen
    $av = unserialize(get_option("wpml-opts"));
    
    // show smileys in commentform if not disabled
    if ( $av['oncomment'] == "1" )
	add_action('comment_form','wpml_comment');
}

function wpml_comment($postid=0)
{
    global $wpdb;

    // table name
    $wpml_table = $wpdb->prefix . "monalisa";
    
    // optionen einlesen
    $av = unserialize(get_option("wpml-opts"));
    
     // icons lesen
    $sql="select tid,emoticon,iconfile from $wpml_table where oncomment=1 order by tid;";
    $results = $wpdb->get_results($sql);

    // ausgabe der icons aufbauen
    $out = "\n\n";

    if ( $av['showicon'] == 0)
	$out .= "<div class='wpml_commentbox_text'>\n";
    else
	$out .= "<div class='wpml_commentbox'>\n";

    foreach($results as $res) 
    {
	$ico_url = site_url($av['icondir']) . '/' . $res->iconfile; 
	if ( $av['replaceicon'] == 0)
	{
	    $smile = $res ->emoticon;
	    $repl = 0;
	} else {
	    $smile = $ico_url;
	    $repl = 1;
	}

	// icon nur als text ausgeben
	if ( $av['showicon'] == 0 )
	{
	    $out .='<div class="wpml_ico_text" onclick="smile2comment(\''.
		$av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
	    $out .= $res->emoticon . "&nbsp;" ; 
	    $out .= "</div>\n";
	}

	// icon nur als bild ausgeben
	if ( $av['showicon'] == 1 )
	{
	    $out .='<div class="wpml_ico_icon" onclick="smile2comment(\''.
		$av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
	    $out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		"' id='icoimg".$res->tid."' src='$ico_url' />&nbsp;";
	    $out .= "</div>\n";
	    
	}

	// icon als bild und text ausgeben
	if ( $av['showicon'] == 2 )
	{
	    $out .='<div class="wpml_ico_both" onclick="smile2comment(\''.
		$av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
	    
	    $out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		"' id='icoimg".$res->tid."' src='$ico_url' />&nbsp;";
	    $out .= "<br />" . $res->emoticon ; 
	    $out .= "</div>\n";
	}


    }
    $out .= "</div>\n"; 
    $out .= '<div style="clear:both;">&nbsp;</div>';


    echo $out;
}
?>
