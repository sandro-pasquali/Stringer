<?php

/******************************************************************
Projectname:   Automatic Keyword Generator
Version:       0.3
Author:        Ver Pangonilo <smp_AT_itsp.info>
Last modified: 12 June 2007
Copyright (C): 2006 Ver Pangonilo, All Rights Reserved

* GNU General Public License (Version 2, June 1991)
*
* This program is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will
* be useful, but WITHOUT ANY WARRANTY; without even the
* implied warranty of MERCHANTABILITY or FITNESS FOR A
* PARTICULAR PURPOSE. See the GNU General Public License
* for more details.

Description:
This class can generates automatically META Keywords for your
web pages based on the contents of your articles. This will
eliminate the tedious process of thinking what will be the best
keywords that suits your article. The basis of the keyword
generation is the number of iterations any word or phrase
occured within an article.

This automatic keyword generator will create single words,
two word phrase and three word phrases. Single words will be
filtered from a common words list.

Change Log:
===========
0.2 Ver Pangonilo - 22 July 2005
================================
Added user configurable parameters and commented codes
for easier end user understanding.
						
0.3 Vasilich  (vasilich_AT_grafin.kiev.ua) - 26 July 2006
=========================================================
Added encoding parameter to work with UTF texts, min number 
of the word/phrase occurrences, 

0.3 Sandro Pasquali  (unifiedapplications@gmail.com) - 12 June 2007
=========================================================
Reformatted into object context, removing $params argument in constructor,
some addition/modifications to comments, and general cleaning up.

******************************************************************/

class AutoKeyword extends Assembly
  {
  	function __construct($encoding="iso-8859-1")
    	{
      	/*
      	 * the site contents
      	 */
      	$this->contents = '';
    
      	$this->keywords = '';
      	
      	/*
      	 * minimum phrase length for inclusion into the 3 word
      	 * phrase metakeys
      	 */
      	$this->phrase3WordLengthMin = 1;

    		$this->encoding = $encoding;
    		mb_internal_encoding($this->encoding);
    		
    		/*
    		 * content to suggest keywords for
    		 * see get_keywords();
    		 */
    		$this->contents = "";
    
      	/*
      	 * minimum length of single words
      	 */
    		$this->wordLengthMin = 5;
    		
    		/*
    		 * minimum occurrence of single words
    		 */
    		$this->wordOccuredMin = 3;
    
      	/*
      	 * minimum length of words for 2 word phrases
      	 */
    		$this->word2WordPhraseLengthMin = 3;
    		
    		/*
    		 * minimum length of 2 word phrases
    		 */
    		$this->phrase2WordLengthMin = 10;
    		
    		/*
    		 * minimum occurrences of 2 word phrases
    		 */
    		$this->phrase2WordLengthMinOccur = 2;
    
      	/*
      	 * minimum length of words for 3 word phrases
      	 */
    		$this->word3WordPhraseLengthMin = 3;
    		
    		/*
    		 * minimum length of 3 word phrases
    		 */
    		$this->phrase3WordLengthMin = 10;
    		
    		/*
    		 * minimum occurrences of 3 word phrase
    		 */
    		$this->phrase3WordLengthMinOccur = 2;
    	}
  
  	function get_keywords($dom_content=false,$add_content="")
    	{
        try
          {
            /*
             * lose unwanted tags
             */
            $tag_array = self::M()->tagsExcludedFromEditable;
            foreach($tag_array as $k => $tag_n)
              {
                while($elem = $dom_content->getElementsByTagName($tag_n)->item(0)) 
                  {
                    $elem->parentNode->removeChild($elem);
                  }
              } 
            
            /*
             * set content
             */
            $ctnt = $dom_content->textContent." $add_content";
            $this->contents = $this->replace_chars($ctnt);
            
            /*
             * get keywords, and do some transoformations.
             */
        		$this->keywords = $this->parse_words().$this->parse_2words(); //.$this->parse_3words();
        		$this->keywords = trim($this->keywords);
        		
        		/*
        		 * replace duplicates; array words, removed duplicates, toString()
        		 */
            $str_arr = explode(" ", $this->keywords);
            $str_arr = array_unique($str_arr);
            $this->keywords = implode(" ", $str_arr);
        		
        		return $this->keywords;
        		
          }
        catch(Exception $e)
          {
            return false;
          }
    	}
    
  	/*
  	 * turn the site contents into an array
  	 * then replace common html tags.
  	 */
  	function replace_chars($content)
    	{
    		$content = mb_strtolower($content);
    		$content = strip_tags($content);
    
    		$punctuations = array(',', ')', '(', '.', "'", '"',
    		'<', '>', ';', '!', '?', '/', '-',
    		'_', '[', ']', ':', '+', '=', '#',
    		'$', '&quot;', '&copy;', '&gt;', '&lt;',
    		chr(10), chr(13), chr(9));
    
    		$content = str_replace($punctuations, " ", $content);
    		
    		/*
    		 * replace multiple gaps
    		 */
    		$content = preg_replace('/ {2,}/si', " ", $content);
    
    		return $content;
    	}
  
  	/*
  	 * single words META KEYWORDS
  	 */
  	function parse_words()
    	{
    		$common = array("able", "about", "above", "act", "add", "afraid", "after", "again", "against", "age", "ago", "agree", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "amount", "an", "and", "anger", "angry", "animal", "another", "answer", "any", "appear", "apple", "are", "arrive", "arm", "arms", "around", "arrive", "as", "ask", "at", "attempt", "aunt", "away", "back", "bad", "bag", "bay", "be", "became", "because", "become", "been", "before", "began", "begin", "behind", "being", "bell", "belong", "below", "beside", "best", "better", "between", "beyond", "big", "body", "bone", "born", "borrow", "both", "bottom", "box", "boy", "break", "bring", "brought", "bug", "built", "busy", "but", "buy", "by", "call", "came", "can", "cause", "choose", "close", "close", "consider", "come", "consider", "considerable", "contain", "continue", "could", "cry", "cut", "dare", "dark", "deal", "dear", "decide", "deep", "did", "die", "do", "does", "dog", "done", "doubt", "down", "during", "each", "ear", "early", "eat", "effort", "either", "else", "end", "enjoy", "enough", "enter", "even", "ever", "every", "except", "expect", "explain", "fail", "fall", "far", "fat", "favor", "fear", "feel", "feet", "fell", "felt", "few", "fill", "find", "fit", "fly", "follow", "for", "forever", "forget", "from", "front", "gave", "get", "gives", "goes", "gone", "good", "got", "gray", "great", "green", "grew", "grow", "guess", "had", "half", "hang", "happen", "has", "hat", "have", "he", "hear", "heard", "held", "hello", "help", "her", "here", "hers", "high", "hill", "him", "his", "hit", "hold", "hot", "how", "however", "I", "if", "ill", "in", "indeed", "instead", "into", "iron", "is", "it", "its", "just", "keep", "kept", "knew", "know", "known", "late", "least", "led", "left", "lend", "less", "let", "like", "likely", "likr", "lone", "long", "look", "lot", "make", "many", "may", "me", "mean", "met", "might", "mile", "mine", "moon", "more", "most", "move", "much", "must", "my", "near", "nearly", "necessary", "neither", "never", "next", "no", "none", "nor", "not", "note", "nothing", "now", "number", "of", "off", "often", "oh", "on", "once", "only", "or", "other", "ought", "our", "out", "please", "prepare", "probable", "pull", "pure", "push", "put", "raise", "ran", "rather", "reach", "realize", "reply", "require", "rest", "run", "said", "same", "sat", "saw", "say", "see", "seem", "seen", "self", "sell", "sent", "separate", "set", "shall", "she", "should", "side", "sign", "since", "so", "sold", "some", "soon", "sorry", "stay", "step", "stick", "still", "stood", "such", "sudden", "suppose", "take", "taken", "talk", "tall", "tell", "ten", "than", "thank", "that", "the", "their", "them", "then", "there", "therefore", "these", "they", "this", "those", "though", "through", "till", "to", "today", "told", "tomorrow", "too", "took", "tore", "tought", "toward", "tried", "tries", "trust", "try", "turn", "two", "under", "until", "up", "upon", "us", "use", "usual", "various", "verb", "very", "visit", "want", "was", "we", "well", "went", "were", "what", "when", "where", "whether", "which", "while", "white", "who", "whom", "whose", "why", "will", "with", "within", "without", "would", "yes", "yet", "you", "young", "your", "br", "img", "p","lt", "gt", "quot", "copy");
    		
    		/* 
    		 * create an array out of the site contents
    		 */
    		$s = split(" ", $this->contents);
    		
    		$k = array();
    		foreach($s as $key => $val) 
    		  {
      			/* 
      			 * delete single or two letter words and
      			 * Add it to the list if the word is not
      			 * contained in the common words list.
      			 */
      			if( (mb_strlen(trim($val)) >= $this->wordLengthMin)  
      			    && !in_array(trim($val), $common)  
      			    && !is_numeric(trim($val))) 
      			  {
      				  $k[] = trim($val);
      			  }
    		  }
    		  
    		/*
    		 * count the words
    		 */
    		$k = array_count_values($k);
    		
    		/*
    		 * sort the words from highest count to the lowest.
    		 */
    		$occur_filtered = $this->occure_filter($k, $this->wordOccuredMin);
    		arsort($occur_filtered);
    
    		$imploded = $this->implode(" ", $occur_filtered);

    		unset($k);
    		unset($s);
    
    		return $imploded;
    	}
  
  	function parse_2words()
    	{
    		/* 
    		 * create an array out of the site contents
    		 */
    		$x = split(" ", $this->contents);

    		//$y = array();
    		for ($i=0; $i < count($x)-1; $i++) 
      		{
      			/*
      			 * delete phrases < 5 characters
      			 */
      			if( (mb_strlen(trim($x[$i])) >= $this->word2WordPhraseLengthMin ) 
      			    && (mb_strlen(trim($x[$i+1])) >= $this->word2WordPhraseLengthMin) )
        			{
        				$y[] = trim($x[$i])." ".trim($x[$i+1]);
        			}
      		}
    
    		/*
    		 * count the 2 word phrases
    		 */
    		$y = array_count_values($y);
    
    		$occur_filtered = $this->occure_filter($y, $this->phrase2WordLengthMinOccur);
    		
    		/*
    		 * sort the words from highest count to the lowest.
    		 */
    		arsort($occur_filtered);
    
    		$imploded = $this->implode(" ", $occur_filtered);

    		unset($y);
    		unset($x);
    
    		return $imploded;
    	}
  
  	function parse_3words()
    	{
    		/*
    		 * create an array out of the site contents
    		 */
    		$a = split(" ", $this->contents);

    		$b = array();
    		for ($i=0; $i < count($a)-2; $i++) 
      		{
      			/*
      			 * delete phrases < 5 characters
      			 */
      			if( (mb_strlen(trim($a[$i])) >= $this->word3WordPhraseLengthMin) 
      			    && (mb_strlen(trim($a[$i+1])) > $this->word3WordPhraseLengthMin) 
      			    && (mb_strlen(trim($a[$i+2])) > $this->word3WordPhraseLengthMin) 
      			    && (mb_strlen(trim($a[$i]).trim($a[$i+1]).trim($a[$i+2])) > $this->phrase3WordLengthMin) )
        			{
        				$b[] = trim($a[$i])." ".trim($a[$i+1])." ".trim($a[$i+2]);
        			}
      		}
    
    		/*
    		 * count the 3 word phrases
    		 */
    		$b = array_count_values($b);
    		
    		/*
    		 * sort the words from highest count to the lowest.
    		 */
    		$occur_filtered = $this->occure_filter($b, $this->phrase3WordLengthMinOccur);
    		arsort($occur_filtered);
    
    		$imploded = $this->implode(" ", $occur_filtered);

    		unset($a);
    		unset($b);
    
    		return $imploded;
    	}
  
  	function occure_filter($array_count_values, $min_occur)
    	{
    		$occur_filtered = array();
    		foreach ($array_count_values as $word => $occured) 
      		{
      			if ($occured >= $min_occur) 
        			{
        				$occur_filtered[$word] = $occured;
        			}
      		}
    
    		return $occur_filtered;
    	}
  
  	function implode($gule, $array)
    	{
    		$c = "";
    		foreach($array as $key=>$val) 
      		{
      			@$c .= $key.$gule;
      		}
    		return $c;
    	}
  }
?>
