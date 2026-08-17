<?php
set_time_limit (0);
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/class.newsApp-3.php');

$fncn = '.../NicerAppWebOS/apps/nicer.app/applications/2D/news/app.dialog.siteContent.php';
global $naLAN;

$newsApp3_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
$newsApp3_factorySettings = json_decode(file_get_contents($newsApp3_factorySettings_fn), true);
$newsApp3 = new newsApp3_class($newsApp3_factorySettings);
$passIPrangeCheck = $newsApp3->checkIPrange();

if ($passIPrangeCheck) {
    echo require_return (dirname(__FILE__).'/app.content_fullService.php');
} else {
    echo require_return (dirname(__FILE__).'/app.content_serviceUnavailableInArea.php');
}
?>
