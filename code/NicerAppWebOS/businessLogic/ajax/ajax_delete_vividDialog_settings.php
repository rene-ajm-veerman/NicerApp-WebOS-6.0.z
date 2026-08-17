<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/';
require_once ($rootPathNA.'/boot.php');

$debug = false;
if ($debug) {
    echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$date = new DateTime();
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

$cdbDomain = $naWebOS->domainFolderForDB;

$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$username = $db->translate_plainUserName_to_couchdbUserName ($_SESSION['cdb_loginName']);

$dbName = $cdbDomain.'___themedata';
$cdb->setDatabase($dbName, false);


$findCommand = array (
    'selector' => array(
    ),
    'fields' => array(
        '_id', '_rev'
    )
);
if (array_key_exists('view',$_POST) && !is_null($_POST['view'])) $findCommand['selector']['view'] = $_POST['view'];
if (array_key_exists('url',$_POST) && !is_null($_POST['url'])) $findCommand['selector']['url'] = $_POST['url'];
if (array_key_exists('role',$_POST) && !is_null($_POST['role'])) $findCommand['selector']['role'] = $_POST['role'];
if (array_key_exists('user',$_POST) && !is_null($_POST['user'])) $findCommand['selector']['user'] = $_POST['user'];

try {
    $call = $cdb->find ($findCommand);
    //echo '<pre>'; var_dump ($call); exit();

} catch (Exception $e) {
    echo 'Status : Failed';
    if ($debug) {
        echo 'When : While trying to find in db "'.$dbName.'" the record with the following fields :'.PHP_EOL;
        var_dump ($findCommand['selector']); echo PHP_EOL;
        echo 'Reason : '.$e->getMessage();
    }
}

$d = $call->body->docs[0];
try {

    $call = $cdb->delete ($d->_id,$d->_rev);
    var_dump ($call);
    echo 'Status : Success';
    
} catch (Exception $e) {
    echo 'Status : Failed.'.PHP_EOL;
    if ($debug) {
        echo 'When : While trying to delete from "'.$dbName.'", record with _id='.$d->_id.' and _rev='.$d->_rev.PHP_EOL;
        echo 'Reason : '.$e->getMessage().PHP_EOL;
    }
}
?>
