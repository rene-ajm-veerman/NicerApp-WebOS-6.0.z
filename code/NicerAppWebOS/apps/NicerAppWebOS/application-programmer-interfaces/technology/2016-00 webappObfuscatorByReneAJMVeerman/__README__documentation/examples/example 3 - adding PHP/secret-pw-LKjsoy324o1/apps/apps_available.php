<?php
//have 1 centralized variable to store app-specific PHP settings in (included here for future extensions of the code)
$mySite__apps = array('apps'=>array());
global $mySite__apps;

require_once (dirname(__FILE__).'/app1/appLogic/boot.php');
//TODO:
//require_once (dirname(__FILE__).'/app2/appLogic/boot.php');
//require_once (dirname(__FILE__).'/app3/appLogic/boot.php');
//require_once (dirname(__FILE__).'/app4/appLogic/boot.php');
?>