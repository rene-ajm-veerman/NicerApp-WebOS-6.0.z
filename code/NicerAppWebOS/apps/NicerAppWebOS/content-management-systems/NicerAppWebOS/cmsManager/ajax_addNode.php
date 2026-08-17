<?php
$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
require_once ($rootPath.'/NicerAppWebOS/boot.php');
$debug = false;
global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;
//echo '<pre>'; var_dump ($dbg); die();

if (!array_key_exists('database',$_POST)) {
    $dbg = [
        '$_GET' => $_GET,
        '$_POST' => $_POST
    ];
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database with '.json_encode($dbg).'). Event logged.');
}
if (strpos($_POST['database'], '_tree')===false
    && strpos($_POST['database'], '_documents')===false
)
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');

$dbName = str_replace ('.', '_', str_replace('_documents','_tree',$_POST['database']));

// create users
$cdb->setDatabase($dbName,false);
try { $parent = $cdb->get($_POST['parent']); } catch (Exception $e) { 
    if ($debug) cdb_error (404, $e, 'could not find record with id='.$_POST['parent'].' in database '.$_POST['database']); exit();
};

if (
    $parent->body->type !== 'naFolder'
    && $parent->body->type !== 'naMediaAlbum'
    && $parent->body->type !== 'naUserRootFolder'
    && $parent->body->type !== 'naGroupRootFolder'
) {
    if ($debug) cdb_error (403, null, 'parent record is not of the correct type ("naFolder", "naMediaAlbum", "naUserRootFolder" or "naGroupRootFolder")'); exit();
}

if (
    $_POST['type'] !== 'naFolder'
    && $_POST['type'] !== 'naDocument'
    && $_POST['type'] !== 'naMediaAlbum'
) {
    if ($debug) cdb_error (403, null, 'record to be added is not of the correct type ("naFolder" or "naDocument" or "naMediaAlbum")'); exit();
}

if ($_POST['type'] == 'naFolder') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = array ("opened" => true,'selected'=>true);
}
if ($_POST['type'] == 'naMediaAlbum') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = array ("opened" => true,'selected'=>true);
}

//$children = $cdb->find (...)
// check if children already have ->text==$text

$findCommand = array (
    'selector' => array ( 'parent' => $_POST['parent'] ),
    'fields' => array ( '_id', 'text' )    
);
//var_dump ($dbName); var_dump ($cdb); echo PHP_EOL; echo json_encode($findCommand,JSON_PRETTY_PRINT);die();
$call = $cdb->find ($findCommand);
if (!isset($text)) $text = '';
$textFinal = $text;
$textNum = 0;
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

if ($_POST['type'] == 'naDocument') {
    $text = array_key_exists('label',$_POST)?$_POST['label']:'New';
    $state = ['selected'=>true];

    $d =
        date('Y-m-d(l)_H:i:s_')
        .str_replace('-','min',
            str_replace('+','plus',
            preg_replace('/.*\s/','',date(DATE_RFC2822)
        )));
    $textFinal = $d;
}

$id = array_key_exists('id',$_POST) && !is_bool($_POST['id']) && is_string($_POST['id']) && $_POST['id']!=='' ? $_POST['id'] : cdb_randomString(50);
$recordToAdd = array (
    '_id' => $id,
    'id' => $id,
    'database' => $_POST['database'],
    'parent' => $_POST['parent'],
    'type' => $_POST['type'],
    'text' =>  $textFinal,
    'state' => $state
);


//echo '<pre>'; var_dump ($recordToAdd); die();

try { $call = $cdb->post($recordToAdd); } catch (Exception $e) {
    if ($debug) {
        //echo '<pre>'; echo $e->getMessage(); var_dump ($recordToAdd); echo '</pre>';
        cdb_error (500, $e, 'Could not add dbName='.$dbName.', record='.json_encode($recordToAdd));
    }
    exit();
}
//$msg = '$call='.json_encode($call, JSON_PRETTY_PRINT);
//echo '<pre>'; var_dump ($dbName); echo json_encode($recordToAdd, JSON_PRETTY_PRINT); echo '<br/>'; var_dump ($msg); var_dump (debug_backtrace(), JSON_PRETTY_PRINT); echo '</pre>'; die();
//trigger_error ($msg, E_USER_NOTICE);

if (array_key_exists('order',$_POST)) $order = json_decode($_POST['order'], true); else $order = [];

$call3a = $cdb->getAllDocs();
//if ($debug) { echo 't321:<pre>'; var_dump ($call); echo '</pre>';  };

foreach ($call3a->body->rows as $idx => $doc) {
    $call2a = $cdb->get($doc->id);
    if ($debug) { echo 't322:<pre>'; var_dump ($call2a); echo '</pre>';  };
    $doc2 = $call2a->body;
    if (property_exists($doc2, 'parent') && $doc2->parent == $_POST['parent']) {
        if (array_key_exists('order',$_POST)) $doc2->order = getOrder ($order, $doc2->_id);
        if ($debug) { var_dump ($doc2->order); echo '<br/>'.PHP_EOL; }
        $cdb->post ($doc2);
    }
}

    function getOrder ($order, $docID) {
        foreach ($order as $idx2 => $orderID)
            if ($docID==$orderID) return $idx2;
        return 0;
    }



if ($_POST['type'] == 'naDocument') {
    $u = array_key_exists('cdb_loginName',$_SESSION) ? $_SESSION['cdb_loginName'] : $cdbConfig['username'];
    var_dump ($_SESSION); die();
    $u = preg_replace('/.*___/','',$u);
    $u = preg_replace('/__/',' ',$u);
    $u1 = str_replace(' ', '-', $u);
    $u2 = $naWebOS->domainFolderForDB.'___'.$u1;
    $uid = '';
    if (strpos($_POST['database'], '_user')!==false) {
        $uid = ' -F \'user='.$u2.'\'';
    }
    if (strpos($_POST['database'], '_role')!==false) {
        $role = preg_replace('/.*___/','', $_POST['database']);
        $uid = ' -F \'role='.$role.'\'';
    }


    $exec = 'curl -X POST "https://'.$naWebOS->domainFolder.'/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_editDocument.php"';
    $exec2 = ' -F \'database='.str_replace('_tree','_documents',$_POST['database']).'\' -F \'id='.$id.'\' -F \'parent='.$_POST['parent'].'\' '.$uid.' -F \'document=<h1>Heading 1</h1><p>Enter your text here.</p>\' -F \'url1=on\' -F \'seoValue='.$d.'\' -F \'pageTitle=Said.by/'.$u1.'/on/'.$d.'\'';
    $exec2 = str_replace('>', '\\>', $exec2);
    $exec2 = str_replace('<', '\\<', $exec2);
    $exec2 = str_replace('(', '\\(', $exec2);
    $exec2 = str_replace(')', '\\)', $exec2);
    $exec2 = str_replace('/', '\\/', $exec2);
    $exec = $exec.$exec2;
    $r = exec ($exec, $output, $result);
    $dbg = [
        'exec' => $exec,
        'output' => $output,
        'result' => $result,
        'r' => $r
    ];
    //echo '<pre>'; var_dump ($dbg); echo '</pre>';
}


$folder = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['relFilePath'].'/'.$textFinal;

global $filePerms_ownerUser;
global $filePerms_ownerGroup;
global $filePerms_perms_publicWriteableExecutable;
global $filePerms_perms_readonly;
global $filePerms_perms_readWrite;
$x = createDirectoryStructure ($folder, $filePerms_ownerGroup, $filePerms_ownerGroup, $filePerms_perms_readWrite);


$dbg = array (
    'msg' => 'Record added',
    'success' => $x,
    'rootPath' => $rootPath,
    'folder' => $folder,
    'recordAdded' => $recordToAdd,
    'record2added' => $dbg
);
echo json_encode($dbg);
?>
