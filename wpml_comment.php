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
    // abfangen wenn wert nicht gesetzt oder 0 ist, dann nehmen wir einfach 1
    if ( (int) $av['smiliesperrow'] == 0)
	$av['smiliesperrow'] = 1;
    if ( (int) $av['smilies1strow'] == 0)
	$av['smilies1strow'] = 7;

     // icons lesen
    $sql="select tid,emoticon,iconfile from $wpml_table where oncomment=1 order by tid;";
    $results = $wpdb->get_results($sql);

    // ausgabe der icons aufbauen
    $out = "\n\n";

    if ( $av['showicon'] == 0)
	$out .= "<div class='wpml_commentbox_text'>\n";
    else
	$out .= "<div class='wpml_commentbox'>\n";
    
    
    if  ( $av['showastable'] == 1 && $av['showicon'] == 1 )
    {
	$out .= "<table class='wpml_smiley_table' >";
    }
    
    $double_check = array(); // array um doppelte auszuschliessen
    $sm_count = 0;
    foreach($results as $res) 
    {
	// prüfe ob icon schon ausgegeben, 
	// wenn ja überspringe es, 
	// wenn nein merken
	if ( in_array($res->iconfile, $double_check) )
	    continue;
	else
	    $double_check[] = $res->iconfile;


	// prüfe ob eine neue zeile anfängt
	if ( ( $sm_count == 0 || 
	       $sm_count % $av['smiliesperrow'] == 0 ) && 
		 $av['showastable'] == 1 && 
		 $av['showicon'] == 1 
	    )
	{
	    $out .= "<tr class='wpml_smiley_row' >";
	}

	$ico_url = site_url($av['icondir']) . '/' . $res->iconfile; 
	if ( $av['replaceicon'] == 0)
	{
	    $smile = $res ->emoticon;
	    $repl = 0;
	} else {
	    $smile = $ico_url;
	    $repl = 1;
	}

	// tooltip html bauen
	$ico_tt="";
	if ( $av['icontooltip'] == 1)
	    $ico_tt = " title='" .addslashes($smile) . "' ";

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
	    if ( $av['showastable'] == 0 )
	    {
		$out .='<div class="wpml_ico_icon" onclick="smile2comment(\''.
		    $av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
		$out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		    "' id='icoimg".$res->tid."' src='$ico_url' alt='".
		    addslashes($smile)."' $ico_tt />&nbsp;";
		$out .= "</div>\n";
	    } 
	    else  // output as a table
	    {
		$out .='<td class="wpml_ico_icon" onclick="smile2comment(\''.
		    $av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
		$out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		    "' id='icoimg".$res->tid."' src='$ico_url' alt='".
		    addslashes($smile)."' $ico_tt />&nbsp;";
		$out .= "</td>\n";	
	    }
	    
	}

	// icon als bild und text ausgeben
	if ( $av['showicon'] == 2 )
	{
	    $out .='<div class="wpml_ico_both" onclick="smile2comment(\''.
		$av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.');">'."\n";
	    
	    $out .= "<img class='wpml_ico' name='icoimg".$res->tid.
		"' id='icoimg".$res->tid."' src='$ico_url' alt='".
		addslashes($smile)."' $ico_tt />&nbsp;";
	    $out .= "<br />" . $res->emoticon ; 
	    $out .= "</div>\n";
	}
	
	// inc smiley count
	$sm_count++;

        // prüfe ob eine zeile fertig ist
	if ( ( $sm_count > 0 && 
	       $sm_count % $av['smiliesperrow'] == 0 )  && 
	     $av['showastable'] == 1 && 
	     $av['showicon'] == 1
	    )
	{
	    $out .= "</tr>";
	}
	
	if  ( $av['showaspulldown'] == 1  && $av['smilies1strow'] == $sm_count )
	{
	    $out1strow = $out;
	}
	
    } // ende foreach

    if  ( $av['showastable'] == 1  && $av['showicon'] == 1 )
    {
	$out .= "</table>";
	$out1strow .= "</table>";
    }

    if  ( $av['showaspulldown'] == 1 ) {
	$out .= "<div class='wpml_nav' id='buttonl' >".__("less...","wpml")."</div>"; 
	$out1strow .= "<div class='wpml_nav' id='buttonm' >".__("more...","wpml")."</div>";
    } 
    
    $out .= "</div>\n";
    $out1strow .= "</div>\n";
    $out .= '<div style="clear:both;display:none">&nbsp;</div>';
    $out1strow .= '<div style="clear:both;">&nbsp;</div>'."\n";
    // img ids tauschen um eindeutigkeit zu gewaehrleisten, da es osnt zu xhtml fehlern kommt
    $out1strow=str_replace("icoimg","hicoimg",$out1strow);

    if  ( $av['showaspulldown'] != 1 )
	echo $out;
    else {
	// nur erste zeile ausgeben
	echo '<div id="smiley1" >' . $out1strow . "</div>";
	echo '<div id="smiley2" style="display:none;">' . $out . "</div>";
    }
}
?>
