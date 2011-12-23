<?php

require("assemblies/EditorAssembly.php");

$_V->Editor->writePostedEdits(false);

echo '<script type="text/javascript">';
echo "history.go(-1);";
echo '</script>';

?>
