<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("get");
$_M->Request->createGlobals();

if(!isset($abandoned_article_url))
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
 
$_V->Editor->abandonFile($abandoned_article_url);

/*
 * if there was an error, the script will have
 * exited by now. if ok, flow continues...
 */

?>

<br />
<br />
<blockquote>
  
  The edit session for <?php echo $_V->Editor->articleURL; ?><br />
  has been abandoned.
  
</blockquote>
  
</body>
</html>