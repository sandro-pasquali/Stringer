<?php

require("assemblies/EditorAssembly.php");
 
$_M->Request->validate('get');
$_M->Request->createGlobals();
 
if(isset($folder))
  {
    /*
     * translate into filesystem path
     */
    $folder = str_replace(":","/",$folder);
    $folder = $_M->localRootPath.$folder;

    if($_M->Filesystem->deleteFolder($folder))
      {
        echo 'ok^deleteF'; 
      }
    else
      {
        echo 'Error when deleting folders. Check your files.';
      }

  }
  
?>