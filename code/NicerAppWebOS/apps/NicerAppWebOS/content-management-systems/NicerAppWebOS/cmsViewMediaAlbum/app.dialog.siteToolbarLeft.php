<?php 
$root = realpath (dirname(__FILE__).'/../../../../../..');
require_once ($root.'/NicerAppWebOS/boot.php');
global $naWebOS;
$view = $naWebOS->view;


$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/


?>
<!--<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ -- ><div></div><div></div><div></div></div> -->
<!--<pre><?php //echo json_encode($view, JSON_PRETTY_PRINT);?></pre>-->

<?php
global $naWebOS;

/*
$couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../../').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
//var_dump ($couchdbConfigFilepath); exit();
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

$cdb->setDatabase(str_replace('_tree', '_documents', $view['cmsText']['database']),false);
try { $call = $cdb->get ($view['cmsText']['id']); } catch (Exception $e) { echo $e->getMessage(); exit(); };

echo $call->body->document;
*/

$baseURL = '/NicerAppWebOS/siteData/'.$naWebOS->domainFolder.'/';
$baseDir = $root.'/NicerAppWebOS/siteData/'.$naWebOS->domainFolder.'/';
$targetDir = $baseDir.$view['cmsViewMediaAlbum']['relPath'].'/';
$targetURL = $baseURL.$view['cmsViewMediaAlbum']['relPath'].'/';
$fn = $view['cmsViewMedia']['filename'];

$dbg = array (
    'baseURL' => $baseURL,
    'baseDir' => $baseDir,
    'targetDir' => $targetDir,
    'targetURL' => $targetURL,
    'fn' => $fn,
    'view' => $view
);
//echo '<pre>'.json_encode($dbg,JSON_PRETTY_PRINT).'</pre>';
$_GET['codePath'] = $view['cmsViewMediaAlbum']['relPath'];
$_GET['relPath1'] = realpath(dirname(__FILE__).'/../../../../../..');
$_GET['noIframe'] = true;
require_once (dirname(__FILE__).'/../../../../../../NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/index.php');
?>
