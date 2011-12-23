<?php

class Model extends Assembly
  {
    function __construct()
		  {
		    require("_config.php");

        /*
         * set up page information/permissions
         */
        $di = $this->currentFileInfo();

        if(isset($this->FILE_PERMISSIONS[$di]))
          {
            $ai = $this->FILE_PERMISSIONS[$di];	
            $this->PAGE_ACCESS_LEVEL = $ai[0];
            $this->PAGE_TITLE = $ai[2];
          }
        else
          {
            $this->PAGE_ACCESS_LEVEL = 0;
            $this->PAGE_TITLE = '';
          }

        /*
         * CONNECT TO DATABASE
         */
        $this->DBConnect();
		  }
		  
  	function currentFileInfo()
  	  {
  	  	return($_SERVER['PHP_SELF']);
  	  }
  	  
		function DBConnect($l=false,$u=false,$p=false,$n=false)
		  {
		    $location   = ($l) ? $l : $this->DB_LOCATION;
		    $username   = ($u) ? $u : $this->DB_USERNAME;
		    $password   = ($p) ? $p : $this->DB_PASSWORD;
		    $name       = ($n) ? $n : $this->DB_NAME;
		    
		    $this->DB = mysql_connect($location, $username, $password);
        @mysql_select_db($name,$this->DB);
		  }  
  	  
  	function strippedFileName()
  	  {
  	  	$fn = $_SERVER['PHP_SELF'];
  	  	$fn = explode('/',$fn);
  	  	
  	  	return($fn[count($fn)-1]);
  	  }
  	  
    function createSearchResult($query="")
      {
        /*
         * tag search will set $_SESSION['last_search_result'] with
         * serialized array of id's in `document_index`
         */
        
        $_SESSION['last_search_result'] = ''; 
         
        $raw_tags = explode(" ",$query);
        $normal_tags = Array();
        
        foreach($raw_tags as $k => $v)
          {
            array_push($normal_tags,$this->Documents->Freetag->normalize_tag($v));  
          }
        
        $hits = $this->Documents->Freetag->get_objects_with_tag_combo($normal_tags,0,$this->maxSearchResults);

        $rez = Array
          (
            'query'   => $query,
            'hits'    => $hits
          );
          
        $_SESSION['last_search_result'] = serialize($rez);
        
        /*
         * returns false if no hits
         */
        return (count($hits) > 0);
      }
  } 

?>
