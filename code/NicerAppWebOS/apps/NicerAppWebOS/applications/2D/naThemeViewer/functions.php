<?php 
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/../../../3rd-party/sag/src/Sag.php');
require_once (dirname(__FILE__).'/../../../Sag-support-functions.php');


function tvGetTableRows() {
    global $naDebugAll;
    $debug = false;
    if ($debug) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    $self = '.../NicerAppWebOS/apps/nicer.app/naThemeViewer/functions.php::tvGetTableRows';
    
    global $naIP;
    global $naLAN;
    $ret = '';

    global $naWebOS;
    if (!isset($naWebOS)) {
        $naWebOS = new NicerAppWebOS();
        $naWebOS->init();
    }

    $couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../../').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
    $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
    if ($debug) { 
        echo '$couchdbConfigFilepath='; var_dump ($couchdbConfigFilepath); echo PHP_EOL;
        echo '$cdbConfig='; var_dump ($cdbConfig); echo PHP_EOL; 
    }

    $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
    $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
    $cdb->useSSL($cdbConfig['useSSL']);
    $cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);
    
    $cdbDomain = $naWebOS->domainFolder;
    $cdbDomain = str_replace ('.', '_', $cdbDomain);
    $dbName = $cdbDomain.'___themeData';

    $cdb->setDatabase($dbName,false);
    
    // mangle username correctly : with spaces or . characters, to my couchdb naming convention.
    $username = $cdbConfig['adminUsername'];
    $username = str_replace(' ', '__', $username);
    $username = str_replace('.', '_', $username);
    $pw = $cdbConfig['adminPassword'];

    try {
        $cdb->login($username, $pw);
    } catch (Exception $e) {
        if ($debug) { echo 'status : Failed : Login failed (username : '.$username.', password : '.$pw.').<br/>'.PHP_EOL; exit(); }
    }
    if ($debug) { echo 'info : Login succesful (username : '.$username.', password : '.$pw.').<br/>'.PHP_EOL;  }

    
    
    $cdb->setDatabase($dbName, false);
    try {
        $call = $cdb->getAllDocs();
        $callOK = $call->status === '200';
    } catch (Exception $e) {
        if ($debug) {
            echo 'info : database does not yet exist ('.$dbName.').<br/>'.PHP_EOL;
            echo '<pre style="color:red;text-shadow:none;">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
        // exit();
        }
    }
    if ($debug && false) { echo '<pre style="color:green;text-shadow:none;background:rgba(255,255,255,0.7)">'.$self.' : $call=<br/>'; var_dump ($call); echo '</pre>'; }
    
    
    $ret = array();
    foreach ($call->body->rows as $idx => $doc) {
        $call2 = $cdb->get($doc->id);
        $ret[] = $call2->body;
    }
    if ($debug) { echo '<pre style="color:green;text-shadow:none;background:rgba(255,255,255,0.7)">'.$self.' : $call=<br/>'; var_dump ($call2); echo '</pre>'; }
    
    return $ret;
}

function dbFind () {
    $findCommand = array (
        'selector' => array (
        ),
        'fields' => array ( 
        )
    );
}
?>
