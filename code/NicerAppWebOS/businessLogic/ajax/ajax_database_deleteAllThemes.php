<?php
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/';
require_once ($rootPathNA.'/boot.php');

    global $naWebOS;
    global $naLAN;
    if ($naLAN) {
        $naWebOS->dbsAdmin->findConnection('couchdb')->createDataSet_themes();
        echo 'status : Success.';
    } else {
        echo 'status : Failed. (no permission).';
    }
?>
