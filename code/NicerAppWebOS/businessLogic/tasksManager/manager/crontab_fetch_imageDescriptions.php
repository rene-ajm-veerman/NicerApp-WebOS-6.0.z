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
$indexFile = $root.'/indexClarifai.json';
$index = safeLoadJSONfile($indexFile, false);
$files = array_merge ($files, getFilePathList($root, true, FILE_FORMATS_photos, $excl, array('file')));
//TODO : list imageCrawler output files


foreach ($files as $idx => $file) {
    if (array_key_exists($file, $index)) continue;

    $file = $files[rand(0,count($files)-1)];
    $url = filePathToURL ($file);
    $url = str_replace(' ', '%20', $url);

    $dbg = [
        'file' => $file,
        'url' => $url,
        'thumb' => '<img src="'.dirname($url).'/thumbs/'.basename($url).'"/>'
    ];


    $crawler_clarifai = new class_nicerappImageDescriberEngine_plugin_clarifaiDotCom();
    $data = $crawler_clarifai->getRawAPIdata($url);

    $index[str_replace($root,'',$file)] = json_decode($data['filteredResults'],true)['concepts'];

    echo '-----------'.$file.'-------------'.PHP_EOL;
    var_dump($data);
    var_dump (file_put_contents ($indexFile, json_encode($index, JSON_PRETTY_PRINT)));
}

/*
$dbg['description'] = json_decode($data['filteredResults'], true);

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
*/
?>
