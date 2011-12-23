<?php

require("assemblies/EditorAssembly.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>	

<title></title>

<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) " />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<link rel="stylesheet" type="text/css" href="css/global.css" media="screen" />
	
<script type="text/javascript" src="../UI/$.js"></script>
<script type="text/javascript" src="../UI/Forms.js"></script>
<script type="text/javascript" src="../UI/XMLHTTP.js"></script>

<script type="text/javascript">

var currentDeleteIndex = false;

window.onload = function()
  {
		$.register('Forms');
		$.register('XMLHTTP');
		$.setAccessKey('hash');
		$.start();	
		  	
		docForm = $.Forms.build('validator',document);
		
		showPermissions(document.getElementById('user_id'));
		updateDeleteRecord(document.getElementById('delete_user_id'));
  }
  
function showPermissions(sel)
  {
    var op = sel.options[sel.options.selectedIndex];
    var id = op.value;
    
    var e = document.getElementById('_edit');
    var c = document.getElementById('_create');
    var d = document.getElementById('_delete');
    var a = document.getElementById('_admin');
    
    e.checked = (permissions[id][0] == '1') ? true : false;
    c.checked = (permissions[id][1] == '1') ? true : false;
    d.checked = (permissions[id][2] == '1') ? true : false;
    a.checked = (permissions[id][3] == '1') ? true : false;
  };	
  
function updateDeleteRecord(sel)
  {
    currentDeleteIndex = sel.options.selectedIndex;
  }  
  
function confirmDelete(frm)
  {
  	var dS = document.getElementById('delete_user_id');
  	
  	/*
  	 * get the full name of user to be deleted
  	 */
  	var fn = dS.options[currentDeleteIndex].firstChild.nodeValue;
  	
  	if(confirm('Are you sure that you want to PERMANENTLY delete the user record for ' + fn + '?'))
  	  {
  	  	frm.parentNode.submit();
  	  }
  }  
  
</script>

</head>

<body>

<?php

$_V->showAdminHeader();

?>

<form id="admin_perm" name="admin_perm" method="post" action="_admin_add_new_user.php">
	
	<div class="form_col">
		<fieldset>
			<legend>Add New Admin User</legend>	
				<br />		
								
				<label for="username">Username:</label><input class="submit_field" type="text" maxlength="32" id="username" name="username" regex="<?php print $_M->metaFieldInfo['username']['regex']; ?>" additionalInfo="<?php print $_M->metaFieldInfo['username']['info']; ?>" value="" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)" /><br clear="left" /><br />
				
				<label for="password">Password:</label><input class="submit_field" type="password" maxlength="32" id="password" name="password" regex="<?php print $_M->metaFieldInfo['password']['regex']; ?>" additionalInfo="<?php print $_M->metaFieldInfo['password']['info']; ?>" value="" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)" /><br clear="left" /><br />		
				
				<label for="full_name">Full Name:</label><input class="submit_field" type="text" maxlength="100" id="full_name" name="full_name" regex="<?php print $_M->metaFieldInfo['full_name']['regex']; ?>" additionalInfo="<?php print $_M->metaFieldInfo['full_name']['info']; ?>" value="" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)" /><br clear="left" /><br />			
				
				<label for="position">Position:</label><input class="submit_field" type="text" maxlength="100" id="position" name="position" regex="<?php print $_M->metaFieldInfo['position']['regex']; ?>" additionalInfo="<?php print $_M->metaFieldInfo['position']['info']; ?>" value="" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)" /><br clear="left" /><br />	
				
				<label for="email">Email:</label><input class="submit_field" type="text" maxlength="255" id="email" name="email" regex="<?php print $_M->metaFieldInfo['email']['regex']; ?>" additionalInfo="<?php print $_M->metaFieldInfo['email']['info']; ?>" value="" onchange="docForm.validateForm(this)" onkeyup="docForm.validateForm(this)" /><br clear="left" /><br />	


		</fieldset>
	</div>
	
	<div class="form_col">
		<fieldset>
			<legend>Set User Permissions</legend>	
				<br />	
	
				<label for="can_create">Allowed to create documents:</label><input class="submit_checkbox" type="checkbox" id="can_create" name="can_create" validate="false" /><br clear="left" /><br />	
				
				<label for="can_delete">Allowed to delete documents</label><input class="submit_checkbox" type="checkbox" id="can_delete" name="can_delete" validate="false" /><br clear="left" /><br />	
				
				<label for="can_edit">Allowed to edit documents</label><input class="submit_checkbox" type="checkbox" id="can_edit" name="can_edit" validate="false" /><br clear="left" /><br />	
				
				<label for="can_admin">Full Permissions</label><input class="submit_checkbox" type="checkbox" id="can_admin" name="can_admin" validate="false" /><br clear="left" /><br />	
					
		</fieldset>
	</div><br clear="all" />
	
	<input class="control_buttons" id="add_new_type" name="add_new_type" type="button" value="Add New User" onclick="return docForm.attemptSubmit(this)" />	
	
</form><br clear="all" />

<form id="change_perm" name="change_perm" method="post" action="_admin_change_permissions.php">
	<fieldset>
		<legend>Change Permissions</legend>	
			<br />		
							
			<?php

      $settings = Array();

			$q = "SELECT * FROM admin_permissions WHERE active = 1 ORDER BY username ASC";
			$r = mysql_query($q);
			
			/*
			 * create a select box which will contain
			 * full_name of all users
			 */
			print '<label for="user_id" style="border: 1px black dotted; background-color: #D7F9C8; padding: 6px; margin-bottom: 10px;">Select user and set permissions:</label><select id="user_id" name="user_id" style="padding: 2px; font-size: 18px; background-color: #D7F9C8; height: 40px; margin-bottom: 10px;" onchange="showPermissions(this)">';
			
			while($t = mysql_fetch_assoc($r))
			  {
			  	$id = $t['id'];
			  	$username = $t['username'];
			  	$full_name = $t['full_name'];
			  	$position = $t['position'];
			  	$can_edit = $t['can_edit'];
			  	$can_create = $t['can_create'];
			  	$can_delete = $t['can_delete'];
			  	$can_admin = $t['can_admin'];
			  	$email = $t['email'];

			  	$selected = "";
			  	if(isset($_SESSION['last_modified_id']) &&  $_SESSION['last_modified_id'] == $id)
			  	  {
			  	    $selected = ' selected="true" ';
			  	  }
			  	
			  	print "<option value=\"$id\" $selected >$full_name</option>";
			  	
			  	/*
			  	 * so, the user will select from a list of names to
			  	 * edit permissions on... how do we indicate what
			  	 * the users *current* settings are? As we aren't 
			  	 * creating an option list for each user, we need to
			  	 * store settings info, to be passed on when a name
			  	 * is selected. W'ell use javascript to do that.  Creates
			  	 * a js array Array[id] = Array(val,val,val,val), which
			  	 * will be called on to set checkboxes onchange of select.
			  	 * For now, we store locally, to be printed later...
			  	 */
			  	$settings[$id] = Array($can_edit,$can_create,$can_delete,$can_admin); 

			  }
			 
			 print '</select><br clear="all" />';
			 
			 /*
			  * now show edit options for users.  note that the order
			  * is important; it should match $settings format, above.
			  */
			  	print '<label for="_edit">edit:</label><input class="submit_checkbox" type="checkbox" id="_edit" name="_edit" validate="false" /><br clear="left" />';
			  	print '<label for="_create">create:</label><input class="submit_checkbox" type="checkbox" id="_create" name="_create" validate="false" /><br clear="left" />';			  	
			  	print '<label for="_delete">delete:</label><input class="submit_checkbox" type="checkbox" id="_delete" name="_delete" validate="false" /><br clear="left" />';	
			  	print '<label for="_admin">admin:</label><input class="submit_checkbox" type="checkbox" id="_admin" name="_admin" validate="false" /><br clear="left" />';	
			  	
			  	print '<br clear="all" /><br />';
			?>
			
	</fieldset>

	<input class="control_buttons" id="update_permissions" name="_admin_update_permissions" type="submit" value="Update Permissions" />	

</form><br />


<form id="delete_user" name="delete_user" method="post" action="_admin_delete_user.php">
	<fieldset>
		<legend>Delete User</legend>	
			<br />	
			
			<?php
			
			$q = "SELECT * FROM admin_permissions WHERE active = 1 ORDER BY username ASC";
			$r = mysql_query($q);
			
			/*
			 * create a select box which will contain
			 * full_name of all users
			 */
			print '<label for="delete_user_id" style="border: 1px black dotted; background-color: #D7F9C8; padding: 6px; margin-bottom: 10px;">Select user record to delete:</label><select id="delete_user_id" name="delete_user_id" style="padding: 2px; font-size: 18px; background-color: #D7F9C8; height: 40px; margin-bottom: 10px;" onchange="updateDeleteRecord(this)">';
			
			while($t = mysql_fetch_assoc($r))
			  {
			  	$id = $t['id'];
			  	$username = $t['username'];
			  	$full_name = $t['full_name'];
			  	$position = $t['position'];
			  	$can_edit = $t['can_edit'];
			  	$can_create = $t['can_create'];
			  	$can_delete = $t['can_delete'];
			  	$can_admin = $t['can_admin'];
			  	$email = $t['email'];
			  	
			  	$selected = "";
			  	if(isset($_SESSION['last_modified_id']) &&  $_SESSION['last_modified_id'] == $id)
			  	  {
			  	    $selected = ' selected="true" ';
			  	  }

			  	
			  	print "<option value=\"$id\" $selected >$full_name</option>";
			  }
			 
			 print '</select><br clear="all" />';
			
			
			/*
			 * now write settings array to javascript...
			 */
			
			print '<script type="text/javascript">';
			print 'var permissions = new Array();';
			
			foreach($settings as $id => $p)
			  {
			  	print "permissions[$id] = new Array(".$p[0].",".$p[1].",".$p[2].",".$p[3].");";
			  }
			
			print '</script>';
			
			?>
	</fieldset>
	<input class="control_buttons" id="delete_user" name="delete_user" type="button" value="Delete User" onclick="confirmDelete(this);" />	
</form><br />

</body>
</html>