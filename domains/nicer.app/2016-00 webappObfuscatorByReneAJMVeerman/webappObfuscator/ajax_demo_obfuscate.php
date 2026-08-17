<?php
//echo '1';die();
require_once (dirname(__FILE__).'/demo_globals.php');
global $obfuscatorHD; 

require_once ($obfuscatorHD.'/boot_latestDevelopment.php');
require_once ($obfuscatorHD.'/webappObfuscator-1.0.0/functions__basicErrorHandling.php');

set_time_limit (0);
ini_set('memory_limit', '300M');

error_reporting(E_ALL);
set_error_handler ('woBasicErrorHandler');


//--- we may need some stylesheets :

// change $reportStatusGoesToStatusfile to false in demo_globals.php if you want statusReport() to echo() instead of to write to $statusFile on disk
//	which means if $reportStatusGoesToStatusfile===false, you can directly call this script in the browser without using the fancier statusreport webinterface and 
//	have it call this script via AJAX (XHR).
if ($wo__reportStatusGoesToStatusfile===false) {
?>
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $obfuscatorURL?>/webappObfuscator-1.0.0/webappObfuscator-1.0.0__ajax.css"> 
	<link type="text/css" rel="StyleSheet" media="screen" href="demo_obfuscate.css">
<?php 
}

//--- prepare for the work :


if (file_exists($wo__statusFile)) unlink ($wo__statusFile); // delete the $statusFile if it exists

//--- prepare - fetch the sources if they need to get fetched
	// see demo_globals.php!
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
		// all of these require_once() calls should output a file (or files) that is/are listed in $sources
	if (false) {
	  $script = '/some/path/to/yourSite.com/secret-pw-blablakdjlf83945/obfuscation/someJSONfileThatNeedsToGetGeneratedForInstance.php';
	  echo '<pre>'.$script.' : $errs=';
	  require_once ($script); // UNCOMMENT THAT EH
	  echo '</pre>';
	}


	// --- FETCH NEW SOURCES
	reportStatus (300, '<p class="webappObfuscator__calculating">Getting new sources</p>');
	$fetchedSources = fetchSources ($sources); 
		// fetchSources() : see ..../webappObfuscator-1.0.0/functions
		// $sources : see ./globals.php
	reportStatus (300, '<p class="webappObfuscator__writeCache">Writing to cachefile "'.$cacheFile_sources.'"</p>');
	file_put_contents ($cacheFile_sources, jsonEncode($fetchedSources));
}
$htmlentitiesSources = htmlentitiesSources ($fetchedSources);

$settings = array(
	'paths' => array (
		'secretOutput' => dirname(__FILE__).'/webappObfuscator__demoSite/secret-pw-JCn._-SA.LJ/webappObfuscator__output',
		'publicOutput' => dirname(__FILE__).'/webappObfuscator__demoSite/public/webappObfuscator__output'
	),
	'sourceServer' => $sourceServer,
	'sources' => array (
		'urls' => &$sources,
		'fetched' => &$fetchedSources,
		'htmlentities' => &$htmlentitiesSources	
	)
);	
//;echo '<pre style="color:lime;background:blue">701 : $settings["sources"]["fetched"] = '; var_dump ($settings['sources']['fetched']); echo '</pre>'; die();






//--- MAIN() : preprocess and then obfuscate everything in $settings['sources']['fetched']
reportStatus (300, '<p id="webappObfuscator__process__start" class="webappObfuscator__process">START processing sources data "'.$cacheFile_sources.'"</p>');
$obfuscator = new webappObfuscator ($settings);
$output = $obfuscator->obfuscate();




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
$obfuscator->writeTokensToDisk();

//--- write out the actual obfuscated output to the .../webappObfuscator__demoSite folder 
	// so it can be used by that website's index.php
//echo '10.0.0---<pre>'; var_dump ($obfuscator); echo '</pre>';
$obfuscator->writeOutputToDisk ();

reportStatus (300, '<p id="webappObfuscator__process__finished" class="webappObfuscator__process">ALL DONE</p>');
?>