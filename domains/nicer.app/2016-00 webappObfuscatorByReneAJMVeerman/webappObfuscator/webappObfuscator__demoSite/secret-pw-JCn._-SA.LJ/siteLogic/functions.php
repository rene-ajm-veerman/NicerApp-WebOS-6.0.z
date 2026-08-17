<?php
require_once (dirname(__FILE__).'/../../../webappObfuscator-1.0.0/webappObfuscator.globals.php');

$fwn = 'dfo'; global $fwn; // fwn = framework name, 'dfo' = demoForObfuscation


function dfoGetAppContent ($appName, $apps) {
	if (!is_array($apps)) {
		dfoError (E_USER_ERROR, 'dfo_getAppContent() : No app settings provided.');
		return false;
	}
	if (!array_key_exists('apps', $apps)) {
		dfoError (E_USER_ERROR, 'dfo_getAppContent() : Invalid app settings provided.');
		return false;
	}
	if (!array_key_exists($appName, $apps['apps'])) {
		dfoError (E_USER_ERROR, 'dfo_getAppContent() : Can not find app settings for appName="'.$appName.'"');
		return false;
	}
	if (!array_key_exists('appHD', $apps['apps'][$appName])) {
		dfoError (E_USER_ERROR, 'dfo_getAppContent() : Invalid app settings for appName="'.$appName.'"');
		return false;
	}
	if (!array_key_exists('appURL', $apps['apps'][$appName])) {
		dfoError (E_USER_ERROR, 'dfo_getAppContent() : Invalid app settings for appName="'.$appName.'"');
		return false;
	}
	
	//var_dump ($apps);
	$as = $apps['apps'][$appName]; // app settings
	
	//var_dump ($as);
	
	
	if (file_exists($as['appHD'].'index.php')) {
	  ob_start();
	  require_once ($as['appHD'].'index.php');
	  $appContent = ob_get_clean(); // turns output buffering off again tool
	  //var_dump ($appContent);
	} else {
	  dfoError (E_USER_ERROR, 'dfo_getAppContent(): app has no PHP or HTML file to display anything at all..');
	}
	
	$pageMetaTags = '';
	if (file_exists($as['appHD'].'index.metatags.php')) {
	  ob_start();
	  include_once ($as['appHD'].'index.metatags.php');
	  $pageMetaTags = ob_get_clean();
	  var_dump ($pageMetaTags);
	} 
	
	
	$pageTitle = '';
	if (file_exists($as['appHD'].'index.title.php')) {
	  ob_start();
	  include_once ($as['appHD'].'index.title.php');
	  $pageTitle = ob_get_clean();
	}
	
	$r = array(
		'pageContent' => $appContent,
		'pageMetaTags' => $pageMetaTags,
		'pageTitle' => $pageTitle
	);
	
	return $r;	
}

function dfoGetCSS ($sources, $obfuscate=true) {
	// TODO : actually provide the obfuscated sources when required.
	//echo '<pre style="color:lime;background:blue">'; var_dump($sources); echo '</pre>';
	$r = '';
	foreach ($sources as $k => $v) {
	  $obfuscatedV = str_replace ('webappObfuscator__demoSite/', 'webappObfuscator__demoSite/public/webappObfuscator__output/css/new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/', $v);
	  //echo '--2--'; 
	  //echo '<pre style="color:lime;background:blue">$obfuscatedV='; var_dump($obfuscatedV); var_dump (file_exists($obfuscatedV)); echo '</pre>';
	  if (file_exists($obfuscatedV)) {
	    //echo '--1--';
	    $r .= file_get_contents($obfuscatedV);
	  } else {
	    $r .= file_get_contents($v);
	  }
	};
	
	
	//echo '<pre style="color:lime;background:blue">$sources='; var_dump($sources); echo '<br/>$obfuscator=';var_dump($obfuscator); echo '<br/>$r='; var_dump ($r); echo '</pre>';
	return $r;
}

function dfoGetJavascripts ($sources, $obfuscate=true) {
	// TODO : actually provide the obfuscated sources when required.
	$r = '';
	foreach ($sources as $k => $v) {
		$r .= file_get_contents($v);
	};
	return $r;
}




/* we're now using .../webappObfuscator/webappObfuscator-1.0.0/functions__basicErrorHandling.php for this website template instead!
function dfoError ($errCode, $errMsgOrArray) {
	if (is_array($errMsgOrArray)) {
		$errMsg = json_encode($errMsgOrArray);
	} else if (
		is_string($errMsgOrArray)
		|| is_int($errMsgOrArray)
		|| is_float($errMsgOrArray)
	) {
		$errMsg = '' . $errMsgOrArray;
	}
	
	trigger_error ($errMsg, $errCode);
}

function dfoErrorHandler ($errno, $errstr, $errfile, $errline, $errcontext) {
	$stacktrace = debug_backtrace();
	echo '<h1>dfo error</h1><p>'.dfo_errorType_humanReadable($errno).' : '.$errstr.'<br/>stacktrace:</p><pre>';
	var_dump ($stacktrace);
	echo '</pre>';
}

function dfo_errorType_humanReadable ($errNo) {

    if (phpversion() < '4.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
		     2047   => 	'E_ALL'
	);
    } elseif (phpversion() < '5.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2047=> 	'E_ALL'
	);

    } elseif (phpversion() < '5.2.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			2047=> 	'E_ALL'
	);

    } elseif (phpversion() < '5.3.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			6143=> 	'E_ALL'
	);

    } elseif (phpversion() >= '5.3.0' && phpversion() < '6.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			8192=> 	'Depracated',
		       16384=>	'User-level Depracated',
		       30719=> 	'E_ALL'
	);

    } elseif (phpversion() >= '6.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			8192=> 	'Depracated',
		       16384=>	'User-level Depracated',
		       32767=> 	'E_ALL'
	);
    }

    return $errorTypes[$errNo];
}
*/
?>