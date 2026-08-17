<?php 
require_once (dirname(__FILE__).'/php-couchdb/boot.php');
    
$date = new DateTime();
$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);


$serverSettings = array (
    'http' => 'http://',
    'domain' => 'localhost',
    'port' => 5984,
    'adminUsername' => 'admin',
    'adminPassword' => 'texas.t33'
);
$server = new couchdb_server ($serverSettings, $codeLocation);
if (!cdb_processResults ($server, $codeLocation, is_object($server) && !is_null($server->address))) {
    if ($calledFromApache) {
        echo PHP_EOL.'<br/><h2>Cannot connect to server, invalid $serverSettings</h2><br/><pre>'.PHP_EOL; var_dump ($serverSettings); echo '</pre>';
    } else {
        echo PHP_EOL.'Cannot connect to server, invalid $serverSettings'.PHP_EOL;
        var_dump ($serverSettings);
    }
    die();
}

$dbName = 'zoned_at___urls';
$dbSettings = array (
    'server' => $server,
    'dbName' => $dbName,
    'createIfNotExists' => true
);
$db = $server->connectToDB ($dbSettings, $codeLocation); // this call will succeed regardless whether or not the database already exists.
if (cdb_processResults ($db, $codeLocation, is_object($db) && $db instanceof couchdb_database)) {


    if (!filter_var($_POST['urlToShorten'], FILTER_VALIDATE_URL))) {
        echo 'false';
    } else {

        $findCommand = array (
            'server' => $server,
            'dbName' => $dbSettings['dbName'],
            '_find' => array(
                'selector' => array(
                    'destination' => $_POST['urlToShorten'],
                    'creator' => $ip
                ),
                'fields' => array(
                    '_id', 'creator', 'destination', 'shortened'
                )
            )
        );
        $doc = $db->find ($findCommand);
        if (cdb_processResults ($doc, $codeLocation, is_array($doc))) {
            foreach ($doc['docs'] as $idx => $d) {
                $r = array (
                    'shortened' => $d['shortened']
                );
                echo json_encode($r);
                die();
            }
        }

        $newID = '';
        $done = false;
        $tokenLength = 3;
        $doneCounter = 0;
        while (!$done) {
            $newID = cdb_randomString($tokenLength);
            
            $findCommand = array (
                'server' => $server,
                'dbName' => $dbSettings['dbName'],
                '_find' => array(
                    'selector' => array(
                        'shortened' => $newID
                    ),
                    'fields' => array(
                        '_id', 'creator', 'destination', 'shortened'
                    )
                )
            );
            $doc = $db->find ($findCommand);
            $done = cdb_processResults ($doc, $codeLocation, is_array($doc));
            if (!$done && $doneCounter>20) {
                $tokenLength++;
                $doneCounter = 0;
            }
        }
        
        $docSettings = array (
            'server' => $server,
            'dbName' => $dbSettings['dbName'],
            'id' => "unix_timestamp_".$date->getTimestamp(),
            'data' => array (
                'creator' => $ip,
                'destination' => $_POST['urlToShorten'],
                'shortened' => $newID // TODO : put in $var, then check in code above here whether $var already exists, using ->find
            )
        );
        $doc = $db->createDoc ($docSettings);
        //echo '<pre style="color:darkgreen;">$db->createDoc('; var_dump ($docSettings); echo ');</pre>';    
        if (cdb_processResults ($doc, $codeLocation, is_array($doc) && strpos($doc['curl output'][0],'"error":')===false)) {
            $r = array (
                'shortened' => $docSettings['data']['shortened']
            );
            echo json_encode($r);
        } else echo 'false';
    }
}

?>
