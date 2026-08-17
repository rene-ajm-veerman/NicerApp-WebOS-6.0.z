<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$fncn = __FILE__;

if (!array_key_exists('database',$_POST)) {
    $dbg = [
        '$_REQUEST' => $_REQUEST
    ];
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database with '.json_encode($dbg).'). Event logged.');
}
if (strpos($_POST['database'], '_tree')===false)
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');



$debug = true;
global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');

//echo '<pre>'; var_dump ($_SESSION); die();

$cdb = $db->cdb;

$cdb->setDatabase($_POST['database'],false);
$call = $cdb->get ($_POST['id']);
$call->body->text = $_POST['text'];
$call->body->url1 = $_POST['url1'];
$call->body->seoValue = $_POST['seoValue'];
$call->body->pageTitle = $_POST['pageTitle'];

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record to database='.$_POST['database']); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }





$dataSetName = $db->dataSetName('data_by_users'); // i know, couchdb calls a 'table' a 'database'. and that sux.

$cdb->setDatabase ($dataSetName, false);


$findCommand = [
    'selector' => [
        'seoValue' => $_REQUEST['seoValue']
    ],
    'fields' => [ '_id' ]
];

try {
    $call = $cdb->find ($findCommand);
} catch (Exception $e) {
    $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
    trigger_error ($msg, E_USER_NOTICE);
    echo $msg;
    return false;
}

if (
    is_object($call)
    && is_object($call->body)
    && is_array($call->body->docs)
) {
    if (count($call->body->docs)===0) {
        $r = '';
        $data = [
            '_id' => randomString(50),
            'database' => $_POST['database'],
            'viewSettings' => [
                '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsText' => [
                    'database' => $_POST['database'],
                    'id' => $_POST['id']
                ],
                'seoValue' => $_POST['seoValue']
            ],
            'url1' => $_POST['url1'],
            'seoValue' => $_POST['seoValue'],
            'pageTitle' => $_POST['pageTitle']
        ];
        if (array_key_exists('user', $_POST) && is_string($_POST['user']) && !$_POST['user']==='') $data['user'] = $_POST['user'];
        if (array_key_exists('role', $_POST) && is_string($_POST['role']) && !$_POST['role']==='') $data['role'] = $_POST['role'];

        try { $call3 = $cdb->post($data); } catch (Exception $e) {
            cdb_error (500, $e, 'Could not add record'); exit();
        }
    } elseif (count($call->body->docs)>=1) {
        //echo '<pre>';
        //var_dump ($call->body->docs); //die();
        $call2 = $cdb->get ($call->body->docs[0]->_id);
        //var_dump ($call2->body); //die();

        $data = json_decode(json_encode($call2->body),true);

        $data['url1'] = $_POST['url1'];
        $data['seoValue'] = $_POST['seoValue'];
        $data['pageTitle'] = $_POST['pageTitle'];
        $data['viewSettings']['seoValue'] = $_POST['seoValue'];
/*
        //var_dump ($call2->body); //die();
        $r = null;
        if (property_exists($call2->body, 'viewSettings')) {
            foreach ($call2->body->viewSettings as $fp => &$vs) {
                $vs->seoValue = $_POST['seoValue'];
            }
            //$call2->body->viewSettings->seoValue = $_POST['seoValue'];
*/
            try { $call3 = $cdb->post($data); } catch (Exception $e) {
                cdb_error (500, $e, 'Could not add record'); exit();
            }
            if ($debug) { echo '$call3='; var_dump ($call3); echo PHP_EOL.PHP_EOL; }

        //}
    }
}



echo 'status : Success';




?>
