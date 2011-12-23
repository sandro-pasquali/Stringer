<?php

require("assemblies/EditorAssembly.php");
 
$_M->Request->validate('get');
$_M->Request->createGlobals();  
 
if($_M->Filesystem->renameFile($file,$new_file))
  {
    /*
     * need to send back the new id of the renamed file
     */
    $nf = str_replace($_M->publicRootPath,"",$new_file);
    $nf = str_replace("/",":",$nf); 
    
    echo "ok^move^$nf";
  }
else
  {
    echo "Move failed.";  
  }
  
?>