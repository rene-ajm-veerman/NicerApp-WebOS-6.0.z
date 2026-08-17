<?php

function getURLtranslationForLocationBarInfo__4app__googleSearch ($viewName, $param) {
    $ret = array (
        'apps' => array (
            $viewName => array (
                    'query' => $param[0]
            )
        )
    );
    return $ret;
}


function getContentSettings_4app_googleSearch ($p) { // OUTDATED!!!
	$u = explode('/', $p['url']);
	global $saDebug_urlResolver;
	if ($saDebug_urlResolver) {
		echo '/apps/search/appLogic/functions.php::getContentSettings_4app_tools().1 - '; 
		var_dump ($u);
	}
	
	$relInstallPath = 'SA_SITE_HD/apps/tools/appContent/';
	
	//var_dump ($u); exit();
	
	if ($u[0]!=='tools') return false;
	
	
	if (strtolower($u[1])==='webappobfuscator') { //&& SA_VISITOR_IS_toolsELOPER) {
		$r = array (
			'url' => $relInstallPath.'webappObfuscator.php'
		);
	} else if (strtolower($u[1])==='json') { //&& SA_VISITOR_IS_toolsELOPER) {
		$r = array (
			'url' => $relInstallPath.'jsonViewer.php'
		);
	} else if (
		strtolower($u[1])==='sa'
		|| strtolower($u[1])==='nicerapp'
	) {
		$r = array (
			'url' => $relInstallPath.'viewna.php'
		);
	} else if (
		strtolower($u[1])==='sadb'
		|| strtolower($u[1])==='nicerappdb'
	) {
		$r = array (
			'url' => $relInstallPath.'viewSAdb.php'
		);
	} else if (
		strtolower($u[1])==='registration'
	) {
		$r = array (
			'url' => $relInstallPath.'register.php'
		);
	} else if (
		strtolower($u[1])==='registred'
	) {
		$_GET['token'] = $u[2];
		$r = array (
			'url' => $relInstallPath.'registred.php'
		);
	} else if (
		strtolower($u[1])==='viewservicelog'
	) {
		$r = array (
			'url' => $relInstallPath.'viewServiceLog.php'
		);
	} else if (
		strtolower($u[1])==='test'
	) {
		$r = array (
			'url' => $relInstallPath.'test.php'
		);
	} else if (
		strtolower($u[1])==='news'
	) {
		$r = array (
			'url' => $relInstallPath.'news.php'
		);
	} else {
		$r = array (
			'url' => $relInstallPath.'frontpage.php'
		);
	}

	global $saDebug_urlResolver;
	if ($saDebug_urlResolver) {
		echo '/apps/search/appLogic/functions.php::getContentSettings_4app_tools().1 - '; 
		var_dump ($r);
	}
	
	return $r;
}

function getCacheDirs_4app_googleSearch ($p) {
	$r = array (
		'apps/tools/appData/treeDB/DBs' => '|.*|'
	);
	return $r;
}

function getTitle_4app_googleSearch ($p) {
	$u = explode('/', $p['url']);
	global $saDebug_urlResolver;
	if ($saDebug_urlResolver) {
		echo '/apps/search/appLogic/functions.php::getContentSettings_4app_googleSearch().1 - '; 
		var_dump ($u);
	}
	
	$relInstallPath = 'SA_SITE_HD/apps/tools/appContent/';
	
	//var_dump ($u); exit();
	
	if ($u[0]!=='tools') return false;

	return 'Search Google via nicer.app';
}


?>
