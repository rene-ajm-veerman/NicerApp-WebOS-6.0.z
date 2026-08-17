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
// !! !! ! --- uncomment the next line to activate the 'open house' mode in the blogging apps of all sites that run this code-file.
//$illegal = false;

if ($illegal) {
    die ('403 Forbidden');
} else {

    global $naWebOS;

    $dataIDs = $naWebOS->dbs->cms_editDocument();
    $dataIDs_simple = [];
    foreach ($dataIDs as $idx => $dr) {
        $ct = $dr['c']['ct'];
        if (
            array_key_exists ('result', $dr)
            && is_bool($dr['result'])
            && $dr['result']
        ) $dataIDs_simple[$ct] = [
            'document' => $dr['resultValue'],
            'success' => true
        ];
    };
    //echo '<pre style="color:blue">'; var_dump ($dataIDs_simple);

    foreach ($dataIDs as $dataIDs_idx => $dr) {
        $ct = $dr['c']['ct'];
        $relTableName = 'data_by_users';
        $dataSetName = $db->dataSetName($relTableName);
        $findCommand = [
            'selector' => [
                'seoValue' => $dr['resultValue']
            ],
            'fields' => [ '_id' ]
        ];
        if ( array_key_exists('user',$_POST) && $_POST['user']!=='' ) $findCommand['selector']['user'] = $_POST['user'];
        if ( array_key_exists('role',$_POST) && $_POST['role']!=='' ) $findCommand['selector']['role'] = $_POST['role'];
        //echo '<pre>'; var_dump ($findCommand); var_dump ($_POST); die();

        $document = [
            'database' => $_POST['database'],
            'viewSettings' => [
                "/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsText" => [
                    'database' => $_POST['database'],
                    'id' => $_POST['id']
                ],
                "seoValue" => $_POST['seoValue']
            ],
            'seoValue' => $_POST['seoValue']
        ];
        if ( array_key_exists('url1',$_POST) && $_POST['url1']!=='' ) $document['url1'] = $_POST['url1'];
        if ( array_key_exists('user',$_POST) && $_POST['user']!=='' ) $document['user'] = $_POST['user'];
        if ( array_key_exists('role',$_POST) && $_POST['role']!=='' ) $document['role'] = $_POST['role'];

        $dataIDs_simple[$ct]['document'][$dataSetName] = $document;
        $dataByUsers = $naWebOS->dbs->editDataSubSet ($ct, $relTableName, $findCommand, $document);
        foreach ($dataByUsers as $idx => $dr) {
            $ct2 = $dr['c']['ct'];
            if ($ct==$ct2 && $dr['result']!==true) $dataIDs_simple[$ct]['result'] = false;
        }

    }

/* NOT NEEDED YET, MIGHT NEVER BE NEEDED :
    foreach ($dataIDs as $dataIDs_idx => $dr) {
        $ct = $dataIDrecord['c']['ct'];
        $relTableName = 'views';
        $dataSetName = $db->dataSetName($relTableName);
        $findCommand = [
            'selector' => [
                'dataID' => $dr['resultValue']
            ],
            'use_index' => 'dataID',
            'fields' => [ '_id' ]
        ];
        if ( array_key_exists('user',$_POST) && $_POST['user']!=='' ) $findCommand['selector']['user'] = $_POST['user'];
        if ( array_key_exists('role',$_POST) && $_POST['role']!=='' ) $findCommand['selector']['role'] = $_POST['role'];
        //echo '<pre>'; var_dump ($findCommand); var_dump ($_POST); die();

        $document = [
            'dataID' => $dr['resultValue'],
            'database' => $_POST['database'],
            'viewSettings' => [
                "/path/to/blogEditor" => [
                ]
            ]
        ];
        if ( array_key_exists('user',$_POST) && $_POST['user']!=='' ) $document['user'] = $_POST['user'];
        if ( array_key_exists('role',$_POST) && $_POST['role']!=='' ) $document['role'] = $_POST['role'];

        $x = $naWebOS->dbs->editDataSubSet ($relTableName, $findCommand, $document);
        if ($x !== true) $dataIDs_simple[$ct]['success'] = false;
    }
*/
    echo json_encode($dataIDs_simple); // reports only what is in or has changed in the logged-in databases.
}
?>
