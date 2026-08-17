<?php 
$rpna = realpath(dirname(__FILE__).'/..');
require_once ($rpna.'/NicerAppWebOS/boot.php');
global $rootPath_na;
ini_set('max_execution_time',''.(2*24*60*60));
set_time_limit (2 * 24 * 60 * 60); // 2 x 24 x 60 minutes max_execution_time for this script

$cacheFilePath = realpath(dirname(__FILE__).'/..').'/siteCache';

$cacheFile = $cacheFilePath.'/backgrounds_recursive.json';
$lockFile = $cacheFilePath.'/backgrounds_recursive.LOCK.txt';
global $naLAN;
if (
    $naLAN
    && array_key_exists('rc', $_GET) // rc = re-calculate.
    && $_GET['rc'] == 'true'
) unlink ($cacheFile);//echo '<pre>';

if (!file_exists($cacheFile)) {
    $mi = [];

    $root = $rootPath_na.'/siteMedia/backgrounds';
    global $naThisServer;
    if (true) { var_dump (!file_exists($lockFile)); var_dump(!file_exists($cacheFile)); var_dump ($naLAN); };
    if (true || (!file_exists($lockFile) && !file_exists($cacheFile) && $naLAN)) {
        file_put_contents($lockFile, 'LOCKED');
        $f = getBackgrounds ($root, $rootPath_na, true, $naThisServer['phpServers'][0]['debug']['ajax_backgrounds_recursive.php']); // from .../NicerAppWebOS/function.php
    }
    $mi[] = [
        'root' => str_replace($rootPath_na, '', $root),
        'thumbnails' => './thumbs',
        'files' => $f
    ];

    /*
    $root = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/crawlers/imageSearch/output';
    $f = getBackgrounds ($root, $rootPath_na, true);
    $mi[] = [
        'root' => str_replace($rootPath_na, '', $root),
        'thumbnails' => './thumbs',
        'files' => $f
    ];*/
//echo '<pre>'; var_dump ($mi);

    $smi = json_encode($mi);
    file_put_contents ($cacheFile, $smi);
    unlink ($lockFile);

    echo $smi;
} else {
    echo file_get_contents ($cacheFile);
}
?>
