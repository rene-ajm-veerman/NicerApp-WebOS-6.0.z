<?php
/* VITAL TIPS FOR NEWBIE DEVELOPERS :

	- If you get yourself a domain name, get yourself only ONE easy-to-remember cool-sounding domain name, 
		and enable "private registration service" when you first register it, 
		or your home address WILL end up at so-called "whowas" sites, and they will NEVER EVER remove your home address!

		- more than 1 domain name is a total waste of money. what you wanna do is create easy-to-remember short URL names immediately after
			your domain name, that gets just as good SEO points as a custom domain name for any app.
			
	- If you're rightfully paranoid (aka "prudent") about industrial espionage, and will be testing in phone stores and apple stores 
		to keep costs down (which i strongly suggest you do - even if you got family / investor money to burn),
		then get yourself a real HTTPS certificate. Without it, anyone can tap your "developer backdoor" URLs 
		and thus get to your actual sources!
		
	- in your javascripts, if you're gonna use a DOM framework like jQuery.com (which i'd recommend), then DO NOT 
		use jQuery or any other DOM framework with a shorthand like $(), always use jQuery(), coz i've found that using $() creates problems
		when u can least use them.
	
*/
require_once (dirname(__FILE__).'/globals.php');
require_once (dirname(__FILE__).'/../webappObfuscator-1.0.0/functions__basicErrorHandling.php');

error_reporting (E_ALL);
set_error_handler ('woBasicErrorHandler');

//var_dump ($dfoSecretHD);die();

require_once ($dfoSecretHD.'siteLogic/functions.php');
require_once ($dfoSecretHD.'apps/apps_available.php'); // provides $dfo__apps

//echo 't700.init: $_GET=<pre>'; var_dump ($_GET); echo '</pre>'; 
//echo 't700.init: $developerMode=<pre>'; var_dump ($developerMode); echo '</pre><br/>'."\r\n";

$debugComplete = false;


$copyrightNotice = file_get_contents(dirname(__FILE__).'/copyrightNotice.txt');

if (!array_key_exists('app', $_GET)) {
	ob_start();
	require_once($dfoSecretHD.'siteContent/frontpage.content.php');
	$pageContent = ob_get_clean();
	
	ob_start();
	include_once($dfoSecretHD.'siteContent/frontpage.metatags.php');
	$pageMetaTags = ob_get_clean();
	
	ob_start();
	include_once($dfoSecretHD.'siteContent/frontpage.title.php');
	$pageTitle = ob_get_clean();
	
} else {
	$r = dfoGetAppContent ($_GET['app'], $dfo__apps);
	$pageContent = $r['pageContent'];
	$pageMetaTags = $r['pageMetaTags'];
	$pageTitle = $r['pageTitle'];
}

//echo 't500'; var_dump ($developerMode); die();
//$developerMode = true;
if ($developerMode) {
	ob_start();
	require_once ($dfoPUBLIChd.'siteContent/head_index_dev_pw-yFnZ7-32.kh76d2h.php'); 
	$headIndex = ob_get_clean();
		// yes, YOU DO change that filename - all after pw- ofcourse. in fact, you'd best change ALL the foldernames & filenames 
		// that contain pw- in this source distribution eh! (they're only in / , /siteContent and /siteLogic)
} else {
	ob_start();
	//echo 't501'; echo $dfoPUBLIChd; var_dump (file_exists($dfoPUBLIChd.'siteContent/head_index.php')); die();
	require_once ($dfoPUBLIChd.'siteContent/head_index.php');
	$headIndex = ob_get_clean();
	
	
	
	ob_start();
	//echo 't7.2'; echo $headIndex; ob_flush(); die();
}
//echo 't7.21'; die();

$sru = substr($siteRootURL, 0, strlen($siteRootURL)-1);
//echo 't732-'.$sru; die();

$headIndex .= 
	'<script type="text/javascript">'
	.'dfo.s.c.globals.urls.site = "'.$sru.'";'
	.'</script>';

//echo 't733'; echo $headIndex; ob_flush(); die();
	//echo'1';die();

	

$wo_output = dirname(__FILE__).'/public/webappObfuscator__output';
//echo $wo_output; die();

/*echo '<pre>$developerMode=';var_dump ($developerMode);echo '</pre>';
echo '<pre>$wo_output =';var_dump ($wo_output);echo '</pre>';
echo '<pre>file_exists($wo_output."/html/siteTemplate.html")=';var_dump(file_exists($wo_output.'/html/siteTemplate.html'));echo '</pre>';
echo '<pre>$_GET=';var_dump($_GET);echo'</pre>';
die();*/

//echo '<pre>804.0.0.0.1=$_GET=';var_dump($_GET);echo'</pre>';

/*if (!array_key_exists('app', $_GET)) {
	$pageContentFile = $wo_output.'/html/frontpage.html';
} else {
	$pageContentFile = $wo_output.'/html/apps__'.$_GET['app'].'.html';
	//$pageContent = file_get_contents($wo_output.'/html/apps__'.$_GET['app'].'.html');
}
$pageContent = file_get_contents($pageContentFile);
*/
/*
echo '<pre>804.0.0.0.1=';var_dump($pageContentFile); var_dump($pageContent);echo'</pre>';
echo '200.0'; 

echo '<pre>'; 
var_dump ($developerMode);
var_dump (file_exists($wo_output.'/html/siteTemplate.html'));
var_dump (  !$developerMode
  && file_exists($wo_output.'/html/siteTemplate.html')
  && !
  (
    array_key_exists ('wo_pw', $_GET)
    || (array_key_exists ('wo_templateOnly', $_GET) && !array_key_exists('wo_contentOnly'))
  )
);
echo '</pre>';
echo '200.1<pre>'; var_dump ($_GET); var_dump (  array_key_exists('wo_contentOnly', $_GET)
  && $_GET['wo_contentOnly']==='yes'
); echo '</pre>';
*/


//echo 't701.beforeMain: <pre>'; var_dump (isset($developerMode)); echo '</pre><br/>'."\r\n";
if (!isset($developerMode)) {
  $err = array (
    'msg' => '$developerMode has not been defined (it\'s a boolean).'
  );
  badResult (E_USER_ERROR, $err);
  die();
  
} else if (!$developerMode) {

    if ($debugComplete) {
      echo 't702.start: $_GET=<pre>'; var_dump ($_GET); echo '</pre>'; 
      echo 't702.start: $developerMode=<pre>'; var_dump ($developerMode); echo '</pre><br/>'."\r\n";
    }


    if (!array_key_exists('wo_pw', $_GET)) {

      if ($debugComplete) { echo 't702.main: wo_pw not given'; }
      /*if (!file_exists($wo_output.'/html/siteTemplate.html')) {
	$err = array (
	  'msg' => '$developerMode==false AND $_GET[\'wo_pw\'] is not passed AND '."\r\n"
	    .'file_exists($wo_output.\'/html/siteTemplate.html\'==false..',
	  '$wo_output' => $wo_output
	);
	badResult (E_USER_ERROR, $err);
	die();
	
      } else { // file_exists($wo_output.'/html/siteTemplate.html') == true
	$err = array (
	  'msg' => '$developerMode==false AND $_GET[\'wo_pw\'] is not passed AND '."\r\n"
	    .'file_exists($wo_output.\'/html/siteTemplate.html\'==true..',
	  '$wo_output' => $wo_output
	);
	badResult (E_USER_NOTICE, $err);
	$template = file_get_contents($wo_output.'/html/siteTemplate.html');
	echo $template;
	die();
      }
      */

      
      if (!array_key_exists('app', $_GET)) {
	      $pageContentFile = $wo_output.'/html/frontpage.html';
	      //$pageContentFile = $dfoSecretHD.'/siteContent/frontpage.content.php';
      } else {
	      $pageContentFile = $wo_output.'/html/apps__'.$_GET['app'].'.html';
	      //$pageContentFile = $dfoSecretHD.'/apps/'.$_GET['app'].'/appContent/'.$_GET['app'].'/index.php';
      }
    
      $pageContent = file_get_contents ($pageContentFile);
    
      $templateFile = $wo_output.'/html/siteTemplate.html';
      if ($debugComplete) { echo 't702.main: $templateFile=<pre>'; var_dump ($templateFile); echo '</pre><br/>'."\r\n"; }
    
      if (!file_exists($templateFile)) {
      //if (!file_exists($filepath)) {
	$err = array(
	  'msg' => 'On requesting $_GET[\'wo_templateOnly\'], an error arose due to '."\r\n"
	    .'!file_exists($filepath)',
	  '$templateFile' => $templateFile
	);
	badResult (E_USER_ERROR, $err);
      } else {
      
	$template = file_get_contents($templateFile);
	$search = array (
		'{$siteRootURL}',
		'{$copyrightNotice}',
		'{$pageTitle}',
		'{$pageMetaTags}',
		'{$headIndex}',
		'{$pageContent}'
	);
	$replace = array (
		$siteRootURL,
		$copyrightNotice,
		$pageTitle,
		$pageMetaTags,
		$headIndex,
		$pageContent
	);

	$template = str_replace ($search, $replace, $template);
      }
      
      
      if (
	array_key_exists('wo_templateOnly',$_GET)
	&& array_key_exists('wo_contentOnly', $_GET)
      ) {
	$err = array(
	  'msg' => 'You can\'t set both $_GET[\'wo_templateOnly\'] and $_GET[\'wo_contentOnly\'].'
	);
	badResult (E_USER_ERROR, $err);
	
      } else if (
	array_key_exists('wo_templateOnly',$_GET)
	&& !array_key_exists('wo_contentOnly', $_GET)
      ) {
	echo $template;
	die();
      } else if (
	!array_key_exists('wo_templateOnly',$_GET)
	&& array_key_exists('wo_contentOnly', $_GET)
      ) {
	echo $pageContent;
	die();
      }
      
    
    } else { // !$developerMode AND array_key_exists('wo_pw', $_GET) == true
      
      if (
	!array_key_exists('wo_templateOnly',$_GET)
	&& !array_key_exists('wo_contentOnly', $_GET)
      ) {
	$err = array(
	  'msg' => '$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND '."\r\n"
	    .'!array_key_exists(\'wo_templateOnly\',$_GET) && !array_key_exists(\'wo_contentOnly\', $_GET)'
	);
	badResult (E_USER_ERROR, $err);
      } else if (
	array_key_exists('wo_templateOnly',$_GET)
	&& array_key_exists('wo_contentOnly', $_GET)
      ) {
	$err = array(
	  'msg' => '$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND '."\r\n"
	    .'array_key_exists(\'wo_templateOnly\',$_GET) && array_key_exists(\'wo_contentOnly\', $_GET).'."\r\n"
	    .'You can pass only one: $_GET[\'wo_templateOnly\']=yes or $_GET[\'wo_contentOnly\']=yes'
	);
	badResult (E_USER_ERROR, $err);
      
      } else if (
	(
	  array_key_exists('wo_templateOnly',$_GET)
	  && !array_key_exists('wo_contentOnly', $_GET)
	) 
	|| (
	  !array_key_exists('wo_templateOnly',$_GET)
	  && array_key_exists('wo_contentOnly', $_GET)
	)
      ) {
      
	if ($debugComplete) {
	  echo 't702.1: $_GET=<pre>'; var_dump ($_GET); echo '</pre>'; 
	  echo 't702.1: $dfo_wo_pw=<pre>'; var_dump ($dfo_wo_pw); echo '</pre><br/>'."\r\n";
	}

	
	
	if (!isset($dfo_wo_pw)) {
	  if ($debugComplete) var_dump (666.1);

	  $err = array(
	    'msg' => 'On requesting either $_GET[\'wo_templateOnly\'] or $_GET[\'wo_contentOnly\'], an error arose due to '."\r\n"
	      .'$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND '."\r\n"
	      .'defined(\'dfo_wo_pw\')==false'
	  );
	  badResult (E_USER_ERROR, $err);
	} else if (!is_string($dfo_wo_pw)) {
	  if ($debugComplete) var_dump (666.2);
	  $err = array(
	    'msg' => 'On requesting either $_GET[\'wo_templateOnly\'] or $_GET[\'wo_contentOnly\'], an error arose due to '."\r\n"
	      .'$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND defined(\'dfo_wo_pw\')==true'."\r\n"
	      .'BUT is_string(\'dfo_wo_pw\')==false'
	  );
	  badResult (E_USER_ERROR, $err);
	} else if (!is_string($_GET['wo_pw'])) {
	  if ($debugComplete) var_dump (666.3);
	  $err = array(
	    'msg' => 'On requesting either $_GET[\'wo_templateOnly\'] or $_GET[\'wo_contentOnly\'], an error arose due to '."\r\n"
	      .'$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND defined(\'dfo_wo_pw\')==true'."\r\n"
	      .'BUT is_string($_GET[\'dfo_wo_pw\'])==false'
	  );
	  badResult (E_USER_ERROR, $err);
	} else if ($_GET['wo_pw'] !== $dfo_wo_pw) {
	  if ($debugComplete) var_dump (666.4);
	  $err = array(
	    'msg' => 'On requesting either $_GET[\'wo_templateOnly\'] or $_GET[\'wo_contentOnly\'], an error arose due to '."\r\n"
	      .'$developerMode==true AND array_key_exists(\'wo_pw\', $GET) == true AND defined(\'dfo_wo_pw\')==true'."\r\n"
	      .'BUT $_GET[\'dfo_wo_pw\'] !== $dfo_wo_pw'
	  );
	  badResult (E_USER_ERROR, $err);
	} else {
	  if ($debugComplete) var_dump (666.5);
	  if (
	    array_key_exists('wo_templateOnly',$_GET)
	    && array_key_exists('wo_contentOnly', $_GET)
	  ) {
	    $err = array(
	      'msg' => 'You can\'t set both $_GET[\'wo_templateOnly\'] and $_GET[\'wo_contentOnly\'].'
	    );
	    badResult (E_USER_ERROR, $err);
	    
	  } else if (
	    array_key_exists('wo_templateOnly',$_GET)
	    && !array_key_exists('wo_contentOnly', $_GET)
	  ) {
	  

	    $templateFile = dirname(__FILE__).'/siteTemplate.tpl';
	    if ($debugComplete) { echo 't703: $templateFile=<pre>'; var_dump ($templateFile); echo '</pre><br/>'."\r\n"; }
	    
	    if (!array_key_exists('app', $_GET)) {
		    //$pageContentFile = $wo_output.'/html/frontpage.html';
		    $pageContentFile = $dfoSecretHD.'/siteContent/frontpage.content.php';
	    } else {
		    //$pageContentFile = $wo_output.'/html/apps__'.$_GET['app'].'.html';
		    //$pageContent = file_get_contents($wo_output.'/html/apps__'.$_GET['app'].'.html');
		    $pageContentFile = $dfoSecretHD.'/apps/'.$_GET['app'].'/appContent/'.$_GET['app'].'/index.php';
	    }
	  
	    $pageContent = file_get_contents ($pageContentFile);
	  
	    //$templateFile = $wo_output.'/html/siteTemplate.html';
	  
	    if (!file_exists($templateFile)) {
	    //if (!file_exists($filepath)) {
	      $err = array(
		'msg' => 'On requesting $_GET[\'wo_templateOnly\'], an error arose due to '."\r\n"
		  .'!file_exists($filepath)',
		'$templateFile' => $templateFile
	      );
	      badResult (E_USER_ERROR, $err);
	    } else {
	    
	      $template = file_get_contents($templateFile);
	      $search = array (
		      '{$siteRootURL}',
		      '{$copyrightNotice}',
		      '{$pageTitle}',
		      '{$pageMetaTags}',
		      '{$headIndex}',
		      '{$pageContent}'
	      );
	      $replace = array (
		      $siteRootURL,
		      $copyrightNotice,
		      $pageTitle,
		      $pageMetaTags,
		      $headIndex,
		      $pageContent
	      );

	      $output = str_replace ($search, $replace, $template);
	      echo $output;
	      die();
	    }
	    
	  } 
	  
	  if ($debugComplete) { echo 't704.t: $_GET=<pre>'; var_dump ($_GET); echo '</pre><br/>'."\r\n"; }
	  if (
	    !array_key_exists('wo_templateOnly',$_GET)
	    && array_key_exists('wo_contentOnly', $_GET)
	  ) {
	    if (!array_key_exists('app', $_GET)) {
		    $pageContentFile = $dfoSecretHD.'/siteContent/frontpage.content.php'; //TODO perhaps load up frontpage.php instead
	    } else {
		    $pageContentFile = $dfoSecretHD.'/apps/'.$_GET['app'].'/appContent/'.$_GET['app'].'/index.php';
	    }
	    if ($debugComplete) { echo 't704: $pageContentFile=<pre>'; var_dump ($pageContentFile); echo '</pre><br/>'."\r\n"; }
	    
	    if (!file_exists($pageContentFile)) {
	      $err = array(
		'msg' => 'On requesting $_GET[\'wo_templateOnly\'], an error arose due to '."\r\n"
		  .'!file_exists($pageContentFile)',
		'$pageContentFile' => $pageContentFile
	      );
	      badResult (E_USER_ERROR, $err);
	    
	    } else {
	    
	      $pageContent = file_get_contents($pageContentFile);
	      echo $pageContent;
	      die();
	    }
	  }
	}
      }
    }
    
    
    
} else { // $developerMode == true
  
  $template = file_get_contents(dirname(__FILE__).'/siteTemplate.tpl');
  //echo 't703: $template=<pre>'; var_dump ($template); echo '</pre><br/>'."\r\n";
  
  if (!array_key_exists('app', $_GET)) {
	  //$pageContentFile = $wo_output.'/html/frontpage.html';
	  $pageContentFile = $dfoSecretHD.'/siteContent/frontpage.content.php';
  } else {
	  //$pageContentFile = $wo_output.'/html/apps__'.$_GET['app'].'.html';
	  //$pageContent = file_get_contents($wo_output.'/html/apps__'.$_GET['app'].'.html');
	  $pageContentFile = $dfoSecretHD.'/apps/'.$_GET['app'].'/appContent/'.$_GET['app'].'/index.php';
  }
  //echo 't703: $pageContentFile=<pre>'; var_dump ($pageContentFile); echo '</pre><br/>'."\r\n";
  //echo 't703: file_exists($pageContentFile)=<pre>'; var_dump (file_exists($pageContentFile)); echo '</pre><br/>'."\r\n";
  
  
  if (!file_exists($pageContentFile)) {
    $err = array(
      'msg' => 'On requesting $_GET[\'wo_templateOnly\'], an error arose due to '."\r\n"
	.'!file_exists($pageContentFile)',
      '$pageContentFile' => $pageContentFile
    );
    badResult (E_USER_ERROR, $err);
  
  } else {
  
    //echo 't703.!developerMode: <pre>$_GET='; var_dump ($_GET); echo '</pre><br/>'."\r\n";

    if (array_key_exists('contentOnly', $_GET)) {
      echo $pageContent;
      die();
      
    } else {
    
      $pageContent = file_get_contents ($pageContentFile);
      $search = array (
	      '{$siteRootURL}',
	      '{$copyrightNotice}',
	      '{$pageTitle}',
	      '{$pageMetaTags}',
	      '{$headIndex}',
	      '{$pageContent}'
      );
      $replace = array (
	      $siteRootURL,
	      $copyrightNotice,
	      $pageTitle,
	      $pageMetaTags,
	      $headIndex,
	      $pageContent
      );

      //echo '733--'.htmlentities($template);die();
      $output = str_replace ($search, $replace, $template);
      echo $output;
      die();
    }
  }


}

/*
if (
  $developerMode
  //&& file_exists($wo_output.'/html/siteTemplate.html')
  && !array_key_exists('wo_pw', $_GET)
  (
    array_key_exists ('wo_pw', $_GET)
    || array_key_exists ('wo_templateOnly', $_GET) 
  )
) {
  //$template = file_get_contents($wo_output.'/html/siteTemplate.html');
  //echo '201.1--<pre>'; var_dump (dirname(__FILE__).'/siteTemplate.tpl'); echo '</pre>'; 
  $template = file_get_contents(dirname(__FILE__).'/siteTemplate.tpl');

} else if (
  !$developerMode
  && (
    array_key_exists ('wo_pw', $_GET)
    && $_GET['wo_pw']===$dfo_wo_pw
  )
  && (
    array_key_exists ('wo_templateOnly', $_GET)
  )
  && file_exists($wo_output.'/html/siteTemplate.html')
  {
  $template = file_get_contents($wo_output.'/html/siteTemplate.html');
  
} else if (
  (
    array_key_exists ('wo_pw', $_GET)
    && $_GET['wo_pw']===$dfo_wo_pw
  )
  && array_key_exists('wo_contentOnly', $_GET)
) {
  //echo '201.2--<pre>'; var_dump ($pageContent); echo '</pre>'; 
  echo $pageContent;
	
} else {
  //echo '201.3--<pre>'; var_dump ($wo_output.'/html/siteTemplate.html'); echo '</pre>'; 
  $template = file_get_contents($wo_output.'/html/siteTemplate.html');
  //$template = file_get_contents(dirname(__FILE__).'/siteTemplate.tpl');

}
//echo 201.4; echo '<pre>'; echo(htmlentities($template)); echo '</pre>'; //die();
*/

$search = array (
	'{$siteRootURL}',
	'{$copyrightNotice}',
	'{$pageTitle}',
	'{$pageMetaTags}',
	'{$headIndex}',
	'{$pageContent}'
);
$replace = array (
	$siteRootURL,
	$copyrightNotice,
	$pageTitle,
	$pageMetaTags,
	$headIndex,
	$pageContent
);

//echo '733--'.htmlentities($template);die();
$output = str_replace ($search, $replace, $template);
//echo 201.5; echo '<pre>'; echo(htmlentities($output)); echo '</pre>'; //die();

//ob_start('ob_gzhandler');
echo $output;
?>