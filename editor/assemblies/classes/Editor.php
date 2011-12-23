<?php

class Editor extends Assembly
	{
	  function __construct()
		  {	
		    /*
         * this is a full http address, ie. http://www.site.com/articles/a/a.html
         */
		    $this->articleURL           = false;
		    
		    /*
		     * this is the local path to the article
		     */
		    $this->articlePath          = false;
		    
		    /*
		     * http path to the checked out file
		     */
		    $this->checkedArticleURL    = false;
		    
		    /*
		     * the local path to the checked out file
		     */
		    $this->checkedArticlePath   = false;
		    
		    /*
		     * http path to the .archive (if any)
		     */
		    $this->archiveURL           = false;
		    
		    /*
		     * the local path to the .archive (if any)
		     */
		    $this->archivePath          = false;
		    
		    
		    $this->storedNodes          = Array();
		    
        $this->document             = new DOMDocument();
        $this->editableContent      = new DOMDocument();
		  }
		
		function setCurrentFile($url=false)
		  {
		    if($url)
		      {
    		    $this->articleURL           = $url;  
    		    $this->articlePath          = str_replace(self::M()->publicRootPath, self::M()->localRootPath, $this->articleURL);
            $this->checkedArticleURL    = $this->articleURL.".pending";
            $this->checkedArticlePath   = $this->articlePath.".pending";
            $this->archiveURL           = $this->articleURL.".archive";
            $this->archivePath          = $this->articlePath.".archive";
            
            return true;
          }
          
        return false;
		  }
		  
		function abandonFile($article=false)
		  {
        if($article)
          {
            $this->setCurrentFile($article); 
          }
        else
          {
            self::V()->notifyOfError();
            return false;
          }
        
        /*
         * abandoning a file simply means that the .pending file
         * is erased, and that the `checkout` record is DELETED;
         * it is as if an edit session never happened.
         */
        
        if(self::M()->Filesystem->delete(self::V()->Editor->checkedArticlePath))
          {
            /*
             * ok, pending file deleted; delete record in `checkout`
             */
           
            $q = " DELETE FROM checkout  
                   WHERE file_path = '".mysql_real_escape_string(self::V()->Editor->articleURL)."' 
                   AND   checked_out_by = ".self::M()->USER_ADMIN_ID;
           
            if(mysql_query($q))
              {
                return true;
              }
          }
        
        /*
         * if we got here something went wrong.
         */
         
        self::V()->notifyOfError(60);
        return false; 
		  }
		  
		function checkInFile($article=false)
		  {
        if($article)
          {
            $this->setCurrentFile($article); 
          }
        else
          {
            self::V()->notifyOfError();
            return false;
          }
        
        /*
         * `Checking in` of course follows `Checking out`, and
         * supposes that the document might have been edited
         * when it was checked out. So we are going to re-save.
         * Need to rename two files:
         * 
         * 1. rename original file to an .archive file
         * 2. rename .pending file to original file name
         * 3. update linkmap
         */
        
        $archive_name = self::V()->Editor->articlePath.".archive";
        
        if(self::M()->Filesystem->rename(self::V()->Editor->articlePath, $archive_name))
          {
            /*
             * ok, original file is now archived.
             * now rename .pending file to original file name
             */
            if(self::M()->Filesystem->rename(self::V()->Editor->checkedArticlePath, self::V()->Editor->articlePath))
              {
                /*
                 * ok, files are changed; check in document
                 */
               
                $q = " UPDATE checkout 
                       SET   checked_out_by = NULL, 
                             check_in_time = NOW() 
                       WHERE file_path = '".mysql_real_escape_string(self::V()->Editor->articleURL)."' 
                       AND   checked_out_by = ".self::M()->USER_ADMIN_ID;
               
                if(mysql_query($q))
                  {
                    /*
                     * checked out, all good.  Now update linkmap.
                     */
                     
                    /*
                     * get document_id for this file, and generate a domDocument for it.
                     */
                    $q = "  SELECT id 
                            FROM document_index 
                            WHERE file_path = '".mysql_real_escape_string(self::V()->Editor->articlePath)."'";

                    $r = mysql_query($q);
                    $inf = mysql_fetch_assoc($r);
                    
                    $document_id = $inf['id'];

                    /*
                     * Update linkmap.
                     * We can expect bad html, so suppress errors.
                     */
                     
                    @$this->document->loadHTMLFile(self::V()->Editor->articleURL);     
                    self::M()->LinkMap->mapDocument($this->document,$document_id);
   
                    return true;
                  }
              }
          }
        
        /*
         * if we got here the check in failed
         */
         
        self::V()->notifyOfError(50);
        return false; 
		  }  
		  
    function checkOutFile($article=false)
      {
        if($article)
          {
            $this->setCurrentFile($article); 
          }
        else
          {
            self::V()->notifyOfError();
            return false;
          }
          
        /*
         * check if file is already checked out; if so, and not
         * checked out by this user, then notify.
         */
        $q = "  SELECT t1.id, t1.checked_out_by, t1.check_out_time, t2.full_name 
                FROM checkout as t1, admin_permissions as t2 
                WHERE t1.checked_out_by = t2.id 
                AND t1.checked_out_by > 0 
                AND t1.file_path = '".mysql_real_escape_string($this->articleURL)."'";
        $r = mysql_query($q);
        
        if($r && (mysql_num_rows($r) > 0))
          {
           $inf = mysql_fetch_assoc($r);
           
           $checked_out_by    = $inf['checked_out_by'];
           $full_name         = ucwords($inf['full_name']);
           $check_out_time    = $inf['check_out_time'];
           
            /*
             * file checked out. is it checked out by the current user?
             * ignore if so; notify of checkout if not
             */  
           if($checked_out_by != self::M()->USER_ADMIN_ID)
             {
               self::V()->notifyOfError(10,Array($this->articleURL,$full_name,$check_out_time));
               
               return false;
             }
          }
        else
          {
            /*
             * ok, not checked out. create .pending file -- take 
             * original file and copy it to a .pending file.
             *
             * NOTE that this .pending file will continue
             * to exist, forever, until this file is checked out.
             */

            
            /*
             * we do a little more work here to make sure we get rid of
             * any strange control characters, newlines, and so on, from
             * the original file, which may have pasted word, DOS content
             */
            if(file_exists($this->articlePath))
              {
                $fa = file($this->articlePath);
                $out = "";

                foreach($fa as $ln => $str)
                  {
                    if(trim($str) != "")
                      {
                        $out .= $str;  
                      }
                  }
                
                /*
                 * loading from a file; make sure that we stick to ISO encoding...
                 */
                $out = mb_convert_encoding($out, "ISO-8859-1", "UTF-8");
              }
            else
              {
                /*
                 * something very odd is going on if the original file doesn't exist!
                 * it could only be some attempt at passing external variables...
                 */
                self::V()->notifyOfError();
                return false;
              }

            /*
             * write the pending file and update dbase, if there
             * isn't already a .pending file
             */
            if(!file_exists($this->checkedArticlePath))
              {
                if(self::M()->Filesystem->write($this->checkedArticlePath,$out))
                  {
                    $q = "  INSERT INTO checkout (checked_out_by,last_checked_out_by,check_out_time,file_path) 
                            VALUES
                              (
                                ".self::M()->USER_ADMIN_ID.",
                                ".self::M()->USER_ADMIN_ID.",
                                NOW(),
                                '".mysql_real_escape_string($this->articleURL)."' 
                              )";
                    
                    /*
                     * try to insert checkout data...
                     */
                    if(!mysql_query($q))
                      {
                        /*
                         * whoops, unable to update dbase; delete .pending file,
                         * notify, and fail
                         */
                         
                         
                        self::V()->notifyOfError(20);
                        return false;
                      }
                  }
              }
          }

        return true;
      }
		  
		function getEditableNode()
		  {
		    /*
		     * This is the helper function that should be used
		     * exclusively to fetch the editable node of the 
		     * document.  Note that it will operate on
		     * the current dom at $this->document, which it is
		     * up to you to keep updated
		     */
		     
		    /*
		     * check for a id="editable_content" block
		     */
		    $tg = $this->document->getElementById('editable_content'); 
		    if($tg)
		      {
		        return $tg;  
		      }
		      
		    return false;
		  }  
		  
    function loadEditableContent()
      {
        /*
         * this is where you find the segment of entire page that
         * is editable content.  Note very specific strings we
         * are searching for: make sure special cases are handled.
         */
        try
          { 
            $fileHTML = file_get_contents($this->checkedArticleURL);

            /*
             * NOTE that we can expect badly formed html; use @
             */
            @$this->document->loadHTML($fileHTML);

            $ec = $this->getEditableNode();

            $n = $this->editableContent->importNode($ec,TRUE);
            $this->editableContent->appendChild($n);

            return true;
          }
        catch(Exception $e)
          {
            return false;
          }
      }
      
    function replaceWithEditedContent($edited_content)
      {
        /*
         * note the arbitrary use of <e_c> element, which is the
         * token for replacement
         */
         
        @$this->document->loadHTMLFile($this->checkedArticleURL);
        
        $v = $this->getEditableNode();
           
        /*
         * now replace the original <id="editable_content"> node
         * with an [empty] dummy node, which will be replaced with HTML, below.
         *
         * NOTE: the editor maintains this node when it returns
         * the edited content; if we don't replace the <editable_content>
         * node in the original, we'll keep nesting new <editable_content>
         * tags, ie: 
         * <editable_content><editable_content>...</editable_content></editable_content>
         * etc...
         */
         
        $tok = $this->document->createElement('e_c');
        $v->parentNode->replaceChild($tok, $v);
  
        //$template = $this->document->saveXML();
        $template = $this->createXMLString($this->document);
        
        /*
         * convert any special characters in edited content
         */
        $edited_content = $this->convertWordCharacters($edited_content);    

        /*
         * now replace in template
         */
        $edited_content = mb_convert_encoding($edited_content, "ISO-8859-1", "UTF-8");  
        $edited_content = mb_convert_encoding($edited_content, 'HTML-ENTITIES', "ISO-8859-1");  
        $final = str_replace("<e_c></e_c>",$edited_content,$template);
        
        return $final;
      }
      
    function writeTemplateForFile(  $folder_path="/",
                                    $article_name="error.html",
                                    $page_title="",
                                    $meta_description="",
                                    $meta_keywords="")
      {
        /*
         * this is where we will write the template to.
         */
        $target_file = $folder_path.'/'.$article_name;
        
        /*
         * NOTE how a token is placed in editable content of 
         * template file, and then is replaced with the 
         * final value of this variable (default editable content);
         */
        $editable_content = '';

        /*
         * Is there a usable _template.html file? the check we 
         * do to see if this is a usable template is simply to 
         * check if we can get an editable node.
         */
        @$this->document->loadHTMLFile($folder_path.'/_template.html');
        $editable_node = $this->getEditableNode();
          
        /*
         * if there is not usable .html in this directory from which
         * derive a template use default template instead
         */
        if(!$editable_node)
          {
            $this->document->loadHTMLFile(self::M()->defaultTemplate);
            $editable_node = $this->getEditableNode();

            if(!$editable_node)
              {
                /*
                 * Default template not found or malformed! 
                 * Inform and exit
                 */  
                self::V()->notifyOfError(110);
                return false;
              }
          }

        /*
         * We need to do a few things to make a template with this file:
         *
         * 1. do any work we need to do on editable content node
         * 2. Create the file (write)
         * 3. Update document index (see Documents->update())
         */

        $template = $this->createXMLString($this->document);
        
        /*
         * write new template file
         */
        if(!self::M()->Filesystem->write($target_file,$template))
          {
            /*
             * can't write template file
             */
            self::V()->notifyOfError(100);
            return false;
          }
          
        /*
         * ok, file created successfully.  Update document index
         */
        self::M()->Documents->update($target_file,$page_title,$meta_description,$meta_keywords);
        
        return true;
      }
      
    function storeNodes()
      {
        $cnt = 0;
        $tag_array = self::M()->tagsExcludedFromEditable;
        
        foreach($tag_array as $k => $tag_n)
          {
            while($elem = $this->editableContent->getElementsByTagName($tag_n)->item(0)) 
              { 
                /*
                 * save the <$tag_n> node.  Unfortunately, this recreation of 
                 * a dom->html business is necessary to maintain proper
                 * format for javascript declarations, etc. Just running:
                 * $this->editableContent->saveXML($elem)
                 * creates cdata declarations, changes tag closing on <script>, etc.
                 */
                $tempdoc = new DOMDocument();
                $tempdoc->appendChild($tempdoc->importNode($elem, true));
                //$nstr = $tempdoc->saveXML();
                $nstr = $this->createXMLString($tempdoc);

                /*
                 * note how we are storing nodes in the session
                 */
                $_SESSION['stored_nodes'][$cnt] = $nstr;
                
                /*
                 * replace script fragment with token; is re-inserted
                 * when file is rewritten (see writePostedEdits())
                 */
                $tok = $this->editableContent->createElement('stored_'.$cnt);
                
                $scr = $elem->parentNode->replaceChild($tok,$elem);
                
                ++$cnt;
              }
          }
      }
    
    function getEditableContentAsString()
      {
        $con = $this->createXMLString($this->editableContent);
        $con = $this->convertWordCharacters($con);     
        
        return $con;
      }
      
    function getStoredNodes()
      {
        if(isset($_SESSION['stored_nodes']) && ($_SESSION['stored_nodes'] != ""))
          {
            return $_SESSION['stored_nodes']; 
          }
        else
          {
            unset($_SESSION['stored_nodes']);
            
            return Array();  
          }
      }  
      
    function createXMLString($nde)
      {
        /*
         * we are using the saveXML() method throughout.  This will do things
         * like creating an xml declaration at the top of the page, and isolating
         * <script> and <style> tag contents with <CDATA> wrappers.  This is 
         * good xml practice.  However, browsers take issue with that, and 
         * regardless, editing/previewing/editing/previewing, back and forth,
         * is multiplying these tags, nesting copies, making a mess. So we're
         * going to write our own function to save as XML, stripping out that
         * stuff for our purposes.  Unfortunate, but just using saveHTML() instead
         * loses all the other yummy xml (xhtml) stuff we really do need.
         *
         * - also will strip `align=""` from tags, which is something the
         *   editor sets on its own when dealing with images, and which is not
         *   acceptable in an xhtml document.
         */
         
        $out = $nde->saveXML();
         
        /*
         * bug? for some reason as i switch back/forth between edit/preview i'm getting this
         * duplication... add to stripper.
         */
         
        $out = str_replace( 'xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml"',
                            'xmlns="http://www.w3.org/1999/xhtml"', $out);
        
        /*
         * what we strip...
         */
        $strips = Array
          (
            '<?xml version="1.0" encoding="iso-8859-1" standalone="yes"?>',
            '<![CDATA[',
            ']]>',
            'align=""'
          );
          
        $out = str_replace($strips,"",$out);

        return $out;     
      }  
      
    function convertWordCharacters($str)
      {
        /*
         * deal with Word smartquotes, etc.
         * âŽ¯
         */
        
        $mCs = Array(
        							'…',
        							'–',
        							'—',
        							'“', 	
        							'’', 	
        							'�',
        							'é',
        							'�'
        						);
        						
        $mCr = Array(
        							"...",
        							"-",
        							"&mdash;",
        							"\"",
        							"'",
        							"\"",
        							"&eacute;",
        							"&mdash;"
        						);
        
        return str_replace($mCs,$mCr,$str);
      }

    function writePostedEdits($tidy=true)
      {
        self::M()->Request->validate("post");
        
        $article_path       = self::M()->Request->post['article'];
        $edited_content     = self::M()->Request->post['txtContent'];
        
        $stored_nodes       = $this->getStoredNodes();

        /*
         * initialize with current file to edit
         */
        self::V()->Editor->setCurrentFile($article_path);

        /*
         * replace stored nodes
         */
        
        foreach($stored_nodes as $k => $node)
          {
            /*
             * just do straight string replacement.  for stored node 3, the 
             * tag will will look like: <stored_3></stored_3>  (etc.)
             */

            $tag = "<stored_$k></stored_$k>";

            $edited_content = str_replace($tag,$node,$edited_content);
          }

        /*
         * tidy up edited content if applicable
         */
        if($tidy)
          {                
            $edited_content = self::M()->Documents->tidyHTML($edited_content);
          }

        /*
         * get original and snip out editable region and replace
         * with edited content
         */
        $ed = self::V()->Editor->replaceWithEditedContent($edited_content);

        /*
         * update .pending file
         */
        if(!self::M()->Filesystem->write($this->checkedArticlePath,$ed))  
          {
            self::V()->notifyOfError(40);
            return false;
          }
        
        /*
         * return edited content
         */
        return $ed;
      }
	}	

?>