<?php
require_once(__DIR__.'/../../boot.php');
global $naWebOS;
ini_set('memory_limit', '2048M');
set_time_limit (1 * 60 * 60);

// serve from cache?
$cacheFile = $naWebOS->domainPath.'/siteCache/backgroundsMetaInfo.json';
if (file_exists($cacheFile)) {
    readfile ($cacheFile);
    exit;
}



// no cache, so calculate (takes a long time)
$dataStage1files = [
    __DIR__.'/../../scripts.maintenance/wallpaper_descriptions_multi_prompts-Landscape..b1.json',
    __DIR__.'/../../scripts.maintenance/wallpaper_descriptions_multi_prompts-Landscape..b2.json'
];
$dataStage1 = [];
foreach ($dataStage1files as $idx => $filepath) {
    $json = json_decode(file_get_contents($filepath),true);
    foreach ($json as $fp2 => $ollamaRec) {
        if (!in_array($fp2,$dataStage1)) $dataStage1[$fp2] = $ollamaRec;
    }
}

$res = json_encode($keywords,JSON_PRETTY_PRINT);
file_put_contents ($cacheFile, $res);
echo $res; exit;

/*
$keywords = [];
foreach ($dataStage1 as $fp => $ollamaRec) {
    foreach ($ollamaRec as $providerName => $providerRec) {
        if (
            array_key_exists('keywords', $providerRec)
            && is_string($providerRec['keywords'])
            && $providerRec['keywords'] !== ''
        ) {
            $kw = &$providerRec['keywords'];
            $kw = str_replace ('10 precise tags for this image include: ','',$kw); // $providerName=='baklava'
            $kwe = explode(',', $kw);
            foreach ($kwe as $idx => $kwe1) {
                $kwe2 = trim($kwe1);
                if (!in_array($kwe2, $keywords)) {
                    $keywords[$kwe2] = [ 'files' => [] ];
                }
                $keywords[$kwe2]['files'][] = [$fp=>&$ollamaRec];
            }
        }
    }
}

$res = json_encode($keywords,JSON_PRETTY_PRINT);
file_put_contents ($cacheFile, $res);
echo $res; exit;
*/
?>
