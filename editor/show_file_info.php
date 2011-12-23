<?php

require("assemblies/EditorAssembly.php");
 
$_M->Request->validate('get');
$_M->Request->createGlobals();
 
if(isset($file))
  {
    /*
     * load the editable document
     */
    @$_V->Editor->document->loadHTMLFile($file);
     
    /*
     * get document meta info (title, keywords, description, etc)
     */
    $minf = $_M->Documents->getMetaInfo($_V->Editor->document);
     
    /*
     * get the editable node of document
     */
    $ec = $_V->Editor->getEditableNode();

    /*
     * get suggested keywords for this document
     */
    $suggested_keywords = $_M->Documents->AutoKeyword->get_keywords($ec,$minf['title']);

    if($minf)
      {
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>	

<title></title>

<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) " />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<link rel="stylesheet" type="text/css" href="css/global.css" media="screen" />

<script type="text/javascript" src="../UI/$.js"></script>
<script type="text/javascript" src="../UI/Forms.js"></script>
<script type="text/javascript" src="../UI/XMLHTTP.js"></script>

<script type="text/javascript">
  
window.onload = function()
  {
		$.register('Forms');
		$.register('XMLHTTP');
		$.setAccessKey('hash');
		$.start();	
		  	
		docForm = $.Forms.build('validator',document);

    /*
     * we want to make sure that the top interface always reflects
     * the current working folder.  Don't change the highlighted 
     * folder until we have fully loaded this page.  As well, should
     * the user click a back button to a previous file list, this
     * will, again, correct the folder highlighting.
     */
    parent.setWorkingFolder(parent.document.getElementById('<?php echo $top_folder_id; ?>'));  
  }  
  
</script>

</head>
<body>

<?php

/*
 * now display info, in editable fields
 */
$title        = $minf['title'];
$keywords     = $minf['keywords'];
$description  = $minf['description'];

?>

<div style="font-size:9px; font-weight:normal; font-family:Verdana,Arial; margin-right:10px; float:right; clear:all;"><?php echo $file; ?></div><br />

<form id="_update_file_info" name="_update_file_info" method="post" action="_update_file_info.php">

<input type="hidden" id="file" name="file" value="<?php echo $file; ?>" /><br />

<label for="page_title">Page Title:</label><textarea class="text_area" id="page_title" name="page_title" class="submit_field" type="text" regex="<?php echo $_M->metaFieldInfo['page_title']['regex']; ?>" additionalInfo="<?php echo $_M->metaFieldInfo['page_title']['info']; ?>" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)"><?php echo $title; ?></textarea><br /><br />

<label for="meta_description">Page Meta Description:</label><textarea class="text_area" id="meta_description" name="meta_description" class="submit_field" type="text" regex="<?php echo $_M->metaFieldInfo['meta_description']['regex']; ?>" additionalInfo="<?php echo $_M->metaFieldInfo['meta_description']['info']; ?>" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)"><?php echo $description; ?></textarea><br /><br />

<label for="meta_keywords">Page Meta Keywords:</label><textarea class="text_area" id="meta_keywords" name="meta_keywords" class="submit_field" type="text" regex="<?php echo $_M->metaFieldInfo['meta_keywords']['regex']; ?>" additionalInfo="<?php echo $_M->metaFieldInfo['meta_keywords']['info']; ?>" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)"><?php echo $keywords; ?></textarea><br /><br />

<fieldset>
  <legend>Keyword Assistant</legend>	
  <div id="keyword_assistant">
    
  <?php
  
  echo $suggested_keywords;
  
  ?>
  
  </div>
</fieldset>

<input class="control_buttons" type="button" onclick="return docForm.attemptSubmit(this)" value="UPDATE" id="update_button" />

</form>
<br />
<br />

</body>
</html>





<?php

      }
  }

?>