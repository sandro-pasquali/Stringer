<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate('get');
$_M->Request->createGlobals();

/*
 * we may be sent a file to select by default
 */
if(isset($selected_file))
  {
    $selected_file_id = str_replace("/",":",$selected_file);  
  }

/*
 * see js window.onload function below
 */
$top_folder_id = str_replace("/",":",$folder);

/*
 * $folder will only be relative to public html dir 
 * (ie. files/articles/subfolder/ )
 */
$path = $_M->localRootPath.$folder;

print '<pre style="line-height:18px;">';

$_V->printFilesystemTree($_M->Filesystem->getFileList($path),true);  
 
print '</pre>';

?>
