<?php

require("assemblies/EditorAssembly.php");

$path = substr($_M->localFilesPath,0,-1);
$_V->printFilesystemTree($_M->Filesystem->getRecursiveFolderList($path),true);  

?>