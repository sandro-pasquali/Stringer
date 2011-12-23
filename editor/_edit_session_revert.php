<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("get");
$_M->Request->createGlobals();

if(!isset($reverted_article_url))
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
 * reverting an editing session simply means abandoning
 * the current session, and reloading the original
 * file in the editor...
 */
$_V->Editor->abandonFile($reverted_article_url);

/*
 * if there was an error, the script will have
 * exited by now. if ok, flow continues...
 */
 
/*
 * now we simply load the original file into editor.
 */ 

?>

<script type="text/javascript">
  
  document.location.href = 'editor.php?article=<?php print $_V->Editor->articleURL; ?>'; 
  
</script>

  
</body>
</html>