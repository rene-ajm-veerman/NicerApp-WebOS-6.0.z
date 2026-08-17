<?php
$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
require_once ($rootPath.'/NicerAppWebOS/boot.php');
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
$oldFoldername = $call->body->text;
$call->body->text = $_POST['node_title_new']; // newFolderName = tadaa

//echo '<pre>'; var_dump ($call->body); var_dump ($oldFoldername); echo '</pre>';

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }


$oldPath = $rootPath.'/NicerAppWebOS/siteData/'.$naWebOS->domainFolder.'/'.$_POST['oldPath'];
$newPath = $rootPath.'/NicerAppWebOS/siteData/'.$naWebOS->domainFolder.'/'.$_POST['newPath'];
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
