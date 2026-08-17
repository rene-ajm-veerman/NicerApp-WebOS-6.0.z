<?php

/*
OBSOLETED!!!

json keys should not be obfuscated afterall.. they are likely to show up in the end-user interface.



require_once (dirname(__FILE__).'/../../../boot__stage__000.php');
$saFrameworkLocation = saConfig__location ('siteFramework', 'hd'); 
if (!$saFrameworkLocation) {
	saError (E_USER_ERROR, '/siteObfuscation/vividThemes.php : do not have a framework code location.');
} else {
	global $saConfig__saCloud;
	$cc = $saConfig__saCloud;
	///var_dump(325); var_dump ($saConfig__saCloud);
	
	require_once ($saFrameworkLocation.'/com/cms/boot.php');
	
	
	global $cms;
	$errs = $cms->getHeadJavascript__vividThemes__populateCacheFileJSON($cc);
	//var_dump ($errs);
}


*/
?>
