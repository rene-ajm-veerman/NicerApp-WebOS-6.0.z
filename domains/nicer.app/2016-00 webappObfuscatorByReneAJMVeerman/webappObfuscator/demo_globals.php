<?php
//require_once(dirname(__FILE__).'/webappObfuscator__demoSite/globals.php');
//echo 'x1'; die();

/* OLD settings 
$sourceServer = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/'; global $sourceServer;
$filenameCopyrightNotice = $sourceServer.'copyrightNotice.txt'; global $filenameCopyrightNotice;
$obfuscateHD = dirname(__FILE__); global $obfuscateHD;
$obfuscateURL = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/'; global $obfuscateURL;
*/

$sourceServer = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/'; global $sourceServer;
$filenameCopyrightNotice = $sourceServer.'copyrightNotice.txt'; global $filenameCopyrightNotice;
$obfuscatorHD = dirname(__FILE__).'/'; global $obfuscatorHD;
$obfuscatorURL = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator'; global $obfuscatorURL;
$obfuscator__outputHD = dirname(__FILE__).'/webappObfuscator__demoSite/webappObfuscator__output'; global $obfuscateHD;
$obfuscator__outputURL = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/public/webappObfuscator__output'; global $obfuscateURL;
$obfuscator__settingsHD = dirname(__FILE__); global $obfuscator__settingsHD;
$obfuscator__settingsURL = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite'; global $obfuscator__settingsURL;
$devServer = 'http://new.localhost/'; global $devServer;

$cacheFile_sources = dirname(__FILE__).'/webappObfuscator__cache/cache.input_source.json'; global $cacheFile_sources;
$cacheFile_workData = dirname(__FILE__).'/webappObfuscator__cache/cache.workData.json'; global $cacheFile_sources;
$wo__statusFile = dirname(__FILE__).'/webappObfuscator__output/status.ajax_demo_obfuscate.html'; global $wo__statusFile;


// google chrome for windows 8 desktop, in July 2015, won't support unicode identifiers/tokens :(
// therefore i don't hold much hope of any other browsers supporting unicode identifiers/tokens..
// see https://groups.google.com/a/chromium.org/forum/#!mydiscussions/chromium-discuss/uuVukTiR6Sc
$useUnicodeIdentifiers = false; global $useUnicodeIdentifiers;


$minTokenLength = 2; global $minTokenLength; // recommended u do not make this less than 2.
$randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); global $randomStringJSO_length; // $randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); are the bare-minimums folks.
//echo'<pre>111:'; var_dump($randomStringJSO_length);

//FAIL : 
//$tokenBoundary = '([\\t\\r\\n\\s\\.;\\(\\)\\[\\]\'"])'; global $tokenBoundary;
//$tokenBoundary = '(\b)'; global $tokenBoundary;
//$tokenBoundary = '([\r\n\s\.;,\(\)\x5b\x5d\x3d\x3a])';global $tokenBoundary; 
$tokenBoundary = '\b'; global $tokenBoundary;
$regxBoundary = '#'; global $regxBoundary;
$searchTokenSpecialChars = array ( '(', ')', '[', ']', '#', '?', '&', '*', '.', '+' ); 
$replaceTokenSpecialChars = array ( '\(', '\)', '\[', '\]', '\#', '\?', '\&', '\*', '\.', '\+' ); 
global $searchTokenSpecialChars; global $replaceTokenSpecialChars;


$wo__reportStatusGoesToStatusfile = (
	array_key_exists('HTTP_REFERER', $_SERVER)
	&& strpos ($_SERVER['HTTP_REFERER'], 'demo_obfuscate')!==false
);
	// if false, reportStatus() outputs via echo(), rather than write to the $statusFile on disk 
	//	(which is read in by the fancier status report webinterface via ajax polling every few seconds)
global $wo__reportStatusGoesToStatusfile;

$lowerMemoryUsage = true; global $lowerMemoryUsage;
	// if true, all that the author of this software package didn't need for testing, gets removed a.s.a.p. from PHP memory.
	
$wo__logLevel = 1000; global $wo__logLevel; // range = 1 to 1000 ; 
	// anything above 900 will print muchos muchos output during preprocess()ing...
		// ...(the token-discovery process, what tokens are found in what snippet of source provided to this obfuscator).
	// 700 is the "oversight" level for preprocessing
	// 500 is the "oversight" level for obfuscation (stage 2, after preprocessing)
	// 300 is the "oversight" level for output production.
	
	// what's listed here is the default, the actually-used $wo__logLevel is listed at the bottom of this file..
	

$errorscodePath = dirname(__FILE__); global $errorscodePath;




//-----------------
	//include your website's global variables that are required for obfuscation operations here:
require_once(dirname(__FILE__).'/webappObfuscator__demoSite/globals.php');


// a website will probably (want to) set $wo__logLevel=0; 
// and then we bump it back up here.. (yea, this is the actual loglevel that'll get used).
$wo__logLevel = 750; // suppress all output
global $wo__logLevel;

//-----------------
	// settings for webappObfuscator that require info from your website's globals go below here :
global $sa_wo_pw;
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
$sources = array (
	'html' => array (
		'siteTemplate' => $sourceServer,
		'frontpage' => $sourceServer,
		'apps__appOne' => $sourceServer.'appOne',
		'apps__appTwo' => $sourceServer.'appTwo'
	),
	'css' => array (
		'siteTemplate' => $sourceServer.'index.css'
	),
	'javascript' => array (
		'siteTemplate' => array(
			$sourceServer.'secret-pw-JCn._-SA.LJ/siteLogic/siteCode.source.js'
		)
	),
	'json' => array (
	)
);

?>
