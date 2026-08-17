<?php
//session_start();//
set_time_limit (0);
ini_set('memory_limit', '2500M');

require_once (dirname(__FILE__).'/globals.php');
//require_once ($webappObfuscatorHD.'/boot_latestDevelopment.php');
require_once (dirname(__FILE__).'/../boot__stage__000.php');

global $woWebsite; // PHP class from webappObfuscator-1.0.0/boot.php
global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
global $woWebsite__factorySettings; // from webappObfuscator-1.0.0/boot.php
global $woWebsite__clientSettings; // from webappObfuscator-1.0.0/boot.php

global $webappObfuscator; // PHP class from webappObfuscator-1.0.0/boot.php
global $webappObfuscator__factorySettings; // from webappObfuscator-1.0.0/boot.php
global $webappObfuscator__clientSettings; // from boot__stage__000.php; client is $saCMS
global $saConfig__saCloud;
global $seductiveapps_installedApps;
  
global $saCMS; // PHP class from siteFramework-pw-* / * /com/cms/boot.php
global $saCMS__settings; // from siteFramework-pw-* / * /com/cms/boot.php

error_reporting(E_ALL);
set_error_handler ('woBasicErrorHandler');

global $saConfig__saCloud;
$cc = $saConfig__saCloud;
$sources = $saConfig__saCloud['webappObfuscator__sourcesURL'];

//--- we may need some stylesheets :

// change $reportStatusGoesToStatusfile to false in demo_globals.php if you want statusReport() to echo() instead of to write to $statusFile on disk
//	which means if $reportStatusGoesToStatusfile===false, you can directly call this script in the browser without using the fancier statusreport webinterface and 
//	have it call this script via AJAX (XHR).
//$webappObfuscatorURL = $woWebsite->clientSettings['URLs']['obfuscator'];
$webappObfuscatorURL = SA_SITE_WEB.$frameworkSecretFolder.'siteComponents/webappObfuscator/';

if ($wo__reportStatusGoesToStatusfile===false) {
?>
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $webappObfuscatorURL?>/webappObfuscator-1.0.0/webappObfuscator-1.0.0__ajax.css"/> 
	<link type="text/css" rel="StyleSheet" media="screen" href="demo_obfuscate.css"/>
<?php 
}

//--- prepare for the work :

//--- prepare by deleting old cache files

function deleteFile ($file) {
    if (file_exists($file)) {
        echo '<span style="margin-left:20px">deleting file <span style="color:green;">'.$file.'</span></span><br/>'; 
        unlink($file);
    }
}

echo 
    '<h1 class="webappObfuscator__deleteCache">Deleting cache files from previous runs<br/>'
    .'<span class="webappObfuscator__deleteCache webappObfuscator__phpFile"'.__FILE__.'</span></h1>'.__FILE__.' : <br/>';
$file = dirname(__FILE__).'/../webappObfuscator__output/html/siteTemplate.html'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/html/siteTemplate.complete.html'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/html/frontpage.html'; deleteFile ($file);
$file = dirname(__FILE__).'/webappObfuscator__output/obfuscatedGlobalsURLs.js'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/javascript/siteTemplate.js'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/javascript/siteTemplate.complete.js'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/head.obfuscated.js'; deleteFile ($file);
$file = dirname(__FILE__).'/../webappObfuscator__output/body.obfuscated.js'; deleteFile ($file);
$file = $cacheFile_sources; deleteFile ($file);
$file = $wo__statusFile; deleteFile ($file);
$file = $cc['site']['roles']['siteFramework_hd'].'/siteData/treeDB/DBs/treeDB.all.json'; deleteFile ($file);

$cacheFilesJSON = $cc['site']['roles']['siteFramework_hd'].'/siteCache/misc';
$files = getFilePathList ($cacheFilesJSON, true, '/(cache.*.json)/', array('file'));
foreach ($files as $idx => $file) {
    deleteFile ($file);
}

$cacheFiles = dirname(__FILE__).'/webappObfuscator__output/';
$files = getFilePathList ($cacheFiles, true, '/(.*.json)/', array('file'));
foreach ($files as $idx => $file) {
    deleteFile ($file);
}


//--- prepare - fetch the sources if they need to get fetched
	// see demo_globals.php!
$_GET['n'] = 'y'; // for noob / forgetful developers --> always fetch fresh sources
if (
	file_exists($cacheFile_sources) 
	&& !(
		array_key_exists('n', $_GET)
		|| array_key_exists('ns', $_GET)
	)
)  {
	$fetchedSources = jsonDecode(file_get_contents($cacheFile_sources), true);
	reportStatus (300, '<p class="webappObfuscator__usingCache">Using cache file '.$cacheFile_sources.'</p>');
} else {

	// --- PREPARE for obfuscation source fetching
		// all of these should output a file (or files) that is/are listed in $sources
        echo 
            '<h1 class="webappObfuscator__prepareForObfuscation">Preparing for obfuscation<br/>'
            .'<span class="webappObfuscator__deleteCache webappObfuscator__phpFile"'.__FILE__.'</span></h1>'.__FILE__.' : <br/>';

		
	/* OBSOLETED - JSON (keys) should not be obfuscated!
	$script = saConfig__location('siteFramework', 'hd').'/siteObfuscation/vividThemes.php';
	echo '<pre>'.$script.' : $errs=';
	require_once ($script);
	echo '</pre>';
	*/
	
        $script = saConfig__location('siteFramework', 'hd').'/siteObfuscation/siteSeductiveApps__preObfuscation.php';
        reportStatus (300, '<p id="webappObfuscator__process__writeOutput" class="webappObfuscator__process">executing '.$script.'</p>');
        //echo '<pre>'.$script.' : $errs=';
        require_once ($script);
        //echo '</pre>';
	
	
	// --- FETCH NEW SOURCES into work memory via http and filereads
	reportStatus (300, '<h1 class="webappObfuscator__fetchResources">Getting new sources</h1>');
	$fetchedSources = fetchSources ($sources); 
		// fetchSources() : see ..../webappObfuscator-1.0.0/functions
		// $sources : see ./globals.php
	reportStatus (300, '<p class="webappObfuscator__writeCache">Writing to cachefile "'.$cacheFile_sources.'"</p>');
	file_put_contents ($cacheFile_sources, jsonEncode($fetchedSources));
}
$htmlentitiesSources = htmlentitiesSources ($fetchedSources);

$settings = array(
	'paths' => array (
		'secretOutput' => dirname(__FILE__).'/webappObfuscator__output',
		'publicOutput' => dirname(__FILE__).'/../webappObfuscator__output'
	),
	'sourceServer' => $sourceServer,
	'sources' => array (
		'urls' => &$sources,
		'fetched' => &$fetchedSources,
		'htmlentities' => &$htmlentitiesSources	
	)
);	
$alreadySet = $webappObfuscator->clientSettings;
$setNow = array_merge (
  $alreadySet,
  $settings
);

$setNow = $settings;
$webappObfuscator->setClientSettings ($setNow);
//echo '<pre>ajax_obfuscate.php:$settings='; var_dump ($settings);die();





//--- MAIN() : preprocess and then obfuscate everything in $settings['sources']['fetched']
reportStatus (300, '<p id="webappObfuscator__process__start" class="webappObfuscator__process">START processing sources data "'.$cacheFile_sources.'"</p>');
//$webappObfuscator = new webappObfuscator ($settings);
$output = $webappObfuscator->obfuscate();




//--- write output to server's disk :

//--------------
$outputDebugData = $output;
jsonPrepareUnicode ($outputDebugData);
$json = jsonEncode($outputDebugData);
$outputFilenameDebugData = dirname(__FILE__).'/webappObfuscator__output/webappObfuscatorDebugData.json';
	// i recommend you do NOT put $outputFilenameDebugData in your webfolder (.../htdocs/* or .../webappObfuscator__demoSite/* in this case)
reportStatus (300, 
	'<p class="webappObfuscator__process__writeOutput" class="webappObfuscator__process">Writing obfuscation output data to<br/>'
	.'<span class="webappObfuscator__outputFilename">"'.$outputFilenameDebugData.'"</span><br/></p>'
);
file_put_contents ($outputFilenameDebugData, $json);

//--------------
$webappObfuscator->writeTokensToDisk();

//--- write out the actual obfuscated output to the .../webappObfuscator__demoSite folder 
	// so it can be used by that website's index.php
$webappObfuscator->writeOutputToDisk ();


reportStatus (300, 
	'<p class="webappObfuscator__process__writeOutput" class="webappObfuscator__process">obfuscating project-specific data:<br/>'
	.'<span class="webappObfuscator__outputFilename">"'.$outputFilenameDebugData.'"</span><br/></p>'
);
$outputFilenameHeadJS = dirname(__FILE__).'/../webappObfuscator__output/head.obfuscated.js';
$appsHead = $woWebsite->getAppsHead('/');
$appsJavascript = $woWebsite->getAppsJavascript('/');//.'; sa.sl.settings.current.file="'.$serviceLogFile.'"; ';
global $saCMS;
$headJS = $woWebsite->getHeadJavascript_do($appsJavascript, $appsHead);
file_put_contents ($outputFilenameHeadJS, $headJS);

$outputFilenameBodyJS = dirname(__FILE__).'/../webappObfuscator__output/body.obfuscated.js';
$bodyJS = 'sa.m.initBootScreen();';
if ($woWebsite->usePlaintextOutput()) {
	$obfuscatedJS = $bodyJS; 
} else {
	$webappObfuscatorOutput = $webappObfuscator->obfuscateString ($bodyJS, 'javascript');
	$wJavascript = $webappObfuscator->getWorker('javascript');
	//echo '<pre>$wJavascript->workData=';var_dump ($wJavascript->workData); echo '</pre>';
	$obfuscatedJS = $wJavascript->getOutput(); 
}
file_put_contents ($outputFilenameBodyJS, $bodyJS);


echo 
    '<h1 class="webappObfuscator__prepareForObfuscation">Post-obfuscation handling <br/>'
    .'<span class="webappObfuscator__deleteCache webappObfuscator__phpFile"'.__FILE__.'</span></h1>'.__FILE__.' : <br/>';


$script = saConfig__location('siteFramework', 'hd').'/siteObfuscation/siteSeductiveApps__postObfuscation.php';
reportStatus (300, '<p id="webappObfuscator__process__writeOutput" class="webappObfuscator__process">executing '.$script.'</p>');
//echo '<pre>'.$script.' : $errs=';
require_once ($script);
//echo '</pre>';



reportStatus (300, '<h1 id="webappObfuscator__finished" class="webappObfuscator__process">ALL DONE</h1>');
?>