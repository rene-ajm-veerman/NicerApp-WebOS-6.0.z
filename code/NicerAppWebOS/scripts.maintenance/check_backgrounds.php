<?php
/**
 * check_backgrounds_for_corruption.php
 *
 * Uses NicerAppWebOS getFilePathList() to list image backgrounds under $rootFolder,
 * then checks each file for corruption with ImageMagick (identify).
 *
 * Requirements:
 *   - ImageMagick installed (identify command available in PATH)
 *   - NicerAppWebOS functions.php (and its dependencies) loadable
 *
 * Usage (CLI):
 *   php check_backgrounds_for_corruption.php /path/to/backgrounds
 *
 * Or set $rootFolder below and run without argument.
 */

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------

$rootFolder = $argv[1] ?? '/var/www/NicerAppWebOS-v6.y.z/siteMedia/backgrounds/landscape';
// Adjust the path above to match your installation.

$recursive        = true;
$excludeThumbsRE  = '/.*thumbs.*/';          // same style of exclusion used in NicerApp
$fileSpecRE       = '/\.(jpe?g|png|gif|webp|bmp|tiff?|avif)$/i';  // common image formats
// If you prefer the NicerApp constant (when available):
// $fileSpecRE = defined('FILE_FORMATS_photos') ? FILE_FORMATS_photos : $fileSpecRE;

$reportOnlyCorrupted = false;   // true = only print bad files
$moveCorruptedTo     = '/var/www/NicerAppWebOS-v6.y.z/siteMedia/corrupted';    // e.g. '/tmp/corrupted_backgrounds' or null to leave in place
$verbose             = true;

// ---------------------------------------------------------------------------
// Bootstrap NicerApp (adjust path to your install)
// ---------------------------------------------------------------------------

$naRoot = realpath(dirname(__FILE__) . '/../..');   // adjust if this script lives elsewhere
// Common locations:
// $naRoot = '/var/www/NicerAppWebOS-v6.y.z/code';
// $naRoot = realpath(dirname(__FILE__) . '/../../..');

$boot   = $naRoot . '/NicerAppWebOS/boot.php';
$funcs  = $naRoot . '/NicerAppWebOS/functions.php';

if (!file_exists($funcs)) {
    fwrite(STDERR, "ERROR: Cannot find NicerApp functions.php at:\n  $funcs\n");
    fwrite(STDERR, "Edit \$naRoot at the top of this script.\n");
    exit(1);
}

// boot.php pulls in a lot of context; if you only need getFilePathList you can
// try requiring functions.php alone, but boot is safer for full compatibility.
if (file_exists($boot)) {
    require_once $boot;
} else {
    require_once $funcs;
}

if (!function_exists('getFilePathList')) {
    fwrite(STDERR, "ERROR: getFilePathList() not found after including NicerApp files.\n");
    exit(1);
}

// ---------------------------------------------------------------------------
// Sanity checks
// ---------------------------------------------------------------------------

$rootFolder = realpath($rootFolder);
if ($rootFolder === false || !is_dir($rootFolder)) {
    fwrite(STDERR, "ERROR: \$rootFolder is not a readable directory:\n  " . ($argv[1] ?? $rootFolder) . "\n");
    exit(1);
}

// Quick check that ImageMagick is available
exec('identify -version 2>&1', $imOut, $imRc);
if ($imRc !== 0) {
    fwrite(STDERR, "ERROR: ImageMagick 'identify' not found in PATH.\n");
    fwrite(STDERR, "Install with: sudo apt install imagemagick   (or equivalent)\n");
    exit(1);
}

// ---------------------------------------------------------------------------
// Collect files with getFilePathList()
// ---------------------------------------------------------------------------

/*
 * Signature (from the source around line 2023):
 *
 * getFilePathList(
 *   $path,
 *   $recursive        = false,
 *   $fileSpecRE       = "/.* /",
 *   $excludeFolders   = null,
 *   $fileTypesFilter  = array(),   // e.g. ['file']
 *   $depth            = null,
 *   $level            = 1,
 *   $returnRecursive  = false,     // flat list when false
 *   $debug            = false,
 *   ...
 * )
 */

$files = getFilePathList(
    $rootFolder,
    $recursive,
    $fileSpecRE,
    $excludeThumbsRE,
    ['file'],           // only regular files
    null,               // depth
    1,                  // level
    false               // flat list (not recursive structure)
);

// Normalise to a simple list of absolute paths
$pathList = [];
if (is_array($files)) {
    foreach ($files as $entry) {
        if (is_string($entry)) {
            $pathList[] = $entry;
        } elseif (is_array($entry) && isset($entry['realPath'])) {
            $pathList[] = $entry['realPath'];
        } elseif (is_array($entry) && isset($entry['path'])) {
            $pathList[] = $entry['path'];
        }
    }
}

$pathList = array_unique(array_filter($pathList));
sort($pathList);

if ($verbose) {
    echo "Root folder : $rootFolder\n";
    echo "Files found : " . count($pathList) . "\n";
    echo str_repeat('-', 60) . "\n";
}

// ---------------------------------------------------------------------------
// Check each file for corruption
// ---------------------------------------------------------------------------

$okCount      = 0;
$corruptCount = 0;
$corruptFiles = [];

foreach ($pathList as $filepath) {
    // identify returns non-zero exit code on many kinds of corruption / unreadable files
    $cmd = 'identify -quiet ' . escapeshellarg($filepath) . ' 2>&1';
    $output = [];
    $rc = 0;
    exec($cmd, $output, $rc);

    $isCorrupt = ($rc !== 0);

    // Extra safety: empty or zero-byte files
    if (!$isCorrupt && filesize($filepath) === 0) {
        $isCorrupt = true;
        $output[] = 'zero-byte file';
    }

    if ($isCorrupt) {
        $corruptCount++;
        $corruptFiles[] = $filepath;
        $msg = implode(' | ', $output) ?: 'identify failed (exit ' . $rc . ')';
        echo "[CORRUPT] $filepath\n";
        if ($verbose) {
            echo "           → $msg\n";
        }

        if ($moveCorruptedTo !== null) {
            if (!is_dir($moveCorruptedTo)) {
                mkdir($moveCorruptedTo, 0750, true);
            }
            $dest = rtrim($moveCorruptedTo, '/') . '/' . basename($filepath);
            // avoid name collisions
            $i = 1;
            while (file_exists($dest)) {
                $dest = rtrim($moveCorruptedTo, '/') . '/' . pathinfo($filepath, PATHINFO_FILENAME)
                      . "_$i." . pathinfo($filepath, PATHINFO_EXTENSION);
                $i++;
            }
            if (@rename($filepath, $dest)) {
                echo "           → moved to $dest\n";
            } else {
                echo "           → FAILED to move\n";
            }
        }
    } else {
        $okCount++;
        if (!$reportOnlyCorrupted && $verbose) {
            echo "[OK]      $filepath\n";
        }
    }
}

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------

echo str_repeat('-', 60) . "\n";
echo "Checked  : " . count($pathList) . "\n";
echo "OK       : $okCount\n";
echo "Corrupt  : $corruptCount\n";

if ($corruptCount > 0 && $verbose) {
    echo "\nCorrupted files:\n";
    foreach ($corruptFiles as $f) {
        echo "  $f\n";
    }
}

exit($corruptCount > 0 ? 2 : 0);
