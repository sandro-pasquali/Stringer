<?php

//error_reporting(0);
//ini_set('display_errors', 0);

/*
 * maximum run time for any script, in seconds.
 */
set_time_limit(30);

/*
 * default system encoding
 */
mb_internal_encoding("iso-8859-1");

/*
 * Start session handling.
 */
session_start();

abstract class Assembly
  {
    private function __construct()
      {
      }
      
    protected function M()
		  {
		  	global $_M;
				return $_M;
		  }

    protected function V()
		  {
		  	global $_V;
				return $_V;
		  }
	  
    protected function C()
		  {
		  	global $_C;
				return $_C;
		  }
		  
		public function attach($lib)
		  {
				eval('$this->'.$lib.' = new '.$lib.'();');
		  }
  }

/*
 * initialize Assembly->MVC classes
 */  

require("classes/Model.php");
require("classes/View.php");
require("classes/Controller.php");
require("classes/Authenticate.php");

$_M = new Model();
$_V = new View();
$_C = new Controller();  

/*
 * do authentication before we begin...
 */
$_M->attach('Authenticate');
$_M->Authenticate->authenticate();

  