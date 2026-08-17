<h1>NicerApp Image Describer Engine : clarifai.com test results</h1>
<?php 
require_once(dirname(__FILE__).'/boot.php');

$testImageURL = 'https://nicer.app/siteMedia/backgrounds/landscape/homes%20and%20buildings/1920x1200-data-wallpapers-41-952370.jpg';
$testImageThumbURL = str_replace('siteMedia/', 'siteMedia.thumbs/', $testImageURL);

$API = new class_nicerappImageDescriberEngine_plugin_clarifaiDotCom();
$APIresult = $API->getRawAPIdata($testImageURL); // NEVER use $testImageThumbURL here, this will send far too little image data to the API web service provider
$html = 
    '<p>Test Image : <img src="'.$testImageURL.'" style="width:500px"/></p>'
    .'<pre class="resultsJSON">'.$APIresult['rawResults'].'</pre>';
echo $html;
?>
