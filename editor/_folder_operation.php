<?php 

require("assemblies/EditorAssembly.php");

$_M->Request->validate("get");
$_M->Request->createGlobals();

/*
 * translate the : separated folder naming of interface with slashes
 */
$cur_folder = str_replace(":","/",$cur_folder);

/*
 * user is allowed to use spaces in folder names, but ultimately we
 * must translate to a proper filesystem folder name.  So, we simply
 * replace spaces with underscores.
 */
$folder_name = str_replace(" ","_",$folder_name);

/*
 * determine operation, and execute command
 */
switch($folder_op)
  {
    case 'add':

      $new_path = $_M->localRootPath.$cur_folder.'/'.$folder_name;
      
      if(!$_M->Filesystem->createFolder($cur_folder, $folder_name))
        {
          echo 'Addition of new folder has failed.';
          exit;  
        }
        
      $op = 'addF';
        
    break;
    
    case 'rename':

      /*
       * replace last dir in $cur_folder with $folder_name and send
       */
      $new_folder   = substr($cur_folder,0,strrpos($cur_folder,"/"))."/".$folder_name;

      if(!$_M->Filesystem->renameFolder($cur_folder, $new_folder))
        {
          echo 'Renaming of folder has failed';
          exit;  
        }
        
      $old_path   = $_M->localRootPath.$cur_folder;
      $new_path   = substr($old_path,0,strrpos($old_path,"/"))."/".$folder_name;
      
      $op = 'renameF';

    break;
    
    case 'move':
    
      $move_folder = str_replace(":","/",$move_folder);
      
      /*
       * It is not possible to move a parent folder into one of its
       * own subfolders.  We need to ensure that this is not attempted.
       * Simply check if $cur_folder exists in $move_folder.
       */
      if(strpos($move_folder, $cur_folder.'/') !== false)
        {
          echo 'You cannot move a parent folder into one of its own subfolders';
          exit;  
        }
      
      /*
       * We're now in possession of two paths: the orginal folder path, and the
       * new folder path. However, we're not simply moving the CONTENTS of the
       * original folder path; we need to recreate that original folder underneath
       * the new folder, including all of the original subfolders.  So, we need to append
       * the old folder name to the new folder path.
       */
      $ap = explode("/",$cur_folder);
      $lf = $ap[count($ap)-1];
      
      $move_folder .= "/$lf";

      if(!$_M->Filesystem->renameFolder($cur_folder, $move_folder))
        {
          echo 'Moving of folder has failed';
          exit;  
        }
        
      $new_path   = $_M->localRootPath.$move_folder;
      
      $op = 'moveF';
    
    break;
    
    case 'delete':
      exit;
    break;
    
    default:
      exit;
    break;  
  }

/*
 * need to inform files.php that we've created a new folder,
 * so it can automagically load that folder's files
 */
$nf = str_replace("/",":",str_replace($_M->localRootPath,"",$new_path));
            
echo "ok^$op^$nf";


?>