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
		      '/' . PLUGINDIR . '/wp-monalisa/wpml_admin.js');
    
    // add thickbox and jquery for import interface 
    //wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_style( 'thickbox' );
    
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

  // optionen einlesen
  $av = unserialize(get_option("wpml-opts"));
  
  //
  // post operationen
  //
  //
  // allgemeine optionen updaten
  //
  if ( $_POST['action'] == "editopts" )
  {
      $av['onedit']           = $_POST['onedit'];
      $av['oncomment']        = $_POST['oncomment'];
      $av['showicon']         = $_POST['showicon'];
      $av['replaceicon']      = $_POST['replaceicon'];
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
  if ( $_POST['action'] == "editicons" and isset ( $_POST['deletemarked']) )
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
  if ( $_POST['action'] == "editicons"  and isset ( $_POST['updateicons']) )
  { 
      // hoechste satz-id ermitteln bevor ggf. ein neuer satz hinzukommt
      // denn der neue satz darf/muss nicht upgedated werden
      $sql="select max(tid) from $wpml_table;";
      $maxnum = $wpdb->get_var($sql);

      // neuen satz anlegen
      if ( $_POST['NEWemoticon'] != "" )
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
	      $sql .= "'".$_POST['NEWemoticon']."',";
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
	      $sql .= "set emoticon='" . $_POST['emoticon'.$i]."',";
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
  $out .= '<form name="editopts" id="editopts" method="post" action="">';
  $out .= '<input type="hidden" name="action" value="editopts" />';

  $out .= '<table class="editform" cellspacing="5" cellpadding="5">';
  $out .= '<tr><th scope="row" valign="top"><label for="iconpath">'.__('Iconpath','wpml').':</label></th>'."\n";

  // icon verzeichnis
  $out .= '<td colspan="5"><input name="iconpath" id="iconpath" type="text" value="'. $av['icondir'].'" size="70" onchange="alert(\''.__('You are about to change the iconpath.\n Please be careful and make sure the icons are still accessible.\n To update your settings klick Save Settings',"wpml").'\');" /></td></tr>'."\n"; 
  
  // anzeige der smilies im editor
  $out .= '<tr><th scope="row" valign="top"><label for="onedit">'.__('Show smilies on edit','wpml').':</label></th>'."\n";
  $out .= '<td><input name="onedit" id="onedit" type="checkbox" value="1"'.($av['onedit']=="1"?'checked="checked"':"").' /></td>'."\n";
  
  $out .= '<td>&nbsp;</td>';
 
   // anzeige der smilies für kommentare
  $out .= '<tr><th scope="row" valign="top"><label for="oncomment">'.__('Show smilies on comment','wpml').':</label></th>'."\n";
  $out .= '<td><input name="oncomment" id="oncomment" type="checkbox" value="1"'.($av['oncomment']=="1"?'checked="checked"':"").'/></td>'."\n";

  // kommentar textarea id
 $out .= '<th scope="row" valign="top"><label for="commenttextid">'.__('Comment Textarea ID','wpml').':</label></th>'."\n";
   $out .= '<td><input name="commenttextid" id="commentextid" type="text" value="'. $av['commenttextid'].'" size="20" onchange="alert(\''.__('You are about to change the id of the textarea of your comment form.\n Please make sure you enter the correct id, to make wp-monalisa work correctly',"wpml").'\');" /></td></tr>'."\n"; 

  $out .= '<tr><th scope="row" valign="top"><label for="replaceicon">'.__('Replace emoticons with html-images','wpml').':</label></th>'."\n";
  $out .= '<td><input name="replaceicon" id="replaceicon" type="checkbox" value="1"'.($av['replaceicon']=="1"?'checked=checked':""). ' /></td>'."\n";

  $out .= '<th scope="row" valign="top"><label for="showicon">'.__('Show emoticons in selection as','wpml').':</label></th>'."\n";
  $out .= '<td><select name="showicon" id="showicon">'."\n";
  $out .= '<option value="1"'.($av['showicon']=="1"?'selected="selected"':"").'>'.__("Icon",'wpml').'</option>';
  $out .= '<option value="0"'.($av['showicon']=="0"?'selected="selected"':"").'>'.__("Text",'wpml').'</option>';
  $out .= '<option value="2"'.($av['showicon']=="2"?'selected="selected"':"").'>'.__("Both",'wpml').'</option>';
  $out .= "</select></td>\n";

 $out .= '</table>'."\n";
  
  // add submit button to form
  $out .= '<p class="submit"><input type="submit" name="updateopts" value="'.__('Save Settings','wpml').' &raquo;" /></p></form>'."\n";

  // add link to import interface
  $out .= '<div style="text-align:right"><a href="../wp-content/plugins/wp-monalisa/wpml_import.php?height=600&amp;width=400" class="thickbox" Title="">'.__("Import Smiley-Package","wpml").'</a></div>'."\n";

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
  
  $out .= '<form name="editicons" id="editicons" method="post" action="">';
  $out .= '<input type="hidden" name="action" value="editicons" />';
  $out .= "<table class=\"widefat\">\n";
  $out .= "<thead><tr>\n";
  $out .= '<th scope="col" style="text-align: center">&nbsp;</th>'."\n";
  $out .= '<th scope="col">'.__('Emoticon',"wpml")."</th>"."\n";
  $out .= '<th scope="col" colspan="2" style="text-align: center">'.__("Icon","wpml").'</th>'."\n";
  $out .= '<th scope="col">'.__('On Post',"wpml").'</th>'."\n";
  $out .= '<th scope="col">'.__('On Comment',"wpml").'</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '<th scope="col">&nbsp;</th>'."\n";
  $out .= '</tr></thead>'."\n";
  
  // submit knöpfe ausgeben
  $out .= '<tr><td colspan="8" class="submit"><input type="submit" name="updateicons" value="'.__('Save','wpml').' &raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" name="deletemarked" value="'.__('Delete marked','wpml').' &raquo;" /></td></tr>'."\n";
  
  // zeile fuer neueintrag
  $out .= '<tr><td align="center"><b>'. __("New Entry",'wpml').":</b></td>";
  $out .= '<td><input name="NEWemoticon" id="NEWemoticon" type="text" value="" size="15" /></td>'."\n";
  $out .= '<td>';
   $out .= '<select name="NEWicon" id="NEWicon" onchange="updateImage(\''.site_url($av['icondir']).'\',\'NEW\')">'."\n";
  // build select html for iconfile
  $icon_select_html="";
  // file loop
  foreach($flist as $iconfile) 
  { 
      $ext = substr($iconfile,strlen($iconfile)-3,3);
      if ($ext == "gif") {
	  $icon_select_html .= "<option value='".$iconfile."' ";
	  $icon_select_html .= ">".$iconfile."</option>\n";
      }
  }
  $out .= $icon_select_html . "</select></td>\n";
  $out .= '<td><img class="wpml_ico" name="icoimg" id="icoimg" src="' . 
      site_url($av['icondir']).'/01smile.gif" /></td>';
  $out .= '<td><input name="NEWonpost" id="NEWonpost" type="checkbox" value="1" /></td>'."\n";
  $out .= '<td><input name="NEWoncomment" id="NEWoncomment" type="checkbox" value="1" />'."\n";
  $out .= '<script type="text/javascript">updateImage("'.site_url($av['icondir']).'","NEW")</script></td>';
  $out .= "<td>&nbsp;</td><td>&nbsp;</td></tr>\n";

  // jetzt kommen die vorhandenen eintraege
  // select all icon entries
  $sql="select tid,emoticon,iconfile,onpost,oncomment from $wpml_table order by tid;";
  $results = $wpdb->get_results($sql);
  // zaehler um ersten und letzten zu erkennen
  $lastnum = count($results)-1;
  $count   = 0;
  $tid=0;
  // icon loop
  foreach($results as $res) 
  {  
      // build select html for iconfile
      $icon_select_html="";
      // file loop
      foreach($flist as $iconfile) 
      {
	  $ext = substr($iconfile,strlen($iconfile)-3,3);
	  if ($ext == "gif") {
	      $icon_select_html .= "<option value='".$iconfile."' ";
	      if ($iconfile == $res->iconfile)
		  $icon_select_html .= 'selected="selected"';
	      $icon_select_html .=">".$iconfile."</option>\n";
	  }
      }
      
      $tid = $res->tid;
      $out .= '<tr><td align="center"><input name="mark'.$tid.'" id="mark'.$tid.'" type="checkbox" value="'. $tid.'" />&nbsp;</td>';
      $out .= '<td><input name="emoticon'.$tid.'" id="emoticon'.$tid.'" type="text" value="'. $res->emoticon.'" size="15" /></td>'."\n";

      $out .= '<td>';
      $out .= '<select name="icon'.$tid.'" id="icon'.$tid.
	  '" onchange="updateImage(\''.site_url($av['icondir'])."',".$tid.')">'."\n";
      $out .= $icon_select_html . "</select></td>\n";
      $out .= '<td><img class="wpml_ico" name="icoimg'.$tid.'" id="icoimg'.$tid.'" src="' . 
	  site_url($av['icondir']).'/01smile.gif" />';
      $out .= '<script type="text/javascript">updateImage("'.site_url($av['icondir']).'","'.$tid.'")</script></td>';

      $out .= '<td><input name="onpost'.$tid.'" id="onpost'.$tid.'" type="checkbox" value="1"'.($res->onpost=="1"?'checked="checked"':"").' /></td>'."\n";
      $out .= '<td><input name="oncomment'.$tid.'" id="oncomment'.$tid.'" type="checkbox" value="1"'.($res->oncomment=="1"?'checked="checked"':"").' /></td>'."\n";
      // add position buttons
      if ($count != 0)
	  $out .= '<td><img width="20" src="'.plugins_url().'/wp-monalisa/up.png" onclick="switch_row('.$tid.',\'up\');"/></td>';
      else
	  $out .= "<td>&nbsp;</td>";
      if ( $count != $lastnum )
	  $out .= '<td><img width="20" src="'.plugins_url().'/wp-monalisa/down.png" onclick="switch_row('.$tid.',\'down\');"/></td>'; 
      else
	  $out .= "<td>&nbsp;</td>";
      $out .= "</tr>\n";
      $count ++; // zaehler erhöhen
  } 
  // submit knöpfe ausgeben
  $out .= '<tr><td colspan="8" class="submit"><input type="submit" name="updateicons" value="'.__('Save','wpml').' &raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" name="deletemarked" value="'.__('Delete marked','wpml').' &raquo;" /></td></tr>'."\n";
  $out .= '</table></form></div>'."\n";
  
  echo $out;
}

?>