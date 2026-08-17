<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$adb = $naWebOS->dbsAdmin->findConnection('couchdb');
$acdb = $adb->cdb;

 //echo '<pre>'; var_dump ($_POST); die();
$html = str_replace('&lt;','<',str_replace('&gt;','>',$_POST['document']));
$pattern = '/<a.*?href="(.*?)".*?>(.*)<\/a>/i';
preg_match_all ($pattern, $html, $matches);
//var_dump ($html); echo '<br/>'.PHP_EOL.PHP_EOL; print_r ($matches);echo '<br/>'.PHP_EOL.PHP_EOL; //exit();

$pm = true;
foreach ($matches[1] as $idx => $m) {
    //svar_dump ($m); echo PHP_EOL;
    if (
        (
        stripos ($matches[2][$idx], 'http://') !== false
        || stripos ($matches[2][$idx], 'https://') !== false
        )
        && (
            stripos($matches[1][$idx], $matches[2][$idx]) !== false
        )
    ) $pm = false;
}


if (
    (
        strpos ($html, '<script') !== false
        || strpos ($html, '<link') !== false
        || strpos ($html, 'javascript:') !== false
        || strpos ($html, '<?') !== false
        //|| strpos ($html, '<iframe') !== false
        //|| !$pm
    )
) exit ('HTTPS ERROR 403 Forbidden - Illegal content.');

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
$illegal = false;
if ($illegal) {
    exit ('HTTPS ERROR 403 Forbidden - No links allowed.');
} else {

    global $naWebOS;


    $dataIDs = $naWebOS->dbsAdmin->cms_editDocument();
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
        echo '{"rec":';
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
                "seoValue" => str_replace('\\','',$_POST['seoValue'])
            ],
            'seoValue' => str_replace('\\','',$_POST['seoValue'])
        ];
        if ( array_key_exists('url1',$_POST) && $_POST['url1']!=='' ) $document['url1'] = $_POST['url1'];
        if ( array_key_exists('user',$_POST) && $_POST['user']!=='' ) $document['user'] = $_POST['user'];
        if ( array_key_exists('role',$_POST) && $_POST['role']!=='' ) $document['role'] = $_POST['role'];

        $dataIDs_simple[$ct]['document'][$dataSetName] = $document;
        $dataByUsers = $naWebOS->dbsAdmin->editDataSubSet ($ct, $relTableName, $findCommand, $document);
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
    echo '}';
}
?>
