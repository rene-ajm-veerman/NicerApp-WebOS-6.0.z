<?php
require_once (dirname(__FILE__).'/boot.php');
global $naWebOS;
global $naLAN;
die('403 Forbidden.');

$authorizedFile1 = '45.83.241.21-2023-10-16_07:07:36_plus0200.html';
$authorizedFile2 = '45.83.241.21-2023-10-16_07:13:08_plus0200.html';

if (
    !$naLAN
    && !(
        array_key_exists('file',$_GET)
        && (
            $_GET['file'] == $authorizedFile1
            || $_GET['file'] == $authorizedFile2
        )
    )
) die('403 Forbidden.');

if (array_key_exists('file', $_GET)) {
    $fp = '/var/www/'.$naWebOS->domainFolder.'/NicerAppWebOS/siteLogs/'.$_GET['file'];
    //echo file_get_contents($fp); die();
    $html = file_get_contents($fp).'<script type="text/javascript">na.site.settings.current.loadingApps = false; na.site.settings.current.running_loadTheme = false; setTimeout (na.hms.startProcessing, 1000); na.site.transformLinks()</script>';

    if ($naLAN) $c = [ 'siteContent' => $html ];
    elseif (
        $_GET['file'] == $authorizedFile1
        || $_GET['file'] == $authorizedFile2
    ) $c = [ 'siteContent' =>  $html ];
    else $c = [ 'siteContent' => '403 Forbidden.' ];

    //echo '<pre>'; var_dump ($_SERVER); die();
    if (
        $_SERVER['SCRIPT_FILENAME']!=='/var/www/'.$naWebOS->domainFolder.'/NicerAppWebOS/index.php'
    ) {
        echo $html;
    } else {
        echo $naWebOS->getSite($c);
    }

} elseif (array_key_exists('seoValue',$_GET) && $_GET['seoValue']==='logs') {
//} elseif ($naLAN) {

    //$excl = '/(?!.*thumbs).*/'; // exclude anything that includes 'thumbs' in it's filepath.
    $excl = null;
    $folder = dirname(__FILE__).'/siteLogs/';
    $files = getFilePathList ($folder, true, FILE_FORMATS_html, $excl, array('file'))['files'];
    asort($files);
    $html = '';
    foreach ($files as $i => $fp) {
        $fp2 = str_replace('/var/www/'.$naWebOS->domainFolder.'/NicerAppWebOS/siteLogs/', '', $fp);
        $url = '/view/logs?file='.$fp2;
        $html .= '<a href="'.$url.'">'.$fp.'</a><br/>';
    }
    $html .= '<script type="text/javascript">setTimeout (function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; na.hms.startProcessing()}, 1500); na.site.transformLinks()</script>';
    $c = [ 'siteContent' => $html ];

    if (
        $_SERVER['REDIRECT_URL']=='/view/logs'
        && !array_key_exists('file',$_GET)
    ) {
        echo $naWebOS->getSite($c);
    } else {
        echo $html;
    }

} else die('403 Forbidden.');
echo '<pre>';var_dump ($_GET); die();
?>
