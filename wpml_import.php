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

// include wordpress stuff
require_once(dirname(__FILE__)."/wpml_config.php");
 
// get sql object
global $wpdb;

// table name
$wpml_table = $wpdb->prefix . "monalisa";

// optionen einlesen
$av = unserialize(get_option("wpml-opts"));


if (!empty($_POST)) {

    // check if current smilies should be deleted
    if ($_POST['pak_delall'] == "true")
    {
	$sql = "delete from $wpml_table;";
	$result = $wpdb->query($sql);
	echo __("Smilies deleted.","wpml")."<br />";
    }

    // insert new smilies
    $row = 1;
    $handle = fopen(ABSPATH . "/" . $av['icondir']."/".$_POST['pakfile'], "r");
    while (($data = fgetcsv($handle, 512, ",", "'")) !== FALSE) {
	$num = count($data);
	$row++;
	if ($num==6 or $num==7) {
	    $sql  = "insert into $wpml_table (tid,emoticon,iconfile,onpost,oncomment) values (0,";
	    $sql .= "'".$data[5]."',";
	    $sql .= "'".$data[0]."',";
	    $sql .= ($data[3]=="1"?"1":"0") . "," . 
		($data[3]=="1"?"1":"0") . ");";
	    $result = $wpdb->query($sql);
	    
	    echo __("Smiley $data[0] inserted.","wpml")."<br />";
	} else
	    echo __("Record $row has wrong field count($num). Ignored.","wpml")."<br />";
    }
    fclose($handle);
    
    // you must end here to stop the displaying of the html below
    exit (0);
}

//
// import formular aufbauen ===================================================
//
$out = "";
// add function to submit form data by adrian callaghan
$out .= '<script type="text/javascript"  src="'.site_url('/wp-content/plugins/wp-monalisa').'/wpml_import.js" ></script>';
// add log area style
$out .= "<style>#message {margin:20px; padding:20px; background:#cccccc; color:#cc0000;}</style>";
 
$out .= '<div id="importform" class="wrap" >';
$out .= '<h2>wp-Monalisa '.__('Import',"wpml").'</h2>';
$out .= '<table class="editform" cellspacing="5" cellpadding="5">';
$out .= '<tr>';
$out .= '<th scope="row" valign="top"><label for="pakfile">'.__('Select smiley package','wpml').
    ':</label></th>'."\n";
$out .= '<td><select name="pakfile" id="pakfile">'."\n";

// icon file list on disk
$flist = scandir(ABSPATH . $av['icondir']);
// file loop
foreach($flist as $pfile) 
{
    if (substr($pfile,0,1) != "." 
	and substr($pfile,strlen($pfile)-4,4) == ".pak") 
    {
	$pak_select_html .= "<option value='".$pfile."' ";
	$pak_select_html .= ">".$pfile."</option>\n";
    }
} 
$out .= $pak_select_html . "</select></td>\n";

// import mit oder ohne überschreiben
$out .= '<tr><th scope="row" valign="top"><label for="pakdelall">'.
    __('Delete current smilies before import','wpml').':</label></th>'."\n";
$out .= '<td><input name="pakdelall" id="pakdelall" type="checkbox" value="1" /></td></tr>'."\n";

// add submit button to form
$href= site_url("wp-admin") . "/admin.php?page=wpml_admin.php";
$out .= '<tr><td><p class="submit">';
$out .= '<input type="submit" name="startimport" id="startimport" value="'.
    __('Start import','wpml').' &raquo;" onclick="submit_this()" />';
$out .= '<td><p class="submit">';
$out .= '<input type="submit" name="cancelimport" id="cancelimport" value="'.
    __('Close','wpml').'" onclick="tb_remove();if (importdone) parent.location=\''.$href.'\'" /></p></td>';
$out .= '</p></td></tr>'."\n";
$out .= '</table><hr />'."\n";
// dic ocntainer fuer das verarbeitungs log
$out .= '<div id="message">Import log</div>';
$out .= "</div>\n";

echo $out;
?>
