<?php
    $root = realpath(dirname(__FILE__).'/../../../../../../');
    require_once ($root.'/NicerAppWebOS/boot.php');

    global $naDebugAll;
    $debug = true;

    global $naWebOS;
//trigger_error (realpath(dirname(__FILE__).'/../../../').'/domainConfigs/'.$naWebOS->domainFolder.'/index.dark.css', E_USER_NOTICE);
    if (!array_key_exists('relPath1', $_GET)) {
        $baseURL = '/siteData/'.$naWebOS->domainFolder.'/';
        $baseDir = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteData/'.$naWebOS->domainFolder.'/';
    } else {
        $baseDir = $_GET['relPath1'];
        $rt = realpath(dirname(__FILE__).'/../../../../../../');
        $baseURL = str_replace($rt, '', $baseDir);
    }

    // sanity check
    $_GET['codePath'] = str_replace('//', '/', $_GET['codePath']);
    //echo '<pre>'; var_dump ($_GET); exit();


    $targetDir = $baseDir.$_GET['codePath'];
    /*
    $targetDir =
        realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'../../../siteData/')
        .DIRECTORY_SEPARATOR.$naWebOS->domainFolder.DIRECTORY_SEPARATOR.$_GET['codePath']
        .$relativePath;*/

    $relativePath = $_GET['codePath'];
    //onzin? $targetDir = $naWebOS->domainPath.'/siteData/'.$relativePath;
    $thumbDir = $targetDir.'/thumbs/300';
    if (!file_exists($thumbDir)) $thumbDir = $targetDir.'/thumbs';
//var_dump ($targetDir);exit();
    $imgStyle = ''; // boxShadow perhaps


    $files = getFilePathList ($targetDir, false, FILE_FORMATS_photos, null, array('file'), 1,1,true);
    //echo '<pre>'; var_dump ($targetDir); var_dump($files);exit();
    if (!array_key_exists('files', $files)) {
        $msg = $naWebOS->getContent__standardErrorMessage('No files found in this folder.')['siteContent'];
        //$msg = '<p>&nbsp;</p><span style="background:rgba(0,0,0,0.5);padding:10px;margin:20px;border-radius:20px;color:ivory;text-shadow:2px 2px 3px rgba(0,0,0,7);">No files found in this folder.</span>';
    } else {
        $msg = null;
        $files = $files['files'];
        sort($files);
    }
    //echo '<pre>'; var_dump ($targetDir); var_dump($files);exit();


if (!array_key_exists('noIframe', $_GET) || $_GET['noIframe']===false) {
?>
<html>
<head>
    <script type="text/javascript">
        var na = {};
    </script>
    <style></style>
    <link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder?>/index.css?c=<?php echo date('Ymd_His',filemtime($naWebOS->domainPath.'/domainConfig/index.css'))?>">
    <link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder?>/index.dark.css?c=<?php echo date('Ymd_His',filemtime($naWebOS->domainPath.'/domainConfig/index.dark.css'))?>">
    <script type="text/javascript" src="/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js?c=<?php echo date('Ymd_His', filemtime($naWebOS->domainPath.'/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js'));?>"></script>
<?php
} else {
?>
    <script type="text/javascript">
        delete na.site.settings.current.loadingApps;
        delete na.site.settings.current.startingApps;
        na.d.s.visibleDivs.push ('#siteToolbarLeft');
        na.desktop.resize();
    </script>
<?php
}
?>
<?php
if (!array_key_exists('noIframe', $_GET) || $_GET['noIframe']===false) {
?>
</head>
<body style="overflow:hidden" onload="window.top.resizeIFrameToFitContent(window.top.$('#themeEditor_photoAlbum', window.top.document.body)[0]);">
<?php
}
?>

<div class="vividScrollpane" style="width:100%;height:100%;overflow:auto;">
    <style>
        .filename {
            color : white;
        }
    </style>
    <?php
    
    $dbg = array (
        'baseURL' => $baseURL,
        'baseDir' => $baseDir,
        'targetDir' => $targetDir,
        'files' => $files
    );
    if ($debug && false) { echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>'; }
    if (is_string($msg)) echo $msg.'<br/>';
    foreach ($files as $idx => $filePath) {
        $fileName = str_replace ($targetDir.'/', '', $filePath['webPath']);
        $fileName = preg_replace ('/^\//', '', $fileName);
        //echo '<pre style="margin:10px;padding:10px;border-radius:10px;border:1px solid grey;background:rgba(0,0,0,0.7);color:skyblue">'; var_dump ($thumbDir); var_dump ($fileName); var_dump ($filePath); echo '</pre>'; exit();
        $thumbPath = $thumbDir.'/'.$fileName;
        $thumbURL = str_replace ($baseDir, $baseURL, $thumbPath);
        $fileURL = str_replace ($baseDir, $baseURL, $filePath);
        $dbg = array (
            'targetDir' => $targetDir,
            'fileName' => $fileName,
            'filePath' => $filePath,
            'baseDir' => $baseDir,
            'thumbDir' => $thumbDir,
            'thumbPath' => $thumbPath,
            'thumbURL' => $thumbURL
        );
        //echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';exit();
        echo '<div style="overflow:hidden;display:inline-block;width:140px;height:auto;margin:5px;padding:10px;padding-top:20px;border-radius:10px;border:1px solid black;background:rgba(0,0,0,0.7);box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 1px 1px 1px rgba(0,0,255,0.5), inset -1px -1px 1px rgba(0,0,255,0.5);">';
        
        
        $onclick = 'onclick="window.top.na.cms.onclick_mediaThumbnail(event, \''.$_GET['codePath'].'\', \''.$fileName.'\');"'; // gets overridden by the Theme Editor for it's backgrounds selection procedures.
        
        echo '<center><img src="'.$thumbURL.'" class="mediaThumb" style="width:134px" '.$onclick.'/><br/><span class="filename">'.$fileName.'</span></center></div>';
    }
?>
</div>
<?php
if (!array_key_exists('noIframe', $_GET) || $_GET['noIframe']===false) {
?>
</body>
</html>
<?php
}
?>
