<?php

require("Assembly.php");

require("classes/Filesystem.php");
require("classes/Request.php");
require("classes/Editor.php");
require("classes/Documents.php");
require("classes/LinkMap.php");
require("classes/AutoKeyword.php");

$_M->attach('Request');
$_M->attach('Filesystem');
$_M->attach('Documents');
$_M->attach('LinkMap');

$_V->attach('Editor');

$_M->Documents->attach("AutoKeyword");


?>