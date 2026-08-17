<?php
$rootPathNA = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

    global $naWebOS;
    $db = $naWebOS->dbsAdmin->findConnection('couchdb');
    $cdb = $db->cdb;

    $findCommand = [
        'selector' => [
            'ip' => $naIP
        ],
        'fields' => [ '_id', '_rev' ]
    ];
    $cdbDomain = $naWebOS->domainFolderForDB; //str_replace('.','_',$naWebOS->domainFolder);
    $dataSetName = $cdbDomain.'___themes';
    $cdb->setDatabase ($dataSetName);
    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        cdb_error (503, $e, 'Could not delete themes. $e->getMessage()=='.$e->getMessage());
        exit();
    }
    if (!property_exists($call->body, 'docs')) {
        cdb_error (503, null, 'Could not delete themes. $call='.json_encode($call->body, JSON_PRETTY_PRINT));
        exit();
    }
    foreach ($call->body->docs as $idx => $doc) {
        $cdb->delete ($doc->_id, $doc->_rev);
    }
?>
status : Success.
