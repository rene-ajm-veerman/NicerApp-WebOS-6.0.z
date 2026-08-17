<?php
require_once (dirname(__FILE__).'/../../../boot__stage__000.php');

$wo__ignoreList__site = array();
global $wo__ignoreList__site;


$saFrameworkLocation = saConfig__location ('siteFramework', 'hd'); 
if (!$saFrameworkLocation) {
	saError (E_USER_ERROR, '/siteObfuscation/vividThemes.php : do not have a framework code location.');
} else {
	global $saConfig__saCloud;
	$cc = $saConfig__saCloud;
	///var_dump(325); var_dump ($saConfig__saCloud);
	
	require_once ($saFrameworkLocation.'/siteComponents/cms/boot.php');
	
	// --- BEGIN --- BOTH in pre and post!
	global $saCMS;
	$errs = $saCMS->getHeadJavascript__JSON__vividThemes__populateCacheFile($cc);
	//var_dump ($errs);
	
	
	$saCMS->getPageSettings__populateCacheFile(null, '/'); // BOTH in pre and post!
	// --- END --- BOTH in pre and post!

        /*
	$cc = $saConfig__saCloud;
	$fd = $cc['site']['roles']['siteFramework_hd'];
	$siteRoot = $cc['site']['roles']['siteRoot_hd'];
	$boot_treeDB__writeFile = true;
	global $boot_treeDB__writeFile;
	require_once ($siteRoot.'/siteLogic/boot_treeDB.php');
	*/
}


?>