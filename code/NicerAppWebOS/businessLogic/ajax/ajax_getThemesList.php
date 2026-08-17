<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

$debug = false;
if ($debug) { echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;  }

global $naWebOS;
$cdbDomain = $naWebOS->domainFolderForDB;

$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
    session_start();
}

$dbName = $cdbDomain.'___data_themes';
$cdb->setDatabase($dbName, false);    
$call = $cdb->getAllDocs();

$themes = [ 0 => 'default' ];
foreach ($call->body->rows as $idx => $row) {
    $call2 = $cdb->get($row->id);
    //var_dump ($call2);
    if (property_exists($call2->body,'theme')) {
        $theme = $call2->body->theme;
        if (!in_array($theme, $themes) && $theme!=='') array_push ($themes, $theme);
    }
}
//var_dump ($themes); exit();

foreach ($themes as $idx => $theme) {
    if ($theme!=='') echo '<div id="div_theme_'.$idx.'" value="'.$theme.'">'.$theme.'</option>';
}
?>
