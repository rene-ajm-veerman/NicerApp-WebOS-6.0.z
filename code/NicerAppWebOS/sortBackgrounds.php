<html>
<head>
<link type="text/css" rel="StyleSheet" href="sortBackgrounds.css">
</head>
<body>
<div class="background"></div>
<div id="content" class="content">
<?php
$debug = false;
$singleFileOnDisplay = true; // leave to boolean true to prevent browser crashes for very large backgrounds collections.
require_once (dirname(__FILE__).'/boot.php');
global $naWebOS;
global $filePerms_ownerUser; global $filePerms_ownerGroup; global $filePerms_perms_publicWriteableExecutable;

$path = dirname(__FILE__).'/siteMedia/backgrounds/landscape/';
$files = getFilePathList ($path, true, FILE_FORMATS_photos, FILE_FORMATS_NO_thumbs, ['file']);
foreach ($files as $idx => $inputFilePath) {
    if (strpos($inputFilePath, 'thumbs/')!==false) continue;
    if ($singleFileOnDisplay) echo '<script type="text/javascript">var c = document.querySelectorAll(".backgroundFile_container"); for (var i = 0; i < c.length; i++) { c[i].remove() };</script>';
    echo '<div class="backgroundFile_container">';
    echo '<div class="backgroundFile">';
    $count = '('.($idx+1).' of '.count($files).')';
    echo '<p class="filepath">'.$count.'<br/>'.$inputFilePath.'</p>';
    $xec = 'identify '.$inputFilePath;
    $output = [];
    exec ($xec, $output, $result);
    if ($result!==0) {
        $replaceFolder = 'landscape_broken/';
        $outputFilePath = str_replace ('landscape/', $replaceFolder, $inputFilePath);
        createDirectoryStructure (dirname($outputFilePath).'/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
        $xec = 'mv "'.$inputFilePath.'" "'.$outputFilePath.'"';
        exec ($xec);
    } else {
        $replaceFolder = 'landscape_sorted/';
        $outputFilePath = str_replace ('landscape/', $replaceFolder, $inputFilePath);
        createDirectoryStructure (dirname($outputFilePath).'/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);

        $xec = 'cp "'.$inputFilePath.'" "'.$outputFilePath.'"';
        exec ($xec, $output, $result);
        if ($result!==0) { echo 'FATAL ERROR : CAN NOT COPY<BR/>'.$xec; die(); };

        $regex = '/\s(\d+)x(\d+)\s/';
        preg_match_all ($regex, $output[0], $m);
        $w = $m[1][0];
        $h = $m[2][0];
        if ($w > 7400) {
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 3840, null);
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 1920, null);
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 250, null);
        } elseif ($w > 2500) {
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 1920, null);
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 250, null);
        } else {
            generateThumbs ($inputFilePath, $outputFilePath, $w, $h, 250, null);
        };
        //echo '<pre>'; var_dump ($output); var_dump ($m); var_dump ($result); echo '</pre>';
        echo '<script type="text/javascript">var el = document.getElementById("content"); el.scrollTo(0, el.scrollHeight);</script>';
        ob_flush();
        ob_end_flush();
        flush();
        ob_start();
        //sleep (1);
    }
    echo '</div>';
    echo '</div>';
}
//echo '<pre>'; var_dump ($files); echo '</pre>';
function generateThumbs ($inputFilePath, $outputFilePath, $w, $h, $w2, $h2) {
    $fncn = 'generateThumbs()';
    $wt = $w2.(!is_null($h2)?'x'.$h2:'');
    $thumbPath = dirname($outputFilePath).'/thumbs/'.$wt.'/';
    global $filePerms_ownerUser; global $filePerms_ownerGroup; global $filePerms_perms_publicWriteableExecutable;
    createDirectoryStructure ($thumbPath, $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
    $outputFilePathThumb = $thumbPath.str_replace(dirname($outputFilePath).'/', '', $outputFilePath);
    $xec = 'identify "'.$outputFilePathThumb.'"';
    exec ($xec, $output, $result);
    if ($result===0) {
        $success = 'status_alreadyExists';
        echo '<p class="done_createThumbs '.$success.'"><span class="outputFilePathThumb">'.$outputFilePathThumb.'</span></p>';
    } else {
        $xec = 'convert "'.$outputFilePath.'" -resize '.$wt.' "'.$outputFilePathThumb.'"';
        exec ($xec, $output, $result);
        global $debug;
        if ($debug) {
            $dbg = [
                '$xec' => $xec,
                '$output' => $output,
                '$result' => $result
            ];
            echo '<pre class="debug">'.$fncn.' : $dbg='; var_dump($dbg); echo '</pre>';
        }
        $success = ($result===0?'status_success':'');
        echo '<p class="done_createThumbs '.$success.'"><span class="outputFilePathThumb">'.$outputFilePathThumb.'</span></p>';
    }
}
?>
</div>
</body>
</html>
