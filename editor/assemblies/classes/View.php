<?php

class View extends Assembly
	{
	  function __construct()
		  {
        $fn = self::M()->strippedFileName();
		  }
		  
		function append($a = '')
		  {
		  	print($a);
		  }
		  
		function flush_out($a = '')
		  {
		  	print($a);
        ob_flush();
        flush();
		  }
		  
  	function notifyOfError($code=0,$replace=Array())
  	  {
  	    if(isset(self::M()->ERRORS[$code]))
  	      {
  	        $err = self::M()->ERRORS[$code];
  	        
  	        if(count($replace > 0))
  	          {
      	        /*
      	         * replace args (##1,##2...) see _config.php
      	         */
      	        for($x=0; $x < count($replace); $x++)
      	          {
      	            $err = str_replace('##'.($x+1),$replace[$x],$err);
      	          }
  	          }

  	        $this->flush_out($err);
  	      }  
  	      
  	    exit;
  	  }
  	  
    function printFilesystemTree($arr,$show_file_count=false)
      {
        static $depth   = 0;
    
        foreach($arr as $k => $v)
          {   
            if(!is_numeric($k))
              {
                /*
                 * indentation of tree
                 */
                $left_margin    = $depth*12;
                
                /*
                 * get unique id of folder (strip out path <= `files/`)
                 */
                $item_id = str_replace(self::M()->localRootPath,"",$v);
                
                /*
                 * lose slashes in id, replacing with (:)
                 * (xhtml does not allow '/' in id attribute)
                 */
                $item_id = ltrim(str_replace('/',':',$item_id));
                
                /*
                 * get the file/folder name (strip path, underscores > spaces)
                 */
                $item_name    = substr(strrchr($v,'/'),1);         
                $item_name    = str_replace("_"," ",$item_name);
                
                /*
                 * determine icon; simply check for file types
                 */
                $extension = strrchr($item_name,'.');

                switch($extension)
                  {
                    case '.html':
                      $icon_image = '<div class="icon" rel="icon" alt="html_blue" style="margin-left:'.$left_margin.'px; width:16px; height:16px;"></div>';
                      $link_tag = '<div  class="inactive_file" id="'.$item_id.'">'.$icon_image.$item_name.'</div><br />'; 
                      echo $link_tag;
                    break;
                    
                    /*
                     * no extension? assume folder
                     */
                    case '':
                    
                      /*
                       * add file count in folder, if requested.
                       * NOTE the glob pattern... any file that terminates
                       * with a '.' followed by any three characters or
                       * any four characters (standard 3 char ext, + .html).
                       * A little loose, but should do the trick. 
                       */
                      $fC = '';
                      if($show_file_count)
                        {
                          $_c = count(glob($v."/*.{???,????}", GLOB_BRACE));
                          if($_c > 0)
                            {
                              $fC = ' ('.$_c.')';
                            }
                        }
                        
                      $icon_image = '<div class="icon" rel="icon" alt="folder_blue" style="margin-left:'.$left_margin.'px; width:16px; height:16px;"></div>';
                      $link_tag = '<div class="inactive_folder" id="'.$item_id.'" accepts="file folder">'.$icon_image.$item_name.$fC.'</div><br />';  
                      echo $link_tag;
                    break;
                    
                    /*
                     * catch only defined extensions; ignore everything else
                     */
                    default:
                    break;
                  }
              }
            else
              {
                ++$depth;
                $this->printFilesystemTree($v,$show_file_count);
                --$depth;
              }     
          }
      }		  

		function showAdminHeader()
		  {
				$cnt = 1;
				$hOut = '';
				
				/*
				 * current page determines whether or not
				 * header is created. Header is shown on any
				 * value higher than zero.
				 */
				$cpI = self::M()->FILE_PERMISSIONS[$_SERVER['PHP_SELF']];
				
				$showHeader = ($cpI[1] > 0);
								
				foreach(self::M()->FILE_PERMISSIONS as $href => $info)
				  {
				    /*
				     * we check for valid user, and we check if this page
				     * should be part of a header
				     *
				     * See Model->FILE_PERMISSIONS for value explanation
				     */
				    		 		
				  	$showBut 	= ($info[1] == 0)
				    					? false
				    					: ($info[1] == 1)
				    					? true
				    					: false;
				    					
				    /*
				     * only show users functionality they can access; check credentials
				     */					


				  if(self::M()->AUTH_LEVEL[9] || self::M()->AUTH_LEVEL[$info[0]])
				  	  {
				  	  	$selClass = 'admin_buttons';
				  	  	if($href == self::M()->currentFileInfo())
				  	  	  {
				  	  	  	$selClass = 'selectedButton';
				  	  	  }
				  	  	
				  	  	if($showBut)
				  	  	  {
						  	  	$hOut .= '<input class="'.$selClass.'" type="button" id="bt_'.$cnt.'" name="bt_'.$cnt.'" value="'.$info[2].'" onclick="document.location.href = \''.$href.'\';" />';
						  	  }                
				  	  }
								  	  
				  	++$cnt;
				  }
					  
				if($showHeader)
				  {
				  	$hOut = '<div id="admin_panel">'.$hOut.'</div>';
				  	self::V()->append($hOut);
				  }
			}
			
	  function flushErrorBuffer()
	    {
	        
	    }
	}

?>