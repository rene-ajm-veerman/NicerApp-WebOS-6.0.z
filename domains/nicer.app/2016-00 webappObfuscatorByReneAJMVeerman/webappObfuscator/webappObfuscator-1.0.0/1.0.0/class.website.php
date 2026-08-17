<?php

class woWebsite { // webappObfuscatorWebsite
// handles URL translation and everything else that's required to display template and content
  public $factorySettings = null;
  public $clientSettings = null;
  public $templateVars;
  public $template;
  

  public function __construct ($factorySettings=null) {
        $this->clearTemplateVars();
        
        //TODO $this->dbMain = new saTreeDB($cmsSettings['treeDBs']['mainTreeDB']);
        
    
        //$call = $this->checkFactorySettings ($factorySettings);
        //$this->factorySettings = result($call);
        $this->factorySettings = $factorySettings;

        $call = $this->getEnvironmentParams();
        $callResult = result($call);
        $call2 = $this->checkEnvironmentParams ($callResult);
        $this->factorySettings['environment'] = $callResult;
        
        // can not use $this->clientSettings at this point yet!
    }

    public function __construct__postSetClientSettings () {
        $call = $this->getURLfromLocation ();
        $gcu = result($call);
        $untranslatedContentURL = $gcu;
        $this->factorySettings['untranslatedContentURL'] = $untranslatedContentURL;

        //echo 'param1; $untranslatedContentURL=<pre>'; var_dump($untranslatedContentURL); echo '</pre>';
		
					//$call = $this->getUntranslatedFilepaths ($untranslatedContentURL);
					
            //echo '$call (getUntranslatedFilePaths)=<pre>'; var_dump($call); echo '</pre>';
            //die();
            //var_dump ($call);
            
            
					// $untranslatedFilepaths = result ($call);
            //echo '$untranslatedFilePaths=<pre>'; var_dump ($untranslatedFilepaths); echo '</pre>'; die();
					//$this->factorySettings['untranslatedFilepaths'] = $untranslatedFilepaths;

        /* needs a call to $this->setClientSettings() first! */
        //$call = $this->translateFilepaths ($untranslatedFilepaths, false);
        //$translatedFilepaths = result ($call);
        //$this->factorySettings['translatedFilepaths'] = $translatedFilepaths;
		
		
        /*$call = $this->resolveURLs ($translatedURLs);
        $resolvedURLs = result($call);
        $this->settings['resolvedURLs'] = $resolvedURLs;
        */
        
        //echo 'webappObfuscator-1.0.0/1.0.0/class.website.php:::__construct() $this->factorySettings=<pre>'; var_dump ($this->factorySettings); echo '</pre>'; die();
    }

	public function getLocation__framework () {
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
		$codeLoc = $cc['currentDomain']['master']['hd'];
		$ver = $cc['versions'];
		$clfw = $codeLoc.$ver['framework'];
		return $clfw;
	}
	
	public function getLocation__media () {
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
	  return $cc['currentDomain']['media'];
	}

	/*public function getLocation__lib () {
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-* / * /com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-* / * /com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
	  return $cc['currentDomain']['lib'];
	}*/
	
  
  
  public function usePlaintextOutput () {
    global $developerMode;
    if (
      !isset($developerMode)
      || !is_bool($developerMode)
    ) {
      $err = array (
	'msg' => 'class.website.php:::woWebsite::usePlaintextOutput() : invalid $developerMode global variable',
	'$developerMode' => $developerMode
      );
      return badResult($err);
    } else {
      return $developerMode;
    }
  }
  
  /*  public function usePlaintextOutput () {
    global $webappObfuscator__clientSettings;
    $wo_pw = $webappObfuscator__clientSettings['password'];
    
    //global $treatDevelopersAsOrdinaryViewers; included in $developerMode calculation
    global $developerMode;
    
    $r = false;
    if (
      array_key_exists('wo_pw', $_GET)
      && $_GET['wo_pw'] = $wo_pw
    ) {
      $r = true;
    };
    
    if (
      $developerMode===true
    ) {
      $r = true;
    };  
     
    return $r;
  }
  */
  
  public function setClientSettings ($settings) {
    $this->clientSettings = $settings;
  }
  
  public function obfuscate() {
    $obfuscator = $this->clientSettings['obfuscator'];
    $ssc = $this->clientSettings['siteSpecificCMS'];
    
    $this->clearCachefiles();
    $this->prepareClientSpecificSources();
    $obfuscator->fetchSources();
  }
  
  /*public function resolveDisplayVars ($vars = null) {
    if (is_null($vars)) {
      $vars1 = array (
	'obfuscate' => true,
	'untranslated' => array (
	  'contentURL' => null,
	  'template' => null,
	  'templateVars' => null,
	  'params' => null
	),
	'translated' => array(),
	'resolved' => array()
      );
    } elseif (is_array($vars)) {
      $vars1 = $vars;
    } else {
      $err = array(
	'msg' => 'webappObfuscator-1.0.0/1.0.0/class.website.php:::woWebsite::resolveDisplayVars : $vars is not null or an array',
	'$vars' => $vars
      );
      return badResult(E_USER_ERROR, $err);
    }
    
    if (is_array($vars1['untranslated'])) {
      if (is_null($vars1['untranslated']['contentURL'])) $vars1['untranslated']['contentURL'] = $this->getContentURL();
      if (is_null($vars1['untranslated']['template'])) $vars1['untranslated']['template'] = 'SITE_FRAMEWORK_HD/siteContent/index.tpl';
      if (is_null($vars1['untranslated']['templateVars'])) $vars1['untranslated']['templateVars'] = array();
      if (is_null($vars1['untranslated']['params'])) $vars1['untranslated']['params'] = array();
    }
    
    if (
      !array_key_exists('contentURL', $vars1['untranslated'])
      || !is_string($vars1['untranslated']['contentURL']
      || !array_key_exists('template', $vars1['untranslated'])
      || !is_string($vars1['untranslated']['template'])
      || !array_key_exists('templateVars', $vars1['untranslated'])
      || !is_array($vars1['untranslated']['templateVars']
      || !array_key_exists('params', $vars1['untranslated'])
      || !is_array($vars1['untranslated']['params'])
    ) {
      $err = array(
	'msg' => 'webappObfuscator-1.0.0/1.0.0/class.website.php:::woWebsite::resolveDisplayVars : invalid $vars1',
	'$vars1' => $vars1
      );
      return badResult (E_USER_ERROR, $err);
    }
    
    
    return $vars1;
  }*/
  
  public function getCopyrightNotice ($template=null, $untranslatedContentURL='/', $params=null, $obfuscated=true) {
    global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
    $cc = $this->clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
    $ssc = $this->clientSettings['siteSpecificCMS'];

    //$cacheFile = $cc['site']['roles']['siteRoot_hd'].'/__README__documentation_and_LICENSE/__README-FIRST__seductiveapps_LICENSE.txt';
    $cacheFile = $cc['site']['roles']['siteRoot_hd'].'/LICENSE.txt';
    
    return file_get_contents($cacheFile);
  }
  
  public function displaySite($template=null, $untranslatedContentURL='/', $params=null, $obfuscated=true) {
    //$vars = $this->resolveDisplayVars($vars);
//var_dump ($obfuscated); die();    
    
    
    global $webappObfuscator__clientSettings;
    $wo_pw = $webappObfuscator__clientSettings['password'];
    
    global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
    $cc = $this->clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
    $ssc = $this->clientSettings['siteSpecificCMS'];
    //$obfuscate = !$this->usePlaintextOutput();
    if (is_null($untranslatedContentURL)) $untranslatedContentURL = $this->getURLfromLocation();

    // stages for the HTML (in order of parsing / execution)
    //  ajax_obfuscate.php -> reads /siteFramework-pw-xyz/date time/siteContent/index.tpl
    //  ajax_obfuscate.php -> outputs /webappObfuscator__output/html/siteTemplate.html
    //  index.php -> outputs /webappObfuscator__output/html/siteTemplate.complete.html
    
    $cacheFile = $cc['site']['roles']['siteRoot_hd'].'/webappObfuscator__output/html/siteTemplate.complete.html';
    
    if (	
        array_key_exists('wo_pw', $_GET)
    ) { // requested by webappObfuscator 
        if ($_GET['wo_pw'] !== $wo_pw) { 
            $err = array (
                'msg' => 'Invalid password wo_pw',
                '$_GET["wo_pw"]' => $_GET['wo_pw']
            );
            badResult (E_USER_ERROR, $err);
      
        } else { // requested by webappObfuscator, password check passed
            if (
                array_key_exists('wo_templateOnly', $_GET)
                || array_key_exists('wo_contentOnly', $_GET)
            )
            if (file_exists($cacheFile)) {
            echo 'unlinking cacheFile'; die();
                unlink($cacheFile);
            }
        }
    }
    
    /*if (file_exists($cacheFile)) {
		//echo '$cacheFile=<pre>'; var_dump($cacheFile); die();
        readfile ($cacheFile);
        die(); // happily btw
    }*/
    
    
    //var_dump ($untranslatedContentURL); die();
    if ($obfuscated) $webappObfuscator->readTokens();
//var_dump ($obfuscated); die();    

    $this->urlSpecificSettings ($untranslatedContentURL);
    if (false) {
      echo 'class.website.php:::woWebsite::displaySite() : $this->factorySettings=<pre>'; var_dump ($this->factorySettings); echo '</pre>'; 
      //echo 'class.website.php:::woWebsite::displaySite() : $this->clientSettings=<pre>'; var_dump ($this->clientSettings); echo '</pre>'; 
      //die();
    };

    //echo '$this->factorySettings=<pre>'; var_dump ($this->factorySettings); echo '</pre>'; die();
    /*
        // EXPERIMENTAL (2017-01)
        $call = $this->getUntranslatedFilepaths ($untranslatedContentURL);
        $untranslatedFilepaths = result ($call);
        $this->factorySettings['untranslatedFilepaths'] = $untranslatedFilepaths;
    
    $call = $this->translateFilepaths ($this->factorySettings['untranslatedFilepaths'], false);
    $translatedFilepaths = result ($call);
    $this->factorySettings['translatedFilepaths'] = $translatedFilepaths;
    */
  
    if (false) {
      //echo 'class.website.php:::woWebsite::displaySite() : $this->factorySettings=<pre>'; var_dump ($this->factorySettings); echo '</pre>'; 
      //echo 'class.website.php:::woWebsite::displaySite() : $this->clientSettings=<pre>'; var_dump ($this->clientSettings); echo '</pre>'; 
      echo 'class.website.php:::woWebsite::displaySite() : $this->templateVars=<pre>'; var_dump ($this->templateVars); echo '</pre>'; 
      die();
    };
    $clientCMS = $this->clientSettings['siteSpecificCMS'];
    $clientCMS__factorySettings = $clientCMS->factorySettings;
    $clientCMS__clientSettings = $clientCMS->clientSettings;
    
    //$templateFilepath = $this->factorySettings['translatedFilepaths']['template'];
    $templateFilepath = 
        $this->factorySettings['environment']['SITE_HD']
        .$this->factorySettings['environment']['SITE_FRAMEWORK_SECRET_FOLDER']
        .'siteContent/index.tpl';
        
    
    //echo '$templateFilepath='.$templateFilepath;die();
    //$this->template = 'index.tpl';
    
    
    //global $wo_pw;
    if (	
      array_key_exists('wo_pw', $_GET)
    ) { // requested by webappObfuscator 
      if ($_GET['wo_pw'] !== $wo_pw) { 
	$err = array (
	  'msg' => 'Invalid password wo_pw',
	  '$_GET["wo_pw"]' => $_GET['wo_pw']
	);
	badResult (E_USER_ERROR, $err);
      
      } else { // requested by webappObfuscator, password check passed
	if (
	  array_key_exists('wo_templateOnly', $_GET)
	  && array_key_exists('wo_contentOnly', $_GET)
	) {
	  $err = array (
	    'msg' => 'You can\'t request wo_templateOnly and wo_contentOnly at the same time.',
	    '$_GET' => $_GET
	  );
	  badResult (E_USER_ERROR, $err);
	} 
	if (
	  !array_key_exists('wo_templateOnly', $_GET)
	  && !array_key_exists('wo_contentOnly', $_GET)
	) {
	  $err = array (
	    'msg' => 'You have specified a password ($_GET[\'wo_pw\']), use an extra $_GET parameter EITHER wo_templateOnly=yes OR wo_contentOnly=yes at the same time.',
	    '$_GET' => $_GET
	  );
	  badResult (E_USER_ERROR, $err);
	} 
	
	
	if (
	  array_key_exists('wo_templateOnly', $_GET)
	) {
	
	  //DO NOT fill in $templateVars here, only on actual display of the site.
	  /*$templateVars = $this->getTemplateVarsForTemplate (null, null, false);
	  $templateContent_unresolvedVars = file_get_contents ($templateFilepath);
	  $templateContent_varsResolved = $this->resolveTemplateVars($templateContent_unresolvedVars, $templateVars);
	  echo $templateContent_varsResolved;
	  die();
	  */
	  // THIS IS KEY :: $templateContent = file_get_contents ($templateFilepath);
	  //echo $templateContent;
	  $obfuscated = false;
	}	
	if (
	  array_key_exists('wo_contentOnly', $_GET)
	) {
	  $call = $this->getContent();
	  $content = result($call);
	  $content = $content['content'];
	  echo $content;
	  die();
	}
      }
    } 
    
    // ---> variables to fill in, at various stages of processing;
    /*
    index.tpl variables :
    <head>
        {$copyrightNotice}
        {$page_title}
        {$page_meta}
        {$definition_ipad_tags}
        {$headCSS}
        {$headJavascript}
        {$webappObfuscator__fullSources} !!! CAREFUL !!! CAREFUL !!! DO NOT include this "on a whim" or without very good webcoding ***AND SYSTEM-ADMINISTRATION*** skills!!
    <body>
      {$googleAnalytics}
      {$backgroundImage}
      {$backgroundImageOrCode}
      {$saPageSettings__siteContent__vividTheme}
      {$content}
      {$initBootscreen}
      {$definition_mainmenu}
      {$menuContent_musicMenu}
      {$comments_all}
      {$saPageSettings__siteAds__content}
      {$productDetails}
      {$cachedData} // #ultiCache_data
    */
    
    
      global $webappObfuscator__clientSettings;
      //$secretOutputFolder = $webappObfuscator__clientSettings['paths']['secretOutput'];
      $publicOutputFolder = $webappObfuscator__clientSettings['paths']['publicOutput'];
      
      
      
      //--------------------------------------------------------------------------------------------------------------
      // ****README NOW!!!**** EXPERIMENTAL SOFTWARE FEATURE, LEGAL DISCLAIMER
      // IF ***YOU WANT*** TO ***INCLUDE THE FULL-SOURCES-DATA*** FOR USE BY THE ***BROWSER DEBUGGER*** ***ONLY!***
      // RUN THIS FROM YOUR OWN HOME (rental/bought/bought-with-mortgage) ONLY!!!!!!!
      // !!! CAREFUL !!! CAREFUL !!! DO NOT include this "on a whim" or without very good webcoding skills!!
      // !!! 
      // !!! LEGAL DISCLAIMER !!! i can not be held accountable in *any* way for loss of life or money or whatever,
      //     NOT AT ALL, in NO COURT OF LAW at all, and NOT in PUBLIC OPINION either!!!.
      //--------------------------------------------------------------------------------------------------------------
      if (false) {
        /*
        $secretOutputFolder = $webappObfuscator__clientSettings['paths']['secretOutput'];
        $templateVars['webappObfuscator__secretFolder'] = $secretOutputFolder;
        */
      }
      
      
      
      $contextTemplateVars = array();

      //echo '$this->factorySettings=<pre>';var_dump($this->factorySettings);echo '</pre>'; die();
      //echo '$obfuscated=<pre>';var_dump($obfuscated);echo '</pre>'; die();
      if ($obfuscated===true) {
        $templateFilepath = $cc['site']['roles']['siteRoot_hd'].'/webappObfuscator__output/html/siteTemplate.html';
      } else {
	//$templateFilepath = $this->factorySettings['translatedFilepaths']['template'];

        $templateFilepath = 
            $this->factorySettings['environment']['SITE_FRAMEWORK_SECRET_FOLDER']
            .'/siteContent/index.tpl';
        
        
        //echo '$templateFilepath 2 = <pre>';var_dump($templateFilepath);echo '</pre>'; 
      }
      //die();
      
      $templateContent_unresolvedVars = file_get_contents ($templateFilepath);	
      //var_dump (htmlentities($templateContent_unresolvedVars)); die();
      if (false) {
	echo 'woWebsite::displaySite() : $this->factorySettings=<pre>'; var_dump ($this->factorySettings); echo '</pre>';
	echo 'woWebsite::displaySite() : $this->clientSettings=<pre>'; var_dump ($this->clientSettings); echo '</pre>';
	die();
      }

      //var_dump ($obfuscated); die();
      $templateVars = $this->getTemplateVarsForTemplate ($template, $untranslatedContentURL, $params, $obfuscated);
      if (array_key_exists('wo_templateOnly', $_GET)) {
        unset ($templateVars['content']);
        unset ($templateVars['headJavascript']);
        unset ($templateVars['headCSS']);
      } 
      
      if (false) {
	echo 'woWebsite::displaySite() : $templateVars=<pre>'; var_dump ($templateVars); echo '</pre>';
	die();
      }
      
      $templateContent_varsResolved = $this->resolveTemplateVars($templateContent_unresolvedVars, $templateVars);
      echo $templateContent_varsResolved;
      
      if (
        $obfuscated===true
        && (
            !array_key_exists('wo_templateOnly', $_GET)
            && !array_key_exists('wo_contentOnly', $_GET)
        )
      ) {
        file_put_contents ($cacheFile, $templateContent_varsResolved);
      }
      
      
      die();
    //}
  }
  
  public function getTemplateVarsForTemplate ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  
	  //var_dump ($obfuscated); die();
    global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
    global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

    global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
    global $saConfig__saCloud;
    global $seductiveapps_installedApps;
      
    global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
    global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
    $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
    $ssc = $this->clientSettings['siteSpecificCMS'];
    if (is_null($untranslatedContentURL)) $untranslatedContentURL = $this->getURLfromLocation();

    $call = $this->getContent();
    $content = result($call);
    //echo 'BUG_content_001 : <pre>$untranslatedContentURL='; var_dump($untranslatedContentURL); echo '</pre>'; 
    //echo '<pre>$content='; var_dump($content); echo '</pre>'; 
    //die();
    $content = $content['content'];

    
    $call = $ssc->getTemplateVarsForTemplate($template, $untranslatedContentURL, null, $obfuscated);
    /* DO NOT USE $ssc->getTemplateVarsForTemplate
      for any variables listed below here, all of these functions $this->get{something}() call 
      $ssc on their own and append results from those calls
    */
    //var_dump ($obfuscated); die();
    $ssctv = result($call);
    
    $tvfst = array(
      'copyrightNotice' => $this->getCopyrightNotice ($template, $untranslatedContentURL, null, $obfuscated),
      'content' => $content,
      'page_title' => 'SeductiveApps.com (Under Construction)',//$this->getTitle($template, $untranslatedContentURL, null, $obfuscated),
      'page_meta' => '',//$this->getMeta($template, $untranslatedContentURL, null, $obfuscated),
      'definition_ipad_tags' => '',//$this->getIPadTags($template, $untranslatedContentURL, null, $obfuscated),
	  'SA_MEDIA_WEB' => SA_MEDIA_WEB,
	  'SA_SITE_WEB' => SA_SITE_WEB
    );

	
	$extra = array(
	'headCSS' => $this->getHeadCSS ($template, $untranslatedContentURL, null, $obfuscated),
	'headJavascript' => $this->getHeadJavascript ($cc, $template, $untranslatedContentURL, null, $obfuscated)
	);
	//var_dump ($extra); die();
	
	$r = array_merge(
		$extra, 
		$tvfst,
		$ssctv
	);
    return $r;
  }
  
  public function translateFilepaths ($untranslatedURLs=null, $obfuscated=true) {
    $client = $this->factorySettings;
    //echo 'translateFilepaths() : $s=<pre>'; var_dump ($s); echo '</pre>'; die();
  
    $r = array();
    if (!is_array($untranslatedURLs)) {
      $err = array (
	'msg' => 'webappObfuscator-1.0.0/1.0.0/class.website.php:::translateURLs : !is_array($untranslatedURLs)',
	'$untranslatedURLs' => $untranslatedURLs
      );
      return badResult ($err);
    }
    
    foreach ($untranslatedURLs as $k => $v) {
      $v2 = $v;
      if ($obfuscated) {
	//$v2 = $v;
	//$v2 = str_replace('SITE_FRAMEWORK_HD', $this->factorySettings['obfuscator']['paths']['publicOutput'].'/html/', $v2);
	$v2 = str_replace('SITE_FRAMEWORK_HD', $this->clientSettings['paths']['publicOutput'], $v2); 
	$v2 = str_replace('SA_SITE_HD', SA_SITE_HD, $v2); 
	//$v2 = str_replace('SITE_HD', $this->clientSettings['paths']['SITE_HD']);
	//echo '$this->clientSettings["paths"]=<pre>'; var_dump ($this->clientSettings['paths']); die();
	//$v2 = str_replace('SITE_HD', $
      } else {
	$v2 = str_replace('SITE_FRAMEWORK_HD', $this->clientSettings['paths']['siteFramework'], $v2);
	$v2 = str_replace('SA_SITE_HD', SA_SITE_HD, $v2); 
	//echo '$this->clientSettings["paths"]=<pre>'; var_dump ($this->clientSettings['paths']); die();
      };
      $r[$k] = $v2;
    }
     
     
    //echo 'translateFilepaths() : $r=<pre>'; var_dump($r); echo '</pre>'; die();
    return goodResult ($r);
  }
  
  public function resolveTemplateVars ($templateContent_unresolvedVars, $templateVars) {
    $search = array();
    $replace = array();
    foreach ($templateVars as $k => $v) {
      $k2 = '{$'.$k.'}';
      $search[] = $k2;
      $replace[] = $v;
    }
    
    if (false) {
      //var_dump ($templateContent_unresolvedVars);die();
      echo '$search=<pre>'; var_dump ($search); echo '</pre>';
      echo '$replace=<pre>'; var_dump ($replace); echo '</pre>';
      die();
    }
    return str_replace ($search, $replace, $templateContent_unresolvedVars);
  }
  
  public function getIPadTags () {
    $r = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">';
    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
      if (
	      strpos($_SERVER['HTTP_USER_AGENT'], 'iPad3C1')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad3C2')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad4C1')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad4C2')!==false
      ) { // iPads without retina display
	      $r = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">';
      
      } else if (
	      strpos($_SERVER['HTTP_USER_AGENT'], 'iPad3C3')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad3C3')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad4C4')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad4C5')!==false
      
      ) { // iPads with retina displays
	      $r = '<meta name="viewport" content="width=device-width, initial-scale=2, maximum-scale=2, user-scalable=yes">';
      
      } else if (
	      strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS 8_')!==false // iPhone 6 + iPad (May 2015)
      ) {
	      $r = '<meta name="viewport" content="width=device-width, initial-scale=1.2, maximum-scale=1.05, user-scalable=yes">';
	      
      } else if (
	      strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')!==false
	      || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')!==false
      ) {
	      $r = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">';
	      //$this->smarty->assign ('definition_ipad_tags', '<meta name="apple-mobile-web-app-capable" content="yes"/><meta name="viewport" content="width=device-width, user-scalable=no" />');
      } 
    }  
    return $r;
  }
  
  public function checkFactorySettings ($factorySettings=null) {
    if (is_null($factorySettings)) $factorySettings = array ();
    if (!is_array($factorySettings)) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings is not an array',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    } 
    
    if (!is_string($factorySettings['siteRootHD'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'siteRootHD\'] is not a string',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    } 
    if (!file_exists($factorySettings['siteRootHD'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'siteRootHD\'] is not a valid directory',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    } 
    $path = $factorySettings['siteRootHD'];
    if (is_writable($path)===true) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : ERROR in parameter $factorySettings[\'siteRootHD\'], is_writeable()===true',
	'$factorySettings' => $factorySettings,
	'$path' => $path
	
      );
      return badResult (E_USER_ERROR, $err);
    }
    
    
    if (!is_array($factorySettings['obfuscator'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'] is not an array',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }
    if (!is_array($factorySettings['obfuscator']['paths'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'][\'paths\'] is not an array',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }

    
    if (!file_exists($factorySettings['obfuscator']['paths']['secretOutput'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'][\'paths\']["secretOutput"] is not an array',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }  
    $path = $factorySettings['obfuscator']['paths']['secretOutput'];
    if (!is_writable($path)) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'][\'paths\'][\'secretOutput\'] can not be written to',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }
    

    if (!file_exists($factorySettings['obfuscator']['paths']['publicOutput'])) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'][\'paths\'][\'publicOutput\'] is not an array',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }  
    $path = $factorySettings['obfuscator']['paths']['publicOutput'];
    if (!is_writable($path)) {
      $err = array (
	'msg' => 'class woWebsite::__construct() : parameter $factorySettings[\'obfuscator\'][\'paths\']["secretOutput"] can not be written to',
	'$factorySettings' => $factorySettings
      );
      return badResult (E_USER_ERROR, $err);
    }
    
    return goodResult($factorySettings);
  }
  
  public function clearTemplateVars () {
    $this->templateVars = array();
  }
  
  public function clearCachefiles () { // TODO : fill in
    $obfuscator = $this->clientSettings['obfuscator'];
    $obfuscator->clearCachefiles();

    $ssc = $this->clientSettings['siteSpecificCMS'];
    $ssc->clearCachefiles();
  }
  
  public function prepareClientSpecificSources () {
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $ssc->prepareClientSpecificSources();
  }
  
  public function getEnvironmentParams () {
    $platform = 'UNIX';
    if (
      !array_key_exists('SystemRoot', $_SERVER) &&
      ( isset ($argv) && array_key_exists(1, $argv) &&
	      (	stripos($argv[1], 'win32')!==false
		      || stripos($argv[1], 'win64')!==false
	      )
      )
    ) {
      $platform='WINDOWS';
    } elseif ( 
      array_key_exists('SystemRoot', $_SERVER) 
      && stripos($_SERVER['SystemRoot'], 'C:\WINDOWS')!==false 
    ) {
      $platform = 'WINDOWS';
    };
    define ('SITE_SERVER_PLATFORM', $platform);


    $serverURL = 'unknown-server';
    if (array_key_exists('HTTP_HOST', $_SERVER)) {
	    $serverURL = 'http://'.$_SERVER['HTTP_HOST'];
    }
    define ('SITE_SERVER_URL', $serverURL);

  
    $docRoot = 'unknownDocRoot';
    if (array_key_exists('REAL_DOCUMENT_ROOT', $_SERVER)) { // for my godaddy 4G hosting
	    $docRoot = str_replace('/var/chroot','', $_SERVER['REAL_DOCUMENT_ROOT']);
	    define ('WO_HOSTING_HD_PREFIX', '/var/chroot');
    } elseif (array_key_exists('DOCUMENT_ROOT', $_SERVER)) {
	    $docRoot = $_SERVER['DOCUMENT_ROOT'];
	    define ('WO_HOSTING_HD_PREFIX', '');
    }
    if (substr($docRoot,strlen($docRoot)-1,1)=='/') $docRoot = substr ($docRoot, 0, strlen($docRoot)-1);
    define ('SITE_DOCUMENT_ROOT', $docRoot);

    $siteHD = str_replace('\\', '/', dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))).'/';
    $siteWeb = SITE_SERVER_URL.str_replace (SITE_DOCUMENT_ROOT, '', $siteHD);


    /*
    if (isset($_SESSION) && array_key_exists('SA_SITE_SUBDIR_BELOW_PROJECT', $_SESSION)) {
	    $appSubDir = $_SESSION['SA_SITE_SUBDIR_BELOW_PROJECT'];
    } else {
	    //$appSubDir = preg_replace('|seductiveapps/$|', '', str_replace(SITE_SERVER_URL, '', $siteWeb));	
	    $appSubDir = '/'; // 2017-01(Jan) : HARDCODED FOR NOW
	    
	    
	    //var_dump ($appSubDir);
	    //die();
    }
    */
    $appSubDir = '/';
    
    define ('SITE_SUBDIR', $appSubDir);
    define ('SITE_HD', SITE_DOCUMENT_ROOT.$appSubDir);
    //echo '<pre>'; $defs = get_defined_constants(); var_dump ($defs); echo '</pre>';
    define ('SITE_WEB', SITE_SERVER_URL.$appSubDir);
    
    global $frameworkSecretFolder;
  
    $r = array (
      //'$siteHD' => $siteHD,
      //'$siteWeb' => $siteWeb,
      'SITE_SERVER_URL' => SITE_SERVER_URL,
      'SITE_SERVER_PLATFORM' => SITE_SERVER_PLATFORM,
      'SITE_DOCUMENT_ROOT' => SITE_DOCUMENT_ROOT,
      'SITE_SUBDIR' => SITE_SUBDIR,
      'SITE_HD' => SITE_HD,
      'SITE_WEB' => SITE_WEB,
      'SITE_FRAMEWORK_SECRET_FOLDER' => $frameworkSecretFolder
    );
    //echo 'class.website.php:::getEnvironmentParams() : $r =<pre>'; var_dump ($r); echo '</pre>'; die();
    
    return goodResult($r);
  }
  
  public function checkEnvironmentParams () {
    $r = array (
    );
    return goodResult($r);
  }
  
  public function getURLfromLocation () {
    //echo '<pre>$_SERVER='; var_dump ($_SERVER); echo '</pre>'; die();
    $u = $_SERVER['REQUEST_URI'];
    $root =  $this->factorySettings['environment']['SITE_SUBDIR'];

    //echo '<pre>$_SERVER='; var_dump ($_SERVER); echo '</pre>'; 
    //echo '<pre>$root='; var_dump ($root); echo '</pre>'; 
    //die();
    
    if ($root!='/') $r = str_replace($root, '', $u); else $r = substr($u,1);// else $r=$u;//else $r = substr($u,1);
    if (!is_string($r)) $r = '/';
    return goodResult($r);
  }
  
  public function resolveURLs ($translatedURLs=null) {
    $r = array();
    if (!is_array($translatedURLs)) {
      $err = array (
	'msg' => 'webappObfuscator-1.0.0/1.0.0/class.website.php:::resolveURLs : !is_array($translatedURLs)',
	'$translatedURLs' => $translatedURLs
      );
      return badResult($err);
    } 
    
    foreach ($translatedURLs as $k => $v) {
      $r[$k] = file_get_contents($v);
    }
    
    return goodResult($r);
  }
  
  public function getDebugSettings () {
	  $r = array();
	  foreach ($_GET as $k=>$v) {
		  if (strpos($k, 'd')!==false) {
			  $r[str_replace('d','',$k)] = $v;
		  }
	  };
	  return $r;
  }
  
  public function getUntranslatedFilepaths ($untranslatedContentURL=null) {
    $ucu = $untranslatedContentURL;
    //echo '$ucu 1 = <pre>'; var_dump ($ucu); echo '</pre>';
    if (!is_string($ucu)) {
      $ucu = $this->getURLfromLocation();
    }
    //echo '$ucu 2 = <pre>'; var_dump ($ucu); echo '</pre>';
    //die();
    
    
    
    $r = array (
      'template' => $this->getTemplateURL ($ucu),
      'title' => $this->getTitleURL ($ucu),
      'meta' => $this->getMetaURL ($ucu),
      'content' => $this->getContentURL ($ucu)
    );
    
    //echo 'getUntranslatedFilepaths $r = <pre>'; var_dump ($r); echo '</pre>';
    //die();

    //$r2 = resultArray($r);
    return goodResult ($r);
    /*
    $r2 = array();
    $flawless = true;
    foreach ($r as $k=>$v) {
      if (good($v)) {
	$v2 = result($v); 
      } else {
	$v2 = $v;
	$flawless = false;
      }
      $r2[$k] = $v2;
    }
    
    if ($flawless) {
      return goodResult ($r2);  
    } else {
      
    }*/
  }
  
  public function getContentBrowserURL () {
    $call = $this->getURLfromLocation();
    $u = $call;
    
    $r = SA_SITE_WEB.'content/'.$u;
    return goodResult($r);
  }
  
  public function getTemplateURL ($untranslatedContentURL=null, $params=null) {
    $r = 'SITE_FRAMEWORK_HD/siteContent/index.tpl';
    //$r = 'SITE_HD/webappObfuscator__output/html/siteTemplate.html';
    return $r;
  }

  public function getCSS_urlSpecific ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
  }
  
  public function getJavascript_urlSpecific ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
  }
  
  public function getContent ($p=null) {
	  if (is_null($p)) $p = array();
	  if (
		!array_key_exists('saAppParams', $p)
		|| !is_array($p['saAppParams'])
	  ) {
		  $saAppParams = resolveURLs_functionCalls();
		  $p['saAppParams'] = $saAppParams;
	  }
	  if (
		!array_key_exists('saInstalledApps', $p)
		|| is_array($p['saInstalledApps'])
	  ) {
		global $saInstalledApps;
		$s['saInstalledApps'] = $saInstalledApps;
	  }	
	  if (
		!array_key_exists('saAppDiv', $p)
	  ) {
		  $p['saAppDiv'] = '#siteContent';
	  }
		
		$r = false;
		$s['saInstalledApps']['site'] = true;
		foreach ($s['saInstalledApps'] as $appName => $appConfig) {
			$functionName = 'getDialogContent__4app__'.$appName;
			$appr = (
				function_exists($functionName)
				? call_user_func($functionName, $p)
				: false
			);
			if ($appr!==false) {
				$r = $appr;
			}
		}
	  
    if (
      $r==''
      || $r===false
    ) {
      $r = $this->getContentURL_4site();
      //return $r;
    };
    
	   echo ' '; // NEEDED
		while (ob_get_level() > 0) ob_end_flush();	
		ob_start();
		
		/*
		if (
			is_null($url) 
			|| $url==='/'
		) {
			$u = $this->getURLfromLocation();
			$u = result($u);
			//echo 'BUG_content_001 $u 1=<pre>'; var_dump ($u); echo '</pre>'; //die();
			//$u = substr($u, 1);
		} else {
			$u = $url;
		}
		
		//echo '3200.2<pre>'; var_dump ($u); echo '</pre>'; //die();
		
		//$this->figureOutWhereSettingsAreStored($u);
		//echo '3201<pre>'; var_dump ($u); echo '</pre>'; die();
		$u = str_replace('/content/','',$u);
		//echo '<pre>$u 2 ='; var_dump ($u); var_dump (str_replace(SA_SITE_WEB,'',$u)); echo '</pre>';
		
		//echo '<pre>'; echo '$_GET='; var_dump ($_GET); var_dump (str_replace(SA_SITE_WEB,'',$u)); //die();
		$cs = $this->getContentURL(str_replace(SITE_WEB,'',$u));
		//echo 'BUG_content_001 $cs=<pre>'; var_dump($cs); echo '</pre>'; //die();
		
		
		//$cs = result($cs);
		//echo '3202'; var_dump ($cs); die();
		
		$u = $cs;
		if (is_array($u) && array_key_exists('url', $u)) {
			$u = $u['url'];
		}*/
		if (
			is_array($r)
			&& array_key_exists('url',$r)
		) {
				$u = $r['url'];
		} else {
			$u = $r;
			//reportVariable ('$r', $r); die();
		}
		
		$u = str_replace('SA_SITE_HD/', SA_SITE_HD, $u);
		$u = str_replace('SITE_HD/', SA_SITE_HD, $u);
		$u = str_replace('SITE_FRAMEWORK_HD', $this->clientSettings['paths']['siteFramework'], $u);
		//echo 'BUG_content_001 require_once() $u =<pre>'; var_dump ($u); echo '</pre>'; die();
				
		if (strpos($u, 'user/')!==false) {
			$externalSite = true;
		};
				
				
		if (strpos($u, 'http://')===false) {
			$externalSite = false;
				//echo 'BUG_content_001 require_once() EXTERNAL_SITE==false<br/>';
			
			$u1 = explode('?',$u);
			if (count ($u1)===2) {
				$u = $u1[0];
				$u3 = explode ('&', $u1[1]);
				foreach ($u3 as $idx2=>$u4) {
					$u5 = explode('=', $u4);
					if (count($u5)===2) {
						$_GET[$u5[0]] = $u5[1];
					}
				}
			}				
			//echo 'BUG_content_001 require_once() $u =<pre>'; var_dump ($u); echo '</pre>'; die();
			require_once ($u);
			
			
			
			
		} else {
			//var_dump ($u);
			//die();
			$externalSite = strpos($u, SITE_WEB)===false;
			if (strpos($u,'plus.google')===false) {
				echo saConvertExternalHTMLToNative ($u, file_get_contents($u));
			} else {
				echo 'This content is not viewable in this interface.';
			}
		}

		$r = ob_get_contents();
		ob_end_clean();
		  
		$ret = array (
		  'content' => $r,
		  'externalSite' => $externalSite
		);
		//reportVariable ('$ret', $ret, true); die();
		//echo 'BUG_content_001 $ret=<pre>'; var_dump ($ret); echo '</pre>'; //die();
		
		return goodResult($ret);
	}
  
  
  public function getContent__OLD ($url=null) {
    //echo '3200.1<pre>'; var_dump ($url); echo '</pre>'; //die();
    echo ' '; // NEEDED
    while (ob_get_level() > 0) ob_end_flush();	
    ob_start();
    
    if (
        is_null($url) 
        || $url==='/'
    ) {
	    $u = $this->getURLfromLocation();
	    $u = result($u);
	    //echo 'BUG_content_001 $u 1=<pre>'; var_dump ($u); echo '</pre>'; //die();
	    //$u = substr($u, 1);
    } else {
	    $u = $url;
    }
    
    //echo '3200.2<pre>'; var_dump ($u); echo '</pre>'; //die();
    
    //$this->figureOutWhereSettingsAreStored($u);
    //echo '3201<pre>'; var_dump ($u); echo '</pre>'; die();
    $u = str_replace('/content/','',$u);
    //echo '<pre>$u 2 ='; var_dump ($u); var_dump (str_replace(SA_SITE_WEB,'',$u)); echo '</pre>';
    
    //echo '<pre>'; echo '$_GET='; var_dump ($_GET); var_dump (str_replace(SA_SITE_WEB,'',$u)); //die();
    $cs = $this->getContentURL(str_replace(SITE_WEB,'',$u));
    //echo 'BUG_content_001 $cs=<pre>'; var_dump($cs); echo '</pre>'; //die();
    
    
    //$cs = result($cs);
    //echo '3202'; var_dump ($cs); die();
    
    $u = $cs;
    if (is_array($u) && array_key_exists('url', $u)) {
        $u = $u['url'];
    }
    
    $u = str_replace('SA_SITE_HD/', SA_SITE_HD, $u);
    $u = str_replace('SITE_HD/', SA_SITE_HD, $u);
    $u = str_replace('SITE_FRAMEWORK_HD', $this->clientSettings['paths']['siteFramework'], $u);
    //echo 'BUG_content_001 require_once() $u =<pre>'; var_dump ($u); echo '</pre>';
		    
    if (strpos($u, 'user/')!==false) {
	    $externalSite = true;
    };
		    
		    
    if (strpos($u, 'http://')===false) {
	    $externalSite = false;
            //echo 'BUG_content_001 require_once() EXTERNAL_SITE==false<br/>';
	    
	    $u1 = explode('?',$u);
	    if (count ($u1)===2) {
		    $u = $u1[0];
		    $u3 = explode ('&', $u1[1]);
		    foreach ($u3 as $idx2=>$u4) {
			    $u5 = explode('=', $u4);
			    if (count($u5)===2) {
				    $_GET[$u5[0]] = $u5[1];
			    }
		    }
	    }				
	    //echo 'BUG_content_001 require_once() $u =<pre>'; var_dump ($u); echo '</pre>';
	    require_once ($u);
	    
	    
	    
	    
    } else {
	    //var_dump ($u);
	    //die();
	    $externalSite = strpos($u, SITE_WEB)===false;
	    if (strpos($u,'plus.google')===false) {
		    echo saConvertExternalHTMLToNative ($u, file_get_contents($u));
	    } else {
		    echo 'This content is not viewable in this interface.';
	    }
    }

    $r = ob_get_contents();
    ob_end_clean();
	  
    $ret = array (
      'content' => $r,
      'externalSite' => $externalSite
    );
    //echo 'BUG_content_001 $ret=<pre>'; var_dump ($ret); echo '</pre>'; //die();
    
    return goodResult($ret);
  }  
  
  public function getContentURL ($untranslatedContentURL=null, $params=null) {
    $s = $this->clientSettings;
    
    /*if (is_null($s)) { 
    //settings check
        echo 'getContentURL() --- $this->clientSettings === $s === <pre>'; 
        badResult (E_USER_ERROR, array(
            'msg' => 'invalid $this->clientSettings',
            '$s' => $s
        )); 
        echo '</pre>'; 
        die();
    }
    if (is_null($s)) return false;
    */
    
    //echo 'getContentURL() --- $s=<pre>'; var_dump ($s); echo '</pre>';
    
    global $saInstalledApps;
    $s['siteInstalledApps'] = $saInstalledApps;
    
    $sia = $s['siteInstalledApps']; //!!! THIS IS THE SOURCE OF THE CURRENT BUG-TO-FIX
    /*if (is_null($sia)) { // if debugging
        echo 'getContentURL() --- $sia === <pre>'; var_dump ($sia);
        badResult (E_USER_ERROR, array(
            'msg' => 'testErr',
            '$sia' => $sia
        )); 
        echo '</pre>'; 
        die();    
    }*/
    
    
    $r = '';
    if (
        !is_array($params)
        || !array_key_exists('url', $params)
    ) {
        $params = array (
            'dialog' => '#siteContent',
            'url' => $untranslatedContentURL
        );
    }
    
    $sia['site'] = true;
    foreach ($sia as $appName => $appConfig) {
            //echo 'getContentURL() --- $appName===<pre>'; var_dump ($appName); echo '</pre>';
            //echo 'getContentURL() --- $appConfig===<pre>'; var_dump ($appConfig); echo '</pre>';
	    $functionName = 'getDialogContent__4app__'.$appName;
            //echo $functionName.' --- function_exists===<pre>'; var_dump (function_exists($functionName)); echo '</pre>';
	    $appr = (
		    function_exists($functionName)
		    ? call_user_func($functionName, $params)
		    : false
	    );
	    if ($appr!==false) {
		    $r = $appr;
	    }
    }
    //die();
    
    //echo 'getContentURL() --- $r=<pre>'; var_dump ($r); echo '</pre>';
    
    if (
      $r==''
      || $r===false
    ) {
      $r = $this->getContentURL_4site($untranslatedContentURL);
      return $r;
    };
    
    return $r;
  }

  public function getContentURL_4site () {
    $file = 'SITE_FRAMEWORK_HD/siteContent/frontpage.content.php';
    return $file;
  }
  
  public function getTitle ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $call = $ssc->getTitle ($untranslatedContentURL, $params, $obfuscated);
    
    
    if (false) {
    //if (!is_null($call)) {
      return $call;
    } else {
      return file_get_contents($this->factorySettings['translatedFilepaths']['title']);
    }
    
    
    /*
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $call = $ssc->getTitle ($untranslatedContentURL, $params, $obfuscated);
    if (!is_null($call)) {
      return $call;
    } else {
      $titleFilepath = $this->getTitleURL ($template, $untranslatedContentURL, $params, $obfuscated);
      $relativePaths = array (
	'title' => $titleFilepath
      );
      $call = $this->translateFilepaths ($relativePaths, $obfuscated);
      $filepaths = result($call);
      
      $filepath = $filepaths['title'];
      return file_get_contents($filepath);
    }
    */
  }
    
  public function getTitleURL ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  //global $seductiveapps_installedApps;
	  $s = $this->clientSettings;
	  $sia = $s['siteInstalledApps'];
	  
	  $r = '';
	  
	  $sia['site'] = true;
	  foreach ($sia as $appName => $appConfig) {
		  $functionName = 'getTitleURL_4app_'.$appName;
		  $appr = (
			  function_exists($functionName)
			  ? call_user_func($functionName, $params)
			  : false
		  );
		  /*
		  $dbg = array (
			  '$appName' => $appName,
			  '$appr' => $appr,
			  '$_GET' => $_GET
		  );
		  var_dump ($dbg);*/
		  
		  if ($appr!==false) {
			  $r = $appr;
		  }
	  }
	  //var_dump ($r); die();
	  
	  if (
	    $r==''
	    || $r===false
	  ) {
	    $r = $this->getTitleURL_4site($untranslatedContentURL);
	  };
	  
	  return $r;
  }

  public function getTitleURL_4site ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  /*
	  $p = getContentBrowserURL ($untranslatedContentURL);
	  if (is_array($p)) {
		  $gcs = result($p);
		  $gcs['url'] = str_replace('SA_SITE_HD/', '', $gcs['url']);
		  $u = $gcs['url'];
	  } else {
		  $u = $p;
	  };
	  var_dump ($u);
	  */
	  
	  //$file = dirname(__FILE__).'/../siteContent/frontpage.php.title.txt';
	  //$file = saConfig__location('siteFramework', 'hd').'/siteContent/frontpage.php.title.txt';
	  $file = 'SITE_FRAMEWORK_HD/siteContent/frontpage.php.title.txt';
	  
	  return $file;
	  
	  /*
	  //var_dump ($file);	
	  $r = file_get_contents($file);	
	  if (SA_DEVELOPMENT_SERVER) {
		  $r = 'TEST '.$r;
	  }
	  
	  //var_dump ($r); die();
	  return $r;
	  */
  }

  public function getMeta ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $call = $ssc->getTitle ($untranslatedContentURL, $params, $obfuscated);
    if (false) {
    //if (!is_null($call)) {
      return $call;
    } else {
      return file_get_contents($this->factorySettings['translatedFilepaths']['meta']);
    }
  /*
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $call = $ssc->getMeta ($untranslatedContentURL, $params, $obfuscated);
    if (!is_null($call)) {
      return $call;
    } else {
      $metaTags_relativeFilepath = $this->getMetaURL ($template, $untranslatedContentURL, $params, $obfuscated);
      $relativePaths = array (
	'meta' => $metaTags_relativeFilepath 
      );
      $call = $this->translateFilepaths ($relativePaths, $obfuscated);
      $filepaths = result($call);
      
      $filepath = $filepaths['meta'];
      return file_get_contents($filepath);
    }
  
  
    $ssc = $this->clientSettings['siteSpecificCMS'];
    $call = $ssc->getTitle ($untranslatedContentURL, $params, $obfuscated);
    if (!is_null($call)) {
      return $call;
    } else {
      $metaURL = $this->getMetaURL ($untranslatedContentURL, $params);
      $cp = array (
	'metaURL' => $metaURL
      );
      $call = $this->translateFilepaths ($cp, $obfuscated);
      $tf = result($call);
      return $tf['metaURL'];
    }
  */
  }

  public function getMetaURL ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  //global $seductiveapps_installedApps;
	  $s = $this->clientSettings;
	  $sia = $s['siteInstalledApps'];
	  
	  $r = null;
	  
	  $sia['site'] = true;
	  foreach ($sia as $appName => $appConfig) {
		  $functionName = 'getMetaURL_4app_'.$appName;
		  if (function_exists($functionName)) {
		    $r = call_user_func($functionName, $params);
		  }
		  /*
		  $dbg = array (
			  '$appName' => $appName,
			  '$appr' => $appr,
			  '$_GET' => $_GET
		  );
		  var_dump ($dbg);*/
	  };
	  
	  if (
	    is_null($r)
	    || $r===false
	  ) {
	    $r = $this->getMetaURL_4site($template, $untranslatedContentURL, $params, $obfuscated);
	  }
	  
	  return $r;
	  
	  /*
	  if (is_array($r)) {
		  $ret = '';
		  
		  foreach ($r as $k=>$v) {
			  $ret .= '<meta name="'.$k.'" content="'.$v.'"/>'."\r\n\t";
		  }
		  
		  $r = $ret;
	  };
	  
	  //var_dump ($r);die();
	  if ($r===false) {
	    $r = str_replace("\r\n", "\r\n\t", getMetaURL_4site($untranslatedContentURL));
	  }
	  
	  return $r;
	  */
  }

  public function getMetaURL_4site ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  //$p = getContentBrowserURL ($contentURL);
	  //$gcs = result($p);
	  
	  //$file = str_replace('SA_SITE_HD/', SA_SITE_HD, $gcs['url']).'.meta.php';
	  
	  
	  //$file = dirname(__FILE__).'/../siteContent/frontpage.php.meta.php';
	  //$file = saConfig__location('siteFramework', 'hd').'/siteContent/frontpage.php.meta.php';
	  $file = 'SITE_FRAMEWORK_HD/siteContent/frontpage.php.meta.php';
	  return $file;
	  
	  //$er = error_reporting (0);
	  //ob_end_flush();
	  /*
	  ob_start();
	  include($file);	
	  $r = ob_get_contents();
	  ob_end_clean();
	  //error_reporting ($er);
	  return $r;
	  */
  }

  public function getAppsHead ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  //if (array_key_exists('rootURL', $_GET)) $untranslatedContentURL = str_replace(SA_SITE_WEB, '', $rootURL) . $untranslatedContentURL;
	  //$untranslatedContentURL = str_replace('content/', '', $untranslatedContentURL);
	  
	  //echo 't1: '; var_dump ($untranslatedContentURL);
	  
	  //global $seductiveapps_installedApps;
	  $s = $this->clientSettings;
	  $sia = $s['siteInstalledApps'];
	  
	  $r = '';
	  
	  $sip['site'] = true;
	  foreach ($sip as $appName => $appConfig) {
		  //echo $appName.' - ';
		  $functionName = 'getHead_4app_'.$appName;
		  //var_dump (function_exists($functionName));
		  //echo '<br/>';
		  $appr = (
			  function_exists($functionName)
			  ? call_user_func($functionName, $params)
			  : false
		  );
		  /*
		  $dbg = array (
			  '$appName' => $appName,
			  '$appr' => $appr//,
			  //'$_GET' => $_GET
		  );
		  var_dump ($dbg);
		  */
		  $r .= $appr;
	  }
	  //die();
	  
	  return goodResult($r);
  }

  public function getAppsJavascript ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  //if (array_key_exists('rootURL', $_GET)) $untranslatedContentURL = str_replace(SA_SITE_WEB, '', $rootURL) . $untranslatedContentURL;
	  //$untranslatedContentURL = str_replace('content/', '', $untranslatedContentURL);
	  
	  //echo 't1: '; var_dump ($untranslatedContentURL);
	  
	  global $seductiveapps_installedApps;
	  
	  $r = '';
	  
	  $seductiveapps_installedApps['site'] = true;
	  foreach ($seductiveapps_installedApps as $appName => $appConfig) {
		  //echo $appName.' - ';
		  $functionName = 'getJavascript_4app_'.$appName;
		  //var_dump (function_exists($functionName));
		  //echo '<br/>';
		  $appr = (
			  function_exists($functionName)
			  ? call_user_func($functionName, $params)
			  : false
		  );
		  /*
		  $dbg = array (
			  '$appName' => $appName,
			  '$appr' => $appr//,
			  //'$_GET' => $_GET
		  );
		  var_dump ($dbg);
		  */
		  
		  $r .= $appr;
	  }
	  return goodResult($r);
  }  
  

	public function urlSpecificSettings ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === saConfig__saCloud()
	  $ssc = $this->clientSettings['siteSpecificCMS'];
	  
	  //$this->urlSpecificSettings_menu ($url, 'siteMenu');
	  
	  $arrayOne = array();
	  //$arrayOne = $this->urlSpecificSettings_menu ($untranslatedContentURL, 'musicMenu');
	  //$arrayTwo = $ssc->urlSpecificSettings($untranslatedContentURL, $params, $obfuscated);
	  $arrayTwo = array();
	  
	  $r = array_merge ($arrayOne, $arrayTwo);
	  return $r;
	}
	
	/*public function urlSpecificSettings_menu ($untranslatedContentURL, $menuHTMLid) {
	  global $saCMS;

 	  $templateVars = array();
 	  //$templateVars['mymenu'] = file_get_contents ('/path/to/UL-LI.menu.html');
 	  return $templateVars;
	}*/

  private function getHeadCSS ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated = true) {
	  //echo '.../com/cms/class.main.php:::getHeadCSS() $obfuscate=<pre>'; var_dump($obfuscate); echo '</pre>';
    global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
    global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

    global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
    global $saConfig__saCloud;
    global $seductiveapps_installedApps;
      
    global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
    global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
    $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === saConfig__saCloud()
    $ssc = $this->clientSettings['siteSpecificCMS'];
  
    $locMedia = $this->getLocation__media();
    $locMediaURL = $locMedia['url'];
    //$locLib = $this->getLocation__lib();
    //var_dump ($locLib);
    //$locLibURL = $locLib['url'];
    
    //$cc = $this->settings['saConfig__saCloud'];
    
    $r = '';
    
    if ($obfuscated === false) {
	    $r .= $this->getHeadCSS__CLEARTEXT_links ($cc);
    } else {
	    $r .= $this->getHeadCSS__obfuscated ($cc);
    };
    $r .= $ssc->getHeadCSS ($untranslatedContentURL, $params, $obfuscated);
    
    return $r;
	  
  }
  
  private function getHeadCSS__CLEARTEXT_links ($cc) {
	  $sources = $cc['sources']['css']['defaultPaths']['siteTemplate'];
	  $r = '';
	  if (array_key_exists('cleartext', $sources)) {
		  //$r .= '<!-- cleartext -->'."\r\n";
		  $r .= $this->getHeadCSS__CLEARTEXT_links_do ($cc, $sources['cleartext']);
	  }
	  if (array_key_exists('obfuscatable', $sources)) {
		  //$r .= '<!-- obfuscatable -->'."\r\n";
		  $r .= $this->getHeadCSS__CLEARTEXT_links_do ($cc, $sources['obfuscatable']);
	  }
	  return $r;
  }
  
  private function getHeadCSS__CLEARTEXT_links_do ($cc, $sources) {
	  $r = '';

	  $search = array(
		  $cc['currentDomain']['master']['hd']
		  //realpath($cc['site']['roles']['lib_hd'])
	  );
	  $replace = array (
		  $cc['currentDomain']['master']['url']
		  //$cc['site']['roles']['lib_url']
	  );
  
	  //echo '11111<pre>'; var_dump ($sources); die();
	  foreach ($sources as $description => $sourcePath) {
		  // TODO : translation from HD path to URL
		  $src = str_replace($search, $replace, $sourcePath);
		  $r .= '<link type="text/css" rel="stylesheet" media="screen" href="'.$src.'"/>'."\r\n";
	  };
	  
	  return $r;		
  }
  
  private function getHeadCSS__obfuscated ($cc) { // MUCH BETTER - DOES NOT DISCLOSE SITEFRAMEWORK-PW-xyz _and_ is concatenated by /obfuscate-pw-xyz/ajax_obfuscate.php?n=y
	/*$obfuscatedSourcePath = str_replace (
	  $cc['currentDomain']['master']['hd'], 
	  $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/css/siteTemplate.css', 
	  $sourcePath
	);*/
	$obfuscatedSourcePath = $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/css/siteTemplate.css';
	$search = array(
	  $cc['currentDomain']['master']['hd'],
	  realpath($cc['site']['roles']['lib_hd'])
	);
	$replace = array (
	  $cc['currentDomain']['master']['url'],
	  $cc['site']['roles']['lib_url']
	);
	$src = str_replace ($search, $replace, $obfuscatedSourcePath);
	$r = '<link type="text/css" rel="stylesheet" media="screen" href="'.$src.'"/>'."\r\n";
        return $r;
  }
  
  private function getHeadCSS__obfuscated_OLD ($cc) { // DO NOT USE - discloses SITEFRAMEWORK-PW-xyz !!!
	  $sources = $cc['sources']['css']['defaultPaths']['siteTemplate'];
	  echo '$sources=<pre>'; var_dump ($sources); die();
	  $r = '';
	  if (array_key_exists('cleartext', $sources)) {
		  $r .= $this->getHeadCSS__CLEARTEXT_links_do ($cc, $sources['cleartext']);
	  }
	  if (array_key_exists('obfuscatable', $sources)) {
		  $r .= $this->getHeadCSS__obfuscated_do ($cc, $sources['obfuscatable']);
	  }
	  return $r;
  }
  
  private function getHeadCSS__obfuscated_OLD_do ($cc, $sources) {
	  $r = '';
	  foreach ($sources as $description => $sourcePath) {
		  $obfuscatedSourcePath = str_replace (
			  $cc['currentDomain']['master']['hd'], 
			  $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/css/new.localhost', //TODO : tidy up - remove new.localhost
			  $sourcePath
		  );
		  $search = array(
			  $cc['currentDomain']['master']['hd'],
			  realpath($cc['site']['roles']['lib_hd'])
		  );
		  $replace = array (
			  $cc['currentDomain']['master']['url'],
			  $cc['site']['roles']['lib_url']
		  );
		  $src = str_replace ($search, $replace, $obfuscatedSourcePath);
		  $r .= '<link type="text/css" rel="stylesheet" media="screen" href="'.$src.'"/>'."\r\n";
		  
		  //$r .= file_get_contents ($obfuscatedSourcePath);
	  }	
	  return $r;
  }

  private function getHeadCSS__CLEARTEXT_do ($cc, $sources) {
	  $r = '';
	  foreach ($sources as $description => $sourcePath) {
		  $r .= file_get_contents ($sourcePath);
	  }	
	  return $r;
  }
	
  public function NEWPROBLEMATIC__getHeadJavascript ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
    global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
    global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

    global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
    global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
    global $saConfig__saCloud;
    global $seductiveapps_installedApps;
      
    global $saCMS; // PHP class from siteFramework-pw-*/*/com/cms/boot.php
    global $saCMS__settings; // from siteFramework-pw-*/*/com/cms/boot.php
    $cc = $this->clientSettings['siteSpecificFiles']; // === saConfig__saCloud()
    $ssc = $this->clientSettings['siteSpecificCMS'];
    
    $r = ''; // only HTML script tags for in <head>!
    if ($obfuscated) {
      $relativeFilepath = 'webappObfuscator__output/javascript/siteTemplate.js';
      $url = $relativeFilepath;
      $r .= '<script type="text/javascript" src="'.$url.'"></script>';
    } else {
      $r .= $this->getHeadJavascript__CLEARTEXT_links ($cc);
    }
    
	var_dump ($obfuscated); die();
    $r .= $ssc->getHeadJavascript ($cc, $template, $untranslatedContentURL, $params, $obfuscated);
    
    return $r;
  }
	
	public function getHeadJavascript ($cc=null, $template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {

	//echo '$obfuscated 0-<pre>', var_dump($obfuscated); echo '</pre>'; die();

	
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-* /* /com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-* /* /com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
	  $ssc = $woWebsite->clientSettings['siteSpecificCMS'];

	  $fn2 = $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/javascript/siteTemplate.complete.js';
	  //if (file_exists($fn2)) {
	  if (false) {
	    $untranslatedURLs = array (
	      'siteTemplate.complete.js' => $fn2
	    );
	    $call = $this->translateFilepaths ($untranslatedURLs);
	    $translatedToURLs = result ($call);
	    $url = $translatedToURLs['siteTemplate.complete.js'];
	    $r = '<script type="text/javascript" src="'.$url.'"></script>';
	    return $r;
	  } else {
            $r = $this->getHeadJavascript_do ($template, $untranslatedContentURL, $params, $obfuscated);
	  };
	  
	  //var_dump ($obfuscated); die();
        $j = $ssc->getHeadJavascript ($cc, $template, $untranslatedContentURL, $params, $obfuscated);
        $r .= $j;
	  //var_dump ($j); die();
      return $r;
	}	

	
	public function getHeadJavascript_do ($template=null, $untranslatedContentURL=null, $params=null, $obfuscated=true) {
	  global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

	  global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
	  global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
	  global $saConfig__saCloud;
	  global $seductiveapps_installedApps;
	    
	  global $saCMS; // PHP class from siteFramework-pw-* /* /com/cms/boot.php
	  global $saCMS__settings; // from siteFramework-pw-* /* /com/cms/boot.php
	  $cc = $woWebsite__clientSettings['siteSpecificFiles']; // === $saConfig__saCloud
	  $ssc = $this->clientSettings['siteSpecificCMS'];
	  $obfuscate = !$this->usePlaintextOutput();
	  $obfuscator = $webappObfuscator;
	  
	
		$locMedia = $this->getLocation__media();
		$locMediaURL = $locMedia['url'];
		//$locLib = $this->getLocation__lib();
		//var_dump ($locLib);
		//$locLibURL = $locLib['url'];
		$locLibURL = SA_SITE_WEB.'/lib/';
		//global $obfuscator;
		global $saObfuscator__settings;		
		
		//echo '<pre>saConfig__saCloud =  ';var_dump ($cc); die();
		
		$r = '';
		$c = '';
		
		/* !!!! site JSON processing for obfuscation (generate whitelist from keys) is done in 
		.../siteFramework-pw-e8xj2.K8-2jTx_zE37/20150623 2220/siteObfuscation/siteSeductiveApps.php,
		called up by .../obfuscate_sa-pw-EA8zj30.z-3_KLp2/ajax_obfuscate.php
		*/
		
		$call = $ssc->getHeadJavascript__JSON__vividThemes($cc);
		$vividThemesJS = $call['jsonThemes'];
		//echo 'class.main.php:::getHeadJavascript_do(): $vividThemesJS=<pre>'; var_dump ($vividThemes); echo '</pre>'; die();
		/*global $cms;
		$call = $ssc->getHeadJavascript__JSON__vividThemes__populateCacheFile($cc);
		$errs = $call['errs'];
		$vividThemesJS = $call['jsonThemes'];
		*/
		
		//global $saObfuscator__settings;
		$call = $obfuscator->readTokens(); 
		//var_dump ($call);die();
		
		
		//var_dump ($obfuscate);die();
		if ($this->usePlaintextOutput()) {
			$r .= $this->getHeadJavascript__CLEARTEXT_links ($cc);
		} else {
			$fn = $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/javascript/siteTemplate.js';
			$fn2 = $cc['currentDomain']['master']['hd'].'/webappObfuscator__output/javascript/siteTemplate.complete.js';
			$url = str_replace (
				$cc['currentDomain']['master']['hd'],
				$cc['currentDomain']['master']['url'],
				$fn2
			);
			if (file_exists($fn)) {
				$c = $this->getHeadJavascript__obfuscated ($cc, file_get_contents($fn));
			} elseif (file_exists($fn2)) {
				$c = $this->getHeadJavascript__obfuscated ($cc, file_get_contents($fn2));
			}
			
			
			
			$pageSettings = $this->getPageSettings();
			$saPageSettings = jsonEncode($pageSettings['ps']); // (a) you dont want to do unnecessary processing in javascript, (b) leak no vital details
			$saDebugSettings = jsonEncode($this->getDebugSettings());
			
			$plaintextGlobalsURLs =
				'sa.m.globals.urls = {'
				.'	app : "'.SA_SITE_WEB.'",'
				.'	subURL : "'.SA_SITE_SUBDIR.'",'
				.'	lib : "'.$cc['site']['roles']['lib_url'].'",'
				.'	siteData : "'.$cc['site']['roles']['siteData_url'].'",'
				.'	media : "'.$cc['site']['roles']['media_url'].'",'
				.'	vividThemes : "'.$cc['site']['roles']['vividThemes'].'"'
				.'};'
				.'sa.vcc.populateThemes ('.$vividThemesJS.');'
				.'sa.m.initApps();'
				.'sa.db["main"] = sa.s.c.settings.db;'
				.'sa.s.c.settings.pageSettings = '.$saPageSettings.';'
				.'sa.s.c.settings.debug = '.$saDebugSettings.';';
	;
			if ($this->usePlaintextOutput()) {
				$obfuscatedGlobalsURLs = $plaintextGlobalsURLs;
			} else  {
					
				//echo '<pre>$obfuscator->workData=';	var_dump ($obfuscator->workData); echo '</pre>';
				$obfuscatorOutput = $obfuscator->obfuscate ($plaintextGlobalsURLs, 'javascript');
				$wJavascript = $obfuscator->getWorker('javascript');
				//echo '<pre>$wJavascript->workData=';var_dump ($wJavascript->workData); echo '</pre>';
				$obfuscatedGlobalsURLs = $wJavascript->getOutput(); 
				//var_dump ($obfuscatedGlobalsURLs); die();
			};
			
			
			$c .= $obfuscatedGlobalsURLs;
			
			if (
			  !array_key_exists('wo_templateOnly', $_GET)
			  && !array_key_exists('wo_contentOnly', $_GET)
			) {
			  file_put_contents ($fn2, $c);
			}
			$r .= '<script type="text/javascript" src="'.$url.'"></script>';
		};

		$pageSettings = $ssc->getPageSettings();
		$saPageSettings = jsonEncode($pageSettings['ps']); // (a) you dont want to do unnecessary processing in javascript, (b) leak no vital details
		$saDebugSettings = jsonEncode($this->getDebugSettings());
		
		$plaintextGlobalsURLs =
			'sa.m.globals.urls = {'
			.'	app : "'.SA_SITE_WEB.'",'
			.'	subURL : "'.SA_SITE_SUBDIR.'",'
			.'	lib : "'.SA_SITE_WEB.'/lib/",'
			.'	siteData : "'.$cc['site']['roles']['siteData_url'].'",'
			.'	media : "'.$cc['site']['roles']['media_url'].'",'
			.'	vividThemes : "'.$cc['site']['roles']['vividThemes'].'"'
			.'};'
			.'sa.vcc.populateThemes ('.$vividThemesJS.');'
			.'sa.m.initApps();'
			.'sa.db["main"] = sa.s.c.settings.db;'
			.'sa.s.c.settings.pageSettings = '.$saPageSettings.';'
			.'sa.s.c.settings.debug = '.$saDebugSettings.';';
		if ($this->usePlaintextOutput()) {
			$obfuscatedGlobalsURLs = $plaintextGlobalsURLs;
		} else  {
				
			//echo '<pre>$obfuscator->workData=';	var_dump ($obfuscator->workData); echo '</pre>';
			$obfuscatorOutput = $obfuscator->obfuscate ($plaintextGlobalsURLs, 'javascript');
			$wJavascript = $obfuscator->getWorker('javascript');
			//echo '<pre>$wJavascript->workData=';var_dump ($wJavascript->workData); echo '</pre>'; die();
			$obfuscatedGlobalsURLs = $wJavascript->getOutput(); 
			//var_dump ($obfuscatedGlobalsURLs); die();
		};
		
		$r .= 
		  "\r\n"
		  .'<script type="text/javascript" src="'.$locLibURL.'/tinymce-3.5.10/jscripts/tiny_mce/tiny_mce.js"></script>'
		  ."\r\n"
		  .'<script type="text/javascript">'
		    .$obfuscatedGlobalsURLs
		  .'</script>';
		
		
		return $r;
	}
	
	
	private function getHeadJavascript__obfuscated ($cc, $obfuscatedSourceContent) {
		$sources = $cc['sources']['javascripts']['defaultPaths']['siteTemplate'];

		$r = '';
		$r .= $this->getHeadJavascript__CLEARTEXT ($cc, $sources['cleartext']);
		$r .= $this->getHeadJavascript__obfuscated_do ($cc, $sources['obfuscatable']);
		//$r .= $this->getHeadJavascript__JSON__vividThemes($cc);
		return $r;
	}

	private function getHeadJavascript__obfuscated_do ($cc, $sources) {
		$r = '';
		foreach ($sources as $description => $sourcePath) {
			$obfuscatedSourcePath = str_replace (
				$cc['currentDomain']['master']['hd'], 
				$cc['currentDomain']['master']['hd'].'/webappObfuscator__output/javascript/new.localhost',
				$sourcePath
			);
			if (file_exists($obfuscatedSourcePath)) {
				$r .= file_get_contents ($obfuscatedSourcePath);
			}
		};
		return $r;
	}
	

	private function getHeadJavascript__CLEARTEXT ($cc, $sources) {
		$r = '';
		foreach ($sources as $description => $sourcePath) {
			if (file_exists($sourcePath)) {
				$r .= file_get_contents ($sourcePath);
			}
		};
		return $r;
	}
	
	private function getHeadJavascript__CLEARTEXT_links ($cc) {
		//echo '<pre>'; var_dump ($cc); die();
		$sources = $cc['sources']['javascripts']['defaultPaths']['siteTemplate'];
		$r = '';
		//$r .= '<!-- cleartext -->'."\r\n";
		$r .= $this->getHeadJavascript__CLEARTEXT_links_do ($cc, $sources['cleartext']);
		//$r .= '<!-- obfuscatable -->'."\r\n";
		$r .= $this->getHeadJavascript__CLEARTEXT_links_do ($cc, $sources['obfuscatable']);
		return $r;
	}
	
	private function getHeadJavascript__CLEARTEXT_links_do ($cc, $sources) {
		$r = '';

		$search = array(
			$cc['currentDomain']['master']['hd']
			//realpath($cc['site']['roles']['lib_hd'])
		);
		$replace = array (
			$cc['currentDomain']['master']['url']
			//$cc['site']['roles']['lib_url']
		);
	
		//echo '11111<pre>'; var_dump ($sources); die();
		foreach ($sources as $description => $sourcePath) {
			// TODO : translation from HD path to URL
			$r .= '<script type="text/javascript" src="'.str_replace($search, $replace, $sourcePath).'"></script>'."\r\n";
		};
		
		return $r;		
	}
}




?>