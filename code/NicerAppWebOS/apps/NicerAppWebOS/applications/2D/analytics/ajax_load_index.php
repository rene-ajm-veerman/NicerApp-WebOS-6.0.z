<?php 
$rootpath = realpath(dirname(__FILE__).'/../../../../..');
require_once ($rootpath.'/boot.php');
require_once ($rootpath.'/3rd-party/sag/src/Sag.php');
require_once ($rootpath.'/Sag-support-functions.php');

global $naDebugAll;
global $naLAN; // true IF the browser is on the internal webserver LAN

$debug = false;
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);

global $naWebOS;
//$naWebOS = new NicerAppWebOS();
//$naWebOS->init();
/*
$couchdbConfigFilepath = $rootpath.'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

if ($debug) { echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;  }

$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
login ($cdb, $cdbConfig);
*/
$db = $naWebOS->dbs->findConnection('couchdb');
 $cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

// create users
//$username = $_SESSION['cdb_loginName'];
//  $username = str_replace(' ', '__', $username);
//$username = str_replace('.', '_', $username);
$username = $db->translate_plainUserName_to_couchdbUserName ($_SESSION['cdb_loginName']);

/*
try {
    $cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);
} catch (Exception $e) {
    if ($debug) { echo 'status : Failed : Login failed (username : '.$username.', password : '.$_GET['pw'].').<br/>'.PHP_EOL; exit(); }
}
if ($debug) { echo 'info : Login succesful (username : '.$username.', password : '.$_GET['pw'].').<br/>'.PHP_EOL;  }

$security_user = '{ "admins": { "names": ["'.$username.'"], "roles": [] }, "members": { "names": ["'.$username.'"], "roles": [] } }';
if ($debug && false) {
    echo 'info : $security_user = '.$security_user.'.<br/>'.PHP_EOL;
    exit();
}*/

//$dbName = $cdbDomain.'___themeData__user___'.strtolower($username);
$dbName = $db->dataSetName('analytics');
if (preg_match('/^\d/', $dbName)) $dbName='number_'.$dbName;
$cdb->setDatabase($dbName, false);

$findCommand = array (
    'selector' => array(
        'date' => $_GET['date']
    ),
    'fields' => array( '_id', 'datetime', 'milliseconds' ),
    'sort' => [
        ['datetime' => 'asc'],
        ['milliseconds' => 'asc'],
    ],
    'use_index' => '_design/7dbf957f7b3c67b312778f5ea6ca91d785ca9cc4'
);
if (array_key_exists('role',$_GET) && !is_null($_GET['role'])) $findCommand['selector']['role'] = $_GET['role'];
if (array_key_exists('user',$_GET) && !is_null($_GET['user'])) $findCommand['selector']['user'] = $_GET['user'];


try { 
    $call = $cdb->find ($findCommand);
} catch (Exception $e) {
    echo 'Error while accessing $dbName='.$dbName.'<br/><pre>'.PHP_EOL;
    echo $e->getMessage();
    exit();
};
if ($debug) {
    echo '<pre class="naCouchDB_findCommand">';
    echo 'info : $findCommand='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
    echo 'info : $call='; var_dump ($call); echo '.<br/>'.PHP_EOL;
    echo '</pre>';
    //exit();
}

$recs = array();

foreach ($call->body->docs as $idx => $d) {
    $call2 = $cdb->get($d->_id);
    $recs[] = (array)$call2->body;
    if ($debug && false) {
        echo '<pre class="naCouchDB_getCommand">';
        echo 'info : $call2='; var_dump ($call2); echo '.<br/>'.PHP_EOL;
        echo '</pre>';
        //exit();
    }
}

if ($debug) {
    echo '<pre class="naCouchDB_dataReturnedToBrowser">';
    echo json_encode ($recs, JSON_PRETTY_PRINT);
    echo '</pre>';
} else {
    echo json_encode ($recs);
}
?>
