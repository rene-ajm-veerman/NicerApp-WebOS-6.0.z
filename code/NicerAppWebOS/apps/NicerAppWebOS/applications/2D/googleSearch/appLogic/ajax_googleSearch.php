<?php
require_once (dirname(__FILE__).'/../../../../../boot_stage_001.php');
//$cc = $saConfig__saCloud;
//require_once ($cc['site']['roles']['siteFramework_hd'].'/boot.php');
require_once (dirname(__FILE__).'/../../../../functions.php');
require_once (dirname(__FILE__).'/../../../../sitewide/functions.php');
require_once (dirname(__FILE__).'/../../../../databases/json/treeDB/boot.php');
require_once (dirname(__FILE__).'/../../../../infoHarvester/boot.php');
require_once (dirname(__FILE__).'/../../../../errorHandling_php/boot.php');
//header ('Content-Type: application/json');

$nicerapp_infoHarvester_settings__config_json = realpath(dirname(__FILE__).'/../appData/infoHarvester/treeDB/config.json'); //NEEDED BY NEXT 
global $nicerapp_infoHarvester_settings__config_json;
//echo 'ajax_search_youtube.php --- $nicerapp_infoHarvester_settings__config_json=<pre>'; var_dump ($nicerapp_infoHarvester_settings__config_json); echo '</pre>';  


global $nicerapp_infoHarvester_settings;
$nicerapp_infoHarvester_settings = json_decode ($nicerapp_infoHarvester_settings__config_json, true);
global $nicerapp_infoHarvester;
$nicerapp_infoHarvester = new nicerapp_infoHarvester($nicerapp_infoHarvester_settings);
/*
echo 'ajax_search_youtube.php --- $nicerapp_infoHarvester_settings=<pre>'; var_dump ($nicerapp_infoHarvester_settings); echo '</pre>';  
echo 'ajax_search_youtube.php --- $nicerapp_infoHarvester=<pre>'; var_dump ($nicerapp_infoHarvester); echo '</pre>';  
echo 'ajax_search_youtube.php --- $nicerapp_infoHarvester[settings][db]=<pre>'; echo svar_dump ($nicerapp_infoHarvester); echo '</pre>';  
exit();
*/

$nicerapp_infoHarvester->settings['db']['treeDBs']['config.json'] = $nicerapp_infoHarvester_settings__config_json;

if (is_null($nicerapp_infoHarvester->db)) {
	if (is_string($nicerapp_infoHarvester->settings['db']['treeDBs']['config.json'])) {
		if (!file_exists($nicerapp_infoHarvester->settings['db']['treeDBs']['config.json'])) {
			badResult (E_USER_ERROR, 'apps/NicerAppWebOS/search/appLogic/ajax_search_youtube.php ::: config file "'.$nicerapp_infoHarvester->settings['db']['treeDBs']['config.json'].'" not found');
		} else {
			$txt = file_get_contents($nicerapp_infoHarvester->settings['db']['treeDBs']['config.json']);
			$json = json_decode($txt,true);
			if (is_null($json)) {
				badResult (E_USER_ERROR, 'apps/NicerAppWebOS/search/appLogic/ajax_search_youtube.php ::: json-invalid config.json file "'.$nicerapp_infoHarvester->settings['db']['treeDBs']['config.json'].'" for needed treeDB');
			}
		}
	}
	if (is_array($nicerapp_infoHarvester->db)) {
	}
	//echo '$json=<pre>'; var_dump ($json); echo '</pre>';  exit();
	
	$nicerapp_infoHarvester->settings['db']['treeDBs']['config'] = $json;
	$db = new saTreeDB ($json);

    if (count($db->db)===0) {
        $db->reInitialize();
    }
	
	$nicerapp_infoHarvester->db = $db;
}

//if (is_null($db)) {
//	echo '<pre>'; var_dump ($nicerapp_infoHarvester); echo '</pre>';
//}
//echo 'ajax_search_youtube.sphp --- $si=<pre>'; var_dump ($si); echo '</pre>';  exit();
/*echo $nicerapp_infoHarvester_settings.'<br/>';
echo $nicerapp_infoHarvester.'<br/>';
exit();*/




/*
$db = $nicerapp_infoHarvester->db;
set_time_limit (0);
*/

//ob_start ('ob_gzhandler');

//var_dump ($_POST); exit();

if (true) {
    $data = &$_GET;
    $url = 'http://google.nl/search?q='.$data['q'];
} else {
    $url = 'https://www.youtube.com/results?search_query=Tropical+Deep+House';
}
	

$urlTreeDB = str_replace('http://','',$url);
$urlTreeDB = str_replace('/', '-', $urlTreeDB);
$urlTreeDB = str_replace('?', '---', $urlTreeDB);
$urlTreeDB = str_replace('&', '-_-', $urlTreeDB);
$urlTreeDB = str_replace('%', '-.-', $urlTreeDB);
$urlTreeDB = str_replace('=', '...', $urlTreeDB);


	global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
	global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
	global $saIsLocalhost; global $saHTDOCShd;
	global $saServerOperatingSystem; global $saDeveloperMode;
	
	global $saUpstreamRootURL; global $locationbarInfo;
	global $saUIdefaults; global $saCouchDB;


$fn = $saFrameworkHD.'/siteCache/googleSearch/'.$urlTreeDB.'.html';

$tid = $db->trStart();
$db->trSet ($tid, 'infoHarvester/', 'urls', '');
$db->trDo ($tid);

global $supressWoErrors; $supressWoErrors = true;
//echo 'ajax_search_youtube.php 2--- $db=<pre>'; var_dump ($db); echo '</pre>'; //exit();
$r = $db->get ('infoHarvester/urls/'.$urlTreeDB, true);

//echo 'ajax_search_youtube.php 3--- $db=<pre>'; var_dump ($db); echo '</pre>'; exit();
$supressWoErrors = false; global $supressWoErrors;
//var_dump ($r); exit();


//reportVariable ('$_POST', $_POST); exit();
// use cache, or not?

if (
    true 
    ||
	(
		array_key_exists('override', $_POST) // manually triggered by #siteYoutubeSearch_submit vividButton
		&& $_POST['override'] == 'true'
	)
) {
	// by doing nothing here, we go to $sourceHTML = file_get_contents($url) further down in this file.
} else {
	if (
		// refresh results every 3 days
		time() <  ((int)$r['crawledAt'] + (60 * 60 * 24 * 3))
	) { 
		echo file_get_contents($fn);
		exit();
	}
}





$sourceHTML = file_get_contents($url);
/*
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=2');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=3');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=4');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=5');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=6');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=7');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=8');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=9');
usleep (330);
$sourceHTML .= file_get_contents($url.'&page=10');
*/

//echo $sourceHTML; exit();

$r = app_googleSearch_process ($sourceHTML); //app_search_youtube_process ($sourceHTML);
$outputHTML = app_googleSearch_process_content ($sourceHTML);


createDirectoryStructure (dirname($fn));
file_put_contents ($fn, $outputHTML);

//echo 'ajax_search_youtube.php 2 --- $db=<pre>'; var_dump ($db); echo '</pre>'; exit();

$tid = $db->trStart();
$db->trSet ($tid, 'infoHarvester/urls', $urlTreeDB, $r);
$db->trDo ($tid);

echo $outputHTML;
//var_dump ($sourceHTML);

function app_googleSearch_process ($s) {
    $ret = array();
    $ret['crawledAt'] = time();
    return $ret;
}

function app_googleSearch_process_content ($s) {
    $r = $s;
    $r = str_replace ('background:#fff','', $r);
    $r = str_replace ('bgcolor="#ffffff"','', $r);
    $r = str_replace ('/xjs','https://google.nl/xjs', $r);
    $r = str_replace ('/client','https://google.nl/client', $r);
    $r = str_replace ('/images','https://google.nl/images', $r);
    $r = str_replace ('/url','https://google.nl/url', $r);
    return $r;
}

function app_search_youtube_process ($s) {
	$r = array();
	
	$criteria = array (
		'videoIDs' => array (
			'regx' => '|<a([^>]*)href="/watch\?v=([^&"]*)([^>]*)title="([^&"]*)([^>]*)"|'
		)
	);
	
	foreach ($criteria as $what => $c) {
		preg_match_all ($c['regx'], $s, $r[$what]);
		//file_put_contents(dirname(__FILE__).'/log1.txt', json_encode($r));
		//file_put_contents(dirname(__FILE__).'/log0.txt', $s);
		//$r[$what] = $r[$what][2];
	}
	$ret = array ();
	$ret[$what] = array ();
	
	foreach ($r[$what][0] as $idx=>$re) {
		if (!is_int($idx/2)) {
			$re2 = array(
				$r[$what][2][$idx] => array (
				'title' => app_search_youtube_process_extractTitle($re)//,
				//'re' => $re
				)
			);
			$ret[$what][] = $re2; 
		}
	}

	$ret['crawledAt'] = time();
	return $ret;
}

function app_search_youtube_process_extractTitle ($html) {
	$p = strpos($html,'title="');
	$q = strpos($html,'"',$p+strlen('title="'));
	return substr($html, $p+7, $q-$p-7);
}


/*
$searchSettings = array (
	'searchterm' => $_GET['searchterm'],
	'plugins' => $nicerapp_infoHarvester_settings['plugins']
);

echo json_encode($nicerapp_infoHarvester->search($searchSettings));
*/
?>
