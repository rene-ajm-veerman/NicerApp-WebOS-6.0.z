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




$cdb->setDatabase($_POST['database'],false);
$call = $cdb->get ($_POST['id']);
$call->body->state->selected = true;
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

echo 'Success'; 
?>
