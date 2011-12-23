<?php

require("assemblies/EditorAssembly.php");

$error = false;

$id = $_POST['delete_user_id'];

				
$q = "UPDATE admin_permissions SET active = 0 WHERE id = $id";
$r = mysql_query($q);

if($r)
  {
    /*
     * last modified is now gone; reset
     */
    $_SESSION['last_modified_id'] = "";
    
  	header("Location:admin.php");
  }
else
  { 
		print "Unable to delete user.  Given mysql error:<br /><br />";
		print mysql_errno($_M->DB)."<br />";
		print mysql_error($_M->DB);
		  	
		print '<br /><br /><a href="javascript:document.location.href=\'admin.php\'">click here to go back.</a>';
  }


?>