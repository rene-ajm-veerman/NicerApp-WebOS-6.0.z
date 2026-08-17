<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

$key = array_key_exists('sessionKeyName', $_GET) ? $_GET['sessionKeyName'] : null;
if (is_null($key)) $key = 'naErrors';

echo json_encode($_SESSION[$key]);
?>
