<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

if (!array_key_exists('database',$_POST)) {
    $dbg = [
        '$_REQUEST' => $_REQUEST
    ];
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database with '.json_encode($dbg).'). Event logged.');
}
if (strpos($_POST['database'], '_tree_')===false)
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');



global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

$cdb->setDatabase($_POST['database'],false);
$doc = array (
    'database' => $_POST['database'],
    '_id' => $_POST['id'],
    'id' => $_POST['id']
);
try { $call = $cdb->get ($_POST['id']); } catch (Exception $e) { cdb_error (404, $e, 'Could not find record'); exit(); };

$root = realpath(dirname(__FILE__).'/../../../').'/siteData/'.$naWebOS->domainFolder.'/';
$path = $root.$_POST['currPath'];
$xec = 'rm -rf "'.$path.'"';
exec ($xec, $output, $result);
//$dbg = array ('x'=>$xec, 'o'=>$output, 'r'=>$result); var_dump ($dbg); exit();

//var_dump ($call->body);exit();

$parents = array ($call->body->_id);
$ids = array(array ('id'=>$call->body->_id,'rev'=>$call->body->_rev));
$docs = $cdb->getAllDocs();
$docz = array();
//var_dump ($docs); exit();
$reset = true;
while ($reset) {
    $reset = false;
    foreach ($docs->body->rows as $idx => $row) {
        if (!array_key_exists($idx, $docz)) $docz[$idx] = $cdb->get ($row->id);
        //echo '<pre>';
        //var_dump ($docz); exit();
        //var_dump ($parents); echo '<br/>'.PHP_EOL;
        if (
            property_exists($docz[$idx],'body')
            && property_exists($docz[$idx]->body,'parent')
            && in_array($docz[$idx]->body->parent, $parents)
            && !in_array($docz[$idx]->body->_id,$parents)
        ) {
            array_push ( $parents, $docz[$idx]->body->_id );
            array_push ( $ids, array ('id'=>$docz[$idx]->body->_id, 'rev'=>$docz[$idx]->body->_rev) );
            $reset = true;
            break;
        }
    }
}
        //echo '<pre>';
        //var_dump ($ids); exit();

foreach ($ids as $idx => $rec) {
    try { $call2 = $cdb->delete($rec['id'], $rec['rev']); } catch (Exception $e) { cdb_error (500, $e, 'Could not delete record'); exit(); };
};
//var_dump ($ids); exit();

//try { $call = $cdb->delete($call->body->_id, $call->body->_rev); } catch (Exception $e) { cdb_error (500, $e, 'Could not delete record'); exit(); };

echo 'status : Success.'; // echo json_encode($recordToAdd); <-- not needed, js will refresh the entire tree (accounting for multiple users working on the same tree at the same time)
?>
