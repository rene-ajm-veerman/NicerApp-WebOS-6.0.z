<?php
set_time_limit (0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
require_once ($rootPath.'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/boot.php');
require_once ($rootPath.'/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/crawlers/imagesMetaInfo/boot.php');
require_once ($rootPath.'/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/dictionaries/humanLanguages/boot.php');
global $hl_english_plugin__princeton;

$fncn = '.../NicerAppWebOS/apps/NicerAppWebOS/tasks/manager/app.dialog.siteContent.php';
global $naWebOS;
global $naLAN;

$excl = '/(?!.*thumbs).*/'; // exclude anything that includes 'thumbs' in it's filepath.

$files = [];
$root = $rootPath.'/siteMedia/backgrounds';
$files = array_merge ($files, getFilePathList($root, true, FILE_FORMATS_photos, $excl, array('file')));
//TODO : list imageCrawler output files

echo '<pre>'; var_dump ($files); echo '</pre>';die();


$file = $files[rand(0,count($files)-1)];
$url = filePathToURL ($file);

$dbg = [
    'file' => $file,
    'url' => $url,
    'thumb' => '<img src="'.dirname($url).'/thumbs/'.basename($url).'"/>'
];


$crawler_clarifai = new class_nicerappImageDescriberEngine_plugin_clarifaiDotCom();
$dbg['description'] = json_decode($crawler_clarifai->getRawAPIdata($url)['filteredResults'], true);

//if (false)
foreach ($dbg['description']['concepts'] as $idx => $rec) {
    $dbg['description']['concepts'][$idx] = json_encode([
        'word' => $rec,
        'anatomy' => $hl_english_plugin__princeton->retrieveWordData($rec)
    ]);
    //break;
}
$dbg['descriptions'] = $dbg['description']['concepts'];
unset($dbg['description']);

?>
<h1>Tasks Manager</h1>

<p><?php echo $fncn ?></p>

<pre><?php var_dump($dbg) ?></pre>
