<?php
require_once (dirname(__FILE__).'/../../globals.php');
global $dfoSecretHD;
global $siteRootURL;
global $developerMode;
require_once (dirname(__FILE__).'/../../../demo_globals.php'); // $regxBoundary -> kinda important.
require_once (dirname(__FILE__).'/../../../boot_latestDevelopment.php'); // load webappObfuscator to obfuscate what is in this file.

error_reporting (E_ALL);
set_error_handler ('woBasicErrorHandler');

ob_start();
?>
dfo.s.c.globals.urls.site = '<?php $siteRootURL?>';
<?php

$wo__logLevel = 0;
$siteSettings = ob_get_clean();
ob_end_flush();


if ($developerMode) {
  echo $siteSettings;
} else {

  $settings = array(
	  'paths' => array (
		  'secretOutput' => dirname(__FILE__).'/../../secret-pw-JCn._-SA.LJ/webappObfuscator__output',
		  'publicOutput' => dirname(__FILE__).'/../../public/webappObfuscator__output'
	  )/*,
	  'sources' => array (
	    'fetched' => array (
	      'javascript' => array (
		'siteSettings' => array (
		  $siteSettings
		)
	      )
	    )
	  )*/
  );	


  $obfuscator = new webappObfuscator ($settings);
  $obfuscator->readTokens();
  //echo 't100.0 - <pre>';var_dump ($siteSettings);//->workData__workers['javascript']); 
  $siteSettingsObfuscated = $obfuscator->obfuscateString($siteSettings, 'javascript');

  //echo 't100.1 - <pre>';var_dump ($siteSettingsObfuscated);//->workData__workers['javascript']); 
  echo $siteSettingsObfuscated; 
}
?>
