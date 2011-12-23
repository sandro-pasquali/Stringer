<?php

class X
  {
    public function __construct()
      {
        require("../editor/assemblies/classes/_config.php");
        
        $this->menu = "";
      }  
      
    public function fetchMenu()
      {
        /*
         * note how we're stripping the trailing slash from files path
         */
        $folders = $this->getRecursiveFolderList(substr($this->localFilesPath,0,-1));
        
        foreach($folders as $k => $r)
          {
            $this->walkList($r);
          }
          
        $this->menu = preg_replace("(\r\n|\n|\r)", "", $this->menu);
        $this->menu = str_replace("'","&#39;", $this->menu); 
        
        return $this->menu;
      }
      
    public function getRecursiveFolderList($curDir,$currentA=false) 
      {       
                       
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
      
    public function fetchSidebar()
      {
        $re = file_get_contents($this->templateSidebar);
        
        $re = preg_replace("(\r\n|\n|\r)", "", $re);
        $re = str_replace("'","&#39;",$re); 
        
        return $re;
      }
      
    public function getLinkInfo($li)
      {
        $path = $this->publicRootPath.strstr($li['path'],'files/');
        $disp = substr(strrchr($path,'/'),1);
        
        /*
         * Note that we are linking to folders, and those folders
         * must have an index.html file; after generating name, add extension.
         */
        $path .= "/index.html";
        
        $disp = strtoupper(str_replace("_"," ",$disp));
        
        $ret = Array
          (
            0   => $path,  
            1   => $disp
          );
          
        return $ret;
      }
      
    function walkList($r,$child=false)
      {
        list($path,$display) = $this->getLinkInfo($r);
        
        $c    = count($r);
    
        if($child === false)
          {
            $this->menu .= "<UL>";
          }
    
        $this->menu .= "<li><a href=\"$path\">$display</a>";
        $this->menu .= "<ul>";
        
        if($c > 1)
          {
            for($i=0; $i < $c; $i++)
              {
                if(isset($r[$i]))
                  {
                    if(count($r[$i]) > 1)
                      {                                               
                        $this->walkList($r[$i],true);    
                      }
                    else
                      {
                        list($path,$display) = $this->getLinkInfo($r[$i]);
                        
                        $this->menu .= '<li><a href="'.$path.'">'.$display.'</a></li>';
                      }
                  }
              }
          }
          
        $this->menu .= "</ul></li>";
        
        if($child === false)
          {
            $this->menu .= "</UL>";
          }
      }
  }

$X = new X();

$menu     = $X->fetchMenu();
$sidebar  = $X->fetchSidebar();

?>

window.onload = function()
  {
    var hT    = document.getElementsByTagName('head')[0]; 
    var css   = document.createElement('link'); 
    css.id    = 'global_stylesheet'; 
    css.rel   = 'stylesheet'; 
    css.type  = 'text/css';
    css.href  = '<?php echo $X->globalStyleSheet; ?>'; 
    css.media = 'screen';
    
    hT.appendChild(css);  
  
    document.getElementById('menu').innerHTML = '<?php echo $menu; ?>';
    document.getElementById('sidebar').innerHTML = '<?php echo $sidebar ?>';
  }

