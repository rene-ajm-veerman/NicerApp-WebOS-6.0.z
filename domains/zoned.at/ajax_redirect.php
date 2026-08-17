<?php 
require_once (dirname(__FILE__).'/functions.php');
require_once (dirname(__FILE__).'/3rd-party/sag/src/Sag.php');

$date = new DateTime();

$couchdbConfigFilepath = realpath(dirname(__FILE__)).'/couchdb.json';
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

$date = new DateTime();
$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);

$dbName = 'zoned_at___urls';
$cdb->setDatabase($dbName,false);

$cmdFind = array (
    'selector' => array ('shortened' => $_POST['shortened']),
    'fields' => array ('_id', 'creator', 'destination', 'shortened', 'displayCount', 'displayCounts', 'redirectionCount', 'redirectionCounts', 'timespanMin', 'timespanSec', 'manualRedirect'),
    'use_index' => 'shortened-index'
);
$call = $cdb->find ($cmdFind);
//var_dump($call);

$call = $cdb->get($call->body->docs[0]->_id);
//var_dump ($call);

$redirectionCounts = json_decode(json_encode($call->body->redirectionCounts), true);
$dt = $date->format('Y-m-d');
if (!$redirectionCounts[$dt]) $redirectionCounts[$dt] = 1; else $redirectionCounts[$dt]++;

$doc = array (
    '_id' => $call->body->_id,
    '_rev' => $call->body->_rev,
    'creator' => $call->body->creator,
    'destination' => $call->body->destination,
    'shortened' => $call->body->shortened,
    'displayCount' => $call->body->displayCount,
    'displayCounts' => $call->body->displayCounts,
    'redirectionCount' => $call->body->redirectionCount + 1,
    'redirectionCounts' => $redirectionCounts,
    'timespanMin' => $call->body->timespanMin,
    'timespanSec' => $call->body->timespanSec,
    'manualRedirect' => $call->body->manualRedirect
);
$call = $cdb->post($doc);
//var_dump ($doc);

?>
