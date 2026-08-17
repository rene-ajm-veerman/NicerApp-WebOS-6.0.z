<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);    

$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
//var_dump ($rootPath); die();

require_once ($rootPath.'/NicerAppWebOS/boot.php');
global $naWebOS;
require_once ($rootPath.'/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/functions.php');
$view = $naWebOS->view['/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsText'];//json_decode (decode_base64_url($_GET['apps']), true);

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}


*/


?>
<script type="text/javascript">
/*
    na.m.waitForCondition ('page loaded?', function() {
        return na.site /*&& na.site.settings.onload_phase2__alreadyCalled * / && na.m.HTMLidle();
    }, function() {
        setTimeout(na.site.onload_phase2, 500);
    }, 100);
    */
    na.d.s.visibleDivs.push ('#siteComments');
    na.d.resize();
</script>

<!--<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ -- ><div></div><div></div><div></div></div> -->
<!--<pre><?php //echo json_encode($view, JSON_PRETTY_PRINT);?></pre>-->

<?php
global $naWebOS;
$cdb = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;

$cdb->setDatabase(str_replace('cms_tree','cms_documents',$view['database']),false);
try { $call = $cdb->get ($view['id']); } catch (Exception $e) {
    echo $e->getMessage();
    echo '<pre>'; var_dump ($call); echo '</pre>';
    exit();

};

$doc = $call->body->document;
//echo '<pre style="color:purple;background:yellow;">'; var_dump($doc); echo '</pre>'; //exit;



$p = preg_match_all ('/<p>.*?:({"mediaFolder":".*?",".*?":["\d]*.*["\d]*}):.*?<\/p>/', $doc, $matches, PREG_OFFSET_CAPTURE);
$p = preg_match_all ('/<p>.*?:({"mediaFolder":".*?",".*?"}):.*?<\/p>/', $doc, $matches, PREG_OFFSET_CAPTURE);
if (count($matches[0])===0) {
    $p = preg_match_all ('/<p>.*?:({"mediaFolder":".*?"}):.*?<\/p>/', $doc, $matches, PREG_OFFSET_CAPTURE);
}
// Make sure we have a string
if (is_object($doc)) {
    $doc = $doc->document ?? $doc->content ?? $doc->html ?? json_encode($doc);
}
$doc = (string)$doc;
//echo '<pre style="color:yellow;background:black;">'; var_dump (htmlentities($doc)); var_dump ($matches); echo '</pre><br/>';// exit();
//echo '<pre style="color:yellow;background:blue;">'; var_dump(array_key_exists(1, $matches)); //exit;
if (
    array_key_exists(1, $matches)
    && count($matches[1])>0
) foreach ($matches[1] as $idx => $match) {
    //var_dump ($idx); var_dump($match); exit;

     if (is_string($match[0])) {
         $cmd = json_decode ($match[0], true);
         //echo '<pre style="color:yellow;background:black;">'; var_dump (htmlentities($match[0])); echo '</pre><br/>'; //exit();
         if (array_key_exists('mediaFolder',$cmd)) {
             //echo '<pre style="color:yellow;background:navy;">'; var_dump (htmlentities($doc)); echo '</pre><br/>'; //exit();
             //echo '<pre style="color:lime;background:navy;">'; var_dump ($matches[0][$idx]); echo '</pre><br/>';exit();

             $doc = str_replace ($matches[0][$idx][0], naPhotoAlbum($cmd), $doc);
             //echo '<pre style="color:yellow;background:navy;">'; var_dump (htmlentities($doc)); echo '</pre><br/>'; //exit();
         }
     }
}
$doc = str_replace ('<p>\s+</p>', '<div style="height:1em"></div>', $doc);
//echo '<pre style="color:white;background:black;">'; var_dump (htmlentities($doc)); var_dump ($matches); echo '</pre><br/>'; //exit();
$doc = str_replace ('<p>&nbsp;</p>', '<div style="height:1em"></div>', $doc);
//echo '<pre style="color:purple;background:cyan;">'; var_dump (htmlentities($doc)); var_dump ($matches); echo '</pre><br/>';// exit();


//echo '<h1 style="color:blue">Output</h1>';
echo $doc;

?>
<script type="text/javascript">
        na.site.settings.loadingApps = false;
        na.site.settings.startingApps = false;
</script>
