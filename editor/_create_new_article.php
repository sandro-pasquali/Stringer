<?php

require("assemblies/EditorAssembly.php");

/* 
 * GET:
 *
 *  $article_name 
 *  $write_path 
 */
 
$_M->Request->validate('get');
$_M->Request->createGlobals();
 
/*
 * the user will use spaces in file names, but we need to
 * translate that name to a proper filesystem name; change
 * spaces to underscores
 */
$article_name = str_replace(" ","_",$article_name); 
 
/*
 * we will be sent an article name without extension.
 * add the .html extension here.
 */
$article_name = $article_name.".html";

/*
 * does this article already exist? 
 */

$folder_path  = $_M->localRootPath.$write_path;
$target_file  = $folder_path.'/'.$article_name;
$fileURL      = $_M->publicRootPath.$write_path.'/'.$article_name;

if(file_exists($target_file))
  {
    echo "A file with that name already exists"; 
    exit;
  }

/*
 * ok; write the new file template.
 *
 * Note that the admin will add meta info later; we simply
 * need to put some dummy values in for now.
 */

if($_V->Editor->writeTemplateForFile($folder_path,$article_name,"--","--","--"))
  {
    /*
     * everything good; now we want to reload the file folder
     * that this new article was created in, with the newly created
     * file being highlighted.  This allows the user to immediately
     * change meta info, edit, and so on.
     */
    $nf = "$write_path/$article_name";
    $nf = str_replace("/",":",$nf); // prepare file id for interface

    echo "ok^create^$nf";
  }
else
  {
    echo "Unable to create new article."; 
  }

?>