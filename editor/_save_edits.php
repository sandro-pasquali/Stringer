<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("get");
$_M->Request->createGlobals();

if(!isset($edited_article_url))
  {
    print "bad data.";
    exit;
  }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>	

<title></title>

<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) " />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<link rel="stylesheet" type="text/css" href="css/global.css" media="screen" />

</head>
<body>
  
<?php

$_V->showAdminHeader();

/*
 * to ensure we don't have overlaps, overwrites, etc. of
 * these session values, we destroy the session for 
 * stored nodes and allow them to be recreated later.
 */
unset($_SESSION['stored_nodes']);

$_V->Editor->checkInFile($edited_article_url);

/*
 * if there was an error, the script will have
 * exited by now. if ok, flow continues...
 */

?>

<br />
<br />
<blockquote>
  
  The file <a href="<?php echo $_V->Editor->articleURL; ?>" target="_new"><?php echo $_V->Editor->articleURL; ?></a><br />
  has been checked in, and your edits have been saved.
  
</blockquote>
  
</body>
</html>