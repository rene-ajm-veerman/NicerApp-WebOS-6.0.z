<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = false;

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

if (!array_key_exists('database',$_POST)) {
    $dbg = [
        '$_REQUEST' => $_REQUEST
    ];
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database with '.json_encode($dbg).'). Event logged.');
}
if (strpos($_POST['database'], '_tree_')===false)
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');



$dbs = json_decode($_POST['databases'],true);
foreach ($dbs as $idx => $dbName) {
    $cdb->setDatabase($dbName, false);
    $call = $cdb->getAllDocs();
    //echo '<pre>'; var_dump ($call); echo '</pre>';
    foreach ($call->body->rows as $idx => $row) {
        $call2 = $cdb->get ($row->id);
        if (
            property_exists($call2->body, 'state')
            && property_exists($call2->body->state, 'selected')
        ) $call2->body->state->selected = false;

        try { $call3 = $cdb->post($call2->body); } catch (Exception $e) {
            cdb_error (500, $e, 'Could not update row'); exit();
        }
    }
};
//die();


$cdb->setDatabase($_POST['database'],false);
$call = $cdb->get ($_POST['id']);
if (!property_exists($call->body,'state') || !is_object($call->body->state))
    $call->body->state = json_decode(json_encode([
        'state' => [ 'selected' => true ]
    ]));
$call->body->state->selected = true;
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add row'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

echo 'Success'; 
?>
