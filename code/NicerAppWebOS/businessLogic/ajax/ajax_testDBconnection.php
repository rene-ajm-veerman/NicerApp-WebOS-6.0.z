<?php
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

global $naWebOS;
echo $naWebOS->dbs->testDBconnection()['couchdb']['results']['result'];

?>
