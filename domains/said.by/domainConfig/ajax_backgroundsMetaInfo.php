<?php
$rpna = realpath(dirname(__FILE__).'/..');
//echo '<pre>';   var_dump ($_SERVER); die();
require_once ($rpna.'/NicerAppWebOS/boot.php');
global $rootPath_na;
//echo $rootPath_na; die();
ini_set('max_execution_time',''.(2*24*60*60));
set_time_limit (2 * 24 * 60 * 60); // 2 x 24 x 60 minutes max_execution_time for this script

$cacheFilePath = realpath(dirname(__FILE__).'/..').'/siteCache';

$cacheFile = $cacheFilePath.'/backgroundsMetaInfo.json';
$gzFile = $cacheFilePath.'/backgroundsMetaInfo.json.gz';



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
?>
