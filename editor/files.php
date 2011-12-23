<?php

require("assemblies/EditorAssembly.php");

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
<script type="text/javascript" src="../UI/XMLHTTP.js"></script>
<script type="text/javascript" src="../UI/DOM.js"></script>
<script type="text/javascript" src="../UI/FileView.js"></script>
<script type="text/javascript" src="../UI/DragDrop.js"></script>
<script type="text/javascript" src="../UI/Behaviour.php"></script>

<script type="text/javascript">
  
window.onload = function()
  {
		$.register('XMLHTTP');
		$.register('DOM');
		$.register('FileView',['<?php echo $_M->publicRootPath; ?>',<?php echo $_M->metaFieldInfo['article_name']['regex']; ?>,'<?php echo $_M->metaFieldInfo['article_name']['info']; ?>','<?php echo $_M->rootFolderName; ?>',<?php echo $_M->metaFieldInfo['folder_name']['regex']; ?>,'<?php echo $_M->metaFieldInfo['folder_name']['info']; ?>']);
		$.register('DragDrop',[$.FileView.handleDrop]);
		$.setAccessKey('hash');
		$.start();			
		
    window.onresize = $.FileView.updateFileBrowser;
		
    /*
     * load folders
     */
    $.FileView.loadFolders();

		/*
		 * initialize file browser
		 */
		$.FileView.updateFileBrowser();

    /*
     * for IE...
     */
    try
      {
        document.onselectstart = function() { return false; }
      }
    catch(e){}
  } 
  
</script>

</head>

<body>

<?php

$_V->showAdminHeader();

?>

<div style="font-weight:bold;">
<div style="float:left; padding-top:16px; margin-left:10px;">
<pre id="folder_container" style="line-height:16px;">
</pre>
</div>

<div id="file_browser_container">

<div id="fb_control">
<div id="fb_directory_container">

<div id="fb_directory"></div>

</div>

<div id="fb_options">
  
<div class="action_icon" rel="icon" alt="button_create_file" onclick="$.FileView.createFile()"></div>
<div class="action_icon" rel="icon" alt="button_edit_file" onclick="$.FileView.editFile()"></div>
<div class="action_icon" rel="icon" alt="button_view_file" onclick="$.FileView.viewFile()"></div>
<div class="action_icon" rel="icon" alt="button_rename_file" onclick="$.FileView.renameFile()"></div>
<div class="action_icon" rel="icon" alt="button_delete_file" onclick="$.FileView.deleteFile()"></div>
<div class="action_icon" rel="icon" alt="button_create_folder" onclick="$.FileView.folderOperation('add')"></div>
<div class="action_icon" rel="icon" alt="button_rename_folder" onclick="$.FileView.folderOperation('rename')"></div>
<div class="action_icon" rel="icon" alt="button_delete_folder" onclick="$.FileView.deleteFolder()"></div>

</div>
</div>

<div id="file_browser" name="file_browser"></div>

</div>

</div>

</body>
</html>