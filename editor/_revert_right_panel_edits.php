<?php

require("assemblies/EditorAssembly.php");

/*
 * all we're doing here is clearing the session, then
 * returning to right panel editor, which will load 
 * the version of the file which existed prior to edit session.
 */

unset($_SESSION['sidebar']);  

header("Location:edit_right_panel.php");
  
?>
