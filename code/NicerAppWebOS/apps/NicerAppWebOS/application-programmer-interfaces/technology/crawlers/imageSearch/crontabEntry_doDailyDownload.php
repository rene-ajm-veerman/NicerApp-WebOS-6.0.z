#!/usr/local/bin/php
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once (dirname(__FILE__).'/boot.php');

if (true) {
    $plugin = new wallpaperScraper_plugin_imagesGoogleCom();
    $plugin->readDB();
    $result = $plugin->doDailyDownload();
    $result = $plugin->retryFailedDownloads();
}

if (false) {
    $plugin = new wallpaperScraper_plugin_bingComImages();
    $plugin->readDB();
    $result = $plugin->doDailyDownload();
    $result = $plugin->retryFailedDownloads();
}
?>
