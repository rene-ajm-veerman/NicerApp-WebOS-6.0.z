<?php
header('Content-type: text/javascript');

$rt = realpath(dirname(__FILE__).'/../..');
require_once ($rt.'/NicerAppWebOS/boot.php');

$js = file_get_contents(dirname(__FILE__).'/na3D.source.js');
$preg = preg_match_all ('/from\s+[\'"](.*?)[\'"]/', $js, $matches);
$matches[2] = [];
$matches[3] = [];

foreach ($matches[1] as $idx => $relPath) {
    $m = filemtime($rt.$relPath);
    $matches[2][] = $rt.$relPath;
    $matches[3][] = $m;
}

foreach ($matches[1] as $idx => $relPath) {
    if ($matches[3][$idx]!==false) {
        $js = str_replace($relPath, $relPath.'?c='.date('Ymd-His',$m), $js);
    }
}

//echo '<pre>'; var_dump ($matches);
echo $js;
?>
