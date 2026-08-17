<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = false;
global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

if (
    !array_key_exists('database', $_POST)
    || strpos('_documents_', $_POST['database'])===false
) cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');



$cdb->setDatabase($_POST['database'],false);
$call = $cdb->get ($_POST['id']);
$call->body->text = $_POST['text'];
$call->body->seoValue = $_POST['seoValue'];

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }





$dataSetName = $db->dataSetName('data_by_users'); // i know, couchdb calls a 'table' a 'database'. and that sux.

$cdb->setDatabase ($dataSetName, false);


$findCommand = [
    'selector' => [
        'dataID' => $_GET['dataID']
    ],
    'use_index' => 'index_dataID',
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
        $call2->body->seoValue = $_POST['seoValue'];
        /*
        //var_dump ($call2->body); //die();
        $r = null;
        if (property_exists($call2->body, 'viewSettings')) {
            foreach ($call2->body->viewSettings as $fp => &$vs) {
                $vs->seoValue = $_POST['seoValue'];
            }
            //$call2->body->viewSettings->seoValue = $_POST['seoValue'];
*/
            try { $call3 = $cdb->post($call2->body); } catch (Exception $e) {
                cdb_error (500, $e, 'Could not add record'); exit();
            }
            if ($debug) { echo '$call3='; var_dump ($call3); echo PHP_EOL.PHP_EOL; }

        //}
    } elseif (count($call->body->docs)>1) {
        $msg = $fncn.' : multiple views for viewID='.$_GET['viewID'].' were found. using only the first.';
        trigger_error($msg, E_USER_WARNING);
        echo $msg;
        error_log($msg);

        $call = $cdb->get ($call->body->docs[0]['_id']);
        $r = $call->body->docs[0];
    }
}






echo 'status : Success'; 
?>
