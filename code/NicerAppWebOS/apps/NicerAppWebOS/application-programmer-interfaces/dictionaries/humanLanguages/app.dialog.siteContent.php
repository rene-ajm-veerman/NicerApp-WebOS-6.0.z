<h1>NicerApp Natural Language Engine : words.bighugelabs.com test results</h1>
<?php 
require_once(dirname(__FILE__).'/boot.php');

$API = new class_nicerappImageDescriberEngine_plugin_clarifaiDotCom();
$result = '<pre class="resultsJSON">'.$API->getRawAPIdata().'</pre>';
echo $result;
?>
