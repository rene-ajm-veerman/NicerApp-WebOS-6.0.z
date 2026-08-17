<?php 
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = false;

//session_start();var_dump ($_SESSION); exit();
global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$username = array_key_exists('cdb_loginName',$_COOKIE) ? $_COOKIE['cdb_loginName'] : 'Guest';
//echo '<pre>t342:';var_dump ($username);echo '</pre>';exit();
//$username = str_replace(' ', '__', $username);
//$username = str_replace('.', '_', $username);
$username1 = $db->translate_couchdbUserName_to_plainUserName ($username);
$username1a = preg_replace('/.*___/','',$db->translate_plainUserName_to_couchdbUserName ($username1));

$cdb_domain = $naWebOS->domainFolderForDB;
$tables = array (
    $cdb_domain.'___cms_tree',
    $cdb_domain.'___cms_tree___role___guests',
    $cdb_domain.'___cms_tree___role___website',
    $cdb_domain.'___cms_tree___user___'.strtolower($username1a)
    //$naWebOS->domainFolder.'___cms_tree__user__administrator',
    //$naWebOS->domainFolder.'___cms_tree__user__guest'
);

$tableIndexes = [
    'nicer_app___cms_tree' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'nicer_app___cms_tree___role___guests' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'nicer_app___cms_tree___role___website' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'nicer_app___cms_tree___user___'.strtolower($username1a) => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'said_by___cms_tree' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'said_by___cms_tree___role___guests' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'said_by___cms_tree___role___website' => '_design/1419d49469158fb476d97982339cf3d08333fa5f',
    'said_by___cms_tree___user___'.strtolower($username1a) => '_design/1419d49469158fb476d97982339cf3d08333fa5f'
];

$data = array();
$ret = array();
foreach ($tables as $idx=>$dbName) {
    $msg = 'ajax_getTreeNodes.php : $dbName='.$dbName;
    //trigger_error ($msg, E_USER_NOTICE);
    //echo $msg.'<br/>';
    try {
        $r = $cdb->setDatabase ($dbName, false);
    }
    //var_dump ($r);
        catch (Throwable $e) { echo '$dbName='.$dbName.'</br>'.PHP_EOL; echo $e->getMessage(); }
        catch (Exception $e) { echo '$dbName='.$dbName.'</br>'.PHP_EOL; echo $e->getMessage(); };

    //echo '$dbName='.$dbName.'</br>'.PHP_EOL;

    //try { $docs = $cdb->getAllDocs(true); } catch (Exception $e) { echo $e->getMessage(); };
    //$data = $docs->body->rows;

    $bm = 'abc';
    $bmOld = 'def';
    $i = 0;

    while (
        !is_null($bm)
        && $bm!==''
        && ($bm!==$bmOld)
    ) {

        try {
            $findCommand = [
                'selector' => [
                    //'$or' => [
                        'database' => $dbName
                        //[ 'database' => str_replace('_tree_','_documents_',$dbName) ]
                    //]
                ],
                'sort' => [
                    ['parent' => 'asc'],
                    ['order' => 'asc']
                ],
                'limit' => 200,
                //'bookmark' => ($bm!=='abc'?$bm:''),
                'use_index' => $tableIndexes[$dbName]
            ];
            if ($debug) { echo 't332:'; var_dump ($findCommand); };
            $call2 = $cdb->find($findCommand);
            if ($debug) { echo 't333:'; var_dump ($call2); };
            $data = $call2->body->docs;
        } catch (Exception $e) { echo '$dbName='.$dbName.', $e='.$e->getMessage(); }

        //var_dump ($data);
        foreach ($data as $idx2=>$recordSummary) {

            // TODO BUG FIX dirty hack:
            $recordSummary->text = preg_replace('/.*?___/','',$recordSummary->text);
            $recordSummary->text = str_replace('__',' ',$recordSummary->text);

            $ret = array_merge ($ret, array(json_decode(json_encode($recordSummary),true)));
        }



        if (property_exists($call2->body,'bookmark')) {
            if ($bmOld=='def') $bmOld = $call2->body->bookmark; else $bmOld = $bm;
            $bm = $call2->body->bookmark;
        } else {
            $bm = '';
            $bmOld = 'ghi';
        }
    }
}

echo json_encode($ret, JSON_PRETTY_PRINT);
?>
