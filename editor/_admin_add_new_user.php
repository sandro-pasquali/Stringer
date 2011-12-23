<?php

require("assemblies/EditorAssembly.php");

/*
 * validate subitted data
 */
$_M->Request->validate("post");

/*
 * check permissions
 */
$_edit    = 0;
$_create  = 0;
$_delete  = 0;
$_admin   = 0;

foreach($_M->Request->post as $k => $v)
  {
  	switch($k)
  	  {				
				case 'can_edit':
				  $_edit    = ($v == 'on') ? 1 : 0;
				break;
				
				case 'can_create':
				  $_create  = ($v == 'on') ? 1 : 0;
				break;
				
				case 'can_delete':
				  $_delete  = ($v == 'on') ? 1 : 0;
				break;
				
				case 'can_admin':
				  $_admin   = ($v == 'on') ? 1 : 0;
				break;
  	  }
  }

/*
 * ok; try insertion
 */
$_M->Request->DBPrepare();
$_M->Request->createGlobals();

$q = "SELECT id FROM admin_permissions WHERE username = $username";
$r = mysql_query($q);

if($r && (mysql_num_rows($r) > 0))
  {
    print 'That username is taken.<br /><br />';
    print '<a href="javascript:history.go(-1)">click here to go back.</a>';
		exit;  	
  }
 
$q = "INSERT INTO admin_permissions (username,password,full_name,position,last_login,can_edit,can_create,can_delete,can_admin,email) values ($username,$password,$full_name,$position,NOW(),$_edit,$_create,$_delete,$_admin,$email)";
$r = mysql_query($q);

if($r)
  {
    $_SESSION['last_modified_id'] = mysql_insert_id();
  	header("Location:admin.php");
  }
else
  { 
		print "User account creation failed.  Given mysql error:<br /><br />";
		print mysql_errno($_M->DB)."<br />";
		print mysql_error($_M->DB);
		  	
		print '<br /><br /><a href="javascript:document.location.href=\'admin.php\'">click here to go back.</a>';
  }

?>