<?php
//require_once (dirname(__FILE__).'/../../../../boot_stage_001.php');
    
function naPhotoAlbum ($codePath=null) {
    $root = realpath(dirname(__FILE__).'/../../../../');
    $fncn = __FILE__;
    global $naWebOS;
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);    
    
    $baseURL = '/siteData/'.$naWebOS->domainFolder;
    $baseDir = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteData/'.$naWebOS->domainFolder;
    $targetDir = $baseDir.'/'.$codePath['mediaFolder'];
    $thumbDir = $targetDir.'/thumbs';
    
    $files = getFilePathList ($targetDir, false, FILE_FORMATS_photos, null, array('file'));
    usort($files, function($a, $b) {
        return strcmp($a['webPath'], $b['webPath']);
    });

    $r = '<style>.filename {color : white;}</style>';
    
    $dbg = array (
        'baseURL' => $baseURL,
        'baseDir' => $baseDir,
        'targetDir' => $targetDir,
        'files' => $files
    );
    //echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($fncn); var_dump ($dbg); echo '</pre>';    exit();





    $r .= '<div style="display:flex;flex-wrap:wrap">';
    foreach ($files as $idx => $filePath) {
        $filePath = $filePath['webPath'];
        $fileName = str_replace ($targetDir.'/', '', $filePath);
        $fileName = preg_replace ('/^\//', '', $fileName);

        $thumbPath = $thumbDir.'/'.$fileName;
        $thumbURL = str_replace ($baseDir, $baseURL, $thumbPath);
        $fileURL = $targetDir.str_replace ($baseDir, $baseURL, $filePath);
        $dbg = array (
            'fileName' => $fileName,
            'filePath' => $filePath,
            'baseDir' => $baseDir,
            'thumbDir' => $thumbDir,
            'thumbPath' => $thumbPath,
            'thumbURL' => $thumbURL
        );
        //echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';
        $r .= '<div style="overflow:hidden;float:left;width:300px;height:fit-content;margin:5px;padding:10px;padding-top:20px;border-radius:10px;border:1px solid black;background:rgba(0,0,0,0.7);box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 1px 1px 1px rgba(0,0,255,0.5), inset -1px -1px 1px rgba(0,0,255,0.5);">';
        
        $onclick = '';
        $href = '';
        $arr = array (
            "/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS" => [
                "cmsViewMedia" => array (
                    "codePath" => $targetDir,
                    "filename" => $fileName
                )
            ]
        );
        //echo '<pre>'; var_dump($arr); echo '</pre>';
        $json = json_encode($arr);
        $href = "/view/".encode_base64_url($json);
        
        
        $r .= '<center><a href="'.$href.'"><img src="'.$thumbURL.'" style="width:270px" '.$onclick.'/><br/><span class="filename">'.$fileName.'</span></a></center></div>';
    }
    $r .= '</div>';
    return $r;
}

function naTarotDecksAlbum() {
    global $naWebOS;
    $baseDir = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks';
    $startDir = $naWebOS->domainPath . $baseDir;

    $files = getFilePathList($startDir,true,'/.*/',null,array('dir'));
    //echo '<pre>'; var_dump ($files); exit;

    $r = '<style>
    .tarotDeck {
    display: inline-block;
    width: 240px;
    margin: 12px;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid #555;
    background: rgba(0,0,0,0.8);
    box-shadow: 0 4px 12px rgba(0,0,0,0.6);
    transition: all 0.3s ease;
    text-align: center;
    vertical-align: top;
}
.tarotDeck:hover {
transform: scale(1.05);
border-color: #aaa;
}
.tarotDeck img {
width: 200px;
height: auto;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.deckName {
color: #ddd;
margin-top: 10px;
font-size: 0.95em;
line-height: 1.3;
}
pre.dbg {
    color : skyblue;
    background : rgba(0,0,50,0.5);
    margin : 20px;
    padding : 10px;
    border-radius : 20px;
    text-shadow : 2px 2px 3px rgba(0,0,0,0.7);
}
</style>';

$r .= '<div style="text-align:center; padding:20px;">';


foreach ($files as $fileInfo) {
    $back = 'back.jpg';
    $backFullPath = $fileInfo['webPath'].$back ?? $fileInfo.$back;
    $deckDir      = dirname($backFullPath);

    $backURL  = $baseDir.$backFullPath;

    if (!(file_exists($naWebOS->domainPath.$backURL))) {
        $back = 'back.png';
        $backFullPath = $fileInfo['webPath'].$back ?? $fileInfo.$back;
        $deckDir      = dirname($backFullPath);

        $backURL  = $baseDir.$backFullPath;

        if (!(file_exists($naWebOS->domainPath.$backURL))) {
            $back = 'back.gif';
            $backFullPath = $fileInfo['webPath'].$back ?? $fileInfo.$back;
            $deckDir      = dirname($backFullPath);

            $backURL  = $baseDir.$backFullPath;
        }
        if (!(file_exists($naWebOS->domainPath.$backURL)))
            continue;
    }

    $frontURL = str_replace($back, '21.jpg', $backURL);
    $b = [
        '1' => (file_exists($naWebOS->domainPath.$backURL)),
        '$baseDir' => $baseDir,
        '$backFullPath' => $backFullPath,
        '$backURL' => $backURL,
        '$frontURL' => $frontURL
    ];
    //$r .= '<pre class="dbg">'.json_encode($b,JSON_PRETTY_PRINT).'</pre>'; //exit;

    // Create nice display name from relative path
    $relPath = str_replace($startDir . '/', '', $deckDir);
    $deckName = substr($relPath,1);           // for JS
    $deckTitle = str_replace(['/', '-'], ' / ', $relPath); // for display

    $r .= '<div class="tarotDeck">';
    $r .= '<a href="#" onclick="naTarot_openDeck(\'' . addslashes($deckName) . '\'); return false;">';
    $r .= '<img loading="lazy" decoding="async" src="' . htmlspecialchars($backURL) . '"
    onmouseover="this.src=\'' . htmlspecialchars($frontURL) . '\'"
    onmouseout="this.src=\'' . htmlspecialchars($backURL) . '\'"
    alt="' . htmlspecialchars($deckTitle) . '">';
    $r .= '<div class="deckName">' . htmlspecialchars($deckTitle) . '</div>';
    $r .= '</a>';
    $r .= '</div>';
}

$r .= '</div>';

if (empty($files)) {
    $r .= '<p style="color:#888;">No tarot decks found.</p>';
}

return $r;
}
?>
