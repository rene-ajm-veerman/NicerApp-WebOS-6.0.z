<?php
//require_once(dirname(__FILE__).'/../opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/globals.php');
$wo__reportStatusGoesToStatusfile = false;
global $wo__reportStatusGoesToStatusfile;

$treatDevelopersAsOrdinaryViewers = false;
global $treatDevelopersAsOrdinaryViewers;

require_once(dirname(__FILE__).'/../globals.php');
global $frameworkSecretFolder;
 
require_once(dirname(__FILE__).'/../config__saCloud.php'); global $saConfig__saCloud;


$sourceServer = 'http://seductiveapps.com/'; global $sourceServer;
$filenameCopyrightNotice = $sourceServer.'LICENSE.txt'; global $filenameCopyrightNotice;
$webappObfuscatorFolderpath = dirname(__FILE__).'/../'.$frameworkSecretFolder.'webappObfuscator'; global $webappObfuscatorFolderpath;
$webappObfuscatorURL = $sourceServer.$frameworkSecretFolder.'webappObfuscator'; global $webappObfuscatorURL;
$webappObfuscator__outputHD = dirname(__FILE__).'/../webappObfuscator__output'; global $webappObfuscator__outputHD;
$webappObfuscator__outputURL = $sourceServer.'webappObfuscator__output'; global $webappObfuscator__outputURL;
$webappObfuscator__settingsHD = dirname(__FILE__); global $webappObfuscator__settingsHD;
$webappObfuscator__settingsURL = $sourceServer.'__obfuscate_thisWebsite__pw-cidjEhsl2_492'; global $webappObfuscator__settingsURL;
//$devServer = 'http://new.localhost/'; global $devServer; NO DUPLICATE ($sourceServer)

$cacheFile_sources = dirname(__FILE__).'/webappObfuscator__cache/cache.input_source.json'; global $cacheFile_sources;
$cacheFile_workData = dirname(__FILE__).'/webappObfuscator__cache/cache.workData.json'; global $cacheFile_sources;
$wo__statusFile = dirname(__FILE__).'/webappObfuscator__output/status.ajax_demo_obfuscate.html'; global $wo__statusFile;


/*
// google chrome for windows 8 desktop, in July 2015, won't support unicode identifiers/tokens :(
// therefore i don't hold much hope of any other browsers supporting unicode identifiers/tokens..
// see https://groups.google.com/a/chromium.org/forum/#!mydiscussions/chromium-discuss/uuVukTiR6Sc
$useUnicodeIdentifiers = false; global $useUnicodeIdentifiers;


$minTokenLength = 2; global $minTokenLength; // minimum = 2.
$randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); global $randomStringJSO_length; // $randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); are the bare-minimums folks.
//echo'<pre>111:'; var_dump($randomStringJSO_length);
*/

/*
//FAIL : 
//$tokenBoundary = '([\\t\\r\\n\\s\\.;\\(\\)\\[\\]\'"])'; global $tokenBoundary;
//$tokenBoundary = '(\b)'; global $tokenBoundary;
//$tokenBoundary = '([\r\n\s\.;,\(\)\x5b\x5d\x3d\x3a])';global $tokenBoundary; 
$tokenBoundary = '\b'; global $tokenBoundary;
$regxBoundary = '#'; global $regxBoundary;
$searchTokenSpecialChars = array ( '(', ')', '[', ']', '#', '?', '&', '*', '.', '+' ); 
$replaceTokenSpecialChars = array ( '\(', '\)', '\[', '\]', '\#', '\?', '\&', '\*', '\.', '\+' ); 
global $searchTokenSpecialChars; global $replaceTokenSpecialChars;
*/

$wo__reportStatusGoesToStatusfile = (
	array_key_exists('HTTP_REFERER', $_SERVER)
	&& strpos ($_SERVER['HTTP_REFERER'], 'demo_obfuscate')!==false
);
	// if false, reportStatus() outputs via echo(), rather than write to the $statusFile on disk 
	//	(which is read in by the fancier status report webinterface via ajax polling every few seconds)

	
global $wo__reportStatusGoesToStatusfile;

$lowerMemoryUsage = true; global $lowerMemoryUsage;
	// if true, all that the author of this software package didn't need for testing, gets removed a.s.a.p. from PHP memory.
	
$wo__logLevel = 500; global $wo__logLevel; // range = 1 to 1000 ; 
	// anything above 900 will print muchos muchos output during preprocess()ing...
		// ...(the token-discovery process, what tokens are found in what snippet of source provided to this obfuscator).
	// 700 is the "oversight" level for preprocessing
	// 500 is the "oversight" level for obfuscation (stage 2, after preprocessing)
	// 300 is the "oversight" level for output production.
	
	// what's listed here is the default, the actually-used $wo__logLevel is listed at the bottom of this file..
	

$errorscodePath = dirname(__FILE__); global $errorscodePath;




//-----------------
	//include your website's global variables that are required for obfuscation operations here:
require_once(dirname(__FILE__).'/../boot__stage__000.php');
global $saConfig__saCloud;


// a website will probably (want to) set $wo__logLevel=0; 
// and then we bump it back up here.. (yea, this is the actual loglevel that'll get used).
$wo__logLevel = 500; // 500 === show me the highlights


//-----------------
	// settings for webappObfuscator that require info from your website's globals go below here :
$wo_pw = $sa_wo_pw; global $wo_pw;
$_GET['wo_pw'] = $wo_pw;

/*
	$sources format : 
		(1) $sources must be an array
		(2) the keys at the first level of the $sources array may ONLY be the programming language names (in *lowercase*), atm : css, html, javascript, json
		(3) below the first key level, you may specify any depth of sub-arrays, BUT:
			(3.1) if you specify any URL in any-subarray, you may ONLY specify URL strings in that sub-array. 
				(or the auto-concatenation features are gonna get waaay too complicated)
*/
$sources = $saConfig__saCloud['webappObfuscator__sourcesURL'];

?>
