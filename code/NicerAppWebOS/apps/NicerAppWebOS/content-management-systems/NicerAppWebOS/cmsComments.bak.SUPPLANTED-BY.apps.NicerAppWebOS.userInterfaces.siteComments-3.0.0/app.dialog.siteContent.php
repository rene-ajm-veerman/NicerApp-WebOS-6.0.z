<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;
$view = $naWebOS->view;//json_decode (decode_base64_url($_GET['apps']), true);

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/
//echo '<pre>'; var_dump($naWebOS); echo '</pre>';
$dbName = $naWebOS->domainFolderForDB.'___cms_comments';
echo '<pre>'; var_dump($dbName); echo '</pre>';
$cdb->setDatabase ($dbName, false);

$in = &$_GET;
$fields = [ '_id', 'parentID', 'html' ];


$findCommand = [
    'selector' => [
        '_id' => $_GET['commentID']
    ],
    'fields' => &$fields,
    //s'sort' => [['millisecondsSinceEpoch'=>'desc']],
    'limit' => 200//, // hardcoded (in couchdb!) max value
    //'use_index' => '_design/aced963374ca4616ccb7836945188842be4e9145'
];
echo '<pre>'; var_dump($findCommand); echo '</pre>'; //die();
$bm = 'abc';
$oldBM = 'def';
$results = [];
while ($bm!==$oldBM) {
    if ($bm!=='abc') $findCommand['bookmark'] = $bm;
    $call = $cdb->find($findCommand);

    $oldBM = $bm;
    if (
        isset($call)
        && property_exists($call,'body')
        && property_exists($call->body, 'bookmark')
        && is_string($call->body->bookmark)
        && $call->body->bookmark !== ''
        && $call->body->bookmark !== 'nil'
    ) {
        $bm = $call->body->bookmark;
    } else {
        $bm = 'abc';
    };

    if (property_exists($call->body, 'rows'))
        $results = array_merge_recursive($results, $call->body->rows);
}

echo '<pre>'; var_dump($results); echo '</pre>';
?>
