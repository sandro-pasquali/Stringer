<?php

require("assemblies/EditorAssembly.php");

/*
 * We're previewing the sidebar. So we just load a default
 * template, replacing `sidebar` node with what we've been
 * sent.  Note as well that we snip out the <script controller.php>
 * which will load the menus and the CURRENT right panel,
 * overwriting the edited version.  This controller also loads
 * the global css, so we'll have to replace that. Note as well that  
 * since we aren't going to see the menu loaded normally, there 
 * will be a faulty layout; so we need to fill the menu container 
 * with some dummy menu info. Sigh.
 */

$doc = new DOMDocument();
@$doc->loadHTMLFile($_M->defaultTemplate);   
      
/*
 * snip out any <script> 
 */
while($script = $doc->getElementsByTagName('script')->item(0)) 
  {
    $script->parentNode->removeChild($script);
  } 
  
/*
 * add dummy menu
 */
$dM = $doc->getElementById('menu');
$f = $doc->createDocumentFragment();
$f->appendXML('<ul><li><a href="#">MENU HERE</a></li></ul>');

$dM->appendChild($f);

/*
 * add global stylesheet
 */
$head = $doc->getElementsByTagName('head')->item(0);

$css = $doc->createElement('link');
$css->setAttribute('id','global_stylesheet');
$css->setAttribute('rel', 'stylesheet'); 
$css->setAttribute('type', 'text/css');
$css->setAttribute('href', $_M->globalStyleSheet); 
$css->setAttribute('media', 'screen');

$head->appendChild($css);
      
/*
 * take the edited content and import it into document
 */ 
      
$rsN = $doc->getElementById('sidebar');
$f = $doc->createDocumentFragment();
$f->appendXML($_SESSION['sidebar']);

$rsN->appendChild($f);      
        
      
/*
 * ...and show the final document
 */
echo $doc->saveHTML();

?>