<?php 
global $naWebOS;
//require_once(dirname(__FILE__).'/functions.php');
//$fn = dirname(__FILE__).'/../../../../../3rd-party/sag/src/Sag.php';
$fn = $naWebOS->domainPath.'/NicerAppWebOS/functions.php';
require_once($fn);

require_once(dirname(__FILE__).'/class.naForums-1.0.0.php');
global $naForums;
$naForums = new class_naForums();
?>
