<?php
require_once (dirname(__FILE__).'/../../../seductiveapps/boot.php');
require_once (dirname(__FILE__).'/boot.php');

global $seductiveapps_infoHarvester;
$db = $seductiveapps_infoHarvester->db;

ob_start('ob_gzhandler');
//error_reporting (E_ALL);

echo json_encode ($db->get());
?>