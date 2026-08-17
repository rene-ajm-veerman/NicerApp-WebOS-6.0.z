<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = true;

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

$cdb->setDatabase($_POST['database'],false);
$call = $cdb->get ($_POST['target']);
$call->body->parent = $_POST['newParent'];

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

$oldPath = realpath(dirname(__FILE__).'/../../../../..').'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['oldPath'];
$newPath = realpath(dirname(__FILE__).'/../../../../..').'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['newPath'];
$xec = 'mv "'.$oldPath.'" "'.$newPath.'"';
exec ($xec, $output, $result);
$dbg = array (
    'xec' => $xec,
    'output' => $output,
    'result' => $result
);
if ($debug) { echo '<pre style="color:green">'; var_dump ($dbg); echo '</pre>'; }

echo 'status : Success'; 
?>
