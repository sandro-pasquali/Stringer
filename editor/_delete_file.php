<?php

require("assemblies/EditorAssembly.php");
 
$_M->Request->validate('get');
$_M->Request->createGlobals();
 
if($_M->Documents->delete($file))
  {
    echo "ok^delete";
  }
else
  {
    echo "Deletion failed";  
  }
  
?>