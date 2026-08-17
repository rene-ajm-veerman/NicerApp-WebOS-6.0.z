<?php 
require_once(dirname(__FILE__).'/boot.php');

$imageURL = $_GET['imageURL'];
$imageThumbURL = str_replace('siteMedia/', 'siteMedia.thumbs/', $imageURL);

$API = new class_nicerappImageDescriberEngine_plugin_clarifaiDotCom();
$APIresult = $API->getRawAPIdata($imageURL); // NEVER use $testImageThumbURL here, this will send far too little image data to the API web service provider
echo $APIresult['filteredResults'];
?>
