<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("post");
$_M->Request->createGlobals();

/*
 * Do some basic checks, mainly for emptiness
 */
if(!isset($txtContent) || ($txtContent == ""))
  {
    echo "Bad Data.  Probably empty.  Hit your back button and try again.";
    exit;  
  }

/*
 * update session storage of current edit.
 */
$_SESSION['sidebar'] = $txtContent;  

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

function revertFile()
  {
    document.location.href = '_revert_right_panel_edits.php';
  }  

function saveEdits()
  {
    document.location.href = "_update_right_panel.php";  
  }  

</script>

</head>
<body>
  
<?php

$_V->showAdminHeader();

?>  
  
<form name="set_width" id="set_width" onSubmit="return false;">
  <div style="padding:10px;">
    
    If you are satisfied with your edits, click <input type="button" value="SAVE EDITS" onclick="saveEdits()" /><br />
    If are not satisfied, click the back button of your browser, or click <input type="button" value="EDIT" onclick="history.go(-1)" /><br />
    
		If you would like to revert to the version of the sidebar prior to this edit session, click:  <input type="button" onclick="revertFile()" value="REVERT" /><br />
		If you simply leave without reverting, your edits will be stored until you logout. This may or may not be what you want.
		<br />
		<br />
    
  </div>
</form>

<table cellpadding="10" cellspacing="0" border="0">
  <tr>
    <td valign="top">
      
      <iframe id="preview_window" name="preview_window" frameborder="no" border="0" style="border:0px; margin:0px; padding:0px; width:900px; height:600px;" src="_preview_right_panel.php"></iframe>
    
    </td>
  </tr>
</table>


</body>
</html>

