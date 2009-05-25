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


// stellt fest ob wir uns in einem der edit dialoge befinden
function in_edit() 
{
    global $pagenow;
    
    $ie=false;
    if ( is_admin() and ( 
	     ( $pagenow == 'post.php' ) or
	     ( $pagenow == 'page.php' ) or 
	     ( $pagenow == 'post-new.php' ) or 
	     ( $pagenow == 'page-new.php' ) )
	)
	$ie=true;
    return $ie;
}

// init funktion fuer die kommentarunterstuetzung
// fuegt das javascript stueckchen hinzu
function wpml_edit_init()
{
    // optionen einlesen
    $av = unserialize(get_option("wpml-opts"));
    
    if (in_edit() and $av['onedit'] == "1") 
    { 
	// meta boxen hinzufügen für posts und pages
	add_meta_box('wpml_metabox', __('wp-Monalisa', "wpml") , 
		     "wpml_metabox", 'post', 'side','default');
		     //$av['metacontext'], $av['metaprio'] );	

	add_meta_box('wpml_metabox', __('wp-Monalisa', "wpml") , 
		     "wpml_metabox", 'page', 'side','default');
		     //$av['metacontext'], $av['metaprio'] );
    }	
}

function wpml_metabox()
{
    global $wpdb;

    // table name
    $wpml_table = $wpdb->prefix . "monalisa";
    
    // optionen einlesen
    $av = unserialize(get_option("wpml-opts"));

    // icons lesen
    $sql="select tid,emoticon,iconfile from $wpml_table where onpost=1 order by tid;";
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
	    $out .='<div class="wpml_ico_text" onclick="smile2edit(\'content\',\''.
		addslashes($smile).'\','.$repl.');">'."\n";
	    $out .= $res->emoticon . "&nbsp;" ; 
	    $out .= "</div>";
	}

	// icon nur als bild ausgeben
	if ( $av['showicon'] == 1 )
	{
	    $out .='<div class="wpml_ico_icon" onclick="smile2edit(\'content\',\''.
		addslashes($smile).'\','.$repl.');">'."\n";
	    $out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		"' id='icoimg".$res->tid."' src='$ico_url' />&nbsp;";
	     $out .= "</div>";
	}

	// icon als bild und text ausgeben
	if ( $av['showicon'] == 2 )
	{
	    $out .='<div class="wpml_ico_both" onclick="smile2edit(\'content\',\''.
		addslashes($smile).'\','.$repl.');">'."\n";
	    $out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		"' id='icoimg".$res->tid."' src='$ico_url' />&nbsp;";
	    $out .= "<br />" . $res->emoticon ; 
	    $out .= "</div>\n";
	}
    }
    $out .= "</div>"; 
    $out .= '<div style="clear:both;">&nbsp;</div>';


    echo $out;
}
?>