<?php
$debug = false;
if ($debug) {
    echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo '<pre>';
}
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

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
$cdbDomain = str_replace('.','_',$naWebOS->domainFolder); global $cdbDomain;

$dbName = $cdbDomain.'___'.$_POST['dbType'];
$cdb = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;
$cdb->setDatabase($dbName, false);

$items = json_decode($_POST['items'],true);
$findCommand = array (
    'selector' => array(
        'menuID' => $_POST['menuID'],
        'url' => $_POST['url'],
        'browserSizeX' => intval($_POST['browserSizeX']),
        'browserSizeY' => intval($_POST['browserSizeY'])
    ),
    'fields' => array(
        '_id', '_rev'
    )
);
if ($debug) { echo '$findCommand='; var_dump ($findCommand); echo PHP_EOL.PHP_EOL; }


$call = $cdb->find ($findCommand);
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

if (count($call->body->docs)===0) { 
    postRecord($cdb, $items);
} else {
    for ($j=0; $j < count($items); $j++) {
        for ($i=0; $i < count($call->body->docs); $i++) {
            try {
                $cdb->delete ($call->body->docs[$i]->_id, $call->body->docs[$i]->_rev);
            } catch (Exception $e) {};
        }
        postRecord($cdb, $items);
    }
}

function postRecord ($cdb, $items) {
    //global $cdb;
    global $cdbDomain;
    global $naWebOS;
    $debug = false;
    $id = cdb_randomString(20); 
    $rec = [
        'id' => $id,
        'menuID' => $_POST['menuID'],
        'url' => $_POST['url'],
        'browserSizeX' => intval($_POST['browserSizeX']),
        'browserSizeY' => intval($_POST['browserSizeY']),
        'items' => $items
    ];
    
    $dbName = $cdbDomain.'___'.$_POST['dbType'];
    $cdb->setDatabase($dbName, false);

    if ($debug) { echo '<pre>'; var_dump ($rec); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
    try {
        $call3 = $cdb->post($rec);
    } catch (Exception $e) {
        if ($debug) {
            echo 'status : Failed : could not update record in database ('.$dbName.').<br/>'.PHP_EOL;
            echo '$rec = <pre style="color:blue">'.PHP_EOL; var_dump ($rec); echo PHP_EOL.'</pre>'.PHP_EOL;
            echo '$call3 = <pre style="color:red">'.PHP_EOL; var_dump ($call3); echo PHP_EOL.'</pre>'.PHP_EOL;
            echo '$e = <pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
            exit();
        
        } else {
            echo 'status : Failed.'; exit();
        }
    }
    if ($debug) {
        echo '<pre>$call3='; var_dump ($call3); var_dump($_POST); var_dump(json_last_error());
        echo '</pre>'.PHP_EOL.PHP_EOL;
    }
            
    if ($call3->headers->_HTTP->status=='201') {
        echo 'status : Success.';
        exit();
    } else {
        echo 'status : Failed.';
        exit();
    }
}

?>
