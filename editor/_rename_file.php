<?php

require("assemblies/EditorAssembly.php");
 
$_M->Request->validate('get');
$_M->Request->createGlobals();

/*
 * translate spaces to underscores
 */
$new_name = str_replace(" ","_",$new_name);

/*
 * now create full path to new file name
 */
$new_file = substr($file,0,strrpos($file,"/"))."/".$new_name.".html";        
 
if($_M->Filesystem->renameFile($file,$new_file))
  {
    /*
     * need to send back the new id of the renamed file
     */
    $nf = str_replace($_M->publicRootPath,"",$new_file);
    $nf = str_replace("/",":",$nf); 
    
    echo "ok^rename^$nf";
  }
else
  {
    echo "Rename failed.";  
  }
  
?>