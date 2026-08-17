<?php
//echo 't100-1'; die();
require_once (dirname(__FILE__).'/../../globals.php');
require_once (dirname(__FILE__).'/../../../webappObfuscator-1.0.0/functions__basicErrorHandling.php');

error_reporting (E_ALL);
set_error_handler ('woBasicErrorHandler');


global $dfoPUBLICurl;
global $dfoPUBLIChd;
global $dfoSecretHD;
global $dfoSecretURL;
global $developerMode;

//echo '502<pre>$dfoSecretHD='; var_dump ($dfoSecretHD); die();
require_once ($dfoSecretHD.'siteLogic/functions.php');
ob_start();

if ($developerMode) {

	$dfoSourcesJShd = array (
		$dfoSecretHD.'/siteLogic/siteCode.source.js'
	);
	//var_dump($dfoSourcesJS); die();

} else {
  $dfoSourcesJShd = array (
    $dfoPUBLIChd.'/webappObfuscator__output/javascript/siteTemplate.js'
  );
};
//$dfoSourcesJSurl = str_replace ($dfoPUBLIChd, $dfoPUBCICurl, $dfoSourcesJShd[0]);

//var_dump($dfoSourcesJS); die();

echo dfoGetJavascripts ($dfoSourcesJShd, true);
?>
<!--  <script type="text/javascript" src="<?php $dfoSourcesURL?>webappObfuscator__output/javascript/siteTemplate"> -->
 