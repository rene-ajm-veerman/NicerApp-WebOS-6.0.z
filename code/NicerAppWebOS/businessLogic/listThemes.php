<?php
require_once(__DIR__.'/../boot.php');
global $naWebOS;

global $naDebugAll;
global $naIP;
$debug = false;

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
 *    $ip !== '::1'
 *    && $ip !== '127.0.0.1'
 *    && $ip !== '80.101.238.137'
 * ) {
 *    header('HTTP/1.0 403 Forbidden');
 *    echo '403 - Access forbidden.';
 *    exit();
 * }*/

global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;
$dbName = $db->dataSetName('themes');
try {
    $cdb->setDatabase($dbName, false);
    //$call = $cdb->getAllDocs();
    //var_dump ($call); exit();
    //$callOK = $call->status === '200';
    $callOK = true;
} catch (Exception $e) {
    echo 'info : database does not yet exist ('.$dbName.').<br/>'.PHP_EOL;
    echo '<pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL;
    exit();
}

echo '<pre>';
var_dump ($cdb->getAllDocs(true));
//if ($callOK) {

?>
