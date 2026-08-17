<?php 
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;
$username = array_key_exists('cdb_loginName',$_SESSION) ? $_SESSION['cdb_loginName'] : $cdbConfig['username'];
//echo '<pre>';var_dump ($_SESSION);echo '</pre>';die();
$username = preg_replace ('/.*___/', '', $username);
$username = str_replace(' ', '__', $username);
$username = str_replace('.', '_', $username);

$cdb_domain = $naWebOS->domainFolderForDB;
$tables = array (
    $cdb_domain.'___cms_tree',
    $cdb_domain.'___cms_tree___role___guests',
    $cdb_domain.'___cms_tree___user___'.strtolower($username)
    //$naWebOS->domainFolder.'___cms_tree__user__administrator',
    //$naWebOS->domainFolder.'___cms_tree__user__guest'
);

$data = array();
$ret = array();
foreach ($tables as $idx=>$dbName) {
    $msg = 'ajax_getTreeNodes.php : $dbName='.$dbName;
    //trigger_error ($msg, E_USER_NOTICE);
    //echo $msg.'<br/>';
    $r = $cdb->setDatabase ($dbName, false);
    //var_dump ($r);
        //catch (Throwable $e) { echo '$dbName='.$dbName.'</br>'.PHP_EOL; echo $e->getMessage(); }
        //catch (Exception $e) { echo '$dbName='.$dbName.'</br>'.PHP_EOL; echo $e->getMessage(); };

    //echo '$dbName='.$dbName.'</br>'.PHP_EOL;

    try { $docs = $cdb->getAllDocs(); } catch (Exception $e) { echo $e->getMessage(); };
    $data = $docs->body->rows;
    foreach ($data as $idx2=>$recordSummary) {
        $record = $cdb->get($recordSummary->id);
        $ret = array_merge ($ret, array(json_decode(json_encode($record->body),true)));
    }
}

echo json_encode($ret, JSON_PRETTY_PRINT);
?>
