<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("post");
$_M->Request->createGlobals();

$_M->createSearchResult($query);

/*
 * go to results display
 */
header("Location:search.php");

?>
