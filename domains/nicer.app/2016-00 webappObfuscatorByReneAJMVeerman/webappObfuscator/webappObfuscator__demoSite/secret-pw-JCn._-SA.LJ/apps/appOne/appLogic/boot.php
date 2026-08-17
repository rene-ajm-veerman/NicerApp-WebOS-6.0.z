<?php
// the folder that this file is in, hosts only code that interfaces between the "siteCode + framework code" and the "appCode". 
	// It does NOT contain any code for the app itself!
	
$dfo__appURL__appOne = $siteRootHD.'apps/appContent/appOne';
$dfo__apps['apps']['appOne'] = array(
	'appHD' => dirname(__FILE__).'/../appContent/appOne/',
	'appURL' => $dfo__appURL__appOne,
	'appJavascripts' => array(
		$dfo__appURL__appOne.'index.source.js'
	)
);
?>
