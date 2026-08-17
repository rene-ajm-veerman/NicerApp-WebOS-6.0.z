<?php 
require_once (realpath(dirname(__FILE__).'/../../../..').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/class.NicerApp.backupAndRestore-1.0.0.php');

// main NicerApp cms PHP class instance, created and initialized in .../NicerAppWebOS/boot.php
global $naWebOS;

// couchdb database class instance (.../NicerAppWebOS/3rd-party/sag/src/Sag.php)
// created and initialized in .../NicerAppWebOS/boot.php
global $cdb; 

global $nicerapp_bar_factorySettings;
$nicerapp_bar_factorySettings = null; // null until it needs to change into a recursive array.

global $nicerapp_bar; // backup and restore class instance.
$nicerapp_bar = new class_NicerApp_backupAndRestore($nicerapp_bar_factorySettings);


$couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../../..').'/domainConfig/'.$naWebOS->domainFolder.'/couchdb.json';
$couchdbConfigExampleFilepath = realpath(dirname(__FILE__).'/../../../..').'/domainConfig/'.$naWebOS->domainFolder.'/couchdb.EXAMPLE.json';
if (!file_exists($couchdbConfigFilepath)) {
    trigger_error ('file "'.$couchdbConfigFilepath.'" does not exist. see "'.$couchdbConfigExampleFilepath.'" for an example template file.', E_USER_ERROR);
}
$cdbConfigRaw = file_get_contents($couchdbConfigFilepath);
$cdbConfig = json_decode ($cdbConfigRaw, true);
checkForJSONerrors($cdbConfigRaw, $couchdbConfigFilepath, $couchdbConfigExampleFilepath);

global $userID_username;
$userID_username = $_SESSION['cdb_loginName'];
$userID_username = str_replace(' ', '__', $username);
$userID_username = str_replace('.', '_', $username);
global $userID_roles;
$userID_roles = $cdb->getSession()->body->roles;

$cdbFunctional = true;
if ($cdbFunctional) {

    $dbName = $naWebOS->domainFolder.'___backup_and_restore';
    
    $dbName = $cdb->dataSetName ($dbName);
    
    
}
?>
