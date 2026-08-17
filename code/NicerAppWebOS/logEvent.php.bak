<?php
require_once (dirname(__FILE__).'/boot.php');
require_once (dirname(__FILE__).'/3rd-party/sag/src/Sag.php');
require_once (dirname(__FILE__).'/Sag-support-functions.php');
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

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


global $naWebOS;
//$naWebOS = new NicerAppWebOS();
//$naWebOS->init();

//$couchdbConfigFilepath = realpath(dirname(__FILE__)).'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
//$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

//$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
//$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
//$cdb->useSSL($cdbConfig['useSSL']);
//$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;
$dbName = $db->dataSetName('analytics');
if (preg_match('/^\d/', $dbName)) $dbName='number_'.$dbName;


$cdb->setDatabase($dbName,false);
$doc = json_decode($_POST['doc'], true);
try { $call = $cdb->post($doc); } catch (Exception $e) { cdb_error (500, $e, 'Could not add record to database "'.$dbName.'"'); exit(); };

echo 'Success'; // echo json_encode($recordToAdd); <-- not needed, js will refresh the entire tree (accounting for multiple users working on the same tree at the same time)
?>
