<?php 
$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
//var_dump ($rootPath); die();
require_once($rootPath.'/NicerAppWebOS/boot.php'); // nicerapp boot.php
require_once(dirname(__FILE__).'/class.imageDescriberEngine.php');

require_once(dirname(__FILE__).'/interface.imageDescriberEngine_plugin.php');
require_once(dirname(__FILE__).'/class.imageDescriberEngine_plugin_clarifaiDotCom.php');

?>
