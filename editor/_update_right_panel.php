<?php

require("assemblies/EditorAssembly.php");

/*
 * make sure we have something in the session...
 */
if(!isset($_SESSION['sidebar']) || (trim($_SESSION['sidebar']) == ""))
  {
    echo "Bad Data. Probably a system error. Hit your back button.";
    exit;  
  }
  
$right_panel = $_SESSION['sidebar'];
        
$right_panel = $_M->Documents->tidyHTML($right_panel);

/*
 * clear the editing session info
 */
unset($_SESSION['sidebar']);  
  
/*
 * ok, write the new right panel
 */
$rp = $_M->templateSidebar;

if(!$_M->Filesystem->write($rp,$right_panel))
  {
    echo "Unable to write the updated sidebar.  Aborting.";
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


?>

<br />
<br />
<blockquote>
  
  The edits you've made to the sidebar have been saved.
  
</blockquote>
  
</body>
</html>