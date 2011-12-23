<?php

$this->DB_LOCATION                = "localhost";
$this->DB_USERNAME                = "root";
$this->DB_PASSWORD                = "rootpass";
$this->DB_NAME                    = "THENAME";

$this->WEBMASTER_EMAIL            = 'support@mymail.com';

$this->relativeRootPath           = '/THENAME/';
$this->localRootPath              = '/usr/local/www/THENAME/';
$this->publicRootPath             = 'http://www.mysite.com/THENAME/';

$this->relativeEditorPath         = $this->relativeRootPath.'editor/';
$this->localEditorPath            = $this->localRootPath.'editor/';
$this->publicEditorPath           = $this->publicRootPath.'editor/';

$this->relativeAssetPath          = $this->relativeRootPath.'images/';
$this->localAssetPath             = $this->localRootPath.'images/';

$this->iconPath                   = $this->publicEditorPath.'images/icons/';

$this->templatePath               = $this->localEditorPath.'templates/';
$this->defaultTemplate            = $this->templatePath.'base.html';
$this->templateSidebar            = $this->templatePath.'base.sidebar.html';

$this->rootFolderName             = 'files';
$this->publicFilesPath            = $this->publicRootPath.$this->rootFolderName.'/';
$this->localFilesPath             = $this->localRootPath.$this->rootFolderName.'/';

$this->globalStyleSheet           = $this->publicRootPath.'css/global.css';
  
/*
 * set to tags (<tag>) that should be removed
 * from any editable content region
 */  
$this->tagsExcludedFromEditable   = Array
  (
    'script',
    'noscript'
  );
  
/*
 * maximum number of results to return
 */
$this->maxSearchResults           = 100;
  
/*
 * maximum number of results to show per page of search
 */
$this->maxSearchResultsPerPage    = 10;

/*
 * this file should contain or import all the css that would
 * be used by the article we are loading
 */
$this->editorCSS = $this->publicRootPath.'css/global.css';

/*
 * regexes, etc. for form entries
 */

$this->metaFieldInfo = Array
  (
    "username" =>  Array
      (
        "regex"   =>  "/^[A-Za-z_\w]{6,32}$/",
        "info"    =>  "Username must be between 6 and 32 characters long, letters, numbers, or underscore(_) only."
      ),
      
    "password" =>  Array
      (
        "regex"   =>  "/^[A-Za-z_\w]{6,32}$/",
        "info"    =>  "Password must be between 6 and 32 characters long, letters, numbers, or underscore(_) only."
      ),
      
    "full_name" =>  Array
      (
        "regex"   =>  "/^[\.\'\- \da-zA-Z]{2,100}$/",
        "info"    =>  "Full name must be between 2 and 100 characters long, containing letters, numbers, period(.), apostrophe('), dash(-), space( ) only."
      ),
      
    "position" =>  Array
      (
        "regex"   =>  "/^[\.\'\- \da-zA-Z]{2,100}$/",
        "info"    =>  "Position info must be between 2 and 100 characters long, containing letters, numbers, period(.), apostrophe('), dash(-), space( ) only."
      ),
      
    "email" =>  Array
      (
        "regex"   =>  "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/",
        "info"    =>  "Please enter a valid email address."
      ),
      
    "folder_name" =>  Array
      (
        "regex"   =>  "/^[A-Za-z0-9]{1}[A-Za-z0-9 ]{0,100}$/",
        "info"    =>  "A folder name contains only letters, numbers, and spaces, and can begin only with a letter or number."
      ),
      
    "article_name" =>  Array
      (
        "regex"   =>  "/^[A-Za-z0-9]{1}[A-Za-z0-9 -]{0,512}$/",
        "info"    =>  "File names can contain only letters, numbers, dashes(-) and spaces, and can begin only with a letter or number. It is NOT necessary to add an extension, such as .html"
      ),

    "page_title" =>  Array
      (
        "regex"   =>  "/^[- .,'?:&\d\w]{1,255}$/",
        "info"    =>  "A title for this page is required.  Enter a page title, 255 character maximum. use only A-Z, numbers, spaces, dash(-) period(.), comma(,), ampersand(&), questionmark(?), colon(:) or apostrophe(')."
      ),

    "meta_description" =>  Array
      (
        "regex"   =>  "/^[- .,\!'\d\w]{1,512}$/",
        "info"    =>  "Description is required.  Enter a description, 512 character maximum. use only A-Z, numbers, spaces, dash(-) period(.), exclamation(!), comma(,) or apostrophe(')."
      ),
      
    "meta_keywords" =>  Array
      (
        "regex"   =>  "/^[- .'\da-zA-Z]{1,512}$/",
        "info"    =>  "Keywords are required for searching.  Enter keywords separated by spaces, 512 character maximum. No commas are allowed; use only A-Z, numbers, spaces or dash(-) period(.) or apostrophe(')."
      )
      
  );
  
  
/*
 * STYLES
 */
 
$this->iconForFolder   = 'icon_folder_blue.png';
$this->iconForHtml     = 'icon_html_blue.png';


/*
 * Authentication
 */

$this->USER_ADMIN_ID			        =	0;
$this->USER_POSITION			        =	'';
$this->USER_FULL_NAME			        =	'';

/*
 * this will be set to the access level for the current
 * page -- see $this->FILE_PERMISSIONS, argument 0
 */
$this->PAGE_ACCESS_LEVEL	        =	0;

 
/*
 * FILE PERMISSIONS
 * 
 * path	=> (admin	level, header	info,	page title);
 *
 * admin level :  To Allow:                         Set To:
 *                ---------                         ------- 
 *                Full admin                          9
 *                (create, edit, delete, admin)
 *                Create                              5
 *                Delete                              4
 *                Edit                                3
 *                Open
 *                (no credentials necessary)          0
 *                
 *
 * header	info : see View->showAdminHeader()	-	0	-	no header	
 *																						-	1	-	show header	w/ button	linked to	this page
 *																						-	2	-	show header, but NO	button link	to current page
 *
 * see View->showAdminHeader()
 */
$this->AUTH_LEVEL = Array
  (
    9 => false,
    5 => false,
    4 => false,
    3 => false,
    0 => true
  );

$this->AUTH_FAILURE_NOTICE	=	"You must	enter	a	valid	username and password	to access	this page.<br	/><br	/>Note that	even a legitimate	user may not have	sufficient administration	permissions	to access	this particular	resource.<br /><br />Contact <a	href=\"".$this->WEBMASTER_EMAIL."\">".$this->WEBMASTER_EMAIL."</a>	if you feel	that this	message	is in	error, or	if you would like	to be	given	permission to	use	this resource.";

$this->FILE_PERMISSIONS	=	Array
	(				 
		$this->relativeEditorPath."index.php"	                          => Array(0,1,'Home'),
		
		$this->relativeEditorPath."create_new_article.php"	            => Array(5,0,''),
		$this->relativeEditorPath."_create_new_article.php"	            => Array(5,0,''),
		
		$this->relativeEditorPath."files.php"	                          => Array(3,1,'Files'),
		$this->relativeEditorPath."show_file_info.php"                  => Array(0,0,''),
		$this->relativeEditorPath."_files_load_folder.php"              => Array(0,0,''),
		$this->relativeEditorPath."_delete_file.php"                    => Array(4,0,''),
	  $this->relativeEditorPath."_update_file_info.php"               => Array(3,0,''),
		$this->relativeEditorPath."_folder_loader.php"                  => Array(0,0,''),
		
		$this->relativeEditorPath."admin.php"	                          => Array(9,1,'Admin'),
		$this->relativeEditorPath."_admin_add_new_user.php"	            => Array(9,0,''),
		$this->relativeEditorPath."_admin_delete_user.php"	            => Array(9,0,''),
		$this->relativeEditorPath."_admin_change_permissions.php"	      => Array(9,0,''),
		
		$this->relativeEditorPath."editor.php"	                        => Array(3,2,''),
		$this->relativeEditorPath."_abandon_file.php"	                  => Array(3,2,''),
		$this->relativeEditorPath."_normalize_file.php"	                => Array(3,0,''),
		$this->relativeEditorPath."_save_edits.php"	                    => Array(3,2,''),
		$this->relativeEditorPath."_edit_session_revert.php"	          => Array(3,0,''),
		$this->relativeEditorPath."preview_edits.php"	                  => Array(3,0,''),
		
		$this->relativeEditorPath."edit_right_panel.php"	              => Array(3,1,'Edit Sidebar'),
		$this->relativeEditorPath."preview_right_panel.php"	            => Array(3,2,''),
		$this->relativeEditorPath."_preview_right_panel.php"	          => Array(3,0,''),
		$this->relativeEditorPath."_update_right_panel.php"	            => Array(3,2,''),
		$this->relativeEditorPath."_revert_right_panel_edits.php"       => Array(3,0,''),
		
		$this->relativeEditorPath."search.php"	                        => Array(0,1,'Search'),
		$this->relativeEditorPath."_handle_search_query.php"	          => Array(0,0,'')
	);

/*
 * 
 * ERROR MESSAGES
 */

$this->ERRORS = Array
  (
    /* general */
    0   =>  "A system error has occurred. We apologize for any inconvenience.",
    
    /* document checked out [article, name checked out by, date checked out] */
    10  =>  "Document ##1 was checked out by ##2 on ##3.",
    
    /* unable to check out document (dbase error) */
    20  =>  "Unable to check out document. Probably a system error. We apologize for any inconvenience.",
    
    /* unable to create .pending file */
    30  =>  "Unable to create checkout file. Probably a system error. We apologize for any inconvenience.",
    
    /* unable to update .pending file in Editor->getPostedPreviewDocument() */
    40  =>  "Unable to update .pending file for preview. Probably a system error. We apologize for any inconvenience.", 
    
    /* unable to update .pending file in Editor->getPostedPreviewDocument() */
    50  =>  "Unable to check in document. Probably a system error. We apologize for any inconvenience.", 
    
    /* unable to abandon edit session Editor->abandonSession() */
    60  =>  "An error occurred when trying to cancel this session. It is likely that this error will affect anything.",
    
    /* unable to get editable node Editor->getEditableNode() */
    70  =>  "This document does not have an editable content area!",
    
    /* unable to create template Editor->writeTemplateForFile() */
    100 =>  "An error occurred when trying to create template for new file. It is likely that this error will affect anything.",
    
    /* no template file found or malformed in Editor->writeTemplateForFile() */
    110 =>  "The path to template file does not exist, or the file has a malformed editable content region. Be sure to create a template, with proper formatting, and set this configuration value accordingly.",
    
    /* Unable to write new index info to file in Documents->update() */
    200 =>  "Indexing of document failed -- unable to write updated meta info to file.",
    
    /* Unable to add new directory in editor/_folder_operation.php */
    300 =>  "Unable to create new file directory ##1.",
    
    /* Unable to rename directory in editor/_folder_operation.php */
    310 =>  "Unable to rename folder to ##1.",
    
    /* When renaming folder, update of linkrefs failed */
    320 =>  "Renaming of folder ##1 failed. Aborting.",
    
    /* When performing action on folder or file that is checked out... */
    330 =>  "There is someone editing a file in this folder, or editing this file.",
    
    /* File operations not permitted on root folder */
    340 =>  "You cannot delete or rename the root folder.",
    
    /* When renaming file, update of linkrefs failed */
    350 =>  "Renaming of file ##1 failed. Aborting.",
    
    /* When renaming file, new file write failed */
    360 =>  "Unable to rename file, probably a system error."
    
  );
?>