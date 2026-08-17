<?php
	require_once (dirname(__FILE__).'/../../globals.php');

	global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
	global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
	global $saIsLocalhost; global $saHTDOCShd;
	global $saServerOperatingSystem; global $saDeveloperMode;
	
	ob_start("ob_gzhandler");
	if ($saIsLocalhost) {
		header ('Access-Control-Allow-Origin: http://localhost'); // replace seductiveapps.com with localhost or your own domain name.
	} else {
		header ('Access-Control-Allow-Origin: http://seductiveapps.com'); // replace seductiveapps.com with localhost or your own domain name.
	}
	
	$url = 'jsonViewer_sample_data.json';
	if (array_key_exists('url', $_GET)) {
		$url = $_GET['url'];
	} 
	if (array_key_exists('url', $_POST)) {
		$url = $_POST['url'];
	}
	
    // always whitelist-filter that shit.
	switch ($url) {
        case 'jsonViewer_sample_data.json':
            returnLocalFile ($url);
            break;
        default :
            echo file_get_contents($url);
            //returnInvalidJSON_warning ($url);
            break;        
	}
	
	
	function returnLocalFile ($url) {
        readfile ($url);
    }
    
    function returnInvalidJSON_warning ($url) {
        $json =
            '{'
            .   '"abc" : "xyz",'
            .   '"001" : "999",'
            .   '"000" : "999",'
            .   '"-_-..D12345" : ":)",'
            .'}';
            
        echo $json;
    }
	//}
?>
