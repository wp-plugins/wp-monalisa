<?php
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

// enter the plugin name
$plugin_name = "wp-monalisa";
// enter the textdoamin for translation
$supp_td="wpml";
// enter the support email address
$supp_email = "support@tuxlog.de";
// array of wordpress option names to send with the request
$opts = array("wpml-opts","wpml_excludes");

// include wordpress stuff
require_once(dirname(dirname(__FILE__))."/wpml_config.php");
require_once(ABSPATH . "wp-admin/includes/plugin.php");
require_once(ABSPATH . "wp-admin/includes/plugin-install.php");

// url to jump back to
$href= site_url("wp-admin") . "/admin.php?page=wpml_admin.php";

// lets send the support mail
if (!empty($_POST)) {
	
	if ($_POST['adddata']== "true") {
		// 	get active plugins info
		$p=get_option('active_plugins');
		$activeplugins="";
		foreach ($p as $i) {
			$slug = substr(basename($i),0,strlen(basename($i))-4);
			$slug1 = substr($i,0, strpos($i,"/"));
		
			$a=plugins_api('plugin_information',array(slug => $slug));

			if ( !isset($a->slug) ) 
				$a=plugins_api('plugin_information',array(slug => $slug1));

			if ( !isset($a->slug) ) {
				$activeplugins .= $slug . ";no info\n";	
			} else {
				$activeplugins .= $a->download_link . "\n";
			}
		}
		$mesg =  $_POST ['request'] . "\n\n" . $_POST ['general'] . "\n\n" . 
				 $activeplugins . "\n\n" .$_POST['pluginsettings'] . "\n\n" ;
	} else {
	 	$mesg =  $_POST ['request'] . "\n\n";
	}
	
	$headers = 'From: ' . $_POST['email'] . "\r\n";
	$res=wp_mail( $supp_email, $plugin_name . " - " . __("Support Request",$supp_td), $mesg, $headers);
	
	if ( $res == true)
		_e("Thanks for your request, you will get feedback as soon as possible.",$supp_td);
	else
		_e("Sorry, there was a problem sending your mail. Please send the contents of this fram to support@tuxlog.de manually.",$supp_td);
    // you must end here to stop the displaying of the html below
    exit (0);
}
?>
<script type="text/javascript">
/*
  javascript function for support dialog
*/
function submit_this(pname){
    // the fields that are to be processed
    var email          = document.getElementById("email").value;
    var request        = document.getElementById("request").value;
    var general        = document.getElementById("general").value;
    var waitmessage    = document.getElementById("waitmessage").value;
    var pluginsettings = document.getElementById("pluginsettings").value; 
    var adddata        = document.getElementById("adddata").checked;
    
    jQuery("#message").html(waitmessage);

    // ajax call to itself
    jQuery.post("../wp-content/plugins/"+pname+"/support/support.php", {
    		email:email,
    		request: request,
    		general: general, 
    		pluginsettings: pluginsettings,
    		adddata:adddata
    		}, function(data){jQuery("#message").html(data);});
    
    return false;
}

function switch_data() {
	var dblock = document.getElementById("datacontainer");
	var sw = document.getElementById("adddata");
	
	if (sw.checked == true)
		dblock.style.color = "#000000";
	else
		dblock.style.color = "#999999";
}
</script>
<style>#message {margin:10px; padding:10px; background:#cccccc; color:#000000;}</style>
<div id="support" class="wrap" >
<h2><?php echo $plugin_name . " "; _e('Support Request',$supp_td);?></h2>

<?php _e("Please notice, the following data will be send via email to analyze your support request.",$supp_td);?> <br/>
<?php _e("You can contact the author of this plugin at",$supp_td); echo " " . $supp_email;?> <br/>
<?php _e("Please enter your problem description in the textarea and fill in a valid email address.",$supp_td);?><br/>
<br /><br />
<table class="editform" cellspacing="5" cellpadding="5">
<tr>
<td><label for="request"><?php _e("Problemdescription",$supp_td);?>:</label></td>
<td><textarea id="request" class="large-text" ></textarea></td>
</tr>
<tr>
<td><label for ="email"><?php _e("Enter your email address",$supp_td);?>:</label></td>
<td><input type="text" id="email" class="regular-text" value="<?php echo get_option('admin_email');?>"/></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" value="<?php _e("Send request",$supp_td);?>" id="submit" class="button-primary" onclick="submit_this('<?php echo $plugin_name;?>')"/>&nbsp;&nbsp;&nbsp;
<input type="submit" value="<?php _e("Close window",$supp_td);?>" id="submit" class="button-secondary" onclick="tb_remove();"/></td>
</table>
<div id="message"><?php _e("Messages...",$supp_td);?></div>
<br />
<input type="checkbox" id="adddata" value="1" checked="checked" onclick="switch_data();"/>
<label for="adddata"><?php _e("In addition the following data is transmitted to analyze your request",$supp_td);?>:</label>
<br />
<div id="datacontainer">
<h3><?php _e("General Settings",$supp_td)?></h3>
<?php _e("Your wordpress version",$supp_td); echo ": $wp_version <br/>";
	  _e("Your wordpress language",$supp_td); echo ": ".WPLANG." <br/>";
      _e("Your local package",$supp_td); echo ": $wp_local_package <br/>";
      _e("Your timezone setting",$supp_td); echo ": ".get_option('gmt_offset') ."<br/>";
      _e("Active theme",$supp_td); echo ": " . get_current_theme() . "<br />";
  	  $general = "WP Version:".$wp_version."/"."Language:".WPLANG."/"."Local:".$wp_local_package."/"."Timezone:".get_option('gmt_offset')."/"."Theme:".get_current_theme();
?>
<input type="hidden" id="general" value="<?php echo $general;?>" />
<input type="hidden" id="waitmessage" value="<?php _e("Fetching the plugin information from wordpress.org...this may take a while",$supp_td);?>" />

<h3><?php _e("Active Plugins",$supp_td)?></h3>
<ul>
<?php 
$p=get_option('active_plugins');
foreach ($p as $i) {
	$slug = substr(basename($i),0,strlen(basename($i))-4);	
	echo "<li>" . $slug . "</li>";
}
?>
</ul>


<h3><?php _e("Plugin Settings",$supp_td)?></h3>
<ul>
<?php 
$pluginsettings="";
foreach ($opts as $i) {
	$val = get_option($i);
	echo "<li>". $i . ": " . urlencode($val) . "</li>";
	$pluginsettings .= $i . ":" . urlencode($val) .";";
}
?>
</ul>
<input type="hidden" id="pluginsettings" value="<?php echo $pluginsettings;?>" />
</div>
</div>