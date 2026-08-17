<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = false;

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;
$dataSetName = str_replace('_documents', '_tree', $_POST['database']);
$cdb->setDatabase($dataSetName,false);

$order = json_decode($_POST['order'], true);

$call = $cdb->getAllDocs();
if ($debug) { echo 't321:<pre>'; var_dump ($call); echo '</pre>';  };

foreach ($call->body->rows as $idx => $doc) {
    $call2 = $cdb->get($doc->id);
    if ($debug) { echo 't322:<pre>'; var_dump ($call2); echo '</pre>';  };
    $doc2 = $call2->body;
    if (property_exists($doc2,'parent') && $doc2->parent == $_POST['newParent']) {
        $doc2->order = getOrder ($order, $doc2->_id);
        if ($debug) { var_dump ($doc2->order); echo '<br/>'.PHP_EOL; }
        $cdb->post ($doc2);
    }
}

    function getOrder ($order, $docID) {
        foreach ($order as $idx2 => $orderID)
            if ($docID==$orderID) return $idx2;
    }

if ($debug) { echo 't222:<pre>'; var_dump($_POST); echo '</pre>'; };
$call = $cdb->get ($_POST['target']);
$call->body->parent = $_POST['newParent'];
//$call->body->lastChanged = time(); // outdated and nonsensical probably

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

$oldPath = $naWebOS->domainPath.'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['oldPath'].'/'.$_POST['nodeName'];
$newPath = $naWebOS->domainPath.'/siteData/'.$naWebOS->domainFolder.'/'.$_POST['newPath'].'/'.$_POST['nodeName'];
$xec = 'mv "'.$oldPath.'" "'.$newPath.'"';
exec ($xec, $output, $result);
$dbg = array (
    'xec' => $xec,
    'output' => $output,
    'result' => $result
);
if ($debug) { echo '<pre style="color:green">'; var_dump ($dbg); echo '</pre>'; }

echo 'status : Success'; 
?>
