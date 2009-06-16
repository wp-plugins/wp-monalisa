/*
  javascript functions for wp-monalisa edit form
*/


/* 
   diese funktion haengt die smilies an den vorhandenen im editor text an

*/
function smile2edit(textid,smile,replace)
{
    var itext;
    var tedit = null;

    // einzufügenden text ermitteln
    if ( replace == 1)
	itext = "<img class='wpml_ico' alt='' src='" + smile + "' />";
    else
	itext = " " + smile + " "; // add space to separate smilies
    
    // editor objekt holen, falls vorhanden
    if ( typeof tinyMCE != "undefined" )
	tedit = tinyMCE.get('content');

    if ( tedit == null || tedit.isHidden() == true)
    {
	// text in html editor einfügen
	tarea = document.getElementById(textid);
    	insert_text(itext, tarea);
    } else if ( (tedit.isHidden() == false) && window.tinyMCE)
    { 
	// füge den text in den tinymce ein
	window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', 
					   false, itext);
    }
}


/* 
   diese funktion fügt den text stxt an der aktuellen position des cursors 
   der textarea obj ein. obj ist als objekt zu übergeben

*/
function smile2comment(textid,smile,replace){
    tarea = document.getElementById(textid);
    if (tarea == null) 
    {
	alert('wp-monalisa: Textarea not found. Please contact the webmaster of this site.');
	return;
    }
    if ( replace == 1)
	insert_text("<img class='wpml_ico' alt='' src='" + smile + "' />", tarea);
    else
	insert_text(" " + smile + " ", tarea); // add space to separate smilies
}

/*
  diese funktion fügt den text stxt an der aktuellen stelle des cursors
  der textarea obj ein. obj ist als objekt zu übergeben
*/
function insert_text(stxt,obj)
{
    if(document.selection)
    {
	obj.focus();
	document.selection.createRange().text=stxt;
	document.selection.createRange().select();
    }
    else if (obj.selectionStart || obj.selectionStart == '0')
    {
	intStart = obj.selectionStart;
	intEnd = obj.selectionEnd;
	obj.value = (obj.value).substring(0, intStart) + stxt + (obj.value).substring(intEnd, obj.value.length);
	obj.selectionStart = obj.selectionEnd = intStart + stxt.length;
	obj.focus();
    }
    else
    {
	obj.value += stxt;
    }
}


