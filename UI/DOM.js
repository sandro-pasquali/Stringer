
function DOM()
  {
    this.DOM = function(){};
  	
  	this.scrollTop = function(w,d)
		  {
		    var win = w || window;
		    var doc = d || document;
		    
			  var top = 0;
			 	if(win.innerHeight)
			    {
			      top = win.pageYOffset;
				  }
				else if(doc.documentElement && doc.documentElement.scrollTop)
				  {
				    top = doc.documentElement.scrollTop;
				  }
				else if(doc.body)
				  {
				    top = doc.body.scrollTop;
				  }
				return(top);
		  };
			  
	  this.scrollLeft = function(w,d)
		  {
		    var win = w || window;
		    var doc = d || document;
		   
		    var left = 0;
			  if(win.innerWidth)
				  {
				    left = win.pageXOffset;
				  }
			  else if(doc.documentElement && doc.documentElement.scrollLeft)
			    {
			      left = doc.documentElement.scrollLeft;
			    }
			  else if(doc.body)
			    {
			      left = doc.body.scrollLeft;
			    }
			  return(left);
		  };
					
	  this.getWindowSize = function(w,d) 
		  {
		  	var win = w || window;
		 	  var doc = d || document;
		    var wd = 0;
		    var ht = 0;
				    
		    if(typeof(win.innerWidth)=='number' ) 
			    {
			      //Non-IE
			      wd = win.innerWidth;
			      ht = win.innerHeight;
			    } 
			  else if(doc.documentElement && (doc.documentElement.clientWidth || doc.documentElement.clientHeight )) 
			    {
			      //IE 6+ in 'standards compliant mode'
			      wd = doc.documentElement.clientWidth;
			      ht = doc.documentElement.clientHeight;
			    }
			  else if(doc.body && (doc.body.clientWidth || doc.body.clientHeight )) 
			 	  {
				    //IE 4 compatible
				    wd = doc.body.clientWidth;
				    ht = doc.body.clientHeight;
				  }
		    return({width:wd,height:ht}); 
		  };
							
    this.findPos = function(obj) 
      {
	      var curleft = curtop = 0;
	      if(obj.offsetParent) 
	        {
		        curleft = obj.offsetLeft
		        curtop = obj.offsetTop
		        
		        while(obj = obj.offsetParent) 
		          {
			          curleft += obj.offsetLeft
			          curtop += obj.offsetTop
		          }
	        }
	      return [curleft,curtop];
      }
      			
	  this.getElWidth = function(el)
	    {
	    	try
	    	  {
	    	  	return(el.offsetWidth);
	    	  }
	    	catch(e){}
	    	
	    	return(false);
	    };
		  
	  this.getElHeight = function(el)
	    {
	    	try
	    	  {
	    	  	return(el.offsetHeight);
	    	  }
	    	catch(e){}
	    	
	    	return(false);
	    };
		  
		this.windowHeight = function()
		  {
		  	return(this.getWindowSize(window,document).height);
		  };
			  
		this.windowWidth = function()
			{
			 	return(this.getWindowSize(window,document).width);
			 };
			 
    this.getStyle = function(oElm, strCssRule)
      {
        var strValue = "";
        if(document.defaultView && document.defaultView.getComputedStyle)
          {
            var css = document.defaultView.getComputedStyle(oElm, null);
            strValue = css ? css.getPropertyValue(strCssRule) : null;
          }
        else if(oElm.currentStyle)
          {
            strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
                return p1.toUpperCase();
            });
            strValue = oElm.currentStyle[strCssRule];
          }
        return strValue;
      };
				  
		this.disableSelect = function(d)
		  {
		  	var doc = d || document;
		  	
			  doc.onselectstart = function() { return false; };
				doc.onmousedown = function(e) 
				  { 
				    var ev = e || window.event;
				    var tg = (e) ? ev.target : ev.srcElement;
				    
				    if(tg.nodeName.toUpperCase() !== 'INPUT')
				      {
				  	    return false; 
				  	  }
				  };		  	
		  };
  };