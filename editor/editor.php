<?php

require("assemblies/EditorAssembly.php");


if(!isset($_GET['article']) || ($_GET['article'] == ""))
  {
    print "bad article info.";
    exit;  
  }

/*
 * to ensure we don't have overlaps, overwrites, etc. of
 * these session values, we destroy the session for 
 * stored nodes and allow them to be recreated later.
 */
unset($_SESSION['stored_nodes']);

/*
 * this is a full http address, ie. http://www.site.com/articles/a/a.html
 */
$article = $_GET['article'];

/*
 * it is possible that an edit may be requested from index.php
 * load (which happens when user simply types in site url).  Check
 * to see if the last character of $article is a slash ( / ), and
 * if so, assume this means that the user wants /files/index.html
 */
if(substr($article,-1) == '/')
  {
    $article = $_M->publicFilesPath."index.html";
  }

/*
 * sometimes we'll be editing the root document (index.html),
 * but the url will just be root dir with end slash (site.com/),
 * or simple a url (site.com).
 *
 * If the last 5 characters aren't `.html`, append a `index.html`.
 * As we should only be editing .html documents, we can assume
 * that a trailing slash must be asking for an index.html file.
 */
if(substr($article, -5) != '.html')
  {
    /* 
     * end slash? add if not and write index.html
     */
    if(substr($article, -1) == '/')
      {
        $article .= 'index.html';  
      }
    else
      {
        $article .= '/index.html';  
      }
  }

/*
 * user is now editing a file.  as such, we need to lock
 * it, to avoid any further editing.  As well, the user may be
 * editing a file, previewieng, editing, etc, and has now 
 * returned to the editor.  So the first thing to do is to check
 * if there is a pending file, and that file will be the same
 * filename as the current file with the addition of a `.chk` extension,
 * in the same folder as current file.
 */
 
$_V->Editor->checkOutFile($article);

$_V->Editor->loadEditableContent();
$_V->Editor->storeNodes();

$full_url             = $_V->Editor->articleURL;
$link_display_name    = substr(strrchr($full_url,'/'),1); 

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
  
function abandonFile()
  {
    document.location.href = '_abandon_file.php?abandoned_article_url=<?php print $_V->Editor->articleURL; ?>';
  }  
  
function editSessionRevert()
  {
    document.location.href = '_edit_session_revert.php?reverted_article_url=<?php print $_V->Editor->articleURL; ?>';
  }  
  
function normalizeFile()
  {
    var frm = document.getElementById('Form1');
    frm.setAttribute('action','_normalize_file.php');
    frm.onsubmit(); 
    frm.submit();
  }
  
function setEditor()
  {
  	/*
  	 * resized view requires a reset
  	 */
	  oEdit1.openStyleSelect();
  }  

</script>	
	
</head>
<body>

<?php

$_V->showAdminHeader();

?>

<form name="Form1" id="Form1" method="post" action="preview_edits.php">
  

<?php

print '<input type="hidden" name="article" value="'.$_V->Editor->articleURL.'" />';

?>


  <table cellpadding="0" cellspacing="0" border="0" height="98%" width="100%">
    <tr>
      <td style="width:640px; padding:0px 10px 10px 10px;">
				<textarea id="txtContent" name="txtContent" rows="1" cols="1" validate="false">
				
				<?php
            
        print $_V->Editor->getEditableContentAsString();
				
				?>  
				  
				</textarea>
				
				<script type="text/javascript">
					
				var oEdit1 = new InnovaEditor("oEdit1");
				
				oEdit1.width='100%';
				oEdit1.height='100%';
		
				oEdit1.features=["FullScreen","Print","Search",
					"XHTMLSource","|","Cut","Copy","PasteWord","Undo","Redo","|",
					"Hyperlink","Image","Table","|",
					"JustifyLeft","JustifyCenter","JustifyRight","JustifyFull",
					"Numbering","Bullets","Indent","Outdent","BRK",
					"StyleAndFormatting","TextFormatting","ListFormatting",
					"BoxFormatting","ParagraphFormatting","CssText","Styles","ForeColor","BackColor",
					"Bold","Italic","Underline","Superscript","Subscript","Characters"];
		
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
		    <div style="width:300px; padding-top:30px;">
		      
		      Now Editing:
		      <br />
		      <a href="javascript:void(window.open('<?php print $full_url; ?>','','width=600,height=400,resizable,scrollbars,top=10,left=10'))"><?php print $link_display_name; ?></a>
		      <br />
		      <br />
		      
		      If you have pasted content in from elsewhere, such as a Word file, it is recommended that you click <input type="button" onclick="normalizeFile()" value="NORMALIZE DOCUMENT ENCODING" />
		      
		      <br />
		      <br />
		      
		      When you are done with your edits, click <input type="submit" value="PREVIEW" />
		      <br />
		      <br />
		      
		      This file has now been checked out by you, which means that nobody else can edit it until you check it back in.  If you would like to abandon this editing session (losing all edits, and allowing others to edit), click  <input type="button" onclick="abandonFile()" value="ABANDON" />
		      <br />
		      <br />
		      
		      <?php
		      
		      /*
		       * is there an .archive file for this page? check, and allow revert if so
		       */
		      if(file_exists($_V->Editor->archivePath))
		        {
		          
		          echo 'If you would like to revert to the current version of this file (what the file looked like before you began this editing session), losing any changes you have made, click <input type="button" onclick="editSessionRevert()" value="REVERT" />';
		           
		        }
		      
		      ?>

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