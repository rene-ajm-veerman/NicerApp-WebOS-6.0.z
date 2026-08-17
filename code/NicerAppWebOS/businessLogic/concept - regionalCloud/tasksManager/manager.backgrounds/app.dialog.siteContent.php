<?php
set_time_limit (0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once (realpath(dirname(__FILE__).'/../../../../').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/boot.php');

$fncn = '.../NicerAppWebOS/apps/nicer.app/api.backgroundsManagement/app.dialog.siteContent.php';
global $naLAN;






/*
$newsApp3_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
$newsApp3_factorySettings = json_decode(file_get_contents($newsApp3_factorySettings_fn), true);
$newsApp3 = new newsApp3_class($newsApp3_factorySettings);
$passIPrangeCheck = $newsApp3->checkIPrange();

if ($passIPrangeCheck) {
    echo require_return (dirname(__FILE__).'/app.content_fullService.php');
} else {
    echo require_return (dirname(__FILE__).'/app.content_serviceUnavailableInArea.php');
}*/
?>
