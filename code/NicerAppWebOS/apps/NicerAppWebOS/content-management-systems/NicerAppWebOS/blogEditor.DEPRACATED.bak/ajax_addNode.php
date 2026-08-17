<?php
$rootPath = realpath(dirname(__FILE__).'/../../../../..');
require_once ($rootPath.'/boot.php');
$debug = true;
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

$dbName = str_replace ('.', '_', $_POST['database']);

// create users
$cdb->setDatabase($dbName,false);
try { $parent = $cdb->get($_POST['parent']); } catch (Exception $e) { 
    if ($debug) cdb_error (404, $e, 'could not find record with id='.$_POST['parent'].' in database '.$_POST['database']); exit();
};

if (
    $parent->body->type !== 'naFolder'
    && $parent->body->type !== 'naMediaFolder'
) {
    if ($debug) cdb_error (403, null, 'parent record is not of the correct type ("naFolder" or "naMediaFolder")'); exit();
}

if (
    $_POST['type'] !== 'naFolder'
    && $_POST['type'] !== 'naDocument'
    && $_POST['type'] !== 'naMediaFolder'
) {
    if ($debug) cdb_error (403, null, 'record to be added is not of the correct type ("naFolder" or "naDocument" or "naMediaFolder")'); exit();
}

if ($_POST['type'] == 'naFolder') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = array ("opened" => true,'selected'=>true);
}
if ($_POST['type'] == 'naDocument') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = ['selected'=>true];
}
if ($_POST['type'] == 'naMediaFolder') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = array ("opened" => true,'selected'=>true);
}

//$children = $cdb->find (...)
// check if children already have ->text==$text
$findCommand = array (
    'selector' => array ( 'parent' => $_POST['parent'] ),
    'fields' => array ( '_id', 'text' )    
);
$call = $cdb->find ($findCommand);
$textFinal = $text;
$found = true;
while ($found) {
    $found = false;
    for ($i=0; $i<count($call->body->docs); $i++) {
        if ($call->body->docs[$i]->text == $textFinal) {
            $textFinal = $text.' ('.($i+1).')';
            $textNum++;
            $found = true;
            break;
        }
    }
}
$id = array_key_exists('id',$_POST) ? $_POST['id'] : cdb_randomString(50);
$recordToAdd = array (
    '_id' => $id,
    'id' => $id,
    'database' => str_replace('_tree','_documents',$_POST['database']),
    'parent' => $_POST['parent'],
    'type' => $_POST['type'],
    'text' =>  $textFinal,
    'state' => $state
);


try { $call = $cdb->post($recordToAdd); } catch (Exception $e) {
    if ($debug) cdb_error (500, $e, 'Could not add record'); exit();
}


$folder = $rootPath.'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['relFilePath'].'/'.$textFinal;

    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms_publicWriteableExecutable;
    global $filePerms_perms_readonly;
    global $filePerms_perms_readWrite;
createDirectoryStructure ($folder, $filePerms_ownerGroup, $filePerms_ownerGroup, $filePerms_perms_readWrite);


$dbg = array (
    'rootPath' => $rootPath,
    'folder' => $folder,
    'success' => true,
    'msg' => 'Record added',
    'recordAdded' => $recordToAdd
);
echo json_encode($dbg);
?>
