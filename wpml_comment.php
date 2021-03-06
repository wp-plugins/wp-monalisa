<?php

/* This file is part of the wp-monalisa plugin for wordpress */

/*  Copyright 2009-2012  Hans Matzen  (email : webmaster at tuxlog.de)

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
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('You are not allowed to call this page directly.');
}

// init funktion fuer die kommentarunterstuetzung
function wpml_comment_init()
{
	// optionen einlesen
	$av=array();
	if (function_exists('is_multisite') && is_multisite())
		$av = maybe_unserialize(get_blog_option(1, "wpml-opts"));
	else
		$av = unserialize(get_option("wpml-opts"));

	// show smileys in commentform if not disabled
	if ( $av['oncomment'] == "1" ) {
		add_action('comment_form','wpml_comment');
	}

	// show smilies in buddypress
	if (defined('BP_VERSION') && $av['wpml4buddypress'] == "1") {
		// add smilis to activities
		add_action('bp_after_activity_post_form','wpml_comment');
		add_action('bp_activity_entry_comments','wpml_comment');
		// add smilies to messages
		add_action('bp_after_messages_compose_content','wpml_comment');
		// add smilies to forums (bbpress)
		add_action('bbp_theme_after_topic_form_content','wpml_comment');
		add_action('bbp_theme_after_reply_form_content','wpml_comment',1);
		add_action('groups_forum_new_topic_after','wpml_comment');
		add_action('groups_forum_new_reply_after','wpml_comment');
		add_action('bp_group_after_edit_forum_topic', 'wpml_comment');
		add_action('bp_after_group_forum_post_new', 'wpml_comment');
	}
	
	// show smilies in bbpress
	if (class_exists( 'bbPress' ) && $av['wpml4bbpress'] == "1") {
		// add smilies to forums (bbpress)
		add_action('bbp_theme_after_topic_form_content','wpml_comment');
		add_action('bbp_theme_after_reply_form_content','wpml_comment',1);
	}
}

function wpml_comment($postid=0)
{
	echo get_wpml_comment($postid);
}


function get_wpml_comment($postid=0)
{
	global $wpdb,$post,$wpml_first_preload;

	$uid = uniqid();
	$out1strow="";
	
	// if this post is excluded return nothing :-)
	$excludes = unserialize(get_option('wpml_excludes'));
	if (is_array($excludes) and in_array($post->ID,$excludes))
		return "";

	// table name
	$wpml_table = $wpdb->prefix . "monalisa";

	if (function_exists('is_multisite') && is_multisite())
		$wpml_table = $wpdb->base_prefix . "monalisa";

	// optionen einlesen
	$av=array();
	if (function_exists('is_multisite') && is_multisite())
		$av = maybe_unserialize(get_blog_option(1, "wpml-opts"));
	else
		$av = unserialize(get_option("wpml-opts"));

	// abfangen wenn wert nicht gesetzt oder 0 ist, dann nehmen wir einfach 1
	if ( (int) $av['smiliesperrow'] == 0)
		$av['smiliesperrow'] = 1;
	if ( (int) $av['smilies1strow'] == 0)
		$av['smilies1strow'] = 7;

	// icons lesen
	$sql="select tid,emoticon,iconfile,width,height from $wpml_table where oncomment=1 order by tid;";
	$results = $wpdb->get_results($sql);

	// ausgabe der icons aufbauen
	$out = "\n\n";
	$loader="";

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

		// url bauen
		$ico_url = site_url($av['icondir']) . '/' . $res->iconfile;
		
		// hohe und breite bauen
		$dimensions="";
		if ($res->width!=0 and $res->height!=0)
			$dimensions = " width='".$res->width."' height='".$res->height."' ";
			
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
				
				$out .='<div class="wpml_ico_icon" id="icodiv-'.$uid.'-'.$res->tid.'" onclick="smile2comment(\''.
		    $av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.',\'icodiv-'.$uid.'-'.$res->tid.'\');">'."\n";
				$out .= "<img class='wpml_ico' " .
						" id='icoimg".$uid.'-'.$res->tid."' src='$ico_url' alt='".
						addslashes($smile)."' $dimensions $ico_tt />&nbsp;";
				$out .= "</div>\n";
			}
			else  // output as a table
			{
				$out .='<td class="wpml_ico_icon" id="icodiv-'.$uid.'-'.$res->tid.'" onclick="smile2comment(\''.
		    $av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.',\'icodiv-'.$uid.'-'.$res->tid.'\');">'."\n";
				$out .= "<img class='wpml_ico' " .
		    " id='icoimg".$res->tid."' src='$ico_url' alt='".
		    addslashes($smile)."' $dimensions $ico_tt />&nbsp;";
				$out .= "</td>\n";
			}
			 
		}

		// icon als bild und text ausgeben
		if ( $av['showicon'] == 2 )
		{
			$out .='<div class="wpml_ico_both" onclick="smile2comment(\''.
					$av['commenttextid'].'\',\''.addslashes($smile).'\','.$repl.',\'icodiv-'.$uid.'-'.$res->tid.'\');">'."\n";
			 
			$out .= "<img class='wpml_ico' name='icoimg".$res->tid.
			"' id='icoimg".$res->tid."' src='$ico_url' alt='". addslashes($smile)."' $dimensions $ico_tt />&nbsp;";
			$out .= "<br />" . $res->emoticon ;
			$out .= "</div>\n";
		}

		// image dem loader hinzufügen
		$loader .= "wpml_imglist[$sm_count]='$ico_url';\n";
		
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
		$out .= "<div class='wpml_nav' id='buttonl-$uid' onclick='wpml_toggle_smilies(\"$uid\");'>".__("less...","wpml")."</div>";
		$out1strow .= "<div class='wpml_nav' id='buttonm-$uid' onclick='wpml_more_smilies(\"$uid\");wpml_toggle_smilies(\"$uid\");'>".__("more...","wpml")."</div>";
	}

	$out .= "</div>\n";
	$out1strow .= "</div>\n";
	$out .= '<div style="clear:both;display:none">&nbsp;</div>';
	$out1strow .= '<div style="clear:both;">&nbsp;</div>'."\n";
	// ids tauschen um eindeutigkeit zu gewaehrleisten, da es sonst zu xhtml fehlern kommt
	$out1strow=str_replace("icoimg","hicoimg",$out1strow);
	$out1strow=str_replace("icodiv-","icodiv1-",$out1strow);

	  $loaderout = addslashes(str_replace(array("\n", "\r"), '', $out));
	// die Liste mit den images wird nur beim ersten Mal ausgegeben
	if ($wpml_first_preload) 
		$wpml_first_preload=false;
	else
		$loader="";
	
	$loader .= "wpml_more_html['$uid']=\"$loaderout\";\n";
	$loader  = "<script type='text/javascript'>\n$loader\n</script>\n";
	
	if  ( $av['showaspulldown'] != 1 )
		return $out;
	else {
		// nur erste zeile ausgeben
		return "\n$loader\n<div id='smiley1-$uid' >" . $out1strow . "</div>\n" . "<div id='smiley2-$uid' style='display:none;'>&nbsp;</div>";
		//return "<div id='smiley1-$uid' >" . $out1strow . "</div>\n" . "<div id='smiley2-$uid' style='display:none;'>" . $out . "</div>";
	}
}
?>
