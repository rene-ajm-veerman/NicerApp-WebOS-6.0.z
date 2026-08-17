<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

global $naLog;
echo json_encode($naLog->fetchIntoPast(2));
?>
