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
}*/
global $naWebOS;
$cdb = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;

$cdb->setDatabase(str_replace('_tree', '_documents', $view['database']),false);
//echo '<pre style="color:lime;background:rgba(0,0,50,0.7);">'; var_dump ($view); echo '</pre>';

try { $call = $cdb->get ($view['id']); } catch (Exception $e) { echo $e->getMessage(); exit(); };

echo $call->body->pageTitle.' on Said.by';
?>
