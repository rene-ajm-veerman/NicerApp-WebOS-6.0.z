<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$fncn = __FILE__;

global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$adb = $naWebOS->dbsAdmin->findConnection('couchdb');
$acdb = $adb->cdb;

$dataSetName = $_REQUEST['database']; // i know, couchdb calls a 'table' a 'database'. and that sux.
$cdb->setDatabase ($dataSetName, false);

    $findCommand = [
        'selector' => [
            'database' => $_REQUEST['database'],//str_replace('_tree_','_document_',$_REQUEST['database']),
            'id' => $_REQUEST['id']
        ],
        'fields' => [ '_id' ]
    ];

    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
        trigger_error ($msg, E_USER_NOTICE);
        echo $msg;
        return false;
    }

    //echo '<pre>'; var_dump ($findCommand); var_dump ($call);

    if (
        is_object($call)
        && is_object($call->body)
        && is_array($call->body->docs)
    ) {
        if (count($call->body->docs)===0) {
            $r = '';
        } elseif (count($call->body->docs)===1) {
            //echo '<pre>';
            //var_dump ($call->body->docs); //die();
            $call2 = $cdb->get ($call->body->docs[0]->_id);
            //var_dump ($call2->body); //die();
            /*
            $r = null;
            if (property_exists($call2->body, 'viewSettings')) {
                foreach ($call2->body->viewSettings as $fp => $vs) {
                    if (property_exists($vs,'seoValue')) $r = $vs->seoValue;
                }
                //$vs = $call2->body->viewSettings;
                //if (property_exists($vs,'seoValue')) $r = $vs->seoValue;
            }*/
            if (property_exists($call2->body,'pageTitle')) $r = $call2->body->pageTitle;

        } elseif (count($call->body->docs)>1) {
            $msg = $fncn.' : multiple views for dataID='.$_GET['dataID'].' were found. using only the first.';
            //trigger_error($msg, E_USER_WARNING);
            //echo $msg;
            error_log($msg);

            $call2 = $cdb->get ($call->body->docs[0]->_id);
            if (property_exists($call2->body,'pageTitle')) $r = $call2->body->pageTitle;
        }
    }

    if (!isset($r)) {
        $newID = '';
        $done = false;
        $tokenLength = 2;
        $doneCounter = 0;
        while (!$done) {
            $newID = randomString($tokenLength);

            $findCommand = [
                'selector' => [ 'database' => $newID ],
                'fields' => [ '_id' ]
            ];
            $call = $cdb->find ($findCommand);
            $done = $call->headers->_HTTP->status!==200;
            if (!$done && $doneCounter>10) {
                $tokenLength++;
                $doneCounter = 0;
            }
            $doneCounter++;
        }

        $seoValue = $newID;
        echo $seoValue;
    } else echo $r;
?>
