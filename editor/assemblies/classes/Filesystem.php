<?php

class Filesystem extends Assembly
	{		  
	  protected function __construct() 
		  {
		  }
		  
    public function getFileList($curDir) 
      {                        
        $files = glob($curDir.'/*.*');     
        $fout = Array();
        
        foreach($files as $file)
          {
            $fout[] = Array('path' => $file);
          }

        return $fout;
      }
		  
    public function getRecursiveFolderList($curDir,$currentA=false) 
      {       
        /*
         * on initial call, store base path info;
         */      
        if(!$currentA)
          {
            $currentA = Array
              (
                'path'  => $curDir  
              );
          }
                       
        $dirs = glob($curDir . '/*', GLOB_ONLYDIR);     
        
        $cur = 0;
        foreach($dirs as $dir)
          {
            $currentA[$cur]['path'] = $dir;
            $currentA[$cur] = $this->getRecursiveFolderList($dir,$currentA[$cur]);
                
            ++$cur;
          }

        return $currentA;
      }
      
    public function getRecursiveFileList($curDir,$currentA=false) 
      {   
        /*
         * on initial call, store base path info
         */      
        if(!$currentA)
          {
            $currentA = Array
              (
                'path'  => $curDir  
              );
          }
                     
        $dirs = glob($curDir . '/*');     
        
        $cur = 0;
        foreach($dirs as $dir)
          {
            $currentA[$cur]['path'] = $dir;
            $currentA[$cur] = $this->getRecursiveFileList($dir,$currentA[$cur]);
                
            ++$cur;
          }

        return $currentA;
      }
      
    public function getRecursiveFlatFileList($curDir,$currentA=false) 
      { 
        /*
         * on initial, set return Array, and send start folder
         * for processing (as opposed to GLOB of folder).
         */
        if($currentA === false)
          {
            $currentA = Array();
            $dirs = Array($curDir);
          }
        else
          {                          
            $dirs = glob($curDir . '/*', GLOB_ONLYDIR);     
          }
          
        foreach($dirs as $dir)
          {
            $files = $this->getFileList($dir);

            /*
             * $files will be an array of arrays, in this structure:
             * [[0]['path']=>'foo.html',[1]['path']=>'bar.html'...
             *
             * Translate that into a simple indexed list.
             */
            foreach($files as $k => $v)
              {
                array_push($currentA,$v['path']);
              }

            $currentA = $this->getRecursiveFlatFileList($dir,$currentA);
          }

        return $currentA;
      }
      
    public function renameFile($old_file=false, $new_file_name=false)
      {
        if(!$old_file || !$new_file_name)
          {
            return false;  
          }
        
        /*
         * need to check if anyone has checked this file out... if
         * so, we cannot change the filename
         */        
        $q = "  SELECT id 
                FROM checkout 
                WHERE file_path = '".mysql_real_escape_string($old_file)."' 
                AND checked_out_by IS NOT NULL 
                LIMIT 1";
        $r = mysql_query($q);
        
        if(mysql_num_rows($r) > 0)
          {
            /*
             * file checked out...
             */
            self::V()->notifyOfError(330);
          }
          
        /*
         * ok. Now we have to update the file paths stored in the following tables:
         * 1. checkout        [ http://www.site.com/files/index.html ]
         * 2. document_index  [ /usr/local/www/files/index.html ]
         * 3. linkrefs        [ files/index.html ]
         *
         * Each of these use a different path, as noted.
         */
         
        $checkout_path_s        = $old_file;
        $document_index_path_s  = str_replace(self::M()->publicRootPath,self::M()->localRootPath,$old_file);
        $linkrefs_s             = str_replace(self::M()->publicRootPath,"",$old_file);
        
        $checkout_path_r        = $new_file_name;
        $document_index_path_r  = str_replace(self::M()->publicRootPath,self::M()->localRootPath,$new_file_name); 
        $linkrefs_r             = str_replace(self::M()->publicRootPath,"",$new_file_name);
        
        /*
         * linkMap updates are a bit more involved...try that, then continue.
         */
        if(self::M()->LinkMap->handleFileRename($linkrefs_s,$linkrefs_r))
          {   
            /*
             * ok, update checkout and document_index by seeking and replacing old/new
             */
            $q = " UPDATE checkout 
                   SET file_path = '".mysql_real_escape_string($checkout_path_r)."' 
                   WHERE file_path = '".mysql_real_escape_string($checkout_path_s)."' ";
            $r = mysql_query($q);
            
            $q = " UPDATE document_index  
                   SET file_path = '".mysql_real_escape_string($document_index_path_r)."'  
                   WHERE file_path = '".mysql_real_escape_string($document_index_path_s)."' ";
            $r = mysql_query($q);
          }
        else
          {
            self::V()->notifyOfError(350,Array($new_file_name));
          }
          
        /*
         * ok. rename file. note that we use the local path version.
         */

        if(!$this->rename($document_index_path_s,$document_index_path_r))
          {
            self::V()->notifyOfError(360,Array($new_file_name));
          }
          
        return true;
      }  
      
    public function createFolder($cur_folder=false, $new_folder_name=false)
      {
        if(!$cur_folder || !$new_folder_name)
          {
            self::V()->notifyOfError(300,Array($new_folder_name));
          }
          
        $new_path = self::M()->localRootPath.$cur_folder.'/'.$new_folder_name;
        
        if(!$this->mkdir($new_path))
          {
            self::V()->notifyOfError(300,Array($new_folder_name));
          }
        
        return true;
      }
      
    public function deleteFolder($folder=false)
      {
        /*
         * NOTE: folder path should NOT have a trailing slash!
         */
         
        /*
         * Cannot delete the root folder...
         */
        if($folder.'/' == self::M()->localFilesPath)
          {
            self::V()->notifyOfError(340);
          }
        
        /*
         * get flat list of all files in dir/subdirs
         */
        $delFiles = self::M()->Filesystem->getRecursiveFlatFileList($folder);
        
        /*
         * Before we delete the folders, we need to delete all the html
         * files in subfolders, which need special treatment (dbase, etc).
         * Other files will be cleaned afterwards, when the folders and
         * their contents are completely wiped.
         */
         
        foreach($delFiles as $k => $file)
          {
            /*
             * .html ?
             */
            if(substr($file,-5) == '.html')
              {
                /*
                 * the ->delete() function expects a public path (http)
                 */
                $file = str_replace(self::M()->localRootPath,self::M()->publicRootPath,$file);
                
                self::M()->Documents->delete($file);  
              }
          }
          
        /*
         * ok, files deleted ... now delete the folders and any files remaining
         */
         
        if($this->recursiveFolderDelete($folder))
          {
            return true;
          }

        return false;
      }
      
    private function recursiveFolderDelete($dir=false)
      {
        /*
         * NOTE: folder path should NOT have a trailing slash!
         */
          
        if(($dir === false) || ($dir == '') || ($dir == '/'))
          {
            return false;  
          }
          
        if(!($d = @dir($dir)))
          {
            return false;  
          }
        
        while(($entry = $d->read()) !== false)
          {
            if($entry == '.' || $entry == '..')
              {
                continue;
              }
            $entry = $dir . '/' . $entry;

            if(is_dir($entry))
              {
                if(!$this->recursiveFolderDelete($entry))
                  {
                    return false;
                  }
                continue;
              }

            if(!unlink($entry))
              {
                $d->close();
                return false;
              }
          }
           
         $d->close(); 
         
         rmdir($dir);
           
         return true;
      }
      
    public function renameFolder($cur_folder=false, $new_folder_name=false)
      {
        if($cur_folder == $new_folder_name)
          {
            /*
             * same name? act as if operation was done.
             */
            return true; 
          }
          
        if(!$cur_folder || !$new_folder_name)
          {
            self::V()->notifyOfError(310,Array($new_folder_name));
          }
          
        $old_path =  self::M()->localRootPath.$cur_folder;
        
        /*
         * We cannot allow the root file folder to be renamed.
         * Note how we're adding a trailing slash.
         */
        if($old_path.'/' == self::M()->localFilesPath)
          {
            self::V()->notifyOfError(340);
          }
        
        /*
         * It is important here to check if any file underneath
         * this folder is being edited.  If so, we must abort, and notify.
         */
        
        $chkPath = self::M()->publicRootPath.$cur_folder.'/';
        
        $q = "  SELECT id 
                FROM checkout 
                WHERE file_path LIKE '".mysql_real_escape_string($chkPath)."%' 
                AND checked_out_by IS NOT NULL 
                LIMIT 1";
        $r = mysql_query($q);
        
        if(mysql_num_rows($r) > 0)
          {
            /*
             * there is a file under this folder that is checked out.
             */  
            self::V()->notifyOfError(330);
          }

        /*
         * ok. Now we have to update the file paths stored in the following tables:
         * 1. checkout        [ http://www.site.com/files/ ]
         * 2. document_index  [ /usr/local/www/files/ ]
         * 3. linkrefs        [ files/ ]
         *
         * Each of these use a different path, as noted.
         */
   
        
        $checkout_path_s        = self::M()->publicRootPath.$cur_folder.'/';
        $document_index_path_s  = self::M()->localRootPath.$cur_folder.'/';
        
        $checkout_path_r        = self::M()->publicRootPath.$new_folder_name.'/';
        $document_index_path_r  = self::M()->localRootPath.$new_folder_name.'/';   
        
        /*
         * linkMap updates are a bit more involved...try that, then continue.
         */
        if(self::M()->LinkMap->handleFolderRename($cur_folder.'/',$new_folder_name.'/'))
          {   
            /*
             * ok, update checkout and document_index by seeking and replacing old/new
             */
            $q = " UPDATE checkout 
                   SET file_path = 
                   REPLACE(file_path, '".mysql_real_escape_string($checkout_path_s)."', '".mysql_real_escape_string($checkout_path_r)."') 
                   WHERE file_path LIKE '".mysql_real_escape_string($checkout_path_s)."%' ";
            $r = mysql_query($q);
            
            $q = " UPDATE document_index  
                   SET file_path = 
                   REPLACE(file_path, '".mysql_real_escape_string($document_index_path_s)."', '".mysql_real_escape_string($document_index_path_r)."') 
                   WHERE file_path LIKE '".mysql_real_escape_string($document_index_path_s)."%' ";
            $r = mysql_query($q);
            
            /*
             * ok. rename folder
             */
      
            if(!$this->rename($old_path,self::M()->localRootPath.$new_folder_name))
              {
                self::V()->notifyOfError(310,Array($new_folder_name));
              }
          }
        else
          {
            self::V()->notifyOfError(320,Array($new_folder_name));
          }
 
        return true;
      }
       
    public function write($file="",$content="",$mode="w+")
      {
        if(!$handle = fopen($file, $mode)) 
          {
            return false;
          }

        if(fwrite($handle,$content) === FALSE) 
          {
            return false;
          }
          
        return true; 
      } 
      
    public function copy($source=false,$dest=false)
      {
        if($source && file_exists($source) && $dest && ($dest != ""))
          {
            return @copy($source,$dest);  
          }
          
        return false;
      }
      
    public function rename($old=false,$new=false)
      {
        if($old && $new)
          {
            return @rename($old,$new);  
          }  
        
        return false;
      }
      
    public function delete($file=false)
      {
        if($file)
          {
            return @unlink($file);  
          }  
        
        return false;
      }
      
    public function mkdir($dir=false)
      {
        if($dir)
          {
            return @mkdir($dir,0755);
          }  
        
        return false;
      }
	}	



?>