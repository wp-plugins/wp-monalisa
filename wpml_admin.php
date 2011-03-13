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

// include functions
require_once('wpml_func.php');

//
// add menuitem for options menu
//
function wpml_admin_init() 
{
    if (function_exists('add_options_page')) 
    {
	add_menu_page('wp-Monalisa', 'wp-Monalisa', 6, 
		      basename(__FILE__), 'wpml_admin',
		      site_url("/wp-content/plugins/wp-monalisa") . '/smiley.png');
    }
    wp_enqueue_script('wpml_admin',
		      '/' . PLUGINDIR . '/wp-monalisa/wpml_admin.js',
		      array(), "9999");
    
    // add thickbox and jquery for import interface 
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_style ( 'thickbox' );
    
} 

//
// function to show and maintain the emoticons and the options
//
function wpml_admin()
{
  // get sql object
  global $wpdb;

  // table name
  $wpml_table = $wpdb->prefix . "monalisa";

  // base url for links
  $thisform = "admin.php?page=wpml_admin.php";

  // optionen einlesen
  $av = unserialize(get_option("wpml-opts"));
  $av['wpml-linesperpage'] = get_option("wpml-linesperpage");

  //
  // post operationen
  //
  //
  // allgemeine optionen updaten
  //
  if ( isset($_POST['action']) and $_POST['action'] == "editopts" )
  {
      $av['onedit']           = $_POST['onedit'];
      $av['oncomment']        = $_POST['oncomment'];
      $av['showicon']         = $_POST['showicon'];
      $av['replaceicon']      = $_POST['replaceicon'];
      $av['showastable']      = $_POST['showastable'];
      $av['smiliesperrow']    = (int) $_POST['smiliesperrow'];
      $av['showaspulldown']   = $_POST['showaspulldown'];
      $av['smilies1strow']    = (int) $_POST['smilies1strow'];
      $av['icontooltip']      = $_POST['icontooltip'];

      if ( $_POST['commenttextid']=="" )
	  $av['commenttextid'] = "comment";
      else
	  $av['commenttextid']=$_POST['commenttextid'];
      if ( is_dir(ABSPATH . stripslashes($_POST['iconpath']) ))
	  $av['icondir']     = stripslashes($_POST['iconpath']);
      else
	  admin_message( __("Iconpath is no valid directory, resetting it. Please enter the path relative to the wordpress main directory.","wpml") );

      update_option("wpml-opts",serialize($av));
      admin_message( __("Settings saved","wpml") );
  }

  //
  // es sollen datensätze gelöscht werden
  //
  if ( isset($_POST['action']) and 
       $_POST['action'] == "editicons" and 
       isset ( $_POST['deletemarked']) )
  {
      $sql="select max(tid) from $wpml_table;";
      $maxnum = $wpdb->get_var($sql);
      for ($i = 1; $i <= $maxnum; $i++) 
      {
	  if ( $_POST['mark' . $i] == $i )
	  {
	      $sql = "delete from $wpml_table where tid=$i;";
	      $result = $wpdb->query($sql);
	      admin_message( __("Deleted record ","wpml") . $i );
	  }
      }
  }
  
  //
  // icon mapping ändern oder neu anlegen
  //
  if ( isset($_POST['action']) and 
       $_POST['action'] == "editicons"  and 
       isset ( $_POST['updateicons']) )
  { 
      // hoechste satz-id ermitteln bevor ggf. ein neuer satz hinzukommt
      // denn der neue satz darf/muss nicht upgedated werden
      $sql="select max(tid) from $wpml_table;";
      $maxnum = $wpdb->get_var($sql);

      // neuen satz anlegen
      if ( trim($_POST['NEWemoticon']) != "" )
      {
	  // pruefen ob bereits ein satz mit dem gleichen emoticon vorhanden ist
	  $sql  = "select count(*) from $wpml_table where emoticon='".$_POST['NEWemoticon']."';";
	  $vorhanden = $wpdb->get_var($sql);
	  if ($vorhanden > 0)
	  {
	      admin_message( __("Emoticon allready used. Record not inserted","wpml") );   
	  } else {
	      // satz einfuegen
	      $sql  = "insert into $wpml_table (tid,emoticon,iconfile,onpost,oncomment) values (0,";
	      $sql .= "'".trim($_POST['NEWemoticon'])."',";
	      $sql .= "'".$_POST['NEWicon']."',";
	      $sql .= ($_POST['NEWonpost']=="1"?"1":"0") . "," . 
		  ($_POST['NEWoncomment']=="1"?"1":"0") . ");";
	      $result = $wpdb->query($sql);
	  }
      }
      
      $i=0;
      for ($i = 1; $i <= $maxnum; $i++) 
      {
	  // nur fuer gefüllte felder updaten
	  if ( ! isset($_POST['emoticon'.$i]) )
	      continue;
	  // pruefen ob bereits ein satz mit dem gleichen emoticon vorhanden ist
	  $vorhanden=0;
	  $j=0;
	  // ermittle wie oft das emoticon eingetragen wurde
	  for ($j=1; $j <= $maxnum; $j++)
	  {
	      // nur für gefüllte felder prüfen
	      if ( ! isset($_POST['emoticon'.$i]) )
		  continue;

	      if ($_POST['emoticon'.$j] == $_POST['emoticon'.$i])
		  $vorhanden += 1;
	  }
	  
          // wenn öfter als einmal, erfolgt kein update
	  if ($vorhanden > 1)
	  {
	      admin_message( __("Emoticon allready used. Record not updated","wpml") );   
	  } else {
	      // datensätze updaten
	      // durch das where tid=$i werden nur vorhandene sätze upgedated
	      // exitiert kein satz mit tid=$i wird auch kein satz gefunden
	      $sql  = "update $wpml_table ";
	      $sql .= "set emoticon='" . trim($_POST['emoticon'.$i])."',";
	      $sql .= " iconfile='"    . $_POST['icon'.$i]."',";
	      $sql .= " onpost="       . ($_POST['onpost'.$i]   == "1"?"1":"0") . ",";
	      $sql .= " oncomment="    . ($_POST['oncomment'.$i]== "1"?"1":"0") . " ";
	      $sql .= "where tid=".$i.";";
	      $result = $wpdb->query($sql);
	  }
      } 
      admin_message( __("Records updated","wpml") );
      
  }
  
  //
  // formular aufbauen ===================================================
  //
  $out = "";

  $out .= '<div class="wrap"><h2>wp-Monalisa '.__('Settings',"wpml").'</h2>';
  $out .= '<div id="ajax-response"></div>'."\n"; 
  
  // hinweis auf wordpress smilies schalter deaktivieren
  if (get_option("use_smilies") == "1")
      $out .= '<div class="error" id="error"><strong>' . __("Please turn off Options -> Write -> 'Convert emoticons like...' to use wp-Monalisa smilies).","wpml") . "</strong></div>\n";

  $out .= '<form name="editopts" id="editopts" method="post" action="">';
  $out .= '<input type="hidden" name="action" value="editopts" />';

  $out .= '<table class="editform" cellspacing="5" cellpadding="5">';
  $out .= '<tr><th scope="row" valign="top"><label for="iconpath">'.__('Iconpath','wpml').':</label></th>'."\n";

  // icon verzeichnis
  $out .= '<td colspan="5"><input name="iconpath" id="iconpath" type="text" value="'. $av['icondir'].'" size="70" onchange="alert(\''.__('You are about to change the iconpath.\n Please be careful and make sure the icons are still accessible.\n To update your settings klick Save Settings',"wpml").'\');" /></td></tr>'."\n"; 
  
  // anzeige der smilies im editor
  $out .= '<tr><th scope="row" valign="top"><label for="onedit">'.__('Show smilies on edit','wpml').':</label></th>'."\n";
  $out .= '<td><input name="onedit" id="onedit" type="checkbox" value="1"'.($av['onedit']=="1"?'checked="checked"':"").' /></td>'."\n";
  
  $out .= '<td>&nbsp;</td></tr>';
 
   // anzeige der smilies für kommentare
  $out .= '<tr><th scope="row" valign="top"><label for="oncomment">'.__('Show smilies on comment','wpml').':</label></th>'."\n";
  $out .= '<td><input name="oncomment" id="oncomment" type="checkbox" value="1"'.($av['oncomment']=="1"?'checked="checked"':"").'/></td>'."\n";

  // kommentar textarea id
 $out .= '<th scope="row" valign="top"><label for="commenttextid">'.__('Comment Textarea ID','wpml').':</label></th>'."\n";
   $out .= '<td><input name="commenttextid" id="commenttextid" type="text" value="'. $av['commenttextid'].'" size="20" onchange="alert(\''.__('You are about to change the id of the textarea of your comment form.\n Please make sure you enter the correct id, to make wp-monalisa work correctly',"wpml").'\');" /></td></tr>'."\n"; 

  $out .= '<tr><th scope="row" valign="top"><label for="replaceicon">'.__('Replace emoticons with html-images','wpml').':</label></th>'."\n";
  $out .= '<td><input name="replaceicon" id="replaceicon" type="checkbox" value="1" '.($av['replaceicon']=="1"?'checked="checked"':""). ' /></td>'."\n";

  $out .= '<th scope="row" valign="top"><label for="showicon">'.__('Show emoticons in selection as','wpml').':</label></th>'."\n";
  $out .= '<td><select name="showicon" id="showicon" onchange="wpml_admin_switch();" >'."\n";
  $out .= '<option value="1"'.($av['showicon']=="1"?'selected="selected"':"").'>'.__("Icon",'wpml').'</option>';
  $out .= '<option value="0"'.($av['showicon']=="0"?'selected="selected"':"").'>'.__("Text",'wpml').'</option>';
  $out .= '<option value="2"'.($av['showicon']=="2"?'selected="selected"':"").'>'.__("Both",'wpml').'</option>';
  $out .= "</select></td></tr>\n";

  // smilies als tabelle anzeigen
  // smiley tabelle
  $out .= '<tr><th scope="row" valign="top"><label for="showastable">'.__('Show smilies in a table','wpml').':</label></th>'."\n";
  $out .= '<td><input name="showastable" id="showastable" type="checkbox" value="1" '.($av['showastable']=="1"?'checked="checked"':""). ' onchange="wpml_admin_switch();" /></td>'."\n";
  $out .= '<th scope="row" valign="top"><label for="smiliesperrow">'.__('Smilies per row','wpml').':</label></th>'."\n";
  $out .= '<td><input name="smiliesperrow" id="smiliesperrow" type="text" value="'. 
      $av['smiliesperrow'] . '" size="3" maxlength="3" /></td>'."\n";
  $out .="</tr>\n";

 
  // smilies zum aufklappen
  // smiley pull-down
  $out .= '<tr><th scope="row" valign="top"><label for="showaspulldown">'.__('Show smilies as Pulldown','wpml').':</label></th>'."\n";
  $out .= '<td><input name="showaspulldown" id="showaspulldown" type="checkbox" value="1" '.($av['showaspulldown']=="1"?'checked="checked"':""). ' onchange="wpml_admin_switch();" /></td>'."\n";
  $out .= '<th scope="row" valign="top"><label for="smilies1strow">'.__('Smilies in 1st row','wpml').':</label></th>'."\n";
  $out .= '<td><input name="smilies1strow" id="smilies1strow" type="text" value="'. 
      $av['smilies1strow'] . '" size="3" maxlength="3" /></td>'."\n";
  $out .="</tr>\n";


  // tooltips fuer icons anzeigen
   $out .= '<tr><th scope="row" valign="top"><label for="icontooltip">'.__('Show tooltip for icons','wpml').':</label></th>'."\n";
  $out .= '<td><input name="icontooltip" id="icontooltip" type="checkbox" value="1" '.($av['icontooltip']=="1"?'checked="checked"':""). ' /></td>'."\n";
  $out .= '<th scope="row" valign="top">&nbsp;</label></th>'."\n";
  $out .= '<td>&nbsp;</td>'."\n";
  $out .= "</tr>\n";

  $out .= '</table>'."\n";
  $out .= '<script  type="text/javascript">wpml_admin_switch();</script>';

  
  
  // add submit button to form
  $out .= '<p class="submit"><input type="submit" name="updateopts" value="'.__('Save Settings','wpml').' &raquo;" /></p></form>'."\n";

  

  // add link to import/export interface
  $out .= '<div style="text-align:right"><a href="../wp-content/plugins/wp-monalisa/wpml_import.php?height=600&amp;width=400" class="thickbox" >'.__("Import Smiley-Package","wpml").'</a>&nbsp;&nbsp;&nbsp;'."\n";
  $out .= '<a href="../wp-content/plugins/wp-monalisa/wpml_export.php?height=640&amp;width=540" class="thickbox" >'.__("Export Smiley-Package (pak-Format)","wpml").'</a></div>'."\n";

  $out .= "</div><hr />\n";

  echo $out;

  //
  // output icon table
  //
  
  // icon file list on disk
  $flist = scandir(ABSPATH . $av['icondir']);

  $out = "";
  $out .= "<div class=\"wrap\">";
  $out .= "<h2>".__("Smilies","wpml")."</h2>\n"; 
  
  if ( empty($flist) )
  {
      admin_message( __("Iconpath is empty or invalid","wpml") );
  }
   

  // navigation leiste
  // anzahl der smilies holen
  $sql="select count(*) as anz from ".$wpml_table;
  $res = $wpdb->get_row($sql);
  $all_lines = $res->anz;
  
  // aufgerufene seite auslesen
  if (isset($_GET['activepage']))
      $active_page= (int) $_GET['activepage'];
  else
      $active_page=1;

  // zeilen pro seite aus dem formular holen aber nur das geänderte feld
  $lines_per_page= $av['wpml-linesperpage'];
  if ( isset($_POST["set_lines_per_page1_x"]) || 
       isset($_POST["set_lines_per_page2_x"]) ||
       isset($_POST["updateicons"]) ) {
      if (isset($_POST['lines_per_page1']) && isset($_POST['lines_per_page2']) ) {
	  if ($av['wpml-linesperpage'] == $_POST['lines_per_page1'])
	      $lines_per_page = (int) $_POST['lines_per_page2'];
	  else
	      $lines_per_page = (int) $_POST['lines_per_page1'];
	  $av['wpml-linesperpage'] = $lines_per_page;
	  update_option("wpml-linesperpage",$lines_per_page);
	  // wenn die anzahl der zeilen veraendert wurde auf erste seite springen
	  $active_page=1;
      }
  }

  // just in case option is not yet set
  if (! $lines_per_page > 0)
      $lines_per_page = 10;
  
  $maxpage = ($all_lines / $lines_per_page);
  if ($all_lines % $lines_per_page > 0)
      $maxpage +=1;
  
  // icons
  $out .= '<form name="editicons" id="editicons" method="post" action="">';
  $out .= '<input type="hidden" name="action" value="editicons" />';

  // submit knöpfe ausgeben
  $out .= '<div class="tablenav"><input type="submit" name="updateicons" value="'.__('Save','wpml').' &raquo;" class="button-secondary" />&nbsp;&nbsp;&nbsp;<input type="submit" name="deletemarked" value="'.__('Delete marked','wpml').' &raquo;" class="button-secondary" />'."\n";

  // seitennaviagtion ausgeben
  $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  $out .= "<a href=\"$thisform&amp;activepage=0\">". __("Show all","wpml") . "</a>&nbsp;&nbsp;"; 
  $out .= "<a href=\"$thisform&amp;activepage=" . (string) ($active_page-1 < 1?1:$active_page-1) ."\">&lt;</a>&nbsp;"; 
  for ($i=1;$i < ($all_lines / $lines_per_page)+1;$i++) { 
      if ( $active_page == $i ) 
	  $out .= "<b>". $i . "</b>&nbsp;";  
      else 
	  $out .= "<a href=\"$thisform&amp;activepage=$i\">". $i . "</a>&nbsp;"; 
  } 
  $out .="<a href=\"$thisform&amp;activepage=" . (string) ($active_page+1 > $maxpage?$active_page:$active_page+1) . "\">&gt;</a>&nbsp;&nbsp;"; 
  $out .= __("Lines per Page:","wpml")."<input style=\"font-size:10px\" type='text' name='lines_per_page1' value='".$lines_per_page."' size='4' />";
  $naviconfile = site_url(PLUGINDIR . "/wp-monalisa/yes.png");
  $out .='<input type="image" align="top" name="set_lines_per_page1" src="' . $naviconfile .'" alt="'.__("Save","wpml").'" /></div>';

  // icon zeilen ausgeben
  $out .= "<table class=\"widefat\">\n";
  $out .= "<thead><tr>\n";
  $out .= '<th scope="col" style="text-align:center"><input style="margin-left: 0;" id="markall" type="checkbox" onchange="wpml_markall(\'markall\');" />&nbsp;</th>'."\n";
  $out .= '<th scope="col">'.__('Emoticon',"wpml")."</th>"."\n";
  $out .= '<th scope="col" colspan="2" style="text-align: left">'.__("Icon","wpml").'<br />(* '.__('not mapped yet',"wpml").')</th>'."\n";
  $out .= '<th scope="col">'.__('On Post',"wpml").'</th>'."\n";
  $out .= '<th scope="col">'.__('On Comment',"wpml").'</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '</tr></thead>'."\n";
  
  // tabellenfuss
  $out .= "<tfoot><tr>\n";
  $out .= '<th scope="col" style="text-align:center"><input style="margin-left: 0;" id="markall1" type="checkbox" onchange="wpml_markall(\'markall1\');" />&nbsp;</th>'."\n";
  $out .= '<th scope="col">'.__('Emoticon',"wpml")."</th>"."\n";
   $out .= '<th scope="col" colspan="2" style="text-align: left">'.__("Icon","wpml").'<br />(* '.__('not mapped yet',"wpml").')</th>'."\n";
  $out .= '<th scope="col">'.__('On Post',"wpml").'</th>'."\n";
  $out .= '<th scope="col">'.__('On Comment',"wpml").'</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '</tr></tfoot>'."\n";
  
  // tabellenbody
  // zeile fuer neueintrag
  $out .= '<tr><td align="center"><b>'. __("New Entry",'wpml').":</b></td>";
  $out .= '<td><input name="NEWemoticon" id="NEWemoticon" type="text" value="" size="15" maxlength="25" /></td>'."\n";
  $out .= '<td>';
   $out .= '<select name="NEWicon" id="NEWicon" onchange="updateImage(\''.site_url($av['icondir']).'\',\'NEW\')">'."\n";
  // build select html for iconfile
  $icon_select_html="";
  // fetch compare list to sign unused files
  $clist=array();
  $notused="";
  $sql="select iconfile from $wpml_table;";
  $results = $wpdb->get_results($sql);
  foreach ($results as $i)
      array_push($clist,$i->iconfile);

  // file loop
  foreach($flist as $iconfile) 
  { 
      if ( in_array($iconfile, $clist) )
	  $notused="";
      else
	  $notused="*";
      $ext = substr($iconfile,strlen($iconfile)-3,3);
      if ($ext == "gif" || $ext == 'png') {
	  $icon_select_html .= "<option value='".$iconfile."' ";
	  $icon_select_html .= ">".$iconfile.$notused."</option>\n";
      }
  }
  $out .= $icon_select_html . "</select></td>\n";
  $out .= '<td><img class="wpml_ico" name="icoimg" id="icoimg" src="' . 
      site_url($av['icondir']).'/wpml_smile.gif" alt="wp-monalisa icon"/></td>';
  $out .= '<td><input name="NEWonpost" id="NEWonpost" type="checkbox" value="1" /></td>'."\n";
  $out .= '<td><input name="NEWoncomment" id="NEWoncomment" type="checkbox" value="1" />'."\n";
  $out .= '<script type="text/javascript">updateImage("'.site_url($av['icondir']).'","NEW")</script></td>';
  $out .= "<td colspan='2'>&nbsp;</td></tr>\n";

  // jetzt kommen die vorhandenen eintraege
  // select all icon entries
  $sql="select tid,emoticon,iconfile,onpost,oncomment from $wpml_table order by tid ";
  
  // die satzgrenzen (erster/letzter)  fuer den select ermitteln
  if ($active_page > 0 ) {
      $lstart = ($active_page -1) * $lines_per_page;
      $lcount = $lines_per_page;
      $sql .= " limit $lstart,$lcount";
  }
  
  // select ausfuehren
  $results = $wpdb->get_results($sql);
  // zaehler um ersten und letzten zu erkennen
  $lastnum = count($results)-1;
  $count   = 0;
  $tid=0;
  $alternate = false;
  // icon loop
  foreach($results as $res) 
  {  
      // build select html for iconfile
      $icon_select_html="";
      // file loop
      foreach($flist as $iconfile) 
      {
	  $ext = substr($iconfile,strlen($iconfile)-3,3);
	  if ($ext == "gif" || $ext == "png") {
	      $icon_select_html .= "<option value='".$iconfile."' ";
	      if ($iconfile == $res->iconfile)
		  $icon_select_html .= 'selected="selected"';
	      $icon_select_html .=">".$iconfile."</option>\n";
	  }
      }
      
      $tid = $res->tid;
      // hintegrund farbe für jede zweite zeile
      if ($alternate)
	  $out .= '<tr class="alternate">';
      else
	  $out .= '<tr>';
      $alternate = !$alternate;
      $out .= '<td align="center"><input class="wpml_mark" name="mark'.$tid.'" id="mark'.$tid.'" type="checkbox" value="'. $tid.'" />&nbsp;</td>';
      $out .= '<td><input name="emoticon'.$tid.'" id="emoticon'.$tid.'" type="text" value="'. $res->emoticon.'" size="15" maxlength="25" /></td>'."\n";

      $out .= '<td>';
      $out .= '<select name="icon'.$tid.'" id="icon'.$tid.
	  '" onchange="updateImage(\''.site_url($av['icondir'])."',".$tid.')">'."\n";
      $out .= $icon_select_html . "</select></td>\n";
      $out .= '<td><img class="wpml_ico" name="icoimg'.$tid.'" id="icoimg'.$tid.'" src="' . 
	  site_url($av['icondir']).'/wpml_smile.gif" alt="wp-monalisa icon" />';
      $out .= '<script type="text/javascript">updateImage("'.site_url($av['icondir']).'","'.$tid.'")</script></td>';

      $out .= '<td><input name="onpost'.$tid.'" id="onpost'.$tid.'" type="checkbox" value="1"'.($res->onpost=="1"?'checked="checked"':"").' /></td>'."\n";
      $out .= '<td><input name="oncomment'.$tid.'" id="oncomment'.$tid.'" type="checkbox" value="1"'.($res->oncomment=="1"?'checked="checked"':"").' /></td>'."\n";
      // add position buttons
      if ($count != 0)
	  $out .= '<td><img width="20" src="'.plugins_url().'/wp-monalisa/up.png" onclick="switch_row('.$tid.',\'up\');" alt="down arrow"/></td>';
      else
	  $out .= "<td>&nbsp;</td>";
      if ( $count != $lastnum )
	  $out .= '<td><img width="20" src="'.plugins_url().'/wp-monalisa/down.png" onclick="switch_row('.$tid.',\'down\');" alt="up arrow"/></td>'; 
      else
	  $out .= "<td>&nbsp;</td>";
      $out .= "</tr>\n";
      $count ++; // zaehler erhöhen
  } 
  
 
  $out .= "</table>";

  // submit knöpfe ausgeben
  $out .= '<div class="tablenav"><input type="submit" name="updateicons" value="'.__('Save','wpml').' &raquo;" class="button-secondary" />&nbsp;&nbsp;&nbsp;<input type="submit" name="deletemarked" value="'.__('Delete marked','wpml').' &raquo;" class="button-secondary" />'."\n";

  // seitennaviagtion ausgeben
  $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  $out .= "<a href=\"$thisform&amp;activepage=0\">". __("Show all","wpml") . "</a>&nbsp;&nbsp;"; 
  $out .= "<a href=\"$thisform&amp;activepage=" . (string) ($active_page-1 < 1?1:$active_page-1) ."\">&lt;</a>&nbsp;"; 
  for ($i=1;$i < ($all_lines / $lines_per_page)+1;$i++) { 
      if ( $active_page == $i ) 
	  $out .= "<b>". $i . "</b>&nbsp;";  
      else 
	  $out .= "<a href=\"$thisform&amp;activepage=$i\">". $i . "</a>&nbsp;"; 
  } 
  $out .="<a href=\"$thisform&amp;activepage=" . (string) ($active_page+1 > $maxpage?$active_page:$active_page+1) . "\">&gt;</a>&nbsp;&nbsp;"; 
  $out .= __("Lines per Page:","wpml")."<input style=\"font-size:10px\" type='text' name='lines_per_page2' value='".$lines_per_page."' size='4' />"; 
  $out .='<input type="image" align="top" name="set_lines_per_page2" src="' . $naviconfile .'" alt="'.__("Save","wpml").'" /></div>';
  

  $out .= '</form></div>'."\n";
  
  echo $out;
}

?>