<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

$cdb->setDatabase($_POST['database'],false);
try { $call = $cdb->get ($_POST['id']); } catch (Exception $e) { cdb_error (404, $e, 'FATAL ERROR : Record with id='.$_POST['id'].' in database='.$_POST['database'].' could not be found.'); exit(); };

//echo '<pre>'; echo json_encode($call->body); echo '</pre>';
echo $call->body->document;
?>
