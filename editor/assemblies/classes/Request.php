<?php

class Request extends Assembly
  {
  	function __construct()
  	  {
        global $_POST;
        global $_GET;
        
        /*
         * see validate()
         */
        $this->method = "post";
         
        /*
         * store any request data
         */
        
        $this->post     = Array();
        $this->get      = Array();

        foreach($_POST as $k => $v)
          {
            /*
             * post vars may be array:
             * ie: field1="formfield[]", field2="formfield[]",... etc.
             * check, and maintain type if applicable
             */
            if(is_array($_POST[$k]))
              {
                $this->post[$k]     = $_POST[$k];
              }
            else
              {
                /*
                 * ...otherwise, just treat as string
                 */
                $this->post[$k]     = trim($v);  
              }
          }
          
        foreach($_GET as $k => $v)
          {
            $this->get[$k]      = trim($v);  
          }
  	  }

    function DBPrepare()
      {
        /*
         * use this if you are going to insert values into a mysql db.
         * NOTE that this function will add apostrophes around the values,
         * so remember not to add them yourself around variable names in the
         * query definition, etc.  
         */
        $data =& $this->getRequest();
        
        foreach($data as $k => $v)
          {
            if(!is_array($data[$k]))
              {
                $data[$k] = "'".mysql_real_escape_string($v)."'";
              }
          }
      }
      
    function createGlobals()
      {
        /*
         * NOTE: this function will create global variables reflecting
         * the same keys as the request data (the POST or GET data);  It will
         * use the values as they currently exist in $this->post, $this->get, etc.
         * If you need to change those values (say, DBPrepare()'ing them), be sure
         * to do that BEFORE calling this function, as, again, it uses the CURRENT
         * values...
         */
        $data =& $this->getRequest();
        
        foreach($data as $k => $v)
          {
            $GLOBALS[$k] = $v;
          }
      }

    function cryptDecrypt($text,$urlenc=true) 
      {
        /*
         * NOTE: the key must be defined in constants. see config.php
         *
         */
        
        $key = 'lkfsdj989dsov8fpazmf080asd0f8';
        $key_len = strlen($key);
    
        $k = array(); 
        /*
         * fill key array with the bitwise AND of the ith key character and 0x1F
         */
        for($i = 0; $i < $key_len; ++$i) 
          {
            $k[$i] = ord($key{$i}) & 0x1F;
          }
    
        /*
         * perform encryption/decryption
         */
        for($i = 0; $i < strlen($text); ++$i) 
          {
            $e = ord($text{$i});
            /*
             * if the bitwise AND of this character and 0xE0 is non-zero
             * set this character to the bitwise XOR of itself
             * and the ith key element, wrapping around key length
             * else leave this character alone
             */
            if($e & 0xE0) 
              {
                $text{$i} = chr($e ^ $k[$i % $key_len]);
              }
          }
    
        if($urlenc)
          {
            return urlencode($text);
          }
          
        return $text;
      } 
  	
  	function &getRequest()
  	  {
  	    /*
  	     * returns the data from submit method, as set in validate()
  	     */
		    switch($this->method)
		      {
		        case "post":
		          return $this->post;
		        break;
		        
		        case "get":
		          return $this->get;
		        break;
		        
		        default:
		          print "data error. aborting.";
		          exit;
		        break;  
		      } 
  	  }
  	    
		function validate($method = "post",$strict=false)
		  {
		    /*
		     * In order for anything to be done with the data, you
		     * must first validate it.  Send one of the following 
		     * values to this method:
		     *
		     * `post`;
		     * `get`;
		     *
		     */
		     
        $this->method = strtolower($method);
        
		    $data =& $this->getRequest();
		    
		    foreach($data as $k => $v)
		      {
		        /*
		         * NOTE: only request data keyed within ->metaFieldInfo[] 
		         * will be checked; other values are left untouched.
		         */
		        if(isset(self::M()->metaFieldInfo[$k]))
		          {
		            $regex  = self::M()->metaFieldInfo[$k]['regex'];
		            $info   = self::M()->metaFieldInfo[$k]['info'];
		            
		            /*
		             * note that all validation is via regex
		             */
		            if($regex != "")
		              {		                
    		            if($info == "")
    		              {
    		                $info = "Form error. Bad data";  
    		              }
    		            
    		            if(!@preg_match($regex,$data[$k]))
    		              {
    		                $msg = "<br /><div style=\"font-family:Verdana,Arial; color:#ff0000; font-size:12px; font-weight:bold; padding:10px; margin:10px; border: 4px #000000 double; width:80%; background-color:#F9F9F9;\"><span style=\"font-size:20px; padding:10px; margin:10px; margin-top: 0px; border:1px #ff0000 solid; float:left;\">!!</span> ".$info."<br clear=\"all\" /><a href=\"javascript:history.go(-1)\">GO BACK</a></div>";
          	  
          	            self::V()->append($msg);
          	            exit;
    		              }
		              }
		          }
		      }
      }
  }
  
?>