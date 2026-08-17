<?php 
$root = realpath (dirname(__FILE__).'/../../../../../..');
require_once ($root.'/NicerAppWebOS/boot.php');
global $naWebOS;
$view = $naWebOS->view;


$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/


?>
<!--<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ -- ><div></div><div></div><div></div></div> -->
<!--<pre><?php //echo json_encode($view, JSON_PRETTY_PRINT);?></pre>-->

<?php
global $naWebOS;

/*
$couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../../').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
//var_dump ($couchdbConfigFilepath); exit();
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

$cdb->setDatabase(str_replace('_tree', '_documents', $view['cmsText']['database']),false);
try { $call = $cdb->get ($view['cmsText']['id']); } catch (Exception $e) { echo $e->getMessage(); exit(); };

echo $call->body->document;
*/

foreach ($view as $viewFolder => $viewSettings) break;
$fn = $view[$viewFolder]['cmsViewMedia']['filename'];

if (substr($view[$viewFolder]['cmsViewMedia']['codePath'],0,1)!=='/') {
    $baseURL = '/siteData/'.$naWebOS->domainFolder.'/';
    $baseDir = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteData/'.$naWebOS->domainFolder.'/';
    echo '<h1>'.$baseDir.'</h1>';
} else {
    $baseURL = '/siteData/'.$naWebOS->domainFolder.'/';
    if (
        !array_key_exists('relPath1',$_GET)
        || !is_string($_GET['relPath1'])
        || $_GET['relPath1']===''
    ) $_GET['relPath1']
        = realpath(dirname(__FILE__).'/../../../../../../..');

    $baseDir = $_GET['relPath1'];
    $rt = realpath(dirname(__FILE__).'/../../../../../../..');
    $baseURL = str_replace($rt, '', $baseDir).$baseURL;
    //echo '<pre>'; var_dump($naWebOS->view);exit;
}

$dbg = array (
    'baseURL' => $baseURL,
    'baseDir' => $baseDir,
    'fn' => $fn,
    'view' => $view
);
//echo '<pre>'.json_encode($dbg,JSON_PRETTY_PRINT).'</pre>'; //exit();

//$baseDir = str_replace ($naWebOS->path.'/code','', $baseDir);
//$baseDir = str_replace ($naWebOS->path,'', $baseDir);
$baseDir = str_replace ('domainConfig','', $naWebOS->domainPath);
$baseDir = str_replace ('//','/', $baseDir);
$targetDir = $view[$viewFolder]['cmsViewMedia']['codePath'].'/';
//var_dump ($targetDir); exit;

foreach ($naWebOS->view as $appFolder=>$appRec) {
    foreach ($appRec as $appName => $appSettings) {
        //var_dump ($baseURL);
        //var_dump (str_replace($naWebOS->path.'/domains'.$naWebOS->domainFolder,'',$appSettings['codePath']));
        $targetURL = $baseURL.str_replace($naWebOS->path.'/domains'.$naWebOS->domainFolder,'',$appSettings['codePath']);
    }
}
$targetURL = str_replace ($naWebOS->domainPath, '', $targetURL);

$dbg = array (
    'baseURL' => $baseURL,
    'baseDir' => $baseDir,
    'targetDir' => $targetDir,
    'targetURL' => $targetURL,
    'fn' => $fn,
    'view' => $view
);
//echo '<pre>'.json_encode($dbg,JSON_PRETTY_PRINT).'</pre>'; //exit();

$files = getFilePathList ($targetDir, false, FILE_FORMATS_photos, null, array('file'), 1, 1, false);
usort($files, function($a, $b) {
    return strcmp($a['webPath'], $b['webPath']);
});
//echo '<pre>'; var_dump ($files); echo '</pre>'; exit();

foreach ($files as $idx => $file) {
    $prev = '';
    $next = '';
    $path = $file['webPath'];
    $path = str_replace('/'.$naWebOS->domainPath,'',$path);
    $path = str_replace($naWebOS->domainPath,'',$path);
    $path = str_replace('/domains/'.$naWebOS->domainFolder,'',$path);
    $path = str_replace('//','/',$path);
    if (substr($fn,0,2)=='//') $fn = substr ($fn, 1);
    if (substr($fn,0,1)=='/') $fn = substr ($fn, 1);
    if (basename($path)===$fn) {
        $myPath = $path;
        //var_dump ($myPath);
        $prev = $idx > 1 ? $files[$idx-1]['webPath'] : $files[count($files)-1]['webPath'];
        $next = $idx < count($files) - 1 ? $files[$idx+1]['webPath'] : $files[0]['webPath'];

        $dbg = [
            'bn' => basename($path),
            'fn' => $fn,
            'prev' => $prev,
            'next' => $next
        ];
        //echo '<pre>'; var_dump ($dbg); echo '</pre>'; die();
        $prevArr = array (
            "/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS" => [
                'cmsViewMedia' => array (
                    'codePath' => $view[$viewFolder]['cmsViewMedia']['codePath'],
                    'filename' => (is_string($prev) && $prev!=='' ? basename($prev) : '')
                )
            ]
        );
        $prevJSON = encode_base64_url (json_encode($prevArr));
        $prevURL = '/view/'.$prevJSON;

        $nextArr = array (
            "/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS" => [
                'cmsViewMedia' => array (
                    'codePath' => $view[$viewFolder]['cmsViewMedia']['codePath'],
                    'filename' => (is_string($next) && $next!=='' ? basename($next) : '' )
                )
            ]
        );
        $nextJSON = encode_base64_url (json_encode($nextArr));
        $nextURL = '/view/'.$nextJSON;
    }
}

//echo '<pre>'.json_encode($files,JSON_PRETTY_PRINT).'</pre>';
?>
<style>
    #btnPrevious {
        position:absolute;
        top : calc(50% - 25px);
        left : 10px;
    }
    #btnNext {
        position:absolute;
        top : calc(50% - 25px);
        right : 10px;

    }
    #btnSetBackground {
        position:absolute;
        width : 50px;
        left : calc(50% - 25px);
        top : 10px;
    }
    #btnBack_fromMediaView, #btnSetBackground, #btnNext img, #btnPrevious img {
        width : 50px;
    }
</style>
<script type="text/javascript" src="/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js?c=<?php echo date('Ymd_His',filemtime($naWebOS->domainPath.'/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js'));?>"></script>
<img id="btnBack_fromMediaView" class="tooltip" tooltipTheme="mainTooltipTheme" title="Go back, leave the photo-album viewer." src="/siteMedia/btnBack.png" onclick="window.history.back();" style="position:absolute"/>
    <img id="viewMedia" src="<?php echo $myPath;?>"/>
<img id="btnSetBackground" class="tooltip" tooltipTheme="mainTooltipTheme" title="Set as site background" src="/siteMedia/btnBackground.png" onclick="na.backgrounds.next ('#siteBackground', na.site.globals.backgroundSearchKey, '<?php echo $myPath;?>');"/>
<a id="btnPrevious" href="<?php echo $prevURL;?>"><img id="btnImgPrevious" src="/siteMedia/btnPrevious.png"/></a>
<a id="btnNext" href="<?php echo $nextURL;?>"><img id="btnImgNext" src="/siteMedia/btnNext.png"/></a>
<div id="naPhotoAlbum__control" style="position:absolute;top:5%;width:200px;height:100px;right:1%;z-index:3200;">
    <div class="naPhotoAlbum_control__background" style="position:absolute;width:100%;height:100%;">&nbsp;</div>
	<div id="naPhotoAlbum__control__naturalWidth" class="naPhotoAlbum_control_element" style="position:absolute;top:1em;"></div>
    <div id="naPhotoAlbum__control__naturalHeight" class="naPhotoAlbum_control_element" style="position:absolute;top:2em;"></div>
	<div id="naPhotoAlbum__control__width" class="naPhotoAlbum_control_element" style="position:absolute;top:3em;"></div>
    <div id="naPhotoAlbum__control__height" class="naPhotoAlbum_control_element" style="position:absolute;top:4em;"></div>
	<div id="naPhotoAlbum__control__zoomPercentage" class="naPhotoAlbum_control_element" style="position:absolute;top:0em;"></div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
    na.m.waitForCondition ('image load',
        function () {
            var
            nw = $('#viewMedia')[0].naturalWidth;
            //debugger;
            return nw > 0 && na.m.desktopIdle();
        },
        function () {
            na.photoAlbum.onload();
        },
        100
    );
    });
</script>
