<?php
require_once (dirname(__FILE__).'/functions.php');

/* from /apps/search:
global $seductiveapps_infoHarvester_settings__config_json;
$seductiveapps_infoHarvester_settings__config_json = realpath(dirname(__FILE__).'/../appData/infoHarvester/treeDB/config.json'); //NEEDED BY NEXT REQUIRE_ONCE()

require_once (dirname(__FILE__).'/../../../seductiveappsClosedsource/infoHarvester/boot.php'); 
*/

function getJStag_4app_tools() {

	global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
	global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
	global $saIsLocalhost; global $saHTDOCShd;
	global $saServerOperatingSystem; global $saDeveloperMode;

		$r = $saSiteURL.'/seductiveapps/apps/tools/appLogic/appCode.source.js';
		return $r;
}

?>