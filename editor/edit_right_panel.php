<?php

require("assemblies/EditorAssembly.php");

/*
 * The user can preview edits. So we need to store current editable 
 * region until it is committed.  We use sessions for this.  So,
 * when this page is loaded, check if the 'sidebar' session var
 * is set; if so, use that. If not, load the current base.sidebar.html
 */

if(isset($_SESSION['sidebar']))
  {
    $right_panel = $_SESSION['sidebar'];  
  }
else
  {
    $right_panel = file_get_contents($_M->templateSidebar);
  }
  
?>

<html>
<head>	
<title>EDITOR +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++</title>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) " />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<link rel="stylesheet" type="text/css" href="css/global.css" media="screen" />

<style type="text/css">

BODY
  {
    overflow: hidden;
  }

</style>

<script language=JavaScript src='../UI/GUI/editor/scripts/innovaeditor.js'></script>
	
<script type="text/javascript">

window.onresize = setEditor;

window.onload = function()
  {	
		setEditor();
  };
  
function setEditor()
  {
  	/*
  	 * resized view requires a reset
  	 */
		oEdit1.openStyleSelect();
  }  
  
function revertFile()
  {
    document.location.href = '_revert_right_panel_edits.php';
  }  

</script>	
	
</head>
<body>

<?php

$_V->showAdminHeader();

?>

<form name="Form1" id="Form1" method="post" action="preview_right_panel.php">

  <table cellpadding="0" cellspacing="0" border="0" height="98%" width="100%">
    <tr>
      <td width="20" style="background-color:silver">
        &nbsp;
      </td>
      <td style="width:440px; padding:0px 10px 10px 17px;">
				<textarea id="txtContent" name="txtContent" rows="1" cols="1" validate="false">
				
				<?php
            
        echo $right_panel;
				
				?>  
				  
				</textarea>
				
				<script type="text/javascript">
					
				var oEdit1 = new InnovaEditor("oEdit1");
				
				oEdit1.width='100%';
				oEdit1.height='100%';
		
				oEdit1.features=["XHTMLSource","|","Cut","Copy","Undo","Redo","|",				"Hyperlink","Image","Bold","Italic","Underline","Numbering","Bullets","|","ForeColor","BackColor","StyleAndFormatting","TextFormatting","ListFormatting",
					"BoxFormatting","ParagraphFormatting","CssText","Styles"];
		
				oEdit1.cmdAssetManager = "modalDialogShow('<?php print $_M->assetManagerPath; ?>',700,500)"; 
				oEdit1.css = "<?php print $_M->editorCSS; ?>";
				
		    oEdit1.mode="XHTMLBody";
		    //oEdit1.useTagSelector=false;
		    //oEdit1.initialRefresh=true;
				oEdit1.REPLACE("txtContent");
				oEdit1.focus();
				
				</script>
		  </td>
		  <td valign="top">
		    
		    <div style="padding:30px; width:90%;">

		      
		      When you are done with your edits, click <input type="submit" value="PREVIEW" />
		      <br />
		      
		      If you would like to revert to the version of the sidebar prior to this edit session, click:  <input type="button" onclick="revertFile()" value="REVERT" /><br />
		      If you simply leave without reverting, your edits will be stored until you logout. This may or may not be what you want.
		      <br />
		      <br />

		    </div>
		    
		  </td>
	  </tr>	
		<tr>
		  
		  <!-- keep some space below editor -->
			<td height="30" colspan="2"></td>
			
		</tr>
  </table>

</form>

</body>
</html>