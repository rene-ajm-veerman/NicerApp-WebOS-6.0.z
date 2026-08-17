<?php

$rootPath_vidls = realpath(dirname(__FILE__).'/../../../../../..');
$myPath_vidls = $rootPath_vidls.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/errorHandler';
require_once ($myPath_vidls.'/class.NicerAppWebOS.errorHandler.php');

$naErr = new class_NicerApp_WCS__errorHandler();
global $naErr;
?>
