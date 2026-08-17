<?php 
require_once (dirname(__FILE__).'/boot.php');

global $nicerapp_bar_factorySettings;
global $nicerapp_bar; // backup and restore class instance.
global $naWebOS;
$naWebOS->init();
$view = $naWebOS->view;

$fn = 'app.error.dialog.siteContent.php';
if (
    array_key_exists('app.2D.backupAndRestore.v1.0.0', $view)
    && array_key_exists('page', $view['app.2D.backupAndRestore.v1.0.0'])
    && is_string($view['app.2D.backupAndRestore.v1.0.0']['page'])
) switch ($view['app.2D.backupAndRestore.v1.0.0']['page']) {
    case 'createBackup':
        $fn = 'app.content.createBackup.php';
        break;
    case 'restoreBackup':
        $fn = 'app.content.restoreBackup.php';
        break;
    default:
        break;
}
echo execPHP(dirname(__FILE__).'/'.$fn, false);
?>
