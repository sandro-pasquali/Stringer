<?php

require("assemblies/EditorAssembly.php");

$error = false;

$id = $_POST['user_id'];
$can_edit = isset($_POST['_edit']) ? (($_POST['_edit'] == 'on') ? 1 : 0) : 0;
$can_create = isset($_POST['_create']) ? (($_POST['_create'] == 'on') ? 1 : 0) : 0;
$can_delete = isset($_POST['_delete']) ? (($_POST['_delete'] == 'on') ? 1 : 0) : 0;
$can_admin = isset($_POST['_admin']) ? (($_POST['_admin'] == 'on') ? 1 : 0) : 0;

				
$q = "UPDATE admin_permissions set can_edit = $can_edit, can_create = $can_create, can_delete = $can_delete, can_admin = $can_admin WHERE id = $id";
$r = mysql_query($q);

if($r)
  {
    /*
     * store last modified id
     */
    $_SESSION['last_modified_id'] = $id;
  	header("Location:admin.php");
  }
else
  { 
		print "Unable to change permissions.  Given mysql error:<br /><br />";
		print mysql_errno($_M->DB)."<br />";
		print mysql_error($_M->DB);
		  	
		print '<br /><br /><a href="javascript:document.location.href=\'admin.php\'">click here to go back.</a>';
  }


?>