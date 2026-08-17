<?php
// the folder that this file is in, hosts only code that interfaces between the "siteCode + framework code" and the "appCode". 
	// It does NOT contain any code for the app itself!

$dfo__apps['apps']['appTwo'] = array(
	'appHD' => dirname(__FILE__).'/../appContent/appTwo/',
	'appURL' => $siteRootHD.'apps/appContent/appTwo'
);
?>