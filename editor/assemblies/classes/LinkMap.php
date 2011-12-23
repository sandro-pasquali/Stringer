<?php

class LinkMap extends Assembly
	{
    /*
     * As documents are created and destroyed we want to track
     * these changes and maintain the link map.  
     *
     * mapDocument()
     * Getting all the anchor<A> tags, checking
     * their `href` properties, and if the link is to 
     * an asset in our library, store the reference.
     *
     * NOTE: this system is purely link based -- images and other
     * embedded objects will not be updated should their
     * target files disappear.
     */
     
	  function __construct()
		  {
		  }
		  
		function mapDocument($xml,$did,$clean=true)
		  {
		    /*
		     * $xml == a valid DOMDocument xml object;
		     * $did == versioning id of document being mapped;
		     * $clean == in order to be sure that we maintain link
		     * integrity, default behaviour is to delete all previous
		     * linkages stored for this document, and rebuilding.
		     */
        $allA = $xml->getElementsByTagName('a');
        
        if($clean)
          {
            $q = "DELETE FROM linkrefs WHERE document_id = '$did'";
            mysql_query($q);            
          }
        
        foreach($allA as $el)
          {
            $ref = $el->getAttribute('href');  

            /*
             * check if link is an internal file. Ignore others.
             */
            if(strpos($ref,self::M()->publicRootPath)!==false)
              {
                /*
                 * strip out the asset path and store the result.
                 */
                $ref = str_replace(self::M()->publicRootPath,"",$ref);
                
                /*
                 * source_id is always a document id...
                 */
                $q = "INSERT INTO linkrefs (document_id,target_file) VALUES ('$did', '".mysql_real_escape_string($ref)."')";
                mysql_query($q);
              }
          }
		  }
		  
		function unlinkFile($doc_path=false,$doc_id=false)
		  {
		    if(!$doc_path || !$doc_id)
		      {
		        return false;  
		      }
		      
		    /*
		     * $doc_path  = full public path to document to be unlinked;
		     * $doc_id    = document_index id for document
		     */
		     
		    /*
		     * get fragment of filename relative to publicRootPath, which
		     * is what linkref table stores (see this->mapDocument())
		     */
        $t_ref = str_replace(self::M()->publicRootPath,"",$doc_path);
        
        /*
         * get all the ids of documents that link to the
         * file being unlinked
         */
        $q = "  SELECT id,document_id  
                FROM linkrefs 
                WHERE target_file = '".mysql_real_escape_string($t_ref)."'";

        $r = mysql_query($q);
        
        while($dInf = mysql_fetch_assoc($r))
          {
            $lr_id = $dInf['id'];
            $did = $dInf['document_id'];

            /*
             * Take the document_id and fetch the file path of source document.
             * NOTE: document_index stores local file path. This doesn't matter
             * here, but keep that in mind should you make changes.
             */
             
            $q1 = " SELECT file_path 
                    FROM document_index 
                    WHERE id = $did 
                    LIMIT 1";
                    
            $r1 = mysql_query($q1);
            $inf1 = mysql_fetch_assoc($r1);
            
            $src_path = $inf1['file_path'];
            
            /*
             * Have path to the source document.
             * We want to go through the source document, find the
             * $t_ref in a a::href string, and do something with it. Note 
             * that it is very likely that the same link ref will exist
             * multiple times in a document.  The linkrefs table will store
             * multiple target references for this document.  As such, we
             * don't delete ALL occurrences of any given link, just the first
             * we find, then exit.
             *
             * Once links updated, replace file with new one...
             */
            
            /*
             * we can expect bad html, so suppress errors.
             */
            $doc = new DOMDocument();
            @$doc->loadHTMLFile($src_path);
            
            if($doc)
              {      
                $allA = $doc->getElementsByTagName('a');
                
                foreach($allA as $el)
                  {
                    $aref = $el->getAttribute('href'); 
                    
                    if(strpos($aref,$t_ref)!== false)
                      {
                        /*
                         * found the link. As we don't know what is
                         * inside the <a>, and don't want to make any assumptions
                         * about how the document is styled (maybe it still needs the
                         * <a> element for css selectors, etc), or do arbitrary 
                         * things to the contents, we just href="#"...
                         */  

                       $el->setAttribute('href','#');
                       
                       /*
                        * again... linkrefs table will store multiple target references
                        * if the document contains multiple links to the same file. So,
                        * we just remove the first one we find, and progress through the
                        * linkrefs list will eventually remove them all.
                        */
                       break;
                      }
                  }                  
                /*
                 * now rewrite source file. 
                 */
                @$doc->saveHTMLFile($src_path);
              }
            /*
             * link updated. remove reference from linkrefs table
             */
            $_q = "DELETE FROM linkrefs WHERE id = $lr_id";
            mysql_query($_q);
          }
          
        /*
         * now delete any outbound links from this file
         */
        $q = "  DELETE FROM linkrefs 
                WHERE document_id = $doc_id";
        mysql_query($q);
        
        return true;
		  }
		  
		function handleFileRename($old_file,$new_file)
		  {
		    /*
		     * When a file is renamed, all document links to that file
		     * will have to be changed.  Once that is done, we can change the
		     * linkrefs references.
		     */
		    
		    /*
		     * Find all links containing $old_file
		     */
        $q = "  SELECT id,document_id  
                FROM linkrefs 
                WHERE target_file = '".mysql_real_escape_string($old_file)."'";
                
        $r = mysql_query($q);
        
        while($dInf = mysql_fetch_assoc($r))
          {
            $did = $dInf['document_id'];

            /*
             * Take the document_id and fetch the file path of source document.
             * NOTE: document_index stores local file path. This doesn't matter
             * here, but keep that in mind should you make changes.
             */
             
            $q1 = " SELECT file_path 
                    FROM document_index 
                    WHERE id = $did 
                    LIMIT 1";
                    
            $r1 = mysql_query($q1);
            $inf1 = mysql_fetch_assoc($r1);
            
            $src_path = $inf1['file_path'];
            
            /*
             * Have path to the source document.
             * We want to go through the source document, find the
             * $old_name in a a::href string, and replace it.  Note as well
             * that it is very likely that the same link ref will exist
             * multiple times in a document.  The linkrefs table will store
             * multiple target references for this document.  As such, we
             * don't delete ALL occurrences of any given link, just the first
             * we find, then exit.
             *
             * Once links updated, replace file with new one...
             */
            
            /*
             * we can expect bad html, so suppress errors.
             */
            $doc = new DOMDocument();
            @$doc->loadHTMLFile($src_path);
            
            if($doc)
              {      
                $allA = $doc->getElementsByTagName('a');
                
                foreach($allA as $el)
                  {
                    $aref = $el->getAttribute('href'); 
                    
                    /*
                     * have to add the public path to the file info
                     * we have, as it is truncated for linkrefs
                     */
                    $old_file_x = self::M()->publicRootPath.$old_file;
                    $new_file_x = self::M()->publicRootPath.$new_file;
                    
                    if($aref == $old_file_x)
                      {
                        /*
                         * found the link. Do string replacement.
                         */  

                       $el->setAttribute('href',$new_file_x);
                       
                       /*
                        * again... linkrefs table will store multiple target references
                        * if the document contains multiple links to the same file. So,
                        * we just handle the first one we find, and progress through the
                        * linkrefs list will eventually remove them all.
                        */
                       break;
                      }
                  }                  
                
                /*
                 * now rewrite source file. 
                 */
                @$doc->saveHTMLFile($src_path);
              }
		      } 
		      
		    /*
		     * Now update the `linkref` table to reflect changes.
		     */
        $q = "  UPDATE linkrefs 
                SET target_file = '".mysql_real_escape_string($new_file)."'  
                WHERE target_file = '".mysql_real_escape_string($old_file)."' ";
        
        if(!mysql_query($q))
          {
            return false;  
          }
          
        return true;
		  }
		  
		function handleFolderRename($old_name,$new_name)
		  {
		    /*
		     * When a folder is renamed, all document links through the previous folder
		     * will have to be changed.  Once that is done, we can change the
		     * linkrefs references.
		     */
		    
		    /*
		     * Find all links containing $old_name
		     */
        $q = "  SELECT id,document_id  
                FROM linkrefs 
                WHERE target_file LIKE '".mysql_real_escape_string($old_name)."%'";
                
        $r = mysql_query($q);
        
        while($dInf = mysql_fetch_assoc($r))
          {
            $did = $dInf['document_id'];

            /*
             * Take the document_id and fetch the file path of source document.
             * NOTE: document_index stores local file path. This doesn't matter
             * here, but keep that in mind should you make changes.
             */
             
            $q1 = " SELECT file_path 
                    FROM document_index 
                    WHERE id = $did 
                    LIMIT 1";
                    
            $r1 = mysql_query($q1);
            $inf1 = mysql_fetch_assoc($r1);
            
            $src_path = $inf1['file_path'];
            
            /*
             * Have path to the source document.
             * We want to go through the source document, find the
             * $old_name in a a::href string, and replace it.  Note as well
             * that it is very likely that the same link ref will exist
             * multiple times in a document.  The linkrefs table will store
             * multiple target references for this document.  As such, we
             * don't delete ALL occurrences of any given link, just the first
             * we find, then exit.
             *
             * Once links updated, replace file with new one...
             */
            
            /*
             * we can expect bad html, so suppress errors.
             */
            $doc = new DOMDocument();
            @$doc->loadHTMLFile($src_path);
            
            if($doc)
              {      
                $allA = $doc->getElementsByTagName('a');
                
                foreach($allA as $el)
                  {
                    $aref = $el->getAttribute('href'); 

                    /*
                     * It is possible that the path fragment could
                     * match on a partial, so we make sure we have
                     * full paths for search and replacement, as well
                     * as a closing slash for folders.
                     */
                    
                    $r_old_name = self::M()->publicRootPath.$old_name;
                    $r_new_name = self::M()->publicRootPath.$new_name; 

                    if(strpos($aref,$r_old_name)!== false)
                      {
                        /*
                         * found the link. Do string replacement.
                         */  

                       $el->setAttribute('href',str_replace($r_old_name,$r_new_name,$aref));
                       
                       /*
                        * again... linkrefs table will store multiple target references
                        * if the document contains multiple links to the same file. So,
                        * we just handle the first one we find, and progress through the
                        * linkrefs list will eventually remove them all.
                        */
                       break;
                      }
                  }                  
                
                /*
                 * now rewrite source file. 
                 */
                @$doc->saveHTMLFile($src_path);
              }
		      } 
		      
		    /*
		     * Now update the `linkref` table to reflect changes.
		     */
        $q = "  UPDATE linkrefs 
                SET target_file = 
                REPLACE(target_file, '".mysql_real_escape_string($old_name)."', '".mysql_real_escape_string($new_name)."') 
                WHERE target_file LIKE '".mysql_real_escape_string($old_name)."%' ";
        
        if(!mysql_query($q))
          {
            return false;  
          }
          
        return true;
		  }
	}	

?>