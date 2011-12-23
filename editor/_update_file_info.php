<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate('post');
$_M->Request->createGlobals();

/*
 * need to convert to local root path
 */
$local_file = str_replace($_M->publicRootPath,$_M->localRootPath,$file);

$title        = $page_title;
$keywords     = $meta_keywords;
$description  = $meta_description;

if($local_file && $page_title && $meta_description && $meta_keywords)
  {
    if($_M->Documents->update($local_file,$page_title,$meta_description,$meta_keywords))
      {
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
  
<br />
<br />
<blockquote>
  
  You have successfully updated the file: <?php echo $file; ?>
  <br /><br />
  
</blockquote>
  
</body>
</html>


<?php
 
      }
  }

?>