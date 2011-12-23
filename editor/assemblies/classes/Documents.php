<?php

class Documents extends Assembly
  {
    function __construct()
      {
        require("freetag/freetag.class.php");

        $freetag_options = Array
          (
            'db_user' => self::M()->DB_USERNAME,
            'db_pass' => self::M()->DB_PASSWORD,
            'db_host' => self::M()->DB_LOCATION,
            'db_name' => self::M()->DB_NAME 
          );
          
        $this->Freetag = new freetag($freetag_options);
			}

	  function tag($user_id=false,$object_id=false,$tags=false)
	    {
	      /*
	       * very important to have proper info here, as
	       * corruption in the tag dbase is very bad!
	       */
	      if($user_id && $object_id && $tags)
	        {
	          /*
	           * clear any previous tagging for this object
	           */
	          //$this->deleteObjectTags($object_id);
	          
            return $this->Freetag->tag_object($user_id, $object_id, $tags);
          }
        
        return false;
	    }
	    
	  function deleteObjectTags($ob_id=0)
	    {
	      return $this->Freetag->delete_all_object_tags($ob_id);
	    }  
	    
	  function delete($file=false)
	    {
	      if(!self::V()->Editor->setCurrentFile($file))
	        {
	          return false;  
	        }  

	      /*
	       * Get file id from `document_index`. Note how if this document
	       * is not registered in document_index, we'll only try to 
	       * delete files -- which should never happen.
	       * 
	       */
	      $q = "  SELECT id 
	              FROM document_index 
	              WHERE file_path = '".mysql_real_escape_string(self::V()->Editor->articlePath)."' 
	              LIMIT 1";
	      $r = mysql_query($q);

	      if($r && (mysql_num_rows($r) > 0))
	        {      
            $inf = mysql_fetch_assoc($r);
	          $ob_id = $inf['id'];
	          
    	      /*
    	       * deleting a file includes a number of steps, in order:
    	       * 
    	       * 1. Remove any relevant links
    	       * 2. Remove all tags tied to object id;
    	       * 3. Remove from `document_index` table;
    	       * 4. Remove file:
    	       *    a. .archive file;
    	       *    b. .pending file;
    	       *    c. file;
    	       */
    	      
    	      /*
    	       * Remove inbound/outbound links. We do this first, as it is possible that
    	       * this file is referencing itself (say, with a common home button on
    	       * each page, and this is the home page itself).  As it will be removed
    	       * below, link ref actions may try to execute on a deleted document...
    	       */
    	      self::M()->LinkMap->unlinkFile(self::V()->Editor->articleURL,$ob_id);

	          $this->deleteObjectTags($ob_id);
	          
	          $q = "  DELETE FROM `document_index` 
	                  WHERE id = $ob_id";
	          $r = mysql_query($q);
	        }
	      
	      /*
	       * now do file deletions
	       */  
	      self::M()->Filesystem->delete(self::V()->Editor->archivePath);
	      self::M()->Filesystem->delete(self::V()->Editor->checkedArticlePath);
	      self::M()->Filesystem->delete(self::V()->Editor->articlePath);

	      return true;
	    }
	    
		function getMetaInfo($dom=false)
		  {
		    /*
		     * returns an array containing all meta
		     * info name => content | + title. Note that we 
		     * are expecting a DOMDocument object here...
		     */

        $ret = Array();        
        $meta = $dom->getElementsByTagName('meta');

        foreach($meta as $m) 
          {
            $m_n = (string)$m->getAttribute('name');
            $m_c = (string)$m->getAttribute('content');
            
            if($m_n != "")
              {
                $ret[strtolower($m_n)] = $m_c;
              }
          }
        
        /*
         * get <title> as well...
         */
        $ret['title'] = $dom->getElementsByTagName('title')->item(0)->nodeValue;

        return $ret;
		  }  
	    
    function update($filename=false,$title=false,$description=false,$keywords=false)
      {
        /*
         * The only required argument is $filename; however, all the info
         * is needed for full indexing.  If this function is only passed
         * a filename, then it will load that document and try to fetch 
         * the info from document (meta tags).  As that is expensive, 
         * it is hoped that this function is called with relevant info 
         * from creation/deletion functions.
         *
         * on error, we simply return false without failing, as not indexing
         * a document isn't a critical problem.
         */  
        
        /*
         * filename?
         */
        if(!$filename)
          {
            return false;  
          }
        
        /*
         * no meta info? fetch...
         */
        if(!$title || !$description || !$keywords)
          {
            /*
             * NOTE that we can expect badly formed html; use @
             */
            $dom = new DOMDocument();
            @$dom->loadHTMLFile($filename);
            
            $minfo = $this->getMetaInfo($dom);
            
            if(   !isset($minfo['title']) 
              ||  !isset($minfo['description']) 
              ||  !isset($minfo['keywords']) )
              {
                /*
                 * hm... page itself doesn't have that info... something amiss
                 */
                return false;
              }
            
            /*
             * set the new page info
             */
            $title        = $minfo['title'];
            $description  = $minfo['description'];
            $keywords     = $minfo['keywords'];
          }       
        else
          {             
            /*
             * if we were passed title,description,keywords, then we need
             * to update the original document with the new info we are indexing.
             */
             
            /*
             * NOTE that we can expect badly formed html; use @
             */
            $dom = new DOMDocument();
            @$dom->loadHTMLFile($filename);
            
            /*
             * set <description><keywords> meta tags
             */
            foreach($dom->getElementsByTagName('meta') as $meta)
              {
                $mn = strtolower($meta->getAttribute('name'));
                
                switch($mn)
                  {
                    case 'keywords':
                      $meta->setAttribute('content',$keywords);
                    break;
                    
                    case 'description':
                      $meta->setAttribute('content',$description);
                    break;
                    
                    default:
                    break;
                  }
              }
              
            /*
             * replace title
             */
            $pt = $dom->getElementsByTagName('title')->item(0);
            $pt->nodeValue = '';
            $pt->appendChild($dom->createTextNode($title));
            
            /*
             * alright... write updated file
             */
            $file_contents = $dom->saveHTML();

            if(!self::M()->Filesystem->write($filename,$file_contents))
              {
                /*
                 * can't write template file
                 */
                self::V()->notifyOfError(200);
                return false;
              }
          }         
        
        /*
         * ok, we have all the data we need, and the original 
         * article reflects the current data we have:
         *
         * 1. update the document_index table;
         * 2. update tag library
         */
        
        $file_path      = mysql_real_escape_string($filename);
        $title          = mysql_real_escape_string($title);
        $description    = mysql_real_escape_string($description);
        
        /*
         * is this an update? check for file
         */
        $q = "  SELECT id 
                FROM document_index 
                WHERE file_path = '$file_path'";
        $r = mysql_query($q);
        
        if($r)
          {
            if(mysql_num_rows($r) > 0)
              {
                /*
                 * already exists; update
                 */
                $inf    = mysql_fetch_assoc($r);
                $ob_id  = $inf['id'];
                 
                $q = "  UPDATE document_index 
                        SET   title = '$title', 
                              description = '$description' 
                        WHERE id = $ob_id"; 
                $r = mysql_query($q); 
              }
            else
              {
                /*
                 * new; insert
                 */  
                $q = "  INSERT INTO document_index (file_path, title, description) 
                        VALUES ('$file_path','$title','$description')";
                $r = mysql_query($q); 
                
                /*
                 * we'll need the document id for tagging, later.  If
                 * this is a new insert, fetch the last id.
                 */
                $ob_id = mysql_insert_id();
              }
          }
        else
          {
            /*
             * dbase error
             */
            return false;
          }
        
        
        if($r && $ob_id)
          {
            /*
             * refresh linkmap
             */
            self::M()->LinkMap->mapDocument($dom,$ob_id);
             
            /*
             * do tagging. We'll also need the user id.
             */
            $user_id = self::M()->USER_ADMIN_ID;
                
            if($this->tag($user_id,$ob_id,$keywords))
              {
                return true;  
              }
          }

        return false;
      }
      
		function XHTMLSplit($str,$withTags=false)
		  {
				$ret = Array();
				
				/*
				 * pattern used to strip tags
				 */
				$pattern = '/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/';
				
				/*
				 * split into tags and data
				 */
				if($withTags)
				  {
						$vs = preg_split($pattern, trim($str), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
					}
				else
					{
						$vs = preg_split($pattern, trim($str), -1, PREG_SPLIT_NO_EMPTY);
					}
		
				/*
				 * clean up array, which may have empty elements
				 */
				foreach($vs as $k => $v)
				  {
				  	$v = trim($v);
				  	if($v != '')
				  		{
				  			array_push($ret,$v);
				  		}
				  }
				return($ret);
		  }
		  
		function tidyHTML($html="")
		  {
        /*
         * tidy up html
         */
        $tidy_conf = Array
          (
            "show-body-only"    => 1,
            "output-xhtml"      => 1,
            "merge-divs"        => 0,
            "preserve-entities" => 1,
            "indent"            => 1,
            "drop-empty-paras"  => 1,
            "hide-comments"     => 1
           );
           
        return tidy_repair_string($html,$tidy_conf,"latin1");
		  }
  }

?>