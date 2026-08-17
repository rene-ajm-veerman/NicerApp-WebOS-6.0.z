<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$adb = $naWebOS->dbsAdmin->findConnection('couchdb');
$acdb = $adb->cdb;


$html = $_POST['document'];
if (
    strpos ($html, '<script') !== false
    || strpos ($html, '<link') !== false
    || strpos ($html, 'javascript:') !== false
    || strpos ($html, '<?php') !== false
    || strpos ($html, '<iframe') !== false
) die ('403 Forbidden');

$illegal = false;
$domain = $_SERVER['HTTP_HOST'];
preg_match_all('/(?<!_)src=([\'"])?(.*?)\\1/', $html, $matches);
preg_match_all('/(?<!_)href=([\'"])?(.*?)\\1/', $html, $matches2);
foreach ($matches[2] as $idx => $url) {
    $internal = (
        false !== stripos( $url, '//' . $domain ) || // include "//my-domain.com" and "http://my-domain.com"
        (
            0 !== strpos( $url, '//' ) &&            // exclude protocol relative URLs, like "//example.com"
            0 === strpos( $url, '/' )                // include root-relative URLs, like "/demo"
        )
    );
    $illegal = !$internal;
    if ($illegal) break;
}
if (!$illegal) foreach ($matches2[2] as $idx => $url) {
    $internal = (
        false !== stripos( $url, '//' . $domain ) || // include "//my-domain.com" and "http://my-domain.com"
        (
            0 !== strpos( $url, '//' ) &&            // exclude protocol relative URLs, like "//example.com"
            0 === strpos( $url, '/' )                // include root-relative URLs, like "/demo"
        )
    );
    $illegal = !$internal;
    if ($illegal) break;
}

if ($illegal) {
    die ('403 Forbidden');
} else {

    global $naWebOS;

    $cdb->setDatabase($_POST['database'],false);
    $dataID = array_key_exists('dataID',$_POST) ? $_POST['dataID'] : $naWebOS->getDataID($_POST['database'], 'dataID');
    $doc = array (
        'database' => $_POST['database'],
        '_id' => $_POST['id'],
        'id' => $_POST['id'],
        'dataID' => $dataID,
        'document' => $_POST['document']
    );
    if (
        array_key_exists('user',$_POST)
        && $_POST['user']!==''
    ) $doc['user'] = $_POST['user'];
    if (
        array_key_exists('role',$_POST)
        && $_POST['role']!==''
    ) $doc['role'] = $_POST['role'];
    try { $call = $cdb->get ($_POST['id']); $doc['_rev'] = $call->body->_rev; } catch (Exception $e) { };
    try { $call = $cdb->post($doc); } catch (Exception $e) { cdb_error (500, $e, 'Could not add/update record in '.$_POST['database']); exit(); };

    $dataSetName = str_replace('_documents', '_tree', $_POST['database']);
    $cdb->setDatabase($dataSetName,false);
    try { $call = $cdb->get ($_POST['id']); $doc = (array)$call->body; } catch (Exception $e) { cdb_error (500, $e, 'Could not find record in '.$dataSetName); exit(); };
    if (!array_key_exists('dataID', $doc) || $doc['dataID']!==$dataID) {
        $doc['dataID'] = $dataID;
        try { $call = $cdb->post($doc); } catch (Exception $e) { cdb_error (500, $e, 'Could not update record'); exit(); };
    }



    if (!array_key_exists('dataID', $_POST) || $doc['dataID']!==$_POST['dataID']) {
        // update {$MYDOMAIN_TLD}___data_by_users table
        $relTableName = 'data_by_users';
        $dataSetName = $db->dataSetName($relTableName);
        $acdb->setDatabase($dataSetName, true);
        $findCommand = [
            'selector' => [
                'username' => $username,
                'dataID' => $dataID
            ],
            'use_index' => 'dataID',
            'fields' => [ '_id' ]
        ];
        if (
            array_key_exists('user',$_POST)
            && $_POST['user']!==''
        ) $findCommand['selector']['user'] = $_POST['user'];
        if (
            array_key_exists('role',$_POST)
            && $_POST['role']!==''
        ) $findCommand['selector']['role'] = $_POST['role'];
        //echo '<pre>'; var_dump ($findCommand); var_dump ($_POST); die();
        $go = true;
        try {
            $call = $acdb->find ($findCommand);
        } catch (Exception $e) {
            $go = false;
        };

        if ($call->headers->_HTTP->status!=='200') {
            $msg = 'Couchdb is not responding with a 200 HTTP code to a $findCommand query.';
            trigger_error ($msg, E_USER_WARNING);
        } elseif (count($call->body->docs)===0) {
            $record = [
                'dataID' => $dataID,
                'database' => $_POST['database'],
                'viewSettings' => [
                    "/path/to/blogEditor" => [
                    ]
                ]
            ];
            if (
                array_key_exists('user',$_POST)
                && $_POST['user']!==''
            ) $record['user'] = $_POST['user'];
            if (
                array_key_exists('role',$_POST)
                && $_POST['role']!==''
            ) $record['role'] = $_POST['role'];

            try { $call = $cdb->get ($_POST['id']); $record['_rev'] = $call->body->_rev; } catch (Exception $e) { };

            try { $call = $acdb->post($record); } catch (Exception $e) { cdb_error (500, $e, 'Could not add/update record in '.$dataSetName); exit(); };
        }


        // update {$MYDOMAIN_TLD}___views table
        $relTableName = 'views';
        $dataSetName = $db->dataSetName($relTableName);
        $acdb->setDatabase($dataSetName, true);
        $findCommand = [
            'selector' => [
                'username' => $username,
                'dataID' => $dataID
            ],
            'use_index' => 'dataID',
            'fields' => [ '_id' ]
        ];
    }


    echo $dataID;//'Success'; // echo json_encode($recordToAdd); <-- not needed, js will refresh the entire tree (accounting for multiple users working on the same tree at the same time)
}
?>
