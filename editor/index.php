<?php

require("assemblies/EditorAssembly.php");

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

<style type="text/css">
  
  SPAN
    {
      background-color: #D5EAFF;
    }
    
  LI
    {
      line-height: 24px;
    }
  
</style>

</head>

<body>

<?php

$_V->showAdminHeader();

?>

<div style="padding:20px;">
  
  <fieldset>
    <legend>Drag this link to your bookmarks toolbar to create an editor bookmarklet</legend>  
    
    <ul>
      <li><a href="javascript:(function(){ document.location.href = '<?php echo $_M->publicEditorPath; ?>editor.php?article=' + location.href;})();">Page Editor</a></li>
    </ul>
    
  </fieldset>
  
<?php

/*
 * display any articles currently being edited
 */
$q = "  SELECT  t1.check_out_time, 
                t1.file_path, 
                t2.full_name 
        FROM  checkout AS t1, 
              admin_permissions AS t2 
        WHERE t1.checked_out_by = t2.id 
        AND t1.checked_out_by != 0";

$r = mysql_query($q);

print '<fieldset><legend>The following files have been checked out:</legend>';
print '<ul>';

if(mysql_num_rows($r) > 0)
  {
    while($inf = mysql_fetch_assoc($r))
      {
        $check_out_time   = date("l, F jS, \a\\t g:i A",strtotime($inf['check_out_time']));
        $file_path        = $inf['file_path'];
        $full_name        = ucwords($inf['full_name']);
        
        print "<li>On <span>$check_out_time</span> <span>$full_name</span> checked out <a href=\"$file_path\" target=\"_new\">$file_path</a></li>";
        
      }
  }
  
print '</ul>';
print '</fieldset>';

  
?>

</div>

</body>
</html>