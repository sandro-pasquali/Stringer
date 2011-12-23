<?php

require("assemblies/EditorAssembly.php");

$_M->Request->validate("get");
$_M->Request->createGlobals();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>	

<title></title>

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
  
A
  {
    color: blue;
  }  
  
</style>

<script type="text/javascript"></script>

</head>

<body>
  
<?php

$_V->showAdminHeader();

/*
 * is session info set? if not, just create dummy array
 */
$hit_info     =   isset($_SESSION['last_search_result']) 
                  ? unserialize($_SESSION['last_search_result']) 
                  : Array('hits'=>Array(),'query'=>'');

$per_page     = $_M->maxSearchResultsPerPage;
$hits         = $hit_info['hits'];
$query        = $hit_info['query'];
$total        = count($hits);
$page         = (isset($page)) ? $page : 0;
$offset       = $page *  $per_page;

$results      = '';
$pagination   = '';

/*
 * build query (mainly, building mysql IN construct)
 */
$q = "  SELECT id, file_path, title, description
        FROM document_index 
        WHERE id IN (";
        
for($x=$offset; $x < ($offset + $per_page); $x++)
  {
    if(isset($hits[$x]))
      {
        $q .= $hits[$x].',';
      }
  } 

/*
 * lose trailing comma, add close bracket, query...
 */
$q = substr($q,0,-1).')';  
$r = mysql_query($q);

if($r)
  {
    /*
     * loop results, building results page
     */
    while($res = mysql_fetch_assoc($r))
      {
        $id                   = $res['id'];
        $title                = $res['title'];
        $description          = $res['description'];
        $file_path            = str_replace($_M->localRootPath,$_M->publicRootPath,$res['file_path']);
            
        $results .= '<li><a href="'.$file_path.'">'.$title.'</a><br />';
        $results .= '<span class="pgraph">'.$description.'</span></li><br />';
      }  
  }
else
  {
    $results = 'No results.';  
  }
  
/*
 * set pagination control
 */
$_page = 0;
for($y=0; $y < $total; $y++)
  {
    if(($y % $per_page) === 0)
      {
        ++$_page;
        /*
         * current page?
         */
        if($_page == ($page + 1))
          {
            $pagination .= '[ '.$_page.' ] &nbsp;&nbsp;';  
          }
        else
          {
            $pagination .= '[ <a href="search.php?page='.($_page-1).'">'.$_page.'</a> ] &nbsp;&nbsp;';  
          }
      }
  }

?>

<div style="width:600px; padding-top:30px; padding:10px; font-weight:normal;">

<form action="_handle_search_query.php" method="post" id="searchbox">
  <input class="submit_field" style="width:300px; margin-left:40px;" id="query" name="query" type="text" value="<?php echo $query; ?>" /> 
  
  <input type="submit" value="search" />
</form>

<ol>
  
<?php

echo $pagination."<br /><br />";

echo '<br />'.$results.'<br />';

echo $pagination."<br /><br />";

?>

</ol>

<?php

/*
 * only show second, below-results search if
 * there are results...
 */
if($total > 0)
  {
    echo '
    
    <form action="_handle_search_query.php" method="post" id="searchbox">
    
      <input class="submit_field" style="width:300px; margin-left:40px;" id="query" name="query" type="text" value="'.$query.'" /> 
      
      <input type="submit" value="search" />
    </form>
    
    </div>

    ';

  }

?>

</body>
</html>