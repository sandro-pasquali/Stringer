<?php

require("assemblies/EditorAssembly.php");

$_V->Editor->writePostedEdits();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>	

<title></title>

<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) " />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<link rel="stylesheet" type="text/css" href="css/global.css" media="screen" />

<style type="text/css">
  
  BODY
    {
    }
  
</style>

<script type="text/javascript">

/*
 * this serves as the initial width as well
 * as the current width, should that be changed
 */
var preview_width   = 900;

var min_width       = 640;
var max_width       = 1280;

function changeDisplayWidth(el)
  {
    preview_width = parseInt(el.value);
   
    /*
     * check if out of bounds
     */
    if(preview_width < min_width)
      {
        preview_width = min_width;  
      }
    else if(preview_width > max_width)
      {
        preview_width = max_width;  
      }
      
    updatePreviewSize();
  }
  
function updatePreviewSize()
  {
    var pr = document.getElementById('preview_window');
    pr.style.width = preview_width.toString() + 'px';
    
    /*
     * update the size input value (we do this as the user may
     * have entered incorrect values, which have been corrected
     * at this point, and should be displayed)
     */
    document.getElementById('current_width').value = preview_width;
  }
  
function saveEdits()
  {
    document.location.href = "_save_edits.php?edited_article_url=<?php echo $_V->Editor->articleURL; ?>";  
  }  
  
function abandonFile()
  {
    document.location.href = '_abandon_file.php?abandoned_article_url=<?php print $_V->Editor->articleURL; ?>';
  }  
  
window.onload = function()
  {
    updatePreviewSize();
  }

</script>

</head>
<body>
  
<form name="set_width" id="set_width" onSubmit="return false;">
  <div style="padding:10px;">
    
    If you are satisfied with your edits, click <input type="button" value="SAVE EDITS" onclick="saveEdits()" /><br />
    If are not satisfied, click the back button of your browser, or click <input type="button" value="EDIT" onclick="history.go(-1)" /><br />
    If you would like to abandon this editing session (losing all edits, and allowing others to edit), click <input type="button" onclick="abandonFile()" value="ABANDON" /><br /><br />
    
    This is how the edited document would look at in a standard browser window at width of <input id="current_width" type="text" value="0" style="width:40px;" onchange="changeDisplayWidth(this)" /> pixels wide:
  </div>
</form>

<table cellpadding="10" cellspacing="0" border="0">
  <tr>
    <td valign="top">
      
      <iframe id="preview_window" name="preview_window" frameborder="no" border="0" style="border:0px; margin:0px; padding:0px; width:800px; height:600px;" src="<?php echo $_V->Editor->checkedArticleURL; ?>"></iframe>
    
    </td>
  </tr>
</table>


</body>
</html>

