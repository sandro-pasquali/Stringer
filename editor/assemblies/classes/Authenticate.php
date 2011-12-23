<?php

class Authenticate extends Assembly
  {
    function __construct()
      {

			}
				  
		function authenticate()
		  {
		  	if($this->isAuthorized())
		  	  {
						return(true);
					}
			  else
			    {
				  	/*
				  	 * failed credential check
				  	 */
				  	self::V()->append(self::M()->AUTH_FAILURE_NOTICE);					    	
			    	
			    	return(false);
			    }
		  }
				  
		function requestCredentials() 
		  {
				header('WWW-Authenticate: Basic realm="'.self::M()->publicRootPath.'"');
				header('HTTP/1.0 401 Unauthorized');
				    
		    self::V()->append(self::M()->AUTH_FAILURE_NOTICE);
				    
				exit;
		  }
				  
		function isAuthorized()
		  {  
		    $page_access_level = self::M()->PAGE_ACCESS_LEVEL;

		    /*
		     * $page_access_level is the minum page
		     * access level (see _config.php->FILE_PERMISSIONS)
		     */
		     
		    /*
		     * ask for credentials if necessary
		     */
				if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) 
				  {
				    $this->requestCredentials();
				  } 
				else 
				  {
				    /*
				     * get user admin info
				     */
				  	$un = $_SERVER['PHP_AUTH_USER'];
				  	$pw = $_SERVER['PHP_AUTH_PW'];

				  	$q = "SELECT * FROM admin_permissions WHERE username = '$un' AND password = '$pw' AND active > 0";
				  	$r = mysql_query($q);
						  	
				  	if(mysql_num_rows($r) > 0)
				  	  {
				  	  	$fn = mysql_fetch_assoc($r);
				  	  	
				  	  	/*
				  	  	 * ok, set user details
				  	  	 */
	
				  	  	self::M()->USER_ADMIN_ID = $fn['id'];
				  	  	self::M()->USER_POSITION = $fn['position'];
				  	  	self::M()->USER_FULL_NAME = $fn['full_name'];
					  	  	
				  	  	/*
				  	  	 * set permission levels
				  	  	 */
                self::M()->AUTH_LEVEL[9] = (bool)$fn['can_admin'];
                self::M()->AUTH_LEVEL[5] = (bool)$fn['can_create'];
                self::M()->AUTH_LEVEL[4] = (bool)$fn['can_delete'];
                self::M()->AUTH_LEVEL[3] = (bool)$fn['can_edit'];
			          
				        /*
				         * check if user has permissions for access type.
				         * (see _config.php for access type definitions)
				         */
				         
 	  	          $user_authenticated = false;
          
 	  	          /*
 	  	           * admins can do everything...
 	  	           */
				        if(self::M()->AUTH_LEVEL[9]) 										
				          { 
				            $user_authenticated = true; 
				          }
				        else if(self::M()->AUTH_LEVEL[(int)$page_acces_level]) 
				          {
    				        $user_authenticated = true;
								  }
								
								/*
								 * now check if user has permission level necessary to
								 * access this resource
								 */
								if($user_authenticated)
								  {
    								/*
    								 * update lastLogin
    								 */
    								$q = "UPDATE admin_permissions SET last_login = NOW() WHERE id = ".self::M()->USER_ADMIN_ID;
    								$r = @mysql_query($q);
    								
    								return(true);
    						  }
    						else
    						  {
    						    /*
    						     * insufficient credentials
    						     */
    						    $this->requestCredentials();
    						  }
				  	  }
				  	else
				  	  {
				  	    /*
				  	     * unable to find u/p combo
				  	     */
				  	  	$this->requestCredentials();
				  	  }
				  }
			}
  }

?>