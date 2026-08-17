<?php 
require_once (dirname(__FILE__).'/functions.php');
require_once (dirname(__FILE__).'/3rd-party/sag/src/Sag.php');


$couchdbConfigFilepath = realpath(dirname(__FILE__)).'/couchdb.json';
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
//echo '<pre>'; var_dump($cdbConfig); echo '</pre>'; die();
$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

$date = new DateTime();
$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);

$dbName = 'zoned_at___log';
$cdb->setDatabase($dbName,true);

$doc = json_decode($_POST['json'], true);
$cdb->post($doc);
?>
