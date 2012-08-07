<?php
/* This file is part of the wp-monalisa plugin for wordpress */

/*  Copyright 2009 Hans Matzen  (email : webmaster at tuxlog dot de)

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

// this function installs the wp-monalisa database tables and
// sets up default values and options
function wp_monalisa_install()
{
    global $wpdb;
    
    // tabelle pruefen und ggf. anlegen
    $sql = 'SHOW TABLES LIKE \''.$wpdb->prefix.'monalisa\'';
    if($wpdb->get_var($sql) != $wpdb->prefix.'monalisa') 
    {
	// erzeugen der smiley tabelle
	$sql = "create table ".$wpdb->prefix."monalisa 
          (
            tid integer not null auto_increment,
            emoticon varchar(25) NOT NULL,
            iconfile varchar(80) NOT NULL,
            onpost tinyint NOT NULL,
            oncomment tinyint NOT NULL,
            primary key(tid)
          )";
	$results = $wpdb->query($sql);
        	
	$smilies_init = array(
	    ':bye:'      => 'wpml_bye.gif',
	    ':good:'     => 'wpml_good.gif',   
	    ':negative:' => 'wpml_negative.gif',  
	    ':scratch:'  => 'wpml_scratch.gif',  
	    ':wacko:'    => 'wpml_wacko.gif',     
	    ':yahoo:'    => 'wpml_yahoo.gif',
	    'B-)'        => 'wpml_cool.gif',  
	    ':heart:'    => 'wpml_heart.gif',  
	    ':rose:'     => 'wpml_rose.gif',      
	    ':-)'        => 'wpml_smile.gif',    
	    ':whistle:'  => 'wpml_whistle3.gif',  
	    ':yes:'      => 'wpml_yes.gif',
	    ':cry:'      => 'wpml_cry.gif',   
	    ':mail:'     => 'wpml_mail.gif',   
	    ':-('        => 'wpml_sad.gif',       
	    ':unsure:'   => 'wpml_unsure.gif',   
	    ';-)'        => 'wpml_wink.gif');

	// ein paar smilies einfuegen
	$sql1 = "insert into ".$wpdb->prefix."monalisa values ";
	$i=0;

	foreach ($smilies_init as $emo => $ico)
	{
	    $i++;
	    $sql2 = sprintf("( %d,'%s', '%s', 1, 1 );", 
			    $i,
			    mysql_real_escape_string($emo),
			    mysql_real_escape_string($ico));
	    echo $sql1 . $sql2;
	    $results = $wpdb->query($sql1 . $sql2);  
	
	}
    } else
    {
	// tabelle schon vorhanden, hier folgen die updates und alters
	// spaltenbreite der spalte emoticon auf 25 verändern
	$sql = "alter table ".$wpdb->prefix."monalisa modify column emoticon varchar(25) not null;";
	$results = $wpdb->query($sql);

	// spaltenbreite der spalte iconfile auf 80 verändern
	$sql = "alter table ".$wpdb->prefix."monalisa modify column iconfile varchar(80) not null;";
	$results = $wpdb->query($sql);
    }    
    
    // Optionen / Parameter
    
    // gibt es bereits eintraege
    $av = get_option("wpml-opts");
    
    if ($av == "") 
    {
	// verzeichnis fuer die icons
	$av['icondir']     =  PLUGINDIR . "/wp-monalisa/icons";
	// smiliey auswahl im editor anzeigen
	$av['onedit']      = 1;
	// smiley auswahl im commentform anzeigen
	$av['oncomment']   = 1;
	// icons zeigen ( 0= nur text, 1= nur icons, 2=beides )
	$av['showicon']    = 1;
	// text durch img tags ersetzen
	$av['replaceicon'] = 1;
	// kommentarfeld id
	$av['commenttextid'] = 'comment';	
        // smilies als table struktur anzeigen
	$av['showastable'] = 0;
        // smilies pro reihe in der tabelle
	$av['smiliesperrow'] = 15;
	// tooltipp fuer icons anzeigen
	$av['icontooltip'] = 1;
	// smilies fuer buddypress aktivieren
	$av['wpml4buddypress'] = 0;

	add_option( "wpml-opts", serialize($av) );
    }
}

function wp_monalisa_deinstall()
{
    global $wpdb;

    // to prevent misuse :-)
    // wenn die naechste zeile auskommentiert wird, werden
    // bei daktivierung des plugins alle datenbankeintraege von wpml geloescht
    return;
    
    $sql = 'SHOW TABLES LIKE \''.$wpdb->prefix.'monalisa\'';
    if($wpdb->get_var($sql) == $wpdb->prefix.'monalisa') 
    {
	// drop tables
	$sql = "drop table ".$wpdb->prefix."monalisa;";
	$results = $wpdb->query($sql);
    } 
  
  // remove options from wp_options
  delete_option("wpml-opts");
  delete_option("wpml_excludes");
}
?>
