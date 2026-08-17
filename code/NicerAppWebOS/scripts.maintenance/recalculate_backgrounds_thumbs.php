<?php
$rootPath = realpath(dirname(__FILE__).'/..');
require_once ($rootPath.'/boot.php');
set_time_limit (60 * 60 * 2); // starts at 4am!

global $naLAN;
global $naWebOS;
//if (!$naLAN) die('403 Forbidden.');


global $delThumbs;
$aStr = is_array($argv) && isset($argv[1]) ? implode(' ', $argv) : '';
$delThumbs = is_array($argv) && isset($argv[1]) ? $argv[1]=='delThumbs' : false;
$jobs = [
    [
        'path' => $rootPath.'/siteMedia/backgrounds',
        'thumbnails' => './thumbs',
        'delThumbs' => $delThumbs,
        'arguments' => $aStr
    ]/*,

    [
        'path' => $rootPath.'/apps/NicerAppWebOS/application-programmer-interfaces/data-validation/imagesMetaInfo/output',
        'thumbnails' => './thumbs',
        'delThumbs' => $delThumbs,
        'arguments' => $aStr
    ]*/
];
//echo 'recalculate_backgrounds_thumbs.php :: Job Listing : '.PHP_EOL; var_dump ($jobs); echo PHP_EOL.PHP_EOL; die();

//$totalFileCount = naWebOS_photoAlbum_countFiles ($jobs);


$jobsDoneCount = 0;
foreach ($jobs as $jobIdx => $job) {
    //echo PHP_EOL.$jobIdx.PHP_EOL;
    //var_dump ($job); var_dump (is_dir($job['path'])); die();

    if (is_dir($job['path']))
        $jobsDoneCount +=
            // .../NicerAppWebOS/functions.php::naWebOS_photoAlbum_resizeFiles()
            // also goes CPU-idle for 500ms between files being resized (into up to 4 thumbnails)
            naWebOS_photoAlbum_resizeFiles ($totalFileCount, count($jobs), $jobsDoneCount, $jobIdx, $job);

}
?>
