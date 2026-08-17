<?php
$rpna = realpath(dirname(__FILE__).'/..');
//echo '<pre>';   var_dump ($_SERVER); die();
require_once ($rpna.'/NicerAppWebOS/boot.php');
global $rootPath_na;
//echo $rootPath_na; die();
ini_set('max_execution_time',''.(2*24*60*60));
set_time_limit (2 * 24 * 60 * 60); // 2 x 24 x 60 minutes max_execution_time for this script

$cacheFilePath = realpath(dirname(__FILE__).'/..').'/siteCache';

$cacheFile = $cacheFilePath.'/backgrounds.json';
$lockFile = $cacheFilePath.'/backgrounds.LOCK.txt';
global $naLAN;
if (
    $naLAN
    && array_key_exists('rc', $_GET) // rc = re-calculate.
    && $_GET['rc'] == 'true'
) unlink ($cacheFile);//echo '<pre>';

if (!file_exists($cacheFile)) {
    $mi = [];

    $root = realpath(dirname(__FILE__).'/..').'/siteMedia/backgrounds';
    //echo PHP_EOL;
    //echo $rootPath_na.PHP_EOL;
    //echo $root.PHP_EOL; die();
    global $naThisServer;

    //echo '<pre>'; var_dump($naThisServer); echo '</pre>';
    //if (!file_exists($lockFile) && !file_exists($cacheFile) && $naLAN) {
        file_put_contents($lockFile, 'LOCKED');
        $f = getBackgrounds ($root, $rootPath_na, false, $naThisServer['debug']['ajax_backgrounds.php']); // from .../NicerAppWebOS/function.php
    //}
    $mi[] = [
        'root' => str_replace($rootPath_na, '', $root),
        'thumbnails' => './thumbs',
        'files' => $f
    ];

    /*
    $root = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/crawlers/imageSearch/output';
    $f = getBackgrounds ($root, $rootPath_na, false); // from .../NicerAppWebOS/function.php
    $mi[] = [
        'root' => str_replace($rootPath_na, '', $root),
        'thumbnails' => './thumbs',
        'files' => $f
    ];
    */

    $smi = json_encode($mi);
	$r = file_put_contents ($cacheFile, $smi);
    unlink ($lockFile);
    echo $smi;
} else {
    echo file_get_contents($cacheFile);
}
echo PHP_EOL;
//$dbg = [ '$r' => $r, '$rootPath_na' => $rootPath_na, 'root' => $root, '$cacheFile' => $cacheFile ];
//echo '<pre>'; var_dump ($dbg);
?>
