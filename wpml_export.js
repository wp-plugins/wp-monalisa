/*
  javascript function for wp-monalisa import dialog
*/
// controls reload of parent page
var importdone=false;

function submit_this(){
    // activate refresh of parent page when leaving
    importdone=true;
    
    // the fields that are to be processed
    var pakfile   = document.getElementById("pakfile").value;
    var pakdelall = document.getElementById("pakdelall").checked;
    
    // ajax call to itself
    jQuery.post("../wp-content/plugins/wp-monalisa/wpml_import.php", {pakfile: pakfile, pak_delall: pakdelall}, function(data){jQuery("#message").html(data);});
    
    return false;
}


