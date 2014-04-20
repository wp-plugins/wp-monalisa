<?php
// Kleine Routine zu Ermittlung wo wor wp-load.php finden
// Wird wp-load.php nicht gefunden, dann wird ein Fehler ausgegeben.
//
 
// Wenn man das Verzeichnis wp-content ausserhalb der normalen Verzeichnissstruktur angelegt hat
// dann muss man die Variable wppath auf diesen Pfad einstellen 
$wppath  = "";     

// Prüfen ob der load path schon definiert ist
if ( !defined('WP_LOAD_PATH') ) {
	// hier ligt wp-load.php, bei der Standardinstallation
	$std_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	
	if (file_exists($std_path . 'wp-load.php')) {
		require_once($std_path."wp-load.php");
	} else if (file_exists($wppath . 'wp-load.php')) {
		require_once( $wppath . "/" . "wp-load.php");
	} else {
		exit("wp-load.php not found. Please set path in wpml_config.php");
	}
}
?>