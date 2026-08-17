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

$dbName = 'zoned_at___urls';
$cdb->setDatabase($dbName,false);

if (!filter_var($_POST['urlToShorten'], FILTER_VALIDATE_URL)) {
    echo 'false';
} else {

    $findCommand = array (
        'selector' => array(
            'destination' => $_POST['urlToShorten'],
            'creator' => $ip
        ),
        'fields' => array(
            '_id', 'creator', 'destination', 'shortened'
        )
    );
    $call = $cdb->find ($findCommand);
    //var_dump ($call); die();
    
    if ($call->headers->_HTTP->status===200) {
        foreach ($call->body->docs as $idx => $d) {
            $r = array (
                'shortened' => $d['shortened']
            );
            echo json_encode($r);
            die();
        }
    }

    $newID = '';
    $done = false;
    $tokenLength = 2;
    $doneCounter = 0;
    while (!$done) {
        $newID = randomString($tokenLength);
        
        $findCommand = array (
            'selector' => array(
                'shortened' => $newID
            ),
            'fields' => array(
                '_id', 'creator', 'destination', 'shortened'
            )
        );
        $call = $cdb->find ($findCommand);
        $done = $call->headers->_HTTP->status!==200;
        if (!$done && $doneCounter>10) {
            $tokenLength++;
            $doneCounter = 0;
        }
        $doneCounter++;
    }
    
    if ((int)$_POST['timespanMin']==0 && (int)$_POST['timespanSec']<10) $_POST['timespanSec'] = 10;
    
    $docID = "unix_timestamp_".$date->getTimestamp();
    $newDoc = new stdClass();
    $newDoc->_id = $docID;
    $newDoc->creator = $ip;
    $newDoc->destination = $_POST['urlToShorten'];
    $newDoc->shortened = $newID;
    $newDoc->timespanMin = $_POST['timespanMin'];
    $newDoc->timespanSec = $_POST['timespanSec'];
    $newDoc->manualRedirect = $_POST['manualRedirect'];
    $call = $cdb->put ($docID, $newDoc);
    //echo '<pre style="color:darkgreen;">$db->createDoc('; var_dump ($call); echo ');</pre>';    
    if ($call->headers->_HTTP->status==='201') {
        $r = array (
            'shortened' => $newDoc->shortened
        );
        echo json_encode($r);
    } else echo 'false';
}

?>
