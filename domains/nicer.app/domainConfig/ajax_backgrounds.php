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
$gzFile = $cacheFilePath.'/backgrounds.json.gz';
$lockFile = $cacheFilePath.'/backgrounds.LOCK.txt';

ini_set('zlib.output_compression', 'Off'); // thanks and credit for this line go to grok.com

global $naLAN;
if (
    $naLAN
    && array_key_exists('rc', $_GET) // rc = re-calculate.
    && $_GET['rc'] == 'true'
) unlink ($cacheFile);//echo '<pre>';

if (!file_exists($cacheFile)) {
    if (file_exists($gzFile)) unlink ($gzFile);
    $mi = [];

    $root = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteMedia/backgrounds';
    //echo PHP_EOL;
    //echo $rootPath_na.PHP_EOL;
    //echo $root.PHP_EOL; die();
    global $naThisServer;

    //echo '<pre>'; var_dump($naThisServer); echo '</pre>';
    //if (!file_exists($lockFile) && !file_exists($cacheFile) && $naLAN) {
        file_put_contents($lockFile, 'LOCKED');
        $f = getBackgrounds ($root, $naWebOS->domainPath, false, $naThisServer['debug']['ajax_backgrounds.php']); // from .../NicerAppWebOS/function.php
    //}
    $mi[] = [
        'root' => str_replace($naWebOS->domainPath, '', $root),
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

    // thanks & credits for this section go to grok.com

    // Assuming the client already sent Accept-Encoding: gzip (you should check this!)
    // If you want to be safe / progressive, add a check:

    $supports_gzip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
                    stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;

    $file = $cacheFile.'.gz';  // or construct dynamically

    if (!$supports_gzip || !file_exists($file)) {
        // Fallback: serve uncompressed version if client doesn't support gzip
        // or .gz is missing
        $file = $cacheFile;
        header('Content-Type: application/json');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    // Serve the pre-gzipped file
    header('Content-Type: application/json');           // ← what the decompressed content is
    header('Content-Encoding: gzip');                   // ← critical: tells browser it's gzipped
    header('Content-Length: ' . filesize($file));       // ← length of the .gz file (not original)
    header('Vary: Accept-Encoding');                    // ← very important for proxies/CDNs

    // Optional but strongly recommended extras
    header('Cache-Control: public, max-age=3600');      // adjust as needed
    // header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');

    readfile($file);
}
echo PHP_EOL;
//$dbg = [ '$r' => $r, '$rootPath_na' => $rootPath_na, 'root' => $root, '$cacheFile' => $cacheFile ];
//echo '<pre>'; var_dump ($dbg);
?>
